<?php

namespace App\Http\Controllers;

use App\Models\DirectBill;
use App\Models\StockItem;
use App\Models\StockBatch;
use App\Models\User;
use App\Models\Customer; // Ensure this model exists
use App\Models\BillPayment; // Import BillPayment
use App\Models\TyreRepair;
use App\Http\Requests\StoreDirectBillRequest;
use App\Http\Requests\UpdateDirectBillRequest;
use App\Traits\Loggable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DirectBillController extends Controller
{
    use Loggable;
    public function __construct()
    {
        $this->middleware('can:view direct bills')->only(['index', 'show', 'suggestions']);
        $this->middleware('can:create direct bills')->only(['create', 'store']);
        $this->middleware('can:edit direct bills')->only(['edit', 'update']);
        $this->middleware('can:delete direct bills')->only(['destroy']);
        $this->middleware('can:print direct bills')->only(['print']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = DirectBill::query();

        if (!$user->hasRole(['Administrator', 'ATH Admin'])) {
            $query->where('user_id', $user->id);
        }

        // Handle Search
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('bill_number', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Handle Search
        if ($request->filled('customer')) {
            $searchTerm = $request->input('customer');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('customer_name', 'LIKE', "%{$searchTerm}%");
            });
        }
        // Handle Date From
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        // Handle Date To
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Handle Payment Type Filter (Optional Bonus)
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Handle Status Filter (Optional Bonus)
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        $customers = Customer::select('name')->distinct()->orderBy('name')->get();
        $perPage = $request->input('per_page', 10);

        $directBills = $query->with('user', 'items')->latest()->paginate($perPage);
        return view('direct_bills.index', compact('directBills', 'customers'));
    }

     public function create()
    {
        $lastBill = DirectBill::latest('id')->first();
        $newBillNumber = $lastBill ? (int) $lastBill->bill_number + 1 : 4886;

        // Load customers with credit_limit + compute current outstanding (total_balance)
        // Adjust query to select id if you later want to use customer_id on bills
        $customers = Customer::select('id', 'name', 'mobile', 'credit_limit')
            ->whereNotNull('name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('name') // keep unique names if your app uses names as identifier
            ->values()
            ->map(function ($c) {
                // Compute outstanding: prefer a cached column (e.g. total_balance) if you have one.
                // Here we compute from DirectBill by customer name to stay compatible with your current schema.
                $outstanding = (float) DirectBill::where('customer_name', $c->name)->sum('balance');
                $c->total_balance = $outstanding;
                // Ensure credit_limit exists and cast to float
                $c->credit_limit = is_null($c->credit_limit) ? 0.0 : (float) $c->credit_limit;
                return $c;
            });

        $stockItems = StockItem::select('id', 'name')
            ->withSum(['stockBatches' => fn($q) => $q->where('quantity', '>', 0)], 'quantity')
            ->get()
            ->map(function ($item) {
                $item->stock_on_hand = $item->stock_batches_sum_quantity ?? 0;
                $oldestAvailableBatch = $item->stockBatches()->reorder()->where('quantity', '>', 0)->orderBy('created_at', 'ASC')->first();
                if ($oldestAvailableBatch) {
                    $item->selling_price = $oldestAvailableBatch->selling_price ?? 0;
                } else {
                    $latestBatch = $item->stockBatches()->reorder()->latest()->first();
                    $item->selling_price = $latestBatch->selling_price ?? 0;
                }
                unset($item->stock_batches_sum_quantity);
                return $item;
            });

        return view('direct_bills.create', compact('newBillNumber', 'stockItems', 'customers'));
    }

    public function store(StoreDirectBillRequest $request)
    {
        // SERVER-SIDE: Prevent creating a bill with requested quantities that exceed stock on hand.
        // Aggregate requested quantities per stock_item_id
        $requestedQuantities = collect($request->items ?? [])
            ->filter(fn($i) => !empty($i['stock_item_id']))
            ->groupBy('stock_item_id')
            ->map(fn($group) => collect($group)->sum(fn($it) => (float) ($it['quantity'] ?? 0)));

        if ($requestedQuantities->isNotEmpty()) {
            $stockItems = StockItem::whereIn('id', $requestedQuantities->keys())
                ->withSum(
                    [
                        'stockBatches' => function ($q) {
                            $q->where('quantity', '>', 0);
                        },
                    ],
                    'quantity',
                )
                ->get()
                ->keyBy('id');

            $errors = [];
            foreach ($requestedQuantities as $stockItemId => $qty) {
                $available = (float) ($stockItems[$stockItemId]->stock_batches_sum_quantity ?? 0);
                if ($qty > $available) {
                    $errors[] = "Requested quantity ({$qty}) for item '{$stockItems[$stockItemId]->name}' exceeds available stock ({$available}).";
                }
            }
            if (!empty($errors)) {
                throw ValidationException::withMessages(['items' => $errors]);
            }
        }

        $directBill = DB::transaction(function () use ($request) {
            // 1. Prepare Stock Items
            $itemIds = collect($request->items)->pluck('stock_item_id')->filter()->unique();
            $stockItems = StockItem::findMany($itemIds)->keyBy('id');

            $billTotal = 0;
            $totalItemDiscounts = 0;
            $itemsToCreate = [];

            // 2. Calculation Loop
            foreach ($request->items as $itemData) {
                $stockItem = null;
                $itemName = $itemData['item_name'];
                $unitPrice = $itemData['unit_price'];
                $quantity = $itemData['quantity'];
                $itemDiscount = $itemData['item_discount'] ?? 0;

                // --- NEW: Capture Job Number ---
                $jobNumber = $itemData['job_number'] ?? null;

                if (!empty($itemData['stock_item_id'])) {
                    $stockItem = $stockItems->get($itemData['stock_item_id']);
                    if ($stockItem) {
                        $itemName = $stockItem->name;
                    }
                }

                $rowSubtotal = $unitPrice * $quantity;
                $rowItemDiscountTotal = $itemDiscount * $quantity;
                $rowFinalPrice = $rowSubtotal - $rowItemDiscountTotal;

                $billTotal += $rowSubtotal;
                $totalItemDiscounts += $rowItemDiscountTotal;

                $itemsToCreate[] = [
                    'stock_item' => $stockItem,
                    'job_number' => $jobNumber, // --- NEW: Pass it to the next step ---
                    'data' => [
                        'stock_item_id' => $stockItem?->id,
                        'item_name' => $itemName,
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'item_discount' => $itemDiscount,
                        'total_price' => $rowFinalPrice,
                    ],
                ];
            }

            $additionalDiscount = $request->input('discount', 0);
            $totalDiscountStored = $totalItemDiscounts + $additionalDiscount;
            $finalAmount = $billTotal - $totalDiscountStored;

            // 3. Payment Logic
            $type = $request->input('type', 'cash');
            $paidAmount = 0;
            $balance = 0;
            $status = 'closed';

            if ($type === 'cash') {
                $paidAmount = $finalAmount;
                $balance = 0;
                $status = 'closed';
            } else {
                $paidAmount = $request->input('paid_amount', 0);
                $balance = $finalAmount - $paidAmount;
                $status = $balance <= 0 ? 'closed' : 'open';
            }

            // 4. Create Bill Header
            $directBill = DirectBill::create([
                'bill_number' => $request->bill_number,
                'customer_name' => $request->customer_name,
                'contact_number' => $request->contact_number,
                'vehicle' => $request->vehicle,
                'type' => $type,
                'bill_total' => $billTotal,
                'discount' => $totalDiscountStored,
                'final_amount' => $finalAmount,
                'paid' => $paidAmount,
                'balance' => $balance,
                'status' => $status,
                'note' => $request->note,
                'user_id' => Auth::id(),
            ]);

            // 5. Create Payment Record
            if ($paidAmount > 0) {
                BillPayment::create([
                    'direct_bill_id' => $directBill->id,
                    'amount' => $paidAmount,
                    'paid_date' => now(),
                    'payment_method' => $type === 'cash' ? 'Cash' : 'Cash/Initial',
                    'note' => 'Initial payment at bill creation',
                    'user_id' => Auth::id(),
                ]);
            }

            // 6. Item Creation Loop
            // IMPORTANT: Consolidate stock-item entries into a single line item per stock_item.
            foreach ($itemsToCreate as $item) {
                // A. Stock Items Logic (consolidate multiple batch assignments into one DB line)
                if ($item['stock_item']) {
                    // decrement from batches (returns array of ['batch_id'=>..., 'quantity'=>...])
                    $batchAssignments = $this->decrementStockFromBatchesWithTracking($item['stock_item'], $item['data']['quantity']);

                    // sum assigned quantities (should equal requested quantity unless shortage)
                    $totalAssignedQty = array_sum(array_map(fn($a) => $a['quantity'], $batchAssignments));
                    // use the first batch id as a representative batch_id (keeping previous behaviour minimally invasive)
                    $representativeBatchId = $batchAssignments[0]['batch_id'] ?? null;

                    $batchItemDiscount = $item['data']['item_discount'];
                    // compute total price using unit_price - discount per unit, times total qty
                    $batchTotalPrice = ($item['data']['unit_price'] - $batchItemDiscount) * $totalAssignedQty;

                    // Create a single consolidated line for this stock item
                    $directBill->items()->create([
                        'item_name' => $item['data']['item_name'],
                        'quantity' => $totalAssignedQty,
                        'unit_price' => $item['data']['unit_price'],
                        'item_discount' => $batchItemDiscount,
                        'total_price' => $batchTotalPrice,
                        'stock_item_id' => $item['data']['stock_item_id'],
                        'batch_id' => $representativeBatchId,
                    ]);

                    // Note: batchAssignments contains the per-batch breakdown. If later you need to restore stock
                    // per batch on bill deletion, consider persisting $batchAssignments (e.g. to a JSON column or a
                    // pivot table). Current implementation stores only a representative batch_id to remain
                    // compatible with existing schema while keeping a single visible line item.
                }
                // B. Generic / Tyre Repair Logic
                else {
                    $directBill->items()->create($item['data']);

                    // --- NEW LOGIC: Update Tyre Repair Table ---
                    if (!empty($item['job_number'])) {
                        // Find the repair job by job_number
                        $tyreRepair = TyreRepair::where('job_number', $item['job_number'])->first();

                        if ($tyreRepair) {
                            $tyreRepair->update([
                                'bill_number' => $directBill->bill_number,
                                // Optional: Update issued_date if not set
                                'issued_date' => $tyreRepair->issued_date ?? now(),
                            ]);
                        }
                    }
                }
            }

            return $directBill;
        });

        $this->logAction('Created a new Direct Bill - ' . $directBill->bill_number);

        return redirect()
            ->route('direct_bills.index')
            ->with([
                'success' => 'Direct Bill created successfully.',
                'print_bill_id' => $directBill->id,
            ]);
    }

    public function show(DirectBill $directBill)
    {
        $directBill->load(['user', 'items.stockItem', 'payments.user']); // Load payments history
        return view('direct_bills.show', compact('directBill'));
    }

    public function edit(DirectBill $directBill)
    {
        $this->authorizeUserAccess($directBill);
        $directBill->load('items.stockItem');

        // 1. FETCH CUSTOMERS (Missing in your original code)
        $customers = Customer::select('name', 'mobile')->whereNotNull('name')->orderBy('created_at', 'desc')->get()->unique('name')->values();

        // 2. FETCH STOCK ITEMS
        $stockItems = StockItem::select('id', 'name')
            ->withSum(['stockBatches' => fn($q) => $q->where('quantity', '>', 0)], 'quantity')
            ->get()
            ->map(function ($item) {
                $item->stock_on_hand = $item->stock_batches_sum_quantity ?? 0;
                $latestBatch = $item->stockBatches()->reorder()->latest()->first();
                $item->selling_price = $latestBatch->selling_price ?? 0;

                unset($item->stock_batches_sum_quantity);
                return $item;
            });

        return view('direct_bills.edit', compact('directBill', 'stockItems', 'customers'));
    }

    public function update(Request $request, DirectBill $directBill)
    {
        Log::info('Starting update for DirectBill ID: ' . $directBill->id);

        // 1. Validate inputs (Make sure totals are numeric)
        $request->validate([
            'bill_number' => 'required|string|max:255',
            'customer_name' => 'required|string',
            'contact_number' => 'nullable|string',
            'vehicle' => 'nullable|string',
            'note' => 'nullable|string',
            'type' => 'required|in:cash,credit',

            // Manual Totals Validation
            'bill_total' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0', // This is "Additional Discount"
            'final_amount' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0',
            'balance' => 'required|numeric',

            // Item Validation
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_type' => 'nullable|in:amount,percentage',
            'items.*.discount_rate' => 'nullable|numeric',
        ]);

        try {
            $updatedBill = DB::transaction(function () use ($request, $directBill) {
                $this->authorizeUserAccess($directBill);

                // 2. Restore Stock from OLD items
                foreach ($directBill->items as $oldItem) {
                    if ($oldItem->stock_item_id && $oldItem->batch_id) {
                        $this->restoreStockToSpecificBatch($oldItem->batch_id, $oldItem->quantity, 'DirectBill Update - Reverting Bill');
                    }
                }

                // 3. Delete Old Items
                $directBill->items()->delete();

                // 4. Process NEW Items (To get Item Discounts & Stock Logic)
                $itemsArray = array_values($request->items);
                $itemIds = collect($itemsArray)->pluck('stock_item_id')->filter()->unique();
                $stockItems = StockItem::findMany($itemIds)->keyBy('id');

                $totalItemDiscounts = 0; // We still calculate this to save correct DB value
                $itemsToCreate = [];

                foreach ($itemsArray as $itemData) {
                    $stockItem = null;
                    $itemName = $itemData['item_name'];
                    $unitPrice = $itemData['unit_price'];
                    $quantity = $itemData['quantity'];
                    $itemDiscount = $itemData['item_discount'] ?? 0;

                    if (!empty($itemData['stock_item_id'])) {
                        $stockItem = $stockItems->get($itemData['stock_item_id']);
                        if ($stockItem) {
                            $itemName = $stockItem->name;
                        }
                    }

                    // Calculate row discount for summation
                    $rowItemDiscountTotal = $itemDiscount * $quantity;
                    $totalItemDiscounts += $rowItemDiscountTotal;

                    // Row total for item table (optional, or use manual if you pass it)
                    $rowSubtotal = $unitPrice * $quantity;
                    $rowFinalPrice = $rowSubtotal - $rowItemDiscountTotal;

                    $itemsToCreate[] = [
                        'stock_item' => $stockItem,
                        'data' => [
                            'stock_item_id' => $stockItem?->id,
                            'item_name' => $itemName,
                            'unit_price' => $unitPrice,
                            'quantity' => $quantity,
                            'item_discount' => $itemDiscount,
                            'total_price' => $rowFinalPrice,
                        ],
                    ];
                }

                // 5. UPDATE BILL DETAILS (MANUAL OVERRIDES)

                // Logic: DB Discount = Sum of Item Discounts + Additional Discount input
                $additionalDiscount = $request->input('discount', 0);
                $dbTotalDiscount = $totalItemDiscounts + $additionalDiscount;

                // Status based on manually entered balance
                $manualBalance = $request->input('balance');
                $newStatus = $manualBalance <= 0.001 ? 'closed' : 'open';

                $directBill->update([
                    'bill_number' => $request->bill_number,
                    'customer_name' => $request->customer_name,
                    'contact_number' => $request->contact_number,
                    'vehicle' => $request->vehicle,
                    'note' => $request->note,

                    // --- MANUAL VALUES ---
                    'bill_total' => $request->input('bill_total'),
                    'discount' => $dbTotalDiscount, // Calculated sum
                    'final_amount' => $request->input('final_amount'),
                    'paid' => $request->input('paid'),
                    'balance' => $manualBalance,
                    'type' => $request->input('type'),
                    'status' => $newStatus,
                ]);

                // 6. Create New Items in DB
                foreach ($itemsToCreate as $item) {
                    if ($item['stock_item']) {
                        $batchAssignments = $this->decrementStockFromBatchesWithTracking($item['stock_item'], $item['data']['quantity']);

                        foreach ($batchAssignments as $assignment) {
                            $batchQty = $assignment['quantity'];
                            $batchItemDiscount = $item['data']['item_discount'];
                            $batchTotalPrice = ($item['data']['unit_price'] - $batchItemDiscount) * $batchQty;

                            $directBill->items()->create([
                                'item_name' => $item['data']['item_name'],
                                'quantity' => $batchQty,
                                'unit_price' => $item['data']['unit_price'],
                                'item_discount' => $batchItemDiscount,
                                'total_price' => $batchTotalPrice,
                                'stock_item_id' => $item['data']['stock_item_id'],
                                'batch_id' => $assignment['batch_id'],
                            ]);
                        }
                    } else {
                        $directBill->items()->create($item['data']);
                    }
                }

                return $directBill;
            });
        } catch (\Exception $e) {
            Log::error("Error updating DirectBill ID {$directBill->id}: " . $e->getMessage(), ['exception' => $e]);
            return redirect()
                ->route('direct_bills.edit', $directBill)
                ->with('error', 'Failed to update bill. ' . $e->getMessage());
        }

        $this->logAction('Updated Direct Bill - ' . $directBill->bill_number);

        return redirect()->route('direct_bills.index')->with('success', 'Direct Bill updated successfully.');
    }

    public function destroy(DirectBill $directBill)
    {
        $this->authorizeUserAccess($directBill);

        DB::transaction(function () use ($directBill) {
            foreach ($directBill->items as $item) {
                if ($item->stock_item_id && $item->batch_id) {
                    $this->restoreStockToSpecificBatch($item->batch_id, $item->quantity, 'DirectBill Deletion');
                }
            }
            $directBill->delete();
        });

        $this->logAction('Deleted Direct Bill - ' . $directBill->bill_number);

        return redirect()->route('direct_bills.index')->with('success', 'Direct Bill deleted successfully.');
    }

    public function print(DirectBill $directBill)
    {
        $directBill->load('user', 'items.stockItem');
        return view('direct_bills.print', compact('directBill'));
    }

    // --- NEW: Search Repair Jobs for adding to Bill ---
    public function searchRepairJobs(Request $request)
    {
        $query = $request->get('query');
        $jobs = TyreRepair::where('job_number', 'like', "%{$query}%")
            ->where('received_from_company_date', '!=', null)
            ->where('issued_date', '=', null)
            ->get()
            ->map(function ($job) {
                return [
                    'job_number' => $job->job_number,
                    'customer' => $job->customer->name ?? 'Unknown',
                    'tyre_info' => trim("{$job->tyre_size} {$job->tyre_make}"),
                    'amount' => $job->amount ?? 0,
                    'item_number' => $job->item_number,
                    'id' => $job->id,
                ];
            });

        return response()->json($jobs);
    }

    private function authorizeUserAccess(DirectBill $directBill)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Authentication required.');
        }
        if ($user->hasRole(['Administrator', 'ATH Admin'])) {
            return;
        }
        if ($directBill->user_id !== $user->id) {
            abort(403, 'You are not authorized to access this resource.');
        }
    }

    private function decrementStockFromBatchesWithTracking(StockItem $stockItem, int $quantityToDecrement): array
    {
        $quantityLeftToDecrement = $quantityToDecrement;
        $batchAssignments = [];
       $batches = $stockItem->stockBatches()->reorder()->where('quantity', '>', 0)->orderBy('created_at', 'ASC')->orderBy('id', 'ASC')->get();

        foreach ($batches as $batch) {
            if ($quantityLeftToDecrement <= 0) {
                break;
            }
            $quantityToTake = min($batch->quantity, $quantityLeftToDecrement);
            $batch->decrement('quantity', $quantityToTake);
            $quantityLeftToDecrement -= $quantityToTake;
            $batchAssignments[] = ['batch_id' => $batch->id, 'quantity' => $quantityToTake];
            Log::info("Took {$quantityToTake} from Batch ID {$batch->id} for Item ID {$stockItem->id}.");
        }
        if ($quantityLeftToDecrement > 0) {
            Log::warning("Could not fulfill {$quantityLeftToDecrement} units for Item ID {$stockItem->id}.");
        }
        return $batchAssignments;
    }

    private function restoreStockToSpecificBatch(int $batchId, int $quantityToRestore, string $reason)
    {
        if ($quantityToRestore <= 0) {
            return;
        }
        $batch = StockBatch::find($batchId);
        if ($batch) {
            $batch->increment('quantity', $quantityToRestore);
            Log::info("Restored {$quantityToRestore} to Batch ID {$batchId}. Reason: {$reason}");
        } else {
            Log::warning("Batch ID {$batchId} not found.");
        }
    }

    public function getBatchInfo(Request $request)
    {
        $request->validate([
            'stock_item_id' => 'required|exists:stock_items,id',
            'price_type' => 'nullable|in:latest,fifo',
        ]);

        $stockItem = StockItem::findOrFail($request->stock_item_id);
        $priceType = $request->input('price_type', 'latest');

        // FIFO Batch (Oldest with stock)
        $fifoBatch = $stockItem->stockBatches()
            ->reorder()
            ->where('quantity', '>', 0)
            ->orderBy('created_at', 'ASC')
            ->orderBy('id', 'ASC')
            ->first();

        // Latest Batch (Newest created, regardless of stock?) 
        // Typically "Latest Price" means current selling price, which comes from latest batch.
        $latestBatch = $stockItem->stockBatches()
            ->reorder()
            ->latest()
            ->first();

        // Determine price based on selected mode
        $priceBatch = null;
        if ($priceType === 'fifo') {
            // FIFO: Use oldest batch with stock. Fallback to Latest if no stock.
            $priceBatch = $fifoBatch ?? $latestBatch;
        } else {
            // Latest: Use newest batch
            $priceBatch = $latestBatch;
        }

        // Calculate Total Stock On Hand for 'available_quantity'
        $totalStock = $stockItem->stockBatches()->where('quantity', '>', 0)->sum('quantity');

        return response()->json([
            'available_quantity' => $totalStock, // Return total stock, not just first batch
            'selling_price' => $priceBatch->selling_price ?? 0,
            'has_stock' => $totalStock > 0,
        ]);
    }
}

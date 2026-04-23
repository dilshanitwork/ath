<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Collection;
use App\Models\Payment;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StockItem;
use App\Models\StockBatch;
use Illuminate\Support\Facades\Log;

class BillController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view bills')->only(['index', 'show', 'suggestions']);
        $this->middleware('can:create bills')->only(['create', 'store']);
        $this->middleware('can:edit bills')->only(['edit', 'update']);
        $this->middleware('can:delete bills')->only(['destroy']);
        $this->middleware('can:print bills')->only(['print']);
        $this->middleware('can:printCollection bills')->only(['printCollection']);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $query = Bill::with('customer');

        if (auth()->user()->hasRole('Van User')) {
            $query->where('category', 1);
        } elseif (auth()->user()->hasRole('Showroom User')) {
            $query->where('category', 0);
        }

        if ($request->filled('customer_name')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%');
            });
        }

        if ($request->filled('customer_nic')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('nic', 'like', '%' . $request->customer_nic . '%');
            });
        }

        if ($request->filled('customer_mobile')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('mobile', 'like', '%' . $request->customer_mobile . '%');
            });
        }

        if ($request->filled('bill_number')) {
            $query->where('bill_number', 'like', '%' . $request->bill_number . '%');
        }

        $query->orderBy('created_at', 'desc');
        $bills = $query->paginate($perPage)->appends($request->query());

        return view('bills.index', compact('bills'));
    }

    public function print(Bill $bill)
    {
        $bill->load('customer', 'items', 'collections', 'user');
        return view('bills.print', compact('bill'));
    }

    public function printCollection(Bill $bill)
    {
        $bill->load('customer', 'items', 'user', 'collections.user');
        $lastCollection = $bill->collections()->latest('date')->first();

        if (!$lastCollection) {
            return redirect()->route('bills.show', $bill)->with('error', 'No collections found for this bill.');
        }

        return view('bills.collectionsPrint', compact('bill', 'lastCollection'));
    }

    public function create()
    {
        $query = Customer::query();

        if (auth()->user()->hasRole('Van User')) {
            $query->where('category', 1);
        } elseif (auth()->user()->hasRole('Showroom User')) {
            $query->where('category', 0);
        }

        $customers = $query->get();

        $stockItems = StockItem::select('id', 'name')
            ->withSum(['stockBatches' => fn($q) => $q->where('quantity', '>', 0)], 'quantity')
            ->get()
            ->map(function ($item) {
                $item->stock_on_hand = $item->stock_batches_sum_quantity ?? 0;
                $oldestAvailableBatch = $item->stockBatches()->reorder()->where('quantity', '>', 0)->orderBy('created_at', 'ASC')->first();

                if ($oldestAvailableBatch) {
                    $item->installment_price = $oldestAvailableBatch->installment_price ?? 0;
                } else {
                    $latestBatch = $item->stockBatches()->reorder()->latest()->first();
                    $item->installment_price = $latestBatch->installment_price ?? 0;
                }

                unset($item->stock_batches_sum_quantity);
                return $item;
            });

        $isShowroomUser = auth()->user()->hasRole('Showroom User');
        $isVanUser = auth()->user()->hasRole('Van User');

        return view('bills.create', compact('customers', 'stockItems', 'isShowroomUser', 'isVanUser'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bill_number' => 'required|unique:bills',
            'customer_id' => 'required|exists:customers,id',
            'total_price' => 'required|numeric',
            'advance_payment' => 'required|numeric',
            'balance' => 'required|numeric',
            'next_payment' => 'required|numeric',
            'installments' => 'required|integer|min:1',
            'type' => 'required|in:1,2,3',
            'items' => 'required|array',
            'items.*.item_name' => 'required|string',
            'items.*.item_quantity' => 'required|integer',
            'items.*.item_price' => 'required|numeric',
            'items.*.total_price' => 'required|numeric',
        ]);

        $category = null;
        if (auth()->user()->hasRole('Showroom User')) {
            $category = 0;
        } elseif (auth()->user()->hasRole('Van User')) {
            $category = 1;
        } else {
            $request->validate(['category' => 'required|in:0,1']);
            $category = $request->category;
        }

        $installment_payment = $request->balance / max(1, $request->installments);

        $bill = DB::transaction(function () use ($request, $category, $installment_payment) {
            $bill = Bill::create(
                array_merge($request->only('bill_number', 'customer_id', 'total_price', 'advance_payment', 'balance', 'type', 'next_bill', 'next_payment', 'installments'), [
                    'user_id' => auth()->id(),
                    'installment_payment' => $installment_payment,
                    'category' => $category,
                    'payment_type' => $request->payment_type ?? 'cash',
                    'next_bill' => is_array($request->bill_dates) && count($request->bill_dates) > 0 ? $request->bill_dates[0] : null,
                ]),
            );

            // Use batch tracking for stock items
            foreach ($request->items as $item) {
                if (!empty($item['stock_item_id'])) {
                    $stockItem = StockItem::find($item['stock_item_id']);
                    if ($stockItem) {
                        // Get batch assignments for this quantity
                        $batchAssignments = $this->decrementStockFromBatchesWithTracking($stockItem, $item['item_quantity']);

                        // Create separate bill items for each batch
                        foreach ($batchAssignments as $assignment) {
                            $bill->items()->create([
                                'item_name' => $item['item_name'],
                                'item_quantity' => $assignment['quantity'],
                                'item_price' => $item['item_price'],
                                'total_price' => $assignment['quantity'] * $item['item_price'],
                                'stock_item_id' => $item['stock_item_id'],
                                'batch_id' => $assignment['batch_id'], // NEW FIELD
                            ]);
                        }
                    } else {
                        $bill->items()->create($item);
                    }
                } else {
                    $bill->items()->create($item);
                }
            }

            $this->createPaymentSchedule($bill, $request->bill_dates);
            return $bill;
        });

        // SMS notification
        $mobile = $bill->customer->mobile;
        $message = 'Hi ' . $bill->customer->name . '! Bill No: ' . $bill->bill_number . '. Total: Rs. ' . number_format($bill->total_price, 2) . '. Advance paid: Rs. ' . number_format($bill->advance_payment, 2) . '. Next payment date: ' . $bill->next_bill . '. Thanks! - Tyre Management System';
        $fname = 'Chamara';
        $lname = 'Lanka';
        // $smsResponse = app(\App\Http\Controllers\SmsController::class)->sendSmsToMobile($mobile, $message, $fname, $lname);

        return redirect()->route('bills.index')->with('success', 'Bill created successfully.');
    }

    protected function createPaymentSchedule(Bill $bill, array $billDates)
    {
        $paymentSchedules = [];
        foreach ($billDates as $paymentDate) {
            $paymentSchedules[] = [
                'bill_id' => $bill->id,
                'payment_date' => $paymentDate,
            ];
        }
        \App\Models\BillPaymentSchedule::insert($paymentSchedules);
    }

    public function show(Bill $bill)
    {
        $bill->load('customer', 'items', 'collections', 'user');
        return view('bills.show', compact('bill'));
    }

    public function edit(Bill $bill)
    {
        $bill->load('customer', 'items', 'paymentSchedules');
        $customers = Customer::all();

        $stockItems = StockItem::select('id', 'name')
            ->withSum(['stockBatches' => fn($q) => $q->where('quantity', '>', 0)], 'quantity')
            ->get()
            ->map(function ($item) {
                $item->stock_on_hand = $item->stock_batches_sum_quantity ?? 0;
                $newestAvailableBatch = $item->stockBatches()->reorder()->where('quantity', '>', 0)->orderBy('created_at', 'ASC')->first();

                if ($newestAvailableBatch) {
                    $item->installment_price = $newestAvailableBatch->installment_price ?? 0;
                } else {
                    $latestBatch = $item->stockBatches()->reorder()->latest()->first();
                    $item->installment_price = $latestBatch->installment_price ?? 0;
                }

                unset($item->stock_batches_sum_quantity);
                return $item;
            });

        return view('bills.edit', compact('bill', 'customers', 'stockItems'));
    }

    public function update(Request $request, Bill $bill)
    {
        $request->validate([
            'bill_number' => 'required|unique:bills,bill_number,' . $bill->id,
            'customer_id' => 'required|exists:customers,id',
            'total_price' => 'required|numeric',
            'advance_payment' => 'required|numeric',
            'balance' => 'required|numeric',
            'next_payment' => 'required|numeric',
            'type' => 'required|in:1,2,3',
            'items' => 'required|array',
            'items.*.item_name' => 'required|string',
            'items.*.item_quantity' => 'required|integer',
            'items.*.item_price' => 'required|numeric',
            'items.*.total_price' => 'required|numeric',
            'payment_schedule' => 'required|array',
            'payment_schedule.*' => 'required|date',
        ]);

        //Store batch_id along with other item info
        $existingItemsMap = [];
        foreach ($bill->items as $oldItem) {
            $existingItemsMap[$oldItem->id] = [
                'stock_item_id' => $oldItem->stock_item_id,
                'quantity' => $oldItem->item_quantity,
                'batch_id' => $oldItem->batch_id,
            ];
        }

        DB::transaction(function () use ($request, $bill, $existingItemsMap) {
            $processedItemIds = [];

            $bill->update($request->only('bill_number', 'customer_id', 'total_price', 'advance_payment', 'balance', 'type', 'next_payment', 'next_bill', 'category', 'installments'));

            $bill->items()->delete();

            foreach ($request->items as $itemData) {
                $stockItemId = $itemData['stock_item_id'] ?? null;
                $billItemId = $itemData['id'] ?? null;

                if ($billItemId && isset($existingItemsMap[$billItemId])) {
                    // Existing item
                    $oldData = $existingItemsMap[$billItemId];
                    $oldStockItemId = $oldData['stock_item_id'];
                    $oldQuantity = $oldData['quantity'];
                    $oldBatchId = $oldData['batch_id'];
                    $newQuantity = $itemData['item_quantity'];

                    if ($stockItemId != $oldStockItemId) {
                        // CHANGED: Restore to specific batch
                        if ($oldStockItemId && $oldBatchId) {
                            $this->restoreStockToSpecificBatch($oldBatchId, $oldQuantity, 'Bill Update - Item Changed');
                        }

                        if ($stockItemId) {
                            $stockItem = StockItem::find($stockItemId);
                            if ($stockItem) {
                                $batchAssignments = $this->decrementStockFromBatchesWithTracking($stockItem, $newQuantity);
                                foreach ($batchAssignments as $assignment) {
                                    $bill->items()->create([
                                        'item_name' => $itemData['item_name'],
                                        'item_quantity' => $assignment['quantity'],
                                        'item_price' => $itemData['item_price'],
                                        'total_price' => $assignment['quantity'] * $itemData['item_price'],
                                        'stock_item_id' => $stockItemId,
                                        'batch_id' => $assignment['batch_id'],
                                    ]);
                                }
                            }
                        } else {
                            $bill->items()->create($itemData);
                        }
                    } else {
                        // Handle batch tracking
                        if ($stockItemId && $oldBatchId) {
                            $quantityDiff = $newQuantity - $oldQuantity;

                            if ($quantityDiff > 0) {
                                // Increased - restore old, get new
                                $this->restoreStockToSpecificBatch($oldBatchId, $oldQuantity, 'Bill Update - Quantity Adjustment');

                                $stockItem = StockItem::find($stockItemId);
                                if ($stockItem) {
                                    $batchAssignments = $this->decrementStockFromBatchesWithTracking($stockItem, $newQuantity);
                                    foreach ($batchAssignments as $assignment) {
                                        $bill->items()->create([
                                            'item_name' => $itemData['item_name'],
                                            'item_quantity' => $assignment['quantity'],
                                            'item_price' => $itemData['item_price'],
                                            'total_price' => $assignment['quantity'] * $itemData['item_price'],
                                            'stock_item_id' => $stockItemId,
                                            'batch_id' => $assignment['batch_id'], // NEW
                                        ]);
                                    }
                                }
                            } elseif ($quantityDiff < 0) {
                                // Decreased - restore difference to same batch
                                $this->restoreStockToSpecificBatch($oldBatchId, abs($quantityDiff), 'Bill Update - Quantity Reduced');

                                $bill->items()->create([
                                    'item_name' => $itemData['item_name'],
                                    'item_quantity' => $newQuantity,
                                    'item_price' => $itemData['item_price'],
                                    'total_price' => $itemData['total_price'],
                                    'stock_item_id' => $stockItemId,
                                    'batch_id' => $oldBatchId, // KEEP SAME BATCH
                                ]);
                            } else {
                                // No change
                                $bill->items()->create([
                                    'item_name' => $itemData['item_name'],
                                    'item_quantity' => $newQuantity,
                                    'item_price' => $itemData['item_price'],
                                    'total_price' => $itemData['total_price'],
                                    'stock_item_id' => $stockItemId,
                                    'batch_id' => $oldBatchId, // KEEP SAME BATCH
                                ]);
                            }
                        } else {
                            $bill->items()->create($itemData);
                        }
                    }

                    $processedItemIds[] = $billItemId;
                } elseif ($stockItemId) {
                    // New item
                    $stockItem = StockItem::find($stockItemId);
                    if ($stockItem) {
                        $batchAssignments = $this->decrementStockFromBatchesWithTracking($stockItem, $itemData['item_quantity']);
                        foreach ($batchAssignments as $assignment) {
                            $bill->items()->create([
                                'item_name' => $itemData['item_name'],
                                'item_quantity' => $assignment['quantity'],
                                'item_price' => $itemData['item_price'],
                                'total_price' => $assignment['quantity'] * $itemData['item_price'],
                                'stock_item_id' => $stockItemId,
                                'batch_id' => $assignment['batch_id'],
                            ]);
                        }
                    } else {
                        $bill->items()->create($itemData);
                    }
                } else {
                    $bill->items()->create($itemData);
                }
            }

            // Restore deleted items to their specific batches
            foreach ($existingItemsMap as $oldItemId => $oldData) {
                if (!in_array($oldItemId, $processedItemIds)) {
                    if ($oldData['stock_item_id'] && $oldData['batch_id']) {
                        $this->restoreStockToSpecificBatch($oldData['batch_id'], $oldData['quantity'], 'Bill Update - Item Removed');
                    }
                }
            }

            $bill->paymentSchedules()->delete();
            foreach ($request->payment_schedule as $paymentDate) {
                $bill->paymentSchedules()->create(['payment_date' => $paymentDate]);
            }
        });

        return redirect()->route('bills.index')->with('success', 'Bill updated successfully.');
    }

    public function editCollection(Collection $collection)
    {
        session(['editing_collection' => $collection->id]);
        return redirect()->back();
    }

    public function updateCollection(Request $request, Collection $collection)
    {
        $request->validate([
            'payment' => 'required|numeric|min:0.01',
            'type' => 'required|in:cash,card,online',
            'date' => 'required|date',
        ]);
        $collection->update($request->only('payment', 'type', 'date'));
        session()->forget('editing_collection');
        return redirect()->route('bills.show', $collection->bill_id)->with('success', 'Collection updated successfully.');
    }

    public function cancelEdit()
    {
        session()->forget('editing_collection');
        return redirect()->back();
    }

    public function destroy(Bill $bill)
    {
        DB::transaction(function () use ($bill) {
            // Restore to specific batches
            foreach ($bill->items as $oldItem) {
                if ($oldItem->stock_item_id && $oldItem->batch_id) {
                    $this->restoreStockToSpecificBatch($oldItem->batch_id, $oldItem->item_quantity, 'Bill Deletion');
                }
            }

            $bill->delete();
        });

        return redirect()->route('bills.index')->with('success', 'Bill deleted successfully.');
    }

    public function addPayment(Request $request, Bill $bill)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'payment_type' => 'required|string',
        ]);

        $payment = new Payment([
            'amount' => $request->amount,
            'payment_type' => $request->payment_type,
        ]);

        $bill->payments()->save($payment);

        if ($request->payment_type == 'advance') {
            $bill->advance_payment += $request->amount;
        } else {
            $bill->balance -= $request->amount;
        }
        $bill->save();

        return redirect()->route('bills.show', $bill)->with('success', 'Payment added successfully.');
    }

    public function paymentPage(Request $request, Bill $bill)
    {
        $billNumber = $bill ? $bill->bill_number : null;
        return view('bills.payment', compact('bill', 'billNumber'));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'bill_number' => 'required|exists:bills,bill_number',
            'payment' => 'required|numeric|min:0.01',
        ]);

        $bill = Bill::where('bill_number', $request->bill_number)->firstOrFail();

        Collection::create([
            'bill_id' => $bill->id,
            'payment' => $request->payment,
            'type' => $request->type,
            'date' => now()->toDateString(),
            'user_id' => auth()->id(),
        ]);

        $bill->balance -= $request->payment;
        $bill->balance = max(0, $bill->balance);

        $lastCollectedPayment = $request->payment;
        $installmentPayment = $bill->installment_payment;

        if ($lastCollectedPayment == $bill->next_payment) {
            $bill->next_payment = $installmentPayment;
        } elseif ($lastCollectedPayment < $bill->next_payment) {
            $bill->next_payment = $installmentPayment + ($bill->next_payment - $lastCollectedPayment);
        } elseif ($lastCollectedPayment > $installmentPayment) {
            $bill->next_payment = $installmentPayment;
        }

        $bill->next_payment = min($bill->balance, $bill->next_payment);

        if ($bill->balance == 0) {
            $bill->status = 1;
        }

        $currentNextBill = $bill->next_bill;
        $compareDate = $currentNextBill && $currentNextBill > now()->toDateString() ? $currentNextBill : now()->toDateString();
        $nextBillSchedule = $bill->paymentSchedules()->where('payment_date', '>', $compareDate)->orderBy('payment_date', 'asc')->first();
        $bill->next_bill = $nextBillSchedule ? $nextBillSchedule->payment_date : null;
        $bill->save();

        $mobile = $bill->customer->mobile;
        $message = "Hi {$bill->customer->name}, Thank you! Rs." . number_format($request->payment, 2) . " received for Bill {$bill->bill_number}. Balance: Rs." . number_format($bill->balance, 2) . '. - Tyre Management System';
        $fname = 'Chamara';
        $lname = 'Lanka';
        $smsResponse = app(\App\Http\Controllers\SmsController::class)->sendSmsToMobile($mobile, $message, $fname, $lname);

        return redirect()->route('bills.show', $bill)->with('success', 'Payment processed successfully.');
    }

    public function destroyCollection(Collection $collection)
    {
        $bill = $collection->bill;
        $collection->delete();
        $totalCollections = $bill->collections()->sum('payment');
        $bill->balance = $bill->total_price - $bill->advance_payment - $totalCollections;
        $bill->save();
        return redirect()->route('bills.show', $bill->id)->with('success', 'Collection deleted and payable amount updated.');
    }

    private function decrementStockFromBatchesWithTracking(StockItem $stockItem, int $quantityToDecrement): array
    {
        $quantityLeftToDecrement = $quantityToDecrement;
        $batchAssignments = [];

        $batches = $stockItem
            ->stockBatches()
            ->reorder()
            ->where('quantity', '>', 0)
            ->orderBy('created_at', 'ASC') // FIFO
            ->get();

        foreach ($batches as $batch) {
            if ($quantityLeftToDecrement <= 0) {
                break;
            }

            $quantityToTake = min($batch->quantity, $quantityLeftToDecrement);
            $batch->decrement('quantity', $quantityToTake);
            $quantityLeftToDecrement -= $quantityToTake;

            // Return batch assignment info
            $batchAssignments[] = [
                'batch_id' => $batch->id,
                'quantity' => $quantityToTake,
            ];

            Log::info("Took {$quantityToTake} from Batch ID {$batch->id} for Item ID {$stockItem->id}. {$quantityLeftToDecrement} remaining to take.");
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
            Log::warning("Batch ID {$batchId} not found. Could not restore {$quantityToRestore} units. Reason: {$reason}");
        }
    }

    public function getBatchInfo(Request $request)
    {
        $request->validate([
            'stock_item_id' => 'required|exists:stock_items,id',
        ]);

        $stockItem = StockItem::findOrFail($request->stock_item_id);

        $oldestBatch = $stockItem->stockBatches()->reorder()->where('quantity', '>', 0)->orderBy('created_at', 'ASC')->first();

        if (!$oldestBatch) {
            return response()->json([
                'available_quantity' => 0,
                'installment_price' => 0,
                'has_stock' => false,
            ]);
        }

        return response()->json([
            'available_quantity' => $oldestBatch->quantity,
            'installment_price' => $oldestBatch->installment_price,
            'has_stock' => true,
        ]);
    }
}

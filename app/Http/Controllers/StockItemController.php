<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\Supplier;
use App\Traits\Loggable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import DB for transactions

class StockItemController extends Controller
{
    use Loggable;
    public function __construct()
    {
        $this->middleware('can:view stock items')->only(['index', 'show', 'create', 'edit', 'store', 'update', 'destroy']);
        // $this->middleware('can:create stock items')->only(['create', 'store']);
        // $this->middleware('can:edit stock items')->only(['edit', 'update']);
        // $this->middleware('can:delete stock items')->only(['destroy']);
    }

    public function index(Request $request)
    {
        // --- UPDATED QUERY ---
        // Eager load supplier and batches.
        // Add a 'withSum' to calculate total quantity from all batches.
        $query = StockItem::with('supplier', 'stockBatches')->withSum('stockBatches as total_quantity', 'quantity');

        // Apply filters
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // --- UPDATED QUANTITY FILTER ---
        // Use 'having' clause to filter by the calculated sum.
        if ($request->filled('quantity')) {
            $quantity = (int) $request->quantity;
            $operator = $request->input('quantity_operator', 'eq'); // default to 'eq'

            if ($operator === 'eq') {
                $query->having('total_quantity', '=', $quantity);
            } elseif ($operator === 'lte') {
                $query->having('total_quantity', '<=', $quantity);
            } elseif ($operator === 'gte') {
                $query->having('total_quantity', '>=', $quantity);
            }
        }

        // --- UPDATED COST PRICE FILTER ---
        // Use 'whereHas' to find items that have at least one batch
        // with the specified cost price.
        if ($request->filled('cost_price')) {
            $costPrice = (float) $request->cost_price;
            $query->whereHas('stockBatches', function ($q) use ($costPrice) {
                $q->where('cost_price', $costPrice);
            });
        }

        // Search items where ANY associated stock batch has this invoice number
        if ($request->filled('invoice_number')) {
            $invoiceNumber = $request->invoice_number;
            $query->whereHas('stockBatches', function ($q) use ($invoiceNumber) {
                // Using 'like' for partial matches, or use '=' for exact match
                $q->where('invoice_number', 'like', '%' . $invoiceNumber . '%');
            });
        }

        // Find low stock items
        $lowStockCount = $query->clone()->having('total_quantity', '<=', 3)->count();

        $perPage = $request->input('per_page', 10);
        $stockItems = $query->paginate($perPage);

        return view('stock_items.index', compact('stockItems', 'lowStockCount'));
    }

    public function autocomplete(Request $request)
    {
        $q = $request->query('q', '');

        if (trim($q) === '') {
            return response()->json([]);
        }

        // Search name, return unique names + ids, limit to 10 results
        $results = StockItem::select('id', 'name')
            ->where('name', 'like', '%' . $q . '%')
            ->orderBy('name')
            ->limit(10)
            ->get();

        // Optionally return unique names if multiple rows share the same name
        // $results = $results->unique('name')->values();

        return response()->json($results);
    }

    public function show(StockItem $stockItem)
    {
        // Eager load supplier and batches, and get the sum.
        $stockItem->load('supplier', 'stockBatches');
        $totalQuantity = $stockItem->stockBatches->sum('quantity');

        // Get the latest batch (if one exists) to show most recent pricing
        $latestBatch = $stockItem->stockBatches->first(); // relation is ordered_by desc

        return view('stock_items.show', compact('stockItem', 'totalQuantity', 'latestBatch'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('stock_items.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        // --- UPDATED VALIDATION ---
        // Validate for the StockItem fields
        $validatedItemData = $request->validate([
            'model_number' => 'nullable|string|max:255',
            'name' => 'required|string|max:255|unique:stock_items,name',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'color' => 'nullable|string|max:255',
            'warranty' => 'nullable|string|max:255',
            'other' => 'nullable|string',
            'service' => 'nullable|boolean',
             'vehicle_type' => 'nullable|string|max:255',
        ]);

        // Validate for the *first batch* fields
        $validatedBatchData = $request->validate([
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'installment_price' => 'nullable|numeric|min:0',
            'initial_quantity' => 'required|integer|min:0', // Renamed on form
        ]);

        // Use a transaction to ensure both records are created
        DB::transaction(function () use ($validatedItemData, $validatedBatchData) {
            // 1. Create the StockItem
            $stockItem = StockItem::create($validatedItemData);

            // 2. Create the first StockBatch
            $stockItem->stockBatches()->create([
                'quantity' => $validatedBatchData['initial_quantity'],
                'cost_price' => $validatedBatchData['cost_price'],
                'selling_price' => $validatedBatchData['selling_price'],
                'installment_price' => $validatedBatchData['installment_price'] ?? null,
                // 'purchase_order_item_id' is null, marking it as an initial/manual batch
            ]);
        });

        $this->logAction('Created a new Stock Item - ' . $request->name);

        return redirect()->route('stock_items.index')->with('success', 'Stock item and initial batch created successfully.');
    }

    public function edit(StockItem $stockItem)
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('stock_items.edit', compact('stockItem', 'suppliers'));
    }

    public function update(Request $request, StockItem $stockItem)
    {
        // --- UPDATED VALIDATION ---
        // We ONLY validate the StockItem fields now.
        // All pricing and quantity is handled by batches.
        $request->validate([
            'model_number' => 'nullable|string|max:255',
            'name' => 'required|string|max:255|unique:stock_items,name,' . $stockItem->id,
            'supplier_id' => 'nullable|exists:suppliers,id',
            'color' => 'nullable|string|max:255',
            'warranty' => 'nullable|string|max:255',
            'other' => 'nullable|string',
            'service' => 'nullable|boolean',
             'vehicle_type' => 'nullable|string|max:255',
        ]);

        // --- UPDATED LOGIC ---
        // We only update the request data that is fillable on the StockItem.
        $stockItem->update($request->all());

        $this->logAction('Updated Stock Item ID: ' . $stockItem->id);

        return redirect()->route('stock_items.index')->with('success', 'Stock item details updated successfully.');
    }

    public function destroy(StockItem $stockItem)
    {
        // This remains the same. The 'onDelete('cascade')' in your
        // migration will handle deleting all associated batches.
        $stockItem->delete();
        $this->logAction('Deleted Stock Item ID: ' . $stockItem->id);
        return redirect()->route('stock_items.index')->with('success', 'Stock item and all its batches deleted successfully.');
    }
    public function customerPurchases(StockItem $stockItem, Request $request)
    {
        // per-page options (same pattern as ChequeController)
        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 25, 100, 500]) ? $perPage : 15;
    
        $customerSearch = $request->input('customer');
    
        // Base query: each direct bill item row joined to its bill (customer may repeat)
        $baseQuery = DB::table('direct_bill_items')
            ->join('direct_bills', 'direct_bill_items.direct_bill_id', '=', 'direct_bills.id')
            ->where('direct_bill_items.stock_item_id', $stockItem->id)
            ->select(
                'direct_bills.customer_name as customer_name',
                'direct_bills.bill_number as bill_number',
                'direct_bills.id as bill_id',
                'direct_bill_items.quantity as quantity',
                'direct_bill_items.created_at as sold_at'
            );
    
        if ($customerSearch) {
            $baseQuery->where('direct_bills.customer_name', 'like', "%{$customerSearch}%");
    
            // compute total for matching customer(s)
            $totalForCustomer = DB::table('direct_bill_items')
                ->join('direct_bills', 'direct_bill_items.direct_bill_id', '=', 'direct_bills.id')
                ->where('direct_bill_items.stock_item_id', $stockItem->id)
                ->where('direct_bills.customer_name', 'like', "%{$customerSearch}%")
                ->sum('direct_bill_items.quantity');
        } else {
            $totalForCustomer = null;
        }
    
        $customerPurchases = $baseQuery
            ->orderByDesc('direct_bill_items.created_at')
            ->paginate($perPage, ['*'], 'customer_page');
    
        // Pass current search so view can pre-fill input
        return view('stock_items.customer_purchases', compact(
            'stockItem',
            'customerPurchases',
            'totalForCustomer',
            'customerSearch'
        ));
    }
}

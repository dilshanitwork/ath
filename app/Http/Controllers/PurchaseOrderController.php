<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\StockItem;
use App\Models\StockBatch;
use App\Traits\Loggable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderController extends Controller
{
    use Loggable;
    public function __construct()
    {
        $this->middleware('can:view purchase orders')->only(['index', 'show', 'create', 'edit', 'store', 'update', 'destroy']);
        $this->middleware('can:create purchase orders')->only(['create', 'store']);
        $this->middleware('can:edit purchase orders')->only(['edit', 'update']);
        $this->middleware('can:delete purchase orders')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = PurchaseOrder::with('supplier');

        // Handle Search (PO Number or Supplier Name)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")->orWhereHas('supplier', function ($s) use ($search) {
                    $s->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Handle Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Handle Date From (Order Date)
        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->input('date_from'));
        }

        // Handle Date To (Order Date)
        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->input('date_to'));
        }

       $perPage = $request->input('per_page', 10);
        $purchaseOrders = $query->latest()->paginate($perPage);
        return view('purchase_orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('purchase_orders.create', compact('suppliers'));
    }

    public function searchItems(Request $request)
    {
        $query = $request->get('query');
        $items = StockItem::where('name', 'like', "%{$query}%")
            ->orWhere('model_number', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($item) {
                // Fetch the latest batch to get the last known cost price
                $latestBatch = $item->stockBatches()->latest()->first();
                $item->last_cost_price = $latestBatch ? $latestBatch->cost_price : 0;
                return $item;
            });
        return response()->json($items);
    }

    public function store(Request $request)
    {
        // REMOVED 'ordered', kept 'canceled'
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            // 'po_number' => 'required|string|unique:purchase_orders,po_number|max:255',
            'order_date' => 'required|date',
            'status' => 'required|in:pending,received,canceled',
            'items' => 'required|array|min:1',
            'items.*.stock_item_id' => 'required|exists:stock_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Create the Purchase Order
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'po_number' => $request->po_number,
                'order_date' => $request->order_date,
                'status' => $request->status,
                'total_amount' => 0,
                'notes' => $request->notes,
            ]);

            $totalAmount = 0;

            // 2. Create Items
            foreach ($request->items as $itemData) {
                $total = $itemData['quantity'] * $itemData['unit_cost'];
                $totalAmount += $total;

                $poItem = $purchaseOrder->items()->create([
                    'stock_item_id' => $itemData['stock_item_id'],
                    'quantity' => $itemData['quantity'],
                    'intial_quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'total_price' => $total,
                ]);

                // 3. Handle Stock: Only create batches if 'received'
                if ($request->status === 'received') {
                    $this->createStockBatch($poItem);
                }
            }

            $purchaseOrder->update(['total_amount' => $totalAmount]);
        });

        $this->logAction('Created a new Purchase Order - ' . $request->po_number);

        return redirect()->route('purchase_orders.index')->with('success', 'Purchase Order created successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.stockItem']);
        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    public function print(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.stockItem']);
        return view('purchase_orders.print', compact('purchaseOrder'));
    }

    public function download(PurchaseOrder $purchaseOrder)
    {
        // Reuse the same view and data logic as print
        $purchaseOrder->load(['supplier', 'items.stockItem']);

        // Load the view into the PDF generator
        $pdf = Pdf::loadView('purchase_orders.download', compact('purchaseOrder'))->setPaper('a4', 'portrait');

        // Return the generated PDF as a download
        return $pdf->download('ATH_PO-' . $purchaseOrder->id . '.pdf');
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items.stockItem');
        $suppliers = Supplier::orderBy('name')->get();
        // Use eager loading to get items for the dropdown if needed, though search is used mostly
        $stockItems = StockItem::limit(50)->get();

        return view('purchase_orders.edit', compact('purchaseOrder', 'suppliers', 'stockItems'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        // REMOVED 'ordered', kept 'canceled'
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'po_number' => 'required|string|max:255|unique:purchase_orders,po_number,' . $purchaseOrder->id,
            'order_date' => 'required|date',
            'status' => 'required|in:pending,received,canceled',
            'items' => 'required|array|min:1',
            'items.*.stock_item_id' => 'required|exists:stock_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $purchaseOrder) {
            $originalStatus = $purchaseOrder->status;

            // 1. Update PO Details
            $purchaseOrder->update([
                'supplier_id' => $request->supplier_id,
                'po_number' => $request->po_number,
                'order_date' => $request->order_date,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            // 2. Logic for Stock Reversal
            // If it WAS received, but now is Pending OR canceled, remove old batches.
            // Also need to remove old batches if we are re-creating items regardless of status change
            // BUT specifically for status change reversal:
            StockBatch::where('purchase_order_id', $purchaseOrder->id)->delete();

            // 3. Re-create Items (Simplest way to handle edits)
            $purchaseOrder->items()->delete();

            $totalAmount = 0;
            foreach ($request->items as $itemData) {
                $total = $itemData['quantity'] * $itemData['unit_cost'];
                $totalAmount += $total;

                $poItem = $purchaseOrder->items()->create([
                    'stock_item_id' => $itemData['stock_item_id'],
                    'quantity' => $itemData['quantity'],
                    'intial_quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'total_price' => $total,
                ]);

                // 4. Logic for Stock Addition
                // Only if NEW status is 'received' do we create batches.
                if ($request->status === 'received') {
                    $this->createStockBatch($poItem, $purchaseOrder->id);
                }
            }

            $purchaseOrder->update(['total_amount' => $totalAmount]);
        });

        $this->logAction('Updated Purchase Order - ' . $purchaseOrder->po_number);

        return redirect()->route('purchase_orders.show', $purchaseOrder)->with('success', 'Purchase Order updated successfully.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        DB::transaction(function () use ($purchaseOrder) {
            StockBatch::where('purchase_order_id', $purchaseOrder->id)->delete();

            $purchaseOrder->delete();
        });

        $this->logAction('Deleted Purchase Order - ' . $purchaseOrder->po_number);

        return redirect()->route('purchase_orders.index')->with('success', 'Purchase Order deleted successfully.');
    }

    /**
     * Helper to create a StockBatch from a PO Item.
     * Inherits selling prices from the latest existing batch or the stock item itself.
     */
    private function createStockBatch($poItem, $purchaseOrderId)
    {
        $stockItem = StockItem::find($poItem->stock_item_id);

        // Find the most recent batch to copy selling prices from
        $latestBatch = $stockItem->stockBatches()->latest()->first();

        // Determine selling price (fallback logic)
        $sellingPrice = $latestBatch ? $latestBatch->selling_price : $stockItem->selling_price ?? 0;
        $installmentPrice = $latestBatch ? $latestBatch->installment_price : $stockItem->installment_price ?? 0;

        StockBatch::create([
            'stock_item_id' => $stockItem->id,
            'purchase_order_id' => $purchaseOrderId,
            'invoice_number' => $poItem->purchaseOrder->po_number,
            'quantity' => $poItem->quantity,
            'initial_quantity' => $poItem->quantity,
            'cost_price' => $poItem->unit_cost, // Map PO Unit Cost to Batch Cost Price
            'selling_price' => $sellingPrice,
            'installment_price' => $installmentPrice,
        ]);
    }
}

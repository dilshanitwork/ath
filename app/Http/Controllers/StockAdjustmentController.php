<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\StockBatch;
use Illuminate\Http\Request;
use App\Traits\Loggable;

class StockAdjustmentController extends Controller
{
    use Loggable;
    /**
     * Show the form for creating a new stock adjustment (a new batch).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $selectedStockItem = null;
        $latestBatch = null;
        $allStockItems = null;

        // Check if an ID was passed from the 'stock_items.show' page
        if ($request->has('stock_item_id')) {
            $selectedStockItem = StockItem::with('stockBatches')->findOrFail($request->stock_item_id);
            // Get the latest batch to pre-fill pricing data on the form
            $latestBatch = $selectedStockItem->stockBatches()->orderBy('created_at', 'desc')->first();
        } else {
            // If no item is pre-selected, load all items for a dropdown
            $allStockItems = StockItem::orderBy('name')->get();
        }

        return view('stock_adjustments.create', compact('selectedStockItem', 'latestBatch', 'allStockItems'));
    }

    /**
     * Store a new stock adjustment (a new batch) in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming data
        $validatedData = $request->validate([
            'stock_item_id' => 'required|exists:stock_items,id',
            'quantity' => 'required|integer', // Can be positive or negative
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'installment_price' => 'nullable|numeric|min:0',
            'invoice_number' => 'nullable|string|max:255',
            'reason' => 'nullable|string|max:255', // An optional field for comments
        ]);

        // Create the new stock batch
        StockBatch::create([
            'stock_item_id' => $validatedData['stock_item_id'],
            'quantity' => $validatedData['quantity'],
            'initial_quantity' => $validatedData['quantity'],
            'invoice_number' => $validatedData['invoice_number'],
            'cost_price' => $validatedData['cost_price'],
            'selling_price' => $validatedData['selling_price'],
            'installment_price' => $validatedData['installment_price'],
            // 'purchase_order_item_id' is left null, as this is a manual adjustment
            // You could add the 'reason' to the 'stock_batches' table if you want to save it
        ]);

        $this->logAction('Created a new Stock Batch for Stock Item ID: ' . $validatedData['stock_item_id']);

        // Redirect back to the stock item's detail page
        return redirect()->route('stock_items.show', $validatedData['stock_item_id'])->with('success', 'New stock batch added successfully!');
    }

    /**
     * Show the form for editing a stock batch.
     *
     * @param  \App\Models\StockBatch  $stockBatch
     * @return \Illuminate\View\View
     */
    public function edit(StockBatch $stockBatch)
    {
        $stockBatch->load('stockItem');
        return view('stock_adjustments.edit', compact('stockBatch'));
    }

    /**
     * Update the specified stock batch.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StockBatch  $stockBatch
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, StockBatch $stockBatch)
    {
        $validatedData = $request->validate([
            'stock_item_id' => 'required|exists:stock_items,id',
            'quantity' => 'required|integer',
            'invoice_number' => 'nullable|string|max:255',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'installment_price' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string|max:255',
        ]);

        $stockBatch->update([
            'quantity' => $validatedData['quantity'],
            'invoice_number' => $validatedData['invoice_number'],
            'cost_price' => $validatedData['cost_price'],
            'selling_price' => $validatedData['selling_price'],
            'installment_price' => $validatedData['installment_price'],
        ]);

        $this->logAction('Updated Stock Batch ID: ' . $stockBatch->id);

        return redirect()->route('stock_items.show', $stockBatch->stock_item_id)->with('success', 'Stock batch updated successfully!');
    }

    /**
     * Remove the specified stock batch.
     *
     * @param  \App\Models\StockBatch  $stockBatch
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(StockBatch $stockBatch)
    {
        $stockItemId = $stockBatch->stock_item_id;
        $stockBatch->delete();

        $this->logAction('Deleted Stock Batch ID: ' . $stockBatch->id);

        return redirect()->route('stock_items.show', $stockItemId)->with('success', 'Stock batch deleted successfully!');
    }
}

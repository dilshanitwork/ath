<?php

namespace App\Http\Requests;

use App\Models\StockItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log; // Keep Log for potential debugging
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateDirectBillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Controller middleware ('can:edit direct bills') handles authorization
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Get the bill being updated using the correct route parameter name
        $directBill = $this->route('direct_bill'); 

        return [
            'bill_number' => [
                'required',
                'string',
                // Ignore the current bill's ID when checking for uniqueness
                Rule::unique('direct_bills')->ignore($directBill?->id), 
            ],
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            // Use 'discount' to match the form field name
            'discount' => 'nullable|numeric|min:0', 
            'discount_type' => 'required|in:amount,percentage',
            'note' => 'nullable|string',
            
            // Item Validation Rules
            'items' => 'required|array|min:1',
            // Allow stock_item_id to be null for custom items
            'items.*.stock_item_id' => 'nullable|exists:stock_items,id', 
            // Require name, price, quantity for all items
            'items.*.item_name' => 'required|string|max:255',
            'items.*.unit_price' => 'required|numeric|min:0', 
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Configure the validator instance. Adds the stock check logic.
     */
    /**
 * Configure the validator instance. Adds the stock check logic.
 */
public function withValidator(Validator $validator): void
{
    // Only run if items are present
    if (!$this->has('items')) {
        return;
    }

    // Get the bill being updated
    $directBill = $this->route('direct_bill');
    if (!$directBill) {
        return; 
    }
    
    // Create a map of old quantities by stock_item_id
    // This tracks how much of each item is currently on the bill
    $oldQuantities = [];
    foreach ($directBill->items as $oldItem) {
        if ($oldItem->stock_item_id) {
            $oldQuantities[$oldItem->stock_item_id] = ($oldQuantities[$oldItem->stock_item_id] ?? 0) + $oldItem->quantity;
        }
    }

    // Get Stock Item IDs from the incoming request
    $requestedItemIds = collect($this->items)->pluck('stock_item_id')->filter()->unique();
    
    // Fetch the corresponding StockItem models from the database
    $stockItems = $requestedItemIds->isNotEmpty() ? StockItem::findMany($requestedItemIds)->keyBy('id') : collect();

    // Add an 'after' hook to run after basic validation passes
    $validator->after(function ($validator) use ($stockItems, $oldQuantities) {
        
        foreach ($this->items as $index => $item) {
            // Skip check if it's a custom item (no stock_item_id)
            if (empty($item['stock_item_id'])) {
                continue; 
            }

            // Check if we actually found the StockItem model
            if (!$stockItem = $stockItems->get($item['stock_item_id'])) {
                continue; 
            }

            $requestedQty = (int) $item['quantity'];
            
            // Calculate total available stock from all batches with quantity > 0
            $totalAvailableStock = $stockItem->stockBatches()
                ->where('quantity', '>', 0)
                ->sum('quantity');
            
            // Get the old quantity of this item from the current bill
            $oldQuantity = $oldQuantities[$item['stock_item_id']] ?? 0;
            
            // The effective available stock includes:
            // 1. Current stock in batches
            // 2. The quantity that will be returned when we delete old items
            $effectiveAvailableStock = $totalAvailableStock + $oldQuantity;

            // Check if the new requested quantity exceeds what will be available
            if ($requestedQty > $effectiveAvailableStock) {
                // Add a specific validation error for this item row
                $validator->errors()->add(
                    "items.{$index}.quantity",
                    "Insufficient stock for '{$stockItem->name}'. Only {$effectiveAvailableStock} available (current bill has {$oldQuantity}, you requested {$requestedQty})."
                );
            }
        }
    });
}
    /**
     * Prepare the data for validation (e.g., set defaults).
     */
    protected function prepareForValidation(): void
    {
        // Ensure discount defaults to 0 if not provided
        $this->merge([
             // Use 'discount' to match the form field name
            'discount' => $this->discount ?? 0,
        ]);
    }
}
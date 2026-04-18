<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\StockItem;
use Illuminate\Validation\Validator;

class StoreDirectBillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Use the 'can' middleware from your controller
        return Auth::user()->can('create direct bills');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'bill_number' => 'required|string|unique:direct_bills,bill_number',
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'note' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:amount,percentage',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount' => 'nullable|integer|min:0',
            'items.*.stock_item_id' => 'nullable|integer|exists:stock_items,id',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $itemQuantities = [];

            // 1. Group all requested quantities by stock_item_id
            foreach ($this->items as $index => $item) {
                if (!empty($item['stock_item_id'])) {
                    $stockItemId = $item['stock_item_id'];
                    $quantity = (int) $item['quantity'];

                    if (!isset($itemQuantities[$stockItemId])) {
                        $itemQuantities[$stockItemId] = 0;
                    }
                    $itemQuantities[$stockItemId] += $quantity;
                }
            }

            if (empty($itemQuantities)) {
                return; // No stock items to check
            }

            // 2. Get the actual available stock for all requested items
            $stockItems = StockItem::select('id', 'name')
                ->whereIn('id', array_keys($itemQuantities))
                // --- THIS LINE IS NOW FIXED ---
                ->withSum(['stockBatches as available_stock' => fn($q) => $q->where('quantity', '>', 0)], 'quantity')
                ->get()
                ->keyBy('id');

            // 3. Compare requested vs. available
            foreach ($itemQuantities as $stockItemId => $requestedQuantity) {
                $item = $stockItems->get($stockItemId);

                if (!$item) {
                    // This should be caught by 'exists' rule, but good to check
                    $validator->errors()->add('items.0.item_name', "Invalid stock item ID: {$stockItemId}.");
                    continue;
                }

                $available = (int) $item->available_stock; // This alias now works

                if ($requestedQuantity > $available) {
                    $validator->errors()->add('items.0.item_name', "Insufficient stock for '{$item->name}'. Only {$available} available.");
                }
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBatch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['stock_item_id', 'purchase_order_id', 'invoice_number', 'cost_price', 'selling_price', 'installment_price', 'quantity', 'initial_quantity'];

    /**
     * Get the stock item that this batch belongs to.
     */
    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    /**
     * Get the purchase order item that this batch came from (if any).
     */
    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }
}

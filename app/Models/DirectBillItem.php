<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectBillItem extends Model
{
    use HasFactory;

    protected $fillable = ['stock_item_id', 'direct_bill_id', 'item_name', 'unit_price', 'quantity', 'item_discount', 'total_price', 'batch_id'];

    public function directBill()
    {
        return $this->belongsTo(DirectBill::class);
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    public function batch()
    {
        return $this->belongsTo(StockBatch::class, 'batch_id');
    }
}

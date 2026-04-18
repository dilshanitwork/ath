<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    use HasFactory;

    protected $fillable = [
    'bill_id', 
    'item_name', 
    'item_quantity', 
    'item_price', 
    'total_price',
    'stock_item_id',
    'batch_id'
];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function stockItem()
{
    return $this->belongsTo(StockItem::class);
}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    use HasFactory;

    protected $fillable = ['model_number', 'name', 'supplier_id', 'color', 'warranty', 'other', 'service', 'vehicle_type'];

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * ADD THIS RELATIONSHIP
     * * Get all of the stock batches for the stock item.
     */
    public function stockBatches()
    {
        return $this->hasMany(StockBatch::class)->orderBy('created_at', 'desc');
    }
    public function getCurrentStockAttribute()
    {
        return $this->stockBatches->sum('quantity');
    }

    public function getAvgCostAttribute()
    {
        $latestBatch = $this->stockBatches->first();
        return $latestBatch ? $latestBatch->cost_price : 0;
    }

    public function getTotalValueAttribute()
    {
        return $this->current_stock * $this->avg_cost;
    }
}

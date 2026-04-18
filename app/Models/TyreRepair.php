<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TyreRepair extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_number',
        'customer_id',
        'received_date',
        'tyre_size',
        'tyre_make',
        'tyre_number',
        'sent_date',
        'rep_receipt_number',
        'job_number',
        'received_from_company_date',
        'issued_date',
        'bill_number',
        'amount',
        'note',
        'status'
    ];

    /**
     * Auto-generate item_number starting from 9000
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->item_number) {
                // Get the highest existing item_number
                $max = static::max('item_number');
                // Start from 9000 if table is empty, otherwise increment
                $model->item_number = $max ? $max + 1 : 9000;
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
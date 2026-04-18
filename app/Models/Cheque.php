<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'cheque_number', 'bank_value_id', 'amount', 'cheque_date', 'status', 'note'];

    protected $casts = [
        'cheque_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function bankValue()
    {
        return $this->belongsTo(AttributeValue::class, 'bank_value_id');
    }
}

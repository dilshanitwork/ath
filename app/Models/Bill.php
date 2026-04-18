<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_number',
        'category',
        'customer_id',
        'total_price',
        'advance_payment',
        'balance',
        'type',
        'next_bill',
        'installment_payment',
        'next_payment',
        'installments',
        'status',
        'guarantor_name',
        'guarantor_mobile',
        'guarantor_nic',
        'payment_type',
        'user_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(BillItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function paymentSchedules()
    {
        return $this->hasMany(BillPaymentSchedule::class);
    }
}

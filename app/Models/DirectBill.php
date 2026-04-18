<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectBill extends Model
{
    use HasFactory;

    protected $fillable = ['bill_number', 'customer_name', 'contact_number', 'type', 'vehicle', 'bill_total', 'discount', 'final_amount', 'paid', 'balance', 'status', 'note', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(DirectBillItem::class);
    }

    public function payments()
    {
        return $this->hasMany(BillPayment::class)->orderBy('paid_date', 'desc');
    }
}

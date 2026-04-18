<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillPayment extends Model
{
    use HasFactory;

    protected $fillable = ['direct_bill_id', 'amount', 'paid_date', 'payment_method', 'note', 'user_id'];

    public function directBill()
    {
        return $this->belongsTo(DirectBill::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillPaymentSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['bill_id', 'payment_date'];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}

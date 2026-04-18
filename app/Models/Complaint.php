<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'uc_number',
        'tire_size',
        'tyre_serial_number',
        'customer_id',
        'company_id',
        'customer_given_date',
        'company_sent_date',
        'company_received_date',
        'customer_hand_over_date',
        'amount_to_customer',
        'status',
    ];

    protected $casts = [
        'customer_given_date' => 'date',
        'company_sent_date' => 'date',
        'company_received_date' => 'date',
        'customer_hand_over_date' => 'date',
        'amount_to_customer' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}

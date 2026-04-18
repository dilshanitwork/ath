<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AttributeValue;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'address',
        'email',
        'mobile',
        'mobile_2',
        'nic',
        'gender',
        'hometown',
        'credit_limit',
        'photo',
        'remark',
    ];
    public function hometownValue()
    {
        return $this->belongsTo(AttributeValue::class, 'hometown', 'id');
    }

    public function directbills()
    {
        // hasMany(RelatedModel, 'foreign_key_on_related_table', 'local_key_on_this_table')
        return $this->hasMany(DirectBill::class, 'customer_name', 'name');
    }

    public function cheques()
    {
        return $this->hasMany(\App\Models\Cheque::class, 'customer_id');
    }
    
    public function complaints()
    {
        return $this->hasMany(\App\Models\Complaint::class, 'customer_id');
    }
}

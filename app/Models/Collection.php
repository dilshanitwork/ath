<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'payment',
        'date',
        'type',
        'user_id',
    ];

    /**
     * Get the bill associated with the collection.
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Get the user who created the collection.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

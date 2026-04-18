<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mobile',
        'address',
        'joined_date',
        'salary',
        'other',
    ];

    public function files()
    {
        return $this->hasMany(EmployeeFile::class);
    }
}

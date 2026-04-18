<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Log extends Model
{
    protected $fillable = [
        'user_id',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::created(function ($log) {
            // Delete old logs if the total count exceeds 1000
            $logsToKeep = 1000;
            $totalLogs = self::count();

            if ($totalLogs > $logsToKeep) {
                $logsToDelete = $totalLogs - $logsToKeep;
                self::oldest()->limit($logsToDelete)->delete();
            }
        });
    }
}

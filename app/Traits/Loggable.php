<?php

namespace App\Traits;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

trait Loggable
{
    /**
     * Save a log entry.
     *
     * @param string $message
     * @return void
     */
    public function logAction($message)
    {
        Log::create([
            'user_id' => Auth::id(),
            'message' => $message,
        ]);
    }
}

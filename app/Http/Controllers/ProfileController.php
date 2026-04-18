<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Traits\Loggable;

class ProfileController extends Controller
{
    use Loggable;
    public function show()
    {
        return view('profile');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        $this->logAction('User ' . Auth::id() . ' updated their password.');

        return back()->with('status', 'Password updated successfully!');
    }
}

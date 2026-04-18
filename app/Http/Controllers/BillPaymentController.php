<?php

namespace App\Http\Controllers;

use App\Models\DirectBill;
use App\Models\BillPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BillPaymentController extends Controller
{
    /**
     * Store a new payment for a specific bill.
     */
    public function store(Request $request, DirectBill $directBill)
    {
        // Validate that the payment amount is valid and doesn't exceed the balance
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $directBill->balance],
            'paid_date' => ['required', 'date'],
            'payment_method' => ['required', 'string'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $directBill) {
            // 1. Create the Payment Record in history
            BillPayment::create([
                'direct_bill_id' => $directBill->id,
                'amount' => $request->amount,
                'paid_date' => $request->paid_date,
                'payment_method' => $request->payment_method,
                'note' => $request->note,
                'user_id' => Auth::id(),
            ]);

            // 2. Update the Main Bill Totals
            $newPaid = $directBill->paid + $request->amount;
            $newBalance = $directBill->final_amount - $newPaid;

            // Floating point safety check
            if ($newBalance < 0) {
                $newBalance = 0;
            }

            // Automatically close bill if balance is 0
            $status = $newBalance <= 0 ? 'closed' : 'open';

            $directBill->update([
                'paid' => $newPaid,
                'balance' => $newBalance,
                'status' => $status,
            ]);
        });

        return redirect()->route('direct_bills.show', $directBill)->with('success', 'Payment recorded successfully.');
    }
}

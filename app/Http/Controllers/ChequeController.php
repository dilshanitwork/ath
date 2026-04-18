<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\Customer;
use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Models\DirectBill;
use App\Models\BillPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChequeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 100, 500]) ? $perPage : 10;

        $query = Cheque::with(['customer', 'bankValue'])->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = $request->input('search');

            $query->where(function ($q) use ($s) {
                // search by customer name
                $q->whereHas('customer', function ($cq) use ($s) {
                    $cq->where('name', 'like', "%{$s}%");
                });

                // OR search by customer id (numeric)
                if (is_numeric($s)) {
                    $q->orWhere('customer_id', $s);
                }
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $cheques = $query->paginate($perPage)->withQueryString();

        return view('cheques.index', compact('cheques'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $bankAttribute = Attribute::where('name', 'bank name')->first();

        $banks = $bankAttribute ? AttributeValue::where('attribute_id', $bankAttribute->id)->orderBy('value')->get() : collect();

        return view('cheques.create', compact('customers', 'banks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'cheque_number' => 'required|string|max:100',
            'bank_value_id' => 'required|exists:attribute_values,id',
            'amount' => 'required|numeric|min:0',
            'cheque_date' => 'required|date',
            'status' => 'required|in:pending,settled,cancelled',
            'note' => 'nullable|string',
        ]);

        $cheque = Cheque::create($data);

        return redirect()->route('cheques.show', $cheque->id)->with('success', 'Cheque created successfully.');
    }

    public function show(Cheque $cheque)
    {
        $cheque->load(['customer', 'bankValue']);
        return view('cheques.show', compact('cheque'));
    }

    public function edit(Cheque $cheque)
    {
        $customers = Customer::orderBy('name')->get();
        $bankAttribute = \App\Models\Attribute::where('name', 'bank name')->first();
        $banks = $bankAttribute ? \App\Models\AttributeValue::where('attribute_id', $bankAttribute->id)->orderBy('value')->get() : collect();

        // --- NEW LOGIC START ---
        // Fetch Direct Bills for the Cheque's Customer
        $directBills = collect();
        $totalPaid = 0;
        $totalBalance = 0;

        if ($cheque->customer) {
            $customerName = $cheque->customer->name;

            // Query bills by customer_name
            $directBills = DirectBill::where('customer_name', $customerName)->orderBy('created_at', 'desc')->get();

            $totalPaid = $directBills->sum('paid');
            $totalBalance = $directBills->sum('balance');
        }
        // --- NEW LOGIC END ---

        return view('cheques.edit', compact('cheque', 'customers', 'banks', 'directBills', 'totalPaid', 'totalBalance'));
    }

    public function update(Request $request, Cheque $cheque)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'cheque_number' => 'required|string|max:100',
            'bank_value_id' => 'required|exists:attribute_values,id',
            'amount' => 'required|numeric|min:0',
            'cheque_date' => 'required|date',
            'status' => 'required|in:pending,settled,cancelled',
            'note' => 'nullable|string',
        ]);

        // Check if status is changing TO 'settled'
        $isSettling = $cheque->status !== 'settled' && $data['status'] === 'settled';

        DB::transaction(function () use ($cheque, $data, $isSettling) {
            // 1. Update the cheque details
            $cheque->update($data);

            // 2. If settling, allocate funds to Direct Bills
            if ($isSettling && $cheque->amount > 0) {
                $this->allocateChequeAmountToBills($cheque);
            }
        });

        return redirect()->route('cheques.show', $cheque->id)->with('success', 'Cheque updated successfully.');
    }

    /**
     * Helper to distribute cheque amount to oldest open bills
     */
    private function allocateChequeAmountToBills(Cheque $cheque)
    {
        $remainingAmount = $cheque->amount;
        $customerName = $cheque->customer->name; // Assuming link is by name based on previous context

        // 1. Fetch open bills for this customer, oldest first
        $openBills = DirectBill::where('customer_name', $customerName)
            ->where('status', '!=', 'closed')
            ->where('balance', '>', 0)
            ->orderBy('created_at', 'asc') // FIFO: Oldest First
            ->get();

        if ($openBills->isEmpty()) {
            return; // Nothing to pay
        }

        foreach ($openBills as $bill) {
            if ($remainingAmount <= 0) {
                break;
            }

            // Determine how much to pay on this bill
            // Pay either the full balance OR whatever is left in the cheque
            $paymentAmount = min($bill->balance, $remainingAmount);

            if ($paymentAmount > 0) {
                // 2. Create Payment Record (History)
                BillPayment::create([
                    'direct_bill_id' => $bill->id,
                    'amount' => $paymentAmount,
                    'paid_date' => now(), // Or use $cheque->cheque_date
                    'payment_method' => 'Cheque', // Use "Cheque" to indicate source
                    'note' => "Settled via Cheque #{$cheque->cheque_number}",
                    'user_id' => Auth::id(),
                ]);

                // 3. Update Bill Totals
                $newPaid = $bill->paid + $paymentAmount;
                $newBalance = $bill->final_amount - $newPaid;

                // Floating point fix
                if ($newBalance < 0.01) {
                    $newBalance = 0;
                }

                $bill->update([
                    'paid' => $newPaid,
                    'balance' => $newBalance,
                    'status' => $newBalance == 0 ? 'closed' : 'open',
                ]);

                // 4. Decrement remaining cheque funds
                $remainingAmount -= $paymentAmount;
            }
        }

        // Optional: If there is still $remainingAmount left, it stays as 'overpayment' logic
        // or just untracked credit depending on your business rules.
        // For now, we simply stop allocating when bills run out.
    }

    public function destroy(Cheque $cheque)
    {
        $cheque->delete();

        return redirect()->route('cheques.index')->with('success', 'Cheque deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 100, 500]) ? $perPage : 10;

        $q = Complaint::with(['customer', 'company'])->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function ($qq) use ($s) {
                $qq->where('uc_number', 'like', "%{$s}%")
                   ->orWhere('tyre_serial_number', 'like', "%{$s}%")
                   ->orWhere('tire_size', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        $complaints = $q->paginate($perPage)->withQueryString();

        return view('complaints.index', compact('complaints'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $companies = Company::orderBy('company_name')->get();

        return view('complaints.create', compact('customers', 'companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'uc_number' => 'required|string|max:100',
            'tire_size' => 'required|string|max:100',
            'tyre_serial_number' => 'required|string|max:150',

            'customer_id' => 'required|exists:customers,id',
            'company_id' => 'required|exists:companies,id',

            'customer_given_date' => 'nullable|date',
            'company_sent_date' => 'nullable|date',
            'company_received_date' => 'nullable|date',
            'customer_hand_over_date' => 'nullable|date',

            'amount_to_customer' => 'required|numeric|min:0',
            'status' => 'required|in:claimed_100,half_claim,rejected',
        ]);

        $complaint = Complaint::create($data);

        return redirect()
            ->route('complaints.show', $complaint->id)
            ->with('success', 'UC complaint created successfully.');
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['customer', 'company']);
        return view('complaints.show', compact('complaint'));
    }

    public function edit(Complaint $complaint)
    {
        $customers = Customer::orderBy('name')->get();
        $companies = Company::orderBy('company_name')->get();

        return view('complaints.edit', compact('complaint', 'customers', 'companies'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $data = $request->validate([
            'uc_number' => 'required|string|max:100',
            'tire_size' => 'required|string|max:100',
            'tyre_serial_number' => 'required|string|max:150',

            'customer_id' => 'required|exists:customers,id',
            'company_id' => 'required|exists:companies,id',

            'customer_given_date' => 'nullable|date',
            'company_sent_date' => 'nullable|date',
            'company_received_date' => 'nullable|date',
            'customer_hand_over_date' => 'nullable|date',

            'amount_to_customer' => 'required|numeric|min:0',
            'status' => 'required|in:claimed_100,half_claim,rejected',
        ]);

        $complaint->update($data);

        return redirect()
            ->route('complaints.show', $complaint->id)
            ->with('success', 'UC complaint updated successfully.');
    }

    public function destroy(Complaint $complaint)
    {
        $complaint->delete();

        return redirect()
            ->route('complaints.index')
            ->with('success', 'UC complaint deleted successfully.');
    }
}

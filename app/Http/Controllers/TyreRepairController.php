<?php

namespace App\Http\Controllers;

use App\Models\TyreRepair;
use App\Models\Customer;
use App\Traits\Loggable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TyreRepairController extends Controller
{
    use Loggable;
    public function __construct()
    {
        $this->middleware('can:manage tyre repairs')->only(['index', 'show', 'create', 'edit', 'store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $query = TyreRepair::query()->with('customer');

        // Fetch customers for the datalist suggestions
        $customers = Customer::orderBy('name')->select('id', 'name', 'mobile')->get();

        // 1. Filter by Customer Name or Mobile (Supports Typing)
        if ($request->filled('customer_name')) {
            $customerSearch = $request->input('customer_name');
            $query->whereHas('customer', function ($q) use ($customerSearch) {
                $q->where('name', 'like', "%{$customerSearch}%")->orWhere('mobile', 'like', "%{$customerSearch}%");
            });
        }

        // 2. Filter by Status
        if ($request->filled('status')) {
            $status = $request->input('status');

            $status = isset($status) ? strtolower(trim($status)) : null;

            if ($status) {
                switch ($status) {
                    case 'rejected':
                        $query->where('status', 4);
                        break;

                    case 'completed':
                        $query->whereNotNull('issued_date')->where('status', '!=', 4); // optional: ensure not rejected
                        break;

                    case 'received_from_company':
                        // received, not yet issued, and not rejected
                        $query->whereNotNull('received_from_company_date')->whereNull('issued_date')->where('status', '!=', 4);
                        break;

                    case 'sent_to_company':
                        // sent, not yet received/issued, and not rejected
                        $query->whereNotNull('sent_date')->whereNull('received_from_company_date')->whereNull('issued_date')->where('status', '!=', 4);
                        break;

                    case 'pending':
                        // not sent/received/issued and not rejected
                        $query->whereNull('sent_date')->whereNull('received_from_company_date')->whereNull('issued_date')->where('status', '!=', 4);
                        break;
                }
            }
        }

        // 3. Filter by Received Date Range
        if ($request->filled('received_from')) {
            $query->whereDate('received_date', '>=', $request->input('received_from'));
        }
        if ($request->filled('received_to')) {
            $query->whereDate('received_date', '<=', $request->input('received_to'));
        }

        // 4. Filter by Job Number
        if ($request->filled('job_number')) {
            $query->where('job_number', 'like', '%' . $request->input('job_number') . '%');
        }

        $perPage = $request->input('per_page', 10);
        $repairs = $query->latest()->paginate($perPage);

        return view('tyre_repairs.index', compact('repairs', 'customers'));
    }

    public function create()
    {
        // Get next likely item number for display purposes (actual assignment happens in Model)
        $nextItemNumber = (TyreRepair::max('item_number') ?? 8999) + 1;

        $customers = Customer::select('id', 'name', 'mobile')->orderBy('name')->get();

        return view('tyre_repairs.create', compact('customers', 'nextItemNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string', // We use name input to find ID
            'received_date' => 'nullable|date',
            'tyre_size' => 'nullable|string|max:50',
            'tyre_make' => 'nullable|string|max:50',
            'tyre_number' => 'nullable|string|max:50',
            'sent_date' => 'nullable|date',
            'rep_receipt_number' => 'nullable|string|max:50',
            'job_number' => 'nullable|string|max:50',
            'received_from_company_date' => 'nullable|date',
            'issued_date' => 'nullable|date',
            'bill_number' => 'nullable|string|max:50',
            'amount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        // Find customer by name/mobile from datalist
        $customerName = $request->input('customer_name');

        $customer = Customer::where('name', $customerName)->orWhere('mobile', $customerName)->first();

        if (!$customer) {
            return back()
                ->withErrors(['customer_name' => 'Selected customer not found. Please create customer first.'])
                ->withInput();
        }

        $validated['customer_id'] = $customer->id;
        unset($validated['customer_name']);

        TyreRepair::create($validated);
        $this->logAction('Created a new Tyre Repair job for Customer ID: ' . $validated['customer_id']);
        return redirect()->route('tyre_repairs.index')->with('success', 'Repair job created successfully.');
    }

    public function show(TyreRepair $tyreRepair)
    {
        return view('tyre_repairs.show', compact('tyreRepair'));
    }

    public function edit(TyreRepair $tyreRepair)
    {
        $customers = Customer::select('id', 'name', 'mobile')->orderBy('name')->get();
        return view('tyre_repairs.edit', compact('tyreRepair', 'customers'));
    }

    public function update(Request $request, TyreRepair $tyreRepair)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'received_date' => 'nullable|date',
            'tyre_size' => 'nullable|string|max:50',
            'tyre_make' => 'nullable|string|max:50',
            'tyre_number' => 'nullable|string|max:50',
            'sent_date' => 'nullable|date',
            'rep_receipt_number' => 'nullable|string|max:50',
            'job_number' => 'nullable|string|max:50',
            'received_from_company_date' => 'nullable|date',
            'issued_date' => 'nullable|date',
            'bill_number' => 'nullable|string|max:50',
            'amount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
            'status' => 'nullable|integer|min:0|max:4',
        ]);

        $customer = Customer::where('name', $request->input('customer_name'))->first();
        if ($customer) {
            $validated['customer_id'] = $customer->id;
        }
        unset($validated['customer_name']);

        $tyreRepair->update($validated);
        $this->logAction('Updated Tyre Repair ID: ' . $tyreRepair->item_number);
        return redirect()->route('tyre_repairs.index')->with('success', 'Repair job updated successfully.');
    }

    public function destroy(TyreRepair $tyreRepair)
    {
        $tyreRepair->delete();
        $this->logAction('Deleted Tyre Repair ID: ' . $tyreRepair->item_number);
        return redirect()->route('tyre_repairs.index')->with('success', 'Repair job deleted successfully.');
    }

    /**
     * Show the form for creating multiple tyre repairs for one customer.
     */
    public function createMultiple()
    {
        $customers = Customer::select('id', 'name', 'mobile')->orderBy('name')->get();
        // Calculate the starting item number for reference (visual only)
        $nextItemNumber = (TyreRepair::max('item_number') ?? 8999) + 1;

        return view('tyre_repairs.create_multiple', compact('customers', 'nextItemNumber'));
    }

    /**
     * Store multiple tyre repair records.
     */
    public function storeMultiple(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            // Validate the array of repairs
            'repairs' => 'required|array|min:1',
            'repairs.*.received_date' => 'nullable|date',
            'repairs.*.tyre_size' => 'nullable|string|max:50',
            'repairs.*.tyre_make' => 'nullable|string|max:50',
            'repairs.*.tyre_number' => 'nullable|string|max:50',
            'repairs.*.job_number' => 'nullable|string|max:50',
            'repairs.*.amount' => 'nullable|numeric|min:0',
            'repairs.*.note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Find Customer ID
            $customerName = $request->input('customer_name');
            $customer = Customer::where('name', $customerName)->orWhere('mobile', $customerName)->first();

            if (!$customer) {
                // If using datalist, validation might pass but customer might not exist if typed manually
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'customer_name' => 'Customer not found. Please select a valid customer.',
                ]);
            }

            // 2. Loop through repairs and create records
            foreach ($request->repairs as $repairData) {
                // Skip empty rows if strictly all null (optional safety check)
                if (empty($repairData['tyre_size']) && empty($repairData['tyre_make'])) {
                    continue;
                }

                TyreRepair::create([
                    'customer_id' => $customer->id,
                    'received_date' => $repairData['received_date'] ?? now(),
                    'tyre_size' => $repairData['tyre_size'],
                    'tyre_make' => $repairData['tyre_make'],
                    'tyre_number' => $repairData['tyre_number'],
                    'job_number' => $repairData['job_number'],
                    'amount' => $repairData['amount'],
                    'note' => $repairData['note'],
                    // Default status fields
                    'sent_date' => null,
                    'received_from_company_date' => null,
                    'issued_date' => null,
                ]);
            }
        });

        $this->logAction('Created multiple Tyre Repair jobs for Customer: ' . $request->input('customer_name'));

        return redirect()->route('tyre_repairs.index')->with('success', 'Multiple repair jobs created successfully.');
    }
}

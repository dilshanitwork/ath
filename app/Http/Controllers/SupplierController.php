<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Traits\Loggable;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use Loggable;
    public function __construct()
    {
        $this->middleware('can:view suppliers')->only(['index', 'show', 'create', 'edit', 'store', 'update', 'destroy']);
        // $this->middleware('can:create suppliers')->only(['create', 'store']);
        // $this->middleware('can:edit suppliers')->only(['edit', 'update']);
        // $this->middleware('can:delete suppliers')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Supplier::query();

        // Handle Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 10);
        $suppliers = $query->latest()->paginate($perPage);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        Supplier::create($request->all());
        $this->logAction('Created a new Supplier - ' . $request->name);
        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email,' . $supplier->id,
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        $supplier->update($request->all());
        $this->logAction('Updated Supplier ID: ' . $supplier->name);
        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        $this->logAction('Deleted Supplier ID: ' . $supplier->name);
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}

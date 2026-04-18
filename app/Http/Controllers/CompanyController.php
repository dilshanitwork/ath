<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 100, 500]) ? $perPage : 10;
        $q = Company::query()->orderBy('company_name');

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where('company_name', 'like', "%{$s}%")
              ->orWhere('contact_number', 'like', "%{$s}%");
        }

        $companies = $q->paginate($perPage)->withQueryString();

        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_name'   => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'notes'          => 'nullable|string',
        ]);

        $company = Company::create($data);

        return redirect()
            ->route('companies.show', $company->id)
            ->with('success', 'Company created successfully.');
    }

    public function show(Company $company)
    {
        return view('companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'company_name'   => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'notes'          => 'nullable|string',
        ]);

        $company->update($data);

        return redirect()
            ->route('companies.show', $company->id)
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()
            ->route('companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}

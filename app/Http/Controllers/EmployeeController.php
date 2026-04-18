<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeFile;
use Illuminate\Http\Request;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view editor')->only(['index', 'show']);
        $this->middleware('can:create editor')->only(['create', 'store']);
        $this->middleware('can:edit editor')->only(['edit', 'update']);
        $this->middleware('can:delete editor')->only(['destroy']);
    }

    private function logAction($message)
    {
        // Save the log entry
        Log::create([
            'user_id' => Auth::id(),
            'message' => $message
        ]);
    }

    public function index(Request $request)
    {
        $query = Employee::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('mobile', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('start_date')) {
            $query->whereDate('joined_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('joined_date', '<=', $request->end_date);
        }

        $employees = $query->paginate(10);

        return view('employees.index', compact('employees'));
    }

    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'mobile' => 'required|max:15',
            'address' => 'required',
            'joined_date' => 'required|date',
            'salary' => 'nullable|numeric',
            'other' => 'nullable|string',
            'files.*' => 'file|mimes:jpg,jpeg,png,pdf,docx|max:20480', // Add validation for files
        ]);

        $employee = Employee::create($request->all());

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('employee_files', 'local');
                EmployeeFile::create([
                    'employee_id' => $employee->id,
                    'file_path' => $path,
                    'name' => $file->getClientOriginalName(),
                ]);
            }
        }

        $this->logAction('Created a new employee - ' . $request->name);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|max:255',
            'mobile' => 'required|max:15',
            'address' => 'required',
            'joined_date' => 'required|date',
            'salary' => 'nullable|numeric',
            'other' => 'nullable|string',
            'files.*' => 'file|mimes:jpg,jpeg,png,pdf,docx|max:20480', // Add validation for files
        ]);

        $employee->update($request->all());

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('employee_files', 'local');
                EmployeeFile::create([
                    'employee_id' => $employee->id,
                    'file_path' => $path,
                    'name' => $file->getClientOriginalName(),
                ]);
            }
        }

        $this->logAction('Updated employee ID: ' . $employee->id);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        foreach ($employee->files as $file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }

        $employee->delete();

        $this->logAction('Deleted employee ID: ' . $employee->id);

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}

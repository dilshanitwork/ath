<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use App\Traits\Loggable;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    use Loggable;
    public function __construct()
    {
        // Restrict access to specific permissions
        $this->middleware('can:view permissions')->only(['index', 'show']);
        $this->middleware('can:create permissions')->only(['create', 'store']);
        $this->middleware('can:edit permissions')->only(['edit', 'update']);
        $this->middleware('can:delete permissions')->only(['destroy']);
    }
    public function index(Request $request)
    {
        $query = Permission::query();

        // Search by Name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        // Filter by Date Range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        // Paginate the results
         $perPage = $request->input('per_page', 10);
        $permissions = $query->paginate($perPage);
        return view('permissions.index', compact('permissions'));
    }

    public function show(Permission $permission)
    {
        return view('permissions.show', compact('permission'));
    }
    public function create()
    {
        return view('permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name, 'guard_name' => 'web']);
        $this->logAction('Created a new Permission - ' . $request->name);
        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $request->name]);
        $this->logAction('Updated Permission ID: ' . $request->name);
    
        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        $this->logAction('Deleted Permission ID: ' . $permission->name);
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }
}

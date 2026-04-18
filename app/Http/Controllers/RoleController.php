<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Traits\Loggable;

class RoleController extends Controller
{
    use Loggable;
    public function __construct()
    {
        // Restrict access to specific permissions
        $this->middleware('can:view roles')->only(['index', 'show']);
        $this->middleware('can:create roles')->only(['create', 'store']);
        $this->middleware('can:edit roles')->only(['edit', 'update']);
        $this->middleware('can:delete roles')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Role::query();

        // Search by Name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sorting Logic
        if ($request->filled('sort')) {
            $sortDirection = $request->sort == 'name_desc' ? 'desc' : 'asc';
            $query->orderBy('name', $sortDirection);
        }

        // Paginate the results
        $perPage = $request->input('per_page', 10);
        $roles = $query->paginate( $perPage);

        return view('roles.index', compact('roles'));
    }


    public function create()
    {
        $permissions = Permission::all(); // Fetch all permissions
        return view('roles.create', compact('permissions'));
    }


    public function show(Role $role)
    {
        // Load permissions assigned to the role
        $role->load('permissions');
        return view('roles.show', compact('role'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->filled('permissions')) {
            // Convert permission IDs to names
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
            $role->syncPermissions($permissions);
        }

        $this->logAction('Created a new Role - ' . $request->name);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }


    public function edit(Role $role)
    {
        $permissions = Permission::all(); // Fetch all permissions
        $role->load('permissions'); // Eager load the role's permissions
        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ]);

        $role->update(['name' => $request->name]);

        if ($request->filled('permissions')) {
            // Convert permission IDs to names
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
            $role->syncPermissions($permissions);
        } else {
            // Clear all permissions if none are selected
            $role->syncPermissions([]);
        }

        $this->logAction('Updated Role ID: ' . $role->name);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }


    public function destroy(Role $role)
    {
        $role->delete();
        $this->logAction('Deleted Role ID: ' . $role->name);
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}

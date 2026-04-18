<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\Loggable;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use Loggable;
    public function __construct()
    {
        // Restrict access to specific permissions
        $this->middleware('can:view users')->only(['index', 'show']);
        $this->middleware('can:create users')->only(['create', 'store']);
        $this->middleware('can:edit users')->only(['edit', 'update', 'editPassword', 'updatePassword']);
        $this->middleware('can:delete users')->only(['destroy']);
    }
    public function index(Request $request)
    {
        $query = User::query();

        // Search by Name or Email
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by Date Range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Paginate the results
       $perPage = $request->input('per_page', 10);
        $users = $query->paginate($perPage);

        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('profile.index');
        }

        $user->load('roles'); // Eager load roles to optimize database queries
        return view('users.show', compact('user'));
    }

    public function create()
    {
        $roles = Role::all(); // Fetch all roles
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'roles' => 'required|array',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->roles()->sync($request->roles);
        $this->logAction('Created a new User - ' . $user->name);
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('profile.index');
        }

        $roles = Role::all(); // Fetch all roles
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'roles' => 'required|array',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $user->roles()->sync($request->roles);
        $this->logAction('Updated User ID: ' . $user->id);
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        $this->logAction('Deleted User ID: ' . $user->id);
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function editPassword(User $user)
    {
        return view('users.password', compact('user'));
    }
    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user->update(['password' => bcrypt($request->password)]);
        $this->logAction('Updated Password for User ID: ' . $user->id);
        return redirect()->route('users.index')->with('success', 'Password updated successfully.');
    }
}

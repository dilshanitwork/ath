@extends('layouts.app')

@section('title', 'Create User')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1 class="mb-0 card-title"><i class="bi bi-person-plus me-2"></i>Create New User</h1>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Create User Form -->
        <div class="card">
            <div class="card-header bg-light">
                <strong><i class="bi bi-person me-2"></i>New User Details</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <!-- Full Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label"><i class="bi bi-person me-2"></i>Full Name</label>
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="Enter full name" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label"><i class="bi bi-envelope me-2"></i>Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Enter email"
                                required>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="col-md-6">
                            <label for="password" class="form-label"><i class="bi bi-lock me-2"></i>Password</label>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Enter password" required>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Assign Roles -->
                        <div class="col-12">
                            <label for="roles" class="form-label"><i class="bi bi-person-badge me-2"></i>Assign Roles</label>
                            <div class="form-check-group">
                                @foreach ($roles as $role)
                                    @if ($role->name !== 'Admin' || auth()->user()->hasRole('Admin'))
                                        <div class="form-check">
                                            <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                                id="role-{{ $role->id }}" class="form-check-input">
                                            <label class="form-check-label" for="role-{{ $role->id }}">
                                                {{ $role->name }}
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @error('roles')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="mt-4 d-flex justify-content-start">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-save me-1"></i> Create & Save
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Update Role')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1 class="mb-0 card-title"><i class="bi bi-pencil-square me-2"></i>Edit Role</h1>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Edit Role Form -->
        <div class="card">
            <div class="card-header bg-light">
                <strong><i class="bi bi-shield-check me-2"></i>Edit Role Details</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('roles.update', $role) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <!-- Role Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label"><i class="bi bi-tag me-2"></i>Role Name</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ $role->name }}" placeholder="Enter role name" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Assign Permissions -->
                        <div class="col-12">
                            <label for="permissions" class="form-label"><i class="bi bi-key me-2"></i>Assign Permissions</label>
                            <div class="form-check-group">
                                @foreach ($permissions as $permission)
                                    <div class="form-check">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                            id="permission-{{ $permission->id }}" class="form-check-input"
                                            @if ($role->permissions->contains($permission->id)) checked @endif>
                                        <label class="form-check-label" for="permission-{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="mt-4 d-flex justify-content-start">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-save me-1"></i> Update & Save
                        </button>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
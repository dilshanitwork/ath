@extends('layouts.app')

@section('title', 'Role Details')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1 class="mb-0 card-title"><i class="bi bi-eye me-2"></i>View Role Details</h1>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Role Details -->
        <div class="card">
            <div class="card-header bg-light">
                <strong><i class="bi bi-info-circle me-2"></i>Role Details</strong>
            </div>
            <div class="card-body">
                <div class="mb-3 row">
                    <div class="col-sm-4 text-dark"><strong><i class="bi bi-hash me-2 text-dark"></i>ID:</strong></div>
                    <div class="col-sm-8">{{ $role->id }}</div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-4 text-dark"><strong><i class="bi bi-shield-check me-2 text-dark"></i>Name:</strong></div>
                    <div class="col-sm-8">{{ $role->name }}</div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-4 text-dark"><strong><i class="bi bi-key me-2 text-dark"></i>Permissions:</strong></div>
                    <div class="col-sm-8">
                        @if ($role->permissions->isEmpty())
                            <span class="badge bg-danger">No Permissions Assigned</span>
                        @else
                            @foreach ($role->permissions as $permission)
                                <span class="badge bg-primary">{{ $permission->name }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light d-flex justify-content-start">
                <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary me-2">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Are you sure you want to delete this role?');">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
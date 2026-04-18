@extends('layouts.app')

@section('title', 'User Details')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1 class="mb-0 card-title"><i class="bi bi-eye me-2"></i>View User Details</h1>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- User Details -->
        <div class="card">
            <div class="card-header bg-light">
                <strong><i class="bi bi-info-circle me-2"></i>User Details</strong>
            </div>
            <div class="card-body">
                <div class="mb-3 row">
                    <div class="col-sm-4 text-dark"><strong><i class="bi bi-hash me-2 text-dark"></i>ID:</strong></div>
                    <div class="col-sm-8">{{ $user->id }}</div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-4 text-dark"><strong><i class="bi bi-person me-2 text-dark"></i>Name:</strong></div>
                    <div class="col-sm-8">{{ $user->name }}</div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-4 text-dark"><strong><i class="bi bi-envelope me-2 text-dark"></i>Email:</strong></div>
                    <div class="col-sm-8">{{ $user->email }}</div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-4 text-dark"><strong><i class="bi bi-calendar-plus me-2 text-dark"></i>Created At:</strong></div>
                    <div class="col-sm-8">{{ $user->created_at->format('Y-m-d H:i') }}</div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-4 text-dark"><strong><i class="bi bi-calendar-check me-2 text-dark"></i>Updated At:</strong></div>
                    <div class="col-sm-8">{{ $user->updated_at->format('Y-m-d H:i') }}</div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-4 text-dark"><strong><i class="bi bi-person-badge me-2 text-dark"></i>Roles:</strong></div>
                    <div class="col-sm-8">
                        @if ($user->roles->isEmpty())
                            <span class="badge bg-danger">No Roles Assigned</span>
                        @else
                            @foreach ($user->roles as $role)
                                <span class="badge bg-primary">{{ $role->name }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light d-flex justify-content-start">
                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary me-2">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Are you sure you want to delete this user?');">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
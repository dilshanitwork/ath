@extends('layouts.app')

@section('title', 'Update Permission')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1 class="mb-0 card-title"><i class="bi bi-pencil-square me-2"></i>Edit Permission</h1>
            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Edit Permission Form -->
        <div class="card">
            <div class="card-header bg-light">
                <strong><i class="bi bi-key me-2"></i>Edit Permission Details</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('permissions.update', $permission) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label"><i class="bi bi-tag me-2"></i>Permission Name</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ $permission->name }}" placeholder="Enter permission name" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-start">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-save me-1"></i> Update & Save
                        </button>
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
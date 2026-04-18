@extends('layouts.app')

@section('title', 'Password Reset')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1 class="mb-0 card-title"><i class="bi bi-key me-2"></i>Update Password - {{ $user->name }}</h1>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Update Password Form -->
        <div class="card">
            <div class="card-header bg-light">
                <strong><i class="bi bi-lock me-2"></i>Change Password of {{ $user->name }}</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update-password', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="password" class="form-label"><i class="bi bi-lock me-2"></i>New Password</label>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Enter new password" required>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="password_confirmation" class="form-label"><i class="bi bi-lock me-2"></i>Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control" placeholder="Confirm new password" required>
                            @error('password_confirmation')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-start">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-save me-1"></i> Update Password
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
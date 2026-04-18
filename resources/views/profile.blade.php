@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Page Header -->
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <h1 class="mb-0 card-title"><i class="bi bi-person-circle me-2"></i>My Profile</h1>
                </div>

                <!-- Include Alerts -->
                @include('components.alerts')

                <!-- Profile Card -->
                <div class="card">
                    <div class="card-header bg-light">
                        <strong><i class="bi bi-person me-2"></i>User Information</strong>
                    </div>
                    <div class="card-body">
                        <!-- User Details -->
                        <div class="mb-3 row">
                            <div class="col-sm-4 text-dark"><strong><i class="bi bi-person me-2 text-dark"></i>Name:</strong></div>
                            <div class="col-sm-8">{{ Auth::user()->name }}</div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-sm-4 text-dark"><strong><i class="bi bi-envelope me-2 text-dark"></i>Email:</strong></div>
                            <div class="col-sm-8">{{ Auth::user()->email }}</div>
                        </div>

                        <hr class="my-4">

                        <!-- Update Password Form -->
                        <h6 class="mb-3"><i class="bi bi-lock me-2"></i>Change Password</h6>
                        <form method="POST" action="{{ route('profile.updatePassword') }}">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="new_password" class="form-label"><i class="bi bi-lock me-2"></i>New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" 
                                        placeholder="Enter new password" required>
                                </div>

                                <div class="col-12">
                                    <label for="new_password_confirmation" class="form-label"><i class="bi bi-lock me-2"></i>Confirm New Password</label>
                                    <input type="password" class="form-control" id="new_password_confirmation"
                                        name="new_password_confirmation" placeholder="Confirm new password" required>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
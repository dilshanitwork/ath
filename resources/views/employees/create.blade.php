@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <h4 class="mb-3"><i class="fas fa-plus-circle"></i> Add New Employee</h4>
            <a href="{{ route('employees.index') }}" class="btn btn-sm btn-back">
                <i class="fas fa-arrow-left"></i> Cancel & Back
            </a>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Create Employee Form -->
        <div class="card">
            <div class="card-header card-header-custom">
                <i class="fas fa-user-plus"></i> New Employee Details
            </div>
            <div class="card-body">
                <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label"><i class="fas fa-user"></i> Name</label>
                        <input type="text" name="name" id="name" class="form-control"
                            placeholder="Enter employee name" required>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="mobile" class="form-label"><i class="fas fa-phone"></i> Mobile</label>
                        <input type="text" name="mobile" id="mobile" class="form-control"
                            placeholder="Enter mobile number" required>
                        @error('mobile')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label"><i class="fas fa-map-marker-alt"></i> Address</label>
                        <textarea name="address" id="address" class="form-control" placeholder="Enter address" required></textarea>
                        @error('address')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="joined_date" class="form-label"><i class="fas fa-calendar-alt"></i> Joined Date</label>
                        <input type="date" name="joined_date" id="joined_date" class="form-control" required>
                        @error('joined_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="salary" class="form-label"><i class="fas fa-dollar-sign"></i> Salary</label>
                        <input type="text" name="salary" id="salary" class="form-control" placeholder="Enter salary">
                        @error('salary')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="other" class="form-label"><i class="fas fa-sticky-note"></i> Other</label>
                        <textarea name="other" id="other" class="form-control" placeholder="Enter other details"></textarea>
                        @error('other')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="files" class="form-label"><i class="fas fa-file-upload"></i> Upload Files</label>
                        <input type="file" name="files[]" id="files" class="form-control" multiple>
                        @error('files.*')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-check-circle"></i> Create & Save
                    </button>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times-circle"></i> Cancel
                    </a>
                </form>
            </div>
        </div>
    </div>
@endsection

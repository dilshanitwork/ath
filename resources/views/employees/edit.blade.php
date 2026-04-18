@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <h4 class="mb-3"><i class="fas fa-edit"></i> Edit Employee</h4>
            <a href="{{ route('employees.index') }}" class="btn btn-sm btn-back">
                <i class="fas fa-arrow-left"></i> Cancel & Back
            </a>
        </div>
        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Edit Employee Form -->
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header card-header-custom">
                        <i class="fas fa-user-edit"></i> Edit Employee Details
                    </div>
                    <div class="card-body">
                        <form action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label"><i class="fas fa-user"></i> Name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ $employee->name }}" placeholder="Enter employee name" required>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="mobile" class="form-label"><i class="fas fa-phone"></i> Mobile</label>
                                <input type="text" name="mobile" id="mobile" class="form-control"
                                    value="{{ $employee->mobile }}" placeholder="Enter mobile number" required>
                                @error('mobile')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label"><i class="fas fa-map-marker-alt"></i> Address</label>
                                <textarea name="address" id="address" class="form-control" placeholder="Enter address" required>{{ $employee->address }}</textarea>
                                @error('address')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="joined_date" class="form-label"><i class="fas fa-calendar-alt"></i> Joined Date</label>
                                <input type="date" name="joined_date" id="joined_date" class="form-control"
                                    value="{{ $employee->joined_date }}" required>
                                @error('joined_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="salary" class="form-label"><i class="fas fa-dollar-sign"></i> Salary</label>
                                <input type="text" name="salary" id="salary" class="form-control"
                                    value="{{ $employee->salary }}" placeholder="Enter salary">
                                @error('salary')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="other" class="form-label"><i class="fas fa-sticky-note"></i> Other</label>
                                <textarea name="other" id="other" class="form-control" placeholder="Enter other details">{{ $employee->other }}</textarea>
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
                            <button type="submit" class="btn btn-update">
                                <i class="fas fa-save"></i> Update & Save
                            </button>
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times-circle"></i> Cancel
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header card-header-custom">
                        <i class="fas fa-file"></i> Uploaded Files
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach ($employee->files as $file)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ Storage::url($file->file_path) }}" target="_blank">{{ basename($file->name) }}</a>
                                    <form action="{{ route('employee_files.destroy', $file) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this file?');">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <h4 class="mb-3"><i class="fas fa-eye"></i> View Employee</h4>
            <div>
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-edit me-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('employees.destroy', $employee) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"
                        onclick="return confirm('Are you sure you want to delete this employee?');">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Employee Details -->
        <div class="card">
            <div class="card-header card-header-custom">
                <i class="fas fa-info-circle"></i> Employee Details
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4 text-secondary"><strong>ID:</strong></div>
                    <div class="col-sm-8">{{ $employee->id }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-secondary"><strong>Name:</strong></div>
                    <div class="col-sm-8">{{ $employee->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-secondary"><strong>Mobile:</strong></div>
                    <div class="col-sm-8">{{ $employee->mobile }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-secondary"><strong>Address:</strong></div>
                    <div class="col-sm-8">{{ $employee->address }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-secondary"><strong>Joined Date:</strong></div>
                    <div class="col-sm-8">{{ $employee->joined_date }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-secondary"><strong>Salary:</strong></div>
                    <div class="col-sm-8">{{ $employee->salary }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-secondary"><strong>Other:</strong></div>
                    <div class="col-sm-8">{{ $employee->other }}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 text-secondary"><strong>Uploaded Files:</strong></div>
                    <div class="col-sm-8">
                        <ul class="list-group">
                            @foreach ($employee->files as $file)
                                <li class="list-group-item">
                                    <a href="{{ Storage::url($file->file_path) }}" target="_blank">{{ basename($file->name) }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
@endsection

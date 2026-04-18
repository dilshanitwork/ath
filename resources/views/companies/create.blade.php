@extends('layouts.app')
@section('title','Add Company')
@section('content')
<div class="container py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><i class="bi bi-plus-circle me-1"></i> Add Company</h1>
        <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('companies.store') }}" class="row g-3">
                @csrf

                <div class="col-12 col-md-6">
                    <label class="form-label">Company Name <span class="text-danger">*</span></label>
                    <input type="text" name="company_name" class="form-control"
                           value="{{ old('company_name') }}" required>
                    @error('company_name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control"
                           value="{{ old('contact_number') }}">
                    @error('contact_number') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    @error('notes') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save
                    </button>
                    <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

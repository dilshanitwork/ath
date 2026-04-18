@extends('layouts.app')
@section('title', 'Supplier Details - ' . $supplier->name)
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Supplier</h1>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card p-4">
            <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Supplier Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $supplier->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="contact_person" class="form-label">Contact Person</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person"
                            value="{{ old('contact_person', $supplier->contact_person) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ old('email', $supplier->email) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                            value="{{ old('phone', $supplier->phone) }}">
                    </div>
                    <div class="col-md-12">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address">{{ old('address', $supplier->address) }}</textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Update Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

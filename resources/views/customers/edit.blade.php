@extends('layouts.app')
@section('title', 'Edit Customers - '.$customer->name)
@section('content')
    <div class="container">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1 class="mb-0 card-title"><i class="bi bi-pencil-square me-2"></i>Edit Customer</h1>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">
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

        <div class="p-4 card">
            <div class="row g-3">
                <div class="col-md-8">
                    <form action="{{ route('customers.update', $customer) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label"><i class="bi bi-person me-2"></i>Name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ old('name', $customer->name) }}" placeholder="Enter Customer name" required>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label"><i class="bi bi-envelope me-2"></i>Email</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="{{ old('email', $customer->email) }}" placeholder="Enter Customer email">
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="mobile" class="form-label"><i class="bi bi-phone me-2"></i>Mobile</label>
                                <input type="text" name="mobile" id="mobile" class="form-control"
                                    value="{{ old('mobile', $customer->mobile) }}" placeholder="Enter mobile number"
                                    required>
                                @error('mobile')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="mobile_2" class="form-label"><i class="bi bi-phone me-2"></i>Additional
                                    Mobile</label>
                                <input type="text" name="mobile_2" id="mobile_2" class="form-control"
                                    value="{{ old('mobile_2', $customer->mobile_2) }}"
                                    placeholder="Enter other mobile number">
                                @error('mobile_2')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="nic" class="form-label"><i class="bi bi-credit-card me-2"></i>NIC</label>
                                <input type="text" name="nic" id="nic" class="form-control"
                                    value="{{ old('nic', $customer->nic) }}" placeholder="Enter NIC number">
                                @error('nic')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label for="address" class="form-label"><i class="bi bi-geo-alt me-2"></i>Address</label>
                                <textarea name="address" id="address" class="form-control" placeholder="Enter address" required>{{ old('address', $customer->address) }}</textarea>
                                @error('address')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="credit_limit" class="form-label"><i class="bi bi-cash-stack me-2"></i>Credit
                                    Limit</label>
                                <input type="number" step="0.01" name="credit_limit" id="credit_limit"
                                    class="form-control" value="{{ old('credit_limit', $customer->credit_limit) }}"
                                    placeholder="Enter credit limit">
                                @error('credit_limit')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="gender" class="form-label"><i
                                        class="bi bi-gender-ambiguous me-2"></i>Gender</label>
                                <select name="gender" id="gender" class="form-control">
                                    <option value="male"
                                        {{ old('gender', $customer->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female"
                                        {{ old('gender', $customer->gender) == 'female' ? 'selected' : '' }}>Female
                                    </option>
                                </select>
                                @error('gender')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="hometown" class="form-label"><i class="bi bi-house me-2"></i>Hometown</label>
                                <select name="hometown" id="hometown" class="form-control">
                                    <option value="">Select Hometown</option>
                                    @foreach ($hometowns as $hometown)
                                        <option value="{{ $hometown->id }}"
                                            {{ old('hometown', $customer->hometown) == $hometown->id ? 'selected' : '' }}>
                                            {{ $hometown->value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('hometown')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="remark" class="form-label"><i
                                        class="bi bi-journal-text me-2"></i>Remark</label>
                                <textarea name="remark" id="remark" class="form-control" placeholder="Enter remark">{{ old('remark', $customer->remark) }}</textarea>
                                @error('remark')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <div>
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="bi bi-save me-1"></i> Update & Save
                                    </button>
                                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-1"></i> Cancel
                                    </a>
                                </div>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                                    <i class="bi bi-trash me-1"></i> Delete
                                </button>
                            </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this customer? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="delete-form" action="{{ route('customers.destroy', $customer) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection


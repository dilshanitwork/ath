@extends('layouts.app')
@section('title', 'Add Customer')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-person-add me-2"></i>Create New Customer</h1>
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

        <div class="card p-4">
            @if ($isShowroomUser || $isVanUser)
                <div class="alert {{ $isShowroomUser ? 'alert-primary' : 'alert-success' }}">
                    <strong>Sale Type:</strong>
                    {{ $isShowroomUser ? 'Showroom Sale & Bill category will be automatically applied to Showroom Sales.' : 'Van Sale & Bill category will be automatically applied to Van Sales.' }}
                </div>
            @else
                {{-- <div class="alert alert-warning">
                    <strong>Sale Type:</strong> Please select the category for this customer.
                </div> --}}
            @endif

            <form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label"><i class="bi bi-person me-2"></i>Name</label>
                        <input type="text" name="name" id="name" class="form-control"
                            placeholder="Enter Customer name" value="{{ old('name') }}" required>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    @if (!$isShowroomUser && !$isVanUser)
                        {{-- <div class="col-md-6">
                            <label for="category" class="form-label"><i class="bi bi-tags me-2"></i>Category</label>
                            <select name="category" id="category" class="form-control" required>
                                <option value="0">Showroom Sale</option>
                                <option value="1">Van Sale</option>
                            </select>
                            @error('category')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div> --}}
                    @endif

                    <input type="hidden" name="category" value="0">

                    <div class="col-md-6">
                        <label for="email" class="form-label"><i class="bi bi-envelope me-2"></i>Email</label>
                        <input type="email" name="email" id="email" class="form-control"
                            placeholder="Enter Customer email" value="{{ old('email') }}">
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="mobile" class="form-label"><i class="bi bi-phone me-2"></i>Mobile</label>
                        <input type="text" name="mobile" id="mobile" class="form-control"
                            placeholder="Enter mobile number" value="{{ old('mobile') }}">
                        @error('mobile')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="mobile_2" class="form-label"><i class="bi bi-phone me-2"></i>Additional Mobile</label>
                        <input type="text" name="mobile_2" id="mobile_2" class="form-control"
                            placeholder="Enter other mobile number" value="{{ old('mobile_2') }}">
                        @error('mobile_2')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label for="address" class="form-label"><i class="bi bi-geo-alt me-2"></i>Address</label>
                        <textarea name="address" id="address" class="form-control" placeholder="Enter address">{{ old('address') }}</textarea>
                        @error('address')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="credit_limit" class="form-label"><i class="bi bi-cash-stack me-2"></i>Credit
                            Limit</label>
                        <input type="number" step="0.01" name="credit_limit" id="credit_limit" class="form-control"
                            placeholder="Enter credit limit" value="{{ old('credit_limit') }}">
                        @error('credit_limit')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="nic" class="form-label"><i class="bi bi-credit-card me-2"></i>NIC</label>
                        <input type="text" name="nic" id="nic" class="form-control"
                            placeholder="Enter nic number" value="{{ old('nic') }}">
                        @error('nic')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="gender" class="form-label"><i class="bi bi-gender-ambiguous me-2"></i>Gender</label>
                        <select name="gender" id="gender" class="form-control">
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
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
                                    {{ old('hometown') == $hometown->id ? 'selected' : '' }}>
                                    {{ $hometown->value }}
                                </option>
                            @endforeach
                        </select>
                        @error('hometown')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="photo" class="form-label"><i class="bi bi-image me-2"></i>Upload photo</label>
                        <input type="file" name="photo" id="photo" class="form-control">
                        @error('photo')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="remark" class="form-label"><i class="bi bi-journal-text me-2"></i>Remark</label>
                        <textarea name="remark" id="remark" class="form-control" placeholder="Enter special notes here">{{ old('remark') }}</textarea>
                        @error('remark')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-start mt-4">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-save me-1"></i> Create & Save
                    </button>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

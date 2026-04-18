@extends('layouts.app')
@section('title','Add Complaints')
@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
       <h1 class="mb-0"><i class="bi bi-plus-circle me-1"></i> Add Under Complaint</h1>
        <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('complaints.store') }}" class="row g-3">
                @csrf

                <div class="col-12 col-md-4">
                    <label class="form-label">UC Number <span class="text-danger">*</span></label>
                    <input type="text" name="uc_number" class="form-control" value="{{ old('uc_number') }}" required>
                    @error('uc_number') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Tire Size <span class="text-danger">*</span></label>
                    <input type="text" name="tire_size" class="form-control" value="{{ old('tire_size') }}" required>
                    @error('tire_size') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Tyre Serial Number <span class="text-danger">*</span></label>
                    <input type="text" name="tyre_serial_number" class="form-control" value="{{ old('tyre_serial_number') }}" required>
                    @error('tyre_serial_number') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 col-md-6">
                        <label for="customer" class="form-label">
                            Customer <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="customer" name="customer"
                            placeholder="Type to search..."
                            value="{{ old('customer', optional($customers->firstWhere('id', old('customer_id')))->name) }}"
                            list="customerList" autocomplete="off">

                        <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id') }}">
                        <datalist id="customerList">
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->name }}" data-id="{{ $customer->id }}"></option>
                            @endforeach
                        </datalist>

                        @error('customer_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Company <span class="text-danger">*</span></label>
                    <select name="company_id" class="form-select" required>
                        <option value="">Select Company</option>
                        @foreach($companies as $co)
                            <option value="{{ $co->id }}" {{ old('company_id') == $co->id ? 'selected' : '' }}>
                                {{ $co->company_name }} (ID: {{ $co->id }})
                            </option>
                        @endforeach
                    </select>
                    @error('company_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Customer Given Date</label>
                    <input type="date" name="customer_given_date" class="form-control" value="{{ old('customer_given_date') }}">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Company Sent Date</label>
                    <input type="date" name="company_sent_date" class="form-control" value="{{ old('company_sent_date') }}">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Company Received</label>
                    <input type="date" name="company_received_date" class="form-control" value="{{ old('company_received_date') }}">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Customer Hand Over Date</label>
                    <input type="date" name="customer_hand_over_date" class="form-control" value="{{ old('customer_hand_over_date') }}">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Amount to Customer</label>
                    <input type="number" step="0.01" name="amount_to_customer" class="form-control" value="{{ old('amount_to_customer', 0) }}" required>
                    @error('amount_to_customer') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="claimed_100" {{ old('status','claimed_100')=='claimed_100'?'selected':'' }}>100% Claimed</option>
                        <option value="half_claim" {{ old('status')=='half_claim'?'selected':'' }}>Half Claim</option>
                        <option value="rejected" {{ old('status')=='rejected'?'selected':'' }}>Reject</option>
                    </select>
                    @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Save</button>
                    <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>
 {{-- Map typed name -> customer_id --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input      = document.getElementById('customer');
    const hiddenId   = document.getElementById('customer_id');
    const dataList   = document.getElementById('customerList');
    const options    = dataList ? dataList.getElementsByTagName('option') : [];

    function syncCustomerId() {
        const value = input.value.trim();
        let foundId = '';

        for (let i = 0; i < options.length; i++) {
            if (options[i].value === value) {
                foundId = options[i].dataset.id || '';
                break;
            }
        }

        hiddenId.value = foundId;
    }

    input.addEventListener('change', syncCustomerId);
    input.addEventListener('blur', syncCustomerId);
});
</script>
@endpush
@endsection

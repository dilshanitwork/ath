@extends('layouts.app')
@section('title','Edit Complaints- '. $complaint->id)
@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
         <h1 class="mb-0"><i class="bi bi-pencil"></i> Edit Under Complaint</h1>
        <a href="{{ route('complaints.show', $complaint->id) }}" class="btn btn-outline-dark">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('complaints.update', $complaint->id) }}" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-12 col-md-4">
                    <label class="form-label">UC Number <span class="text-danger">*</span></label>
                    <input type="text" name="uc_number" class="form-control"
                           value="{{ old('uc_number', $complaint->uc_number) }}" required>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Tire Size <span class="text-danger">*</span></label>
                    <input type="text" name="tire_size" class="form-control"
                           value="{{ old('tire_size', $complaint->tire_size) }}" required>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Tyre Serial Number <span class="text-danger">*</span></label>
                    <input type="text" name="tyre_serial_number" class="form-control"
                           value="{{ old('tyre_serial_number', $complaint->tyre_serial_number) }}" required>
                </div>

                {{-- <div class="col-12 col-md-6">
                    <label class="form-label">Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}"
                                {{ old('customer_id', $complaint->customer_id) == $cust->id ? 'selected' : '' }}>
                                {{ $cust->name }} (ID: {{ $cust->id }})
                            </option>
                        @endforeach
                    </select>
                </div> --}}
                <div class="col-12 col-md-6">
                        <label for="customer" class="form-label">
                            Customer <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="customer" name="customer"
                            placeholder="Type to search..." value="{{ old('customer', optional($complaint->customer)->name) }}"
                            list="customerList" autocomplete="off">

                        <input type="hidden" name="customer_id" id="customer_id"
                            value="{{ old('customer_id', $complaint->customer_id) }}">
                        {{-- Data list --}}
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
                            <option value="{{ $co->id }}"
                                {{ old('company_id', $complaint->company_id) == $co->id ? 'selected' : '' }}>
                                {{ $co->company_name }} (ID: {{ $co->id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Customer Given Date</label>
                    <input type="date" name="customer_given_date" class="form-control"
                           value="{{ old('customer_given_date', optional($complaint->customer_given_date)->format('Y-m-d')) }}">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Company Sent Date</label>
                    <input type="date" name="company_sent_date" class="form-control"
                           value="{{ old('company_sent_date', optional($complaint->company_sent_date)->format('Y-m-d')) }}">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Company Received</label>
                    <input type="date" name="company_received_date" class="form-control"
                           value="{{ old('company_received_date', optional($complaint->company_received_date)->format('Y-m-d')) }}">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Customer Hand Over Date</label>
                    <input type="date" name="customer_hand_over_date" class="form-control"
                           value="{{ old('customer_hand_over_date', optional($complaint->customer_hand_over_date)->format('Y-m-d')) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Amount to Customer</label>
                    <input type="number" step="0.01" name="amount_to_customer" class="form-control"
                           value="{{ old('amount_to_customer', $complaint->amount_to_customer) }}" required>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="claimed_100" {{ old('status', $complaint->status)=='claimed_100'?'selected':'' }}>100% Claimed</option>
                        <option value="half_claim" {{ old('status', $complaint->status)=='half_claim'?'selected':'' }}>Half Claim</option>
                        <option value="rejected" {{ old('status', $complaint->status)=='rejected'?'selected':'' }}>Reject</option>
                    </select>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Update</button>
                    <a href="{{ route('complaints.show', $complaint->id) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input    = document.getElementById('customer');
    const hiddenId = document.getElementById('customer_id');
    const options  = document.querySelectorAll('#customerList option');

    function syncCustomerId() {
        const name = input.value.trim();
        let matched = '';

        options.forEach(opt => {
            if (opt.value === name) matched = opt.dataset.id;
        });

        hiddenId.value = matched ?? '';
    }

    input.addEventListener('change', syncCustomerId);
    input.addEventListener('blur', syncCustomerId);
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'Edit Repair Job')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Repair Job
                #{{ $tyreRepair->item_number }}</h1>
            <a href="{{ route('tyre_repairs.index') }}" class="btn btn-secondary">
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
            <form action="{{ route('tyre_repairs.update', $tyreRepair) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Section 1: Basic Info -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label text-muted">Item Number</label>
                        <input type="text" class="form-control bg-light fw-bold" value="{{ $tyreRepair->item_number }}"
                            readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="customer_name" class="form-label">Customer Name <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name"
                            list="customerList" value="{{ old('customer_name', $tyreRepair->customer->name) }}" required>
                        <datalist id="customerList">
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->name }}">{{ $customer->mobile }}</option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="col-md-4">
                        <label for="received_date" class="form-label">Date Received</label>
                        <input type="date" class="form-control" id="received_date" name="received_date"
                            value="{{ old('received_date', $tyreRepair->received_date ? \Carbon\Carbon::parse($tyreRepair->received_date)->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <!-- Section 2: Tyre Details -->
                <h5 class="text-primary mb-3 mt-4"><i class="bi bi-disc me-2"></i>Tyre Details</h5>
                <div class="card bg-light mb-4 border-0">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="tyre_size" class="form-label">Tyre Size</label>
                                <input type="text" class="form-control" id="tyre_size" name="tyre_size"
                                    value="{{ old('tyre_size', $tyreRepair->tyre_size) }}">
                            </div>
                            <div class="col-md-4">
                                <label for="tyre_make" class="form-label">Make of Tyre</label>
                                <input type="text" class="form-control" id="tyre_make" name="tyre_make"
                                    value="{{ old('tyre_make', $tyreRepair->tyre_make) }}">
                            </div>
                            <div class="col-md-4">
                                <label for="tyre_number" class="form-label">Tyre Number</label>
                                <input type="text" class="form-control" id="tyre_number" name="tyre_number"
                                    value="{{ old('tyre_number', $tyreRepair->tyre_number) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Company Processing -->
                <h5 class="text-primary mb-3 mt-4"><i class="bi bi-building me-2"></i>Company Processing</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="sent_date" class="form-label">Date Sent to Company</label>
                        <input type="date" class="form-control" id="sent_date" name="sent_date"
                            value="{{ old('sent_date', $tyreRepair->sent_date ? \Carbon\Carbon::parse($tyreRepair->sent_date)->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="rep_receipt_number" class="form-label">Rep's Receipt No</label>
                        <input type="text" class="form-control" id="rep_receipt_number" name="rep_receipt_number"
                            value="{{ old('rep_receipt_number', $tyreRepair->rep_receipt_number) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="job_number" class="form-label">Job Number</label>
                        <input type="text" class="form-control" id="job_number" name="job_number"
                            value="{{ old('job_number', $tyreRepair->job_number) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="received_from_company_date" class="form-label">Date Rcvd from Company</label>
                        <input type="date" class="form-control" id="received_from_company_date"
                            name="received_from_company_date"
                            value="{{ old('received_from_company_date', $tyreRepair->received_from_company_date ? \Carbon\Carbon::parse($tyreRepair->received_from_company_date)->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <!-- Section 4: Billing -->
                <h5 class="text-primary mb-3 mt-4"><i class="bi bi-receipt me-2"></i>Completion & Billing</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="issued_date" class="form-label">Date Issued to Customer</label>
                        <input type="date" class="form-control" id="issued_date" name="issued_date"
                            value="{{ old('issued_date', $tyreRepair->issued_date ? \Carbon\Carbon::parse($tyreRepair->issued_date)->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="bill_number" class="form-label">Bill Number</label>
                        <input type="text" class="form-control" id="bill_number" name="bill_number"
                            value="{{ old('bill_number', $tyreRepair->bill_number) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="amount" class="form-label">Amount (LKR)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rs.</span>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01"
                                value="{{ old('amount', $tyreRepair->amount) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Job Status</label>
                        <div class="input-group">
                            <select class="form-select" id="status" name="status">
                                <option value="0" {{ old('status', $tyreRepair->status) == 0 ? 'selected' : '' }}>
                                    Processing
                                </option>
                                <option value="4" {{ old('status', $tyreRepair->status) == 4 ? 'selected' : '' }}>
                                    Rejected
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="note" class="form-label">Notes / Remarks</label>
                        <textarea class="form-control" id="note" name="note" rows="3">{{ old('note', $tyreRepair->note) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Update Repair
                        Job</button>
                </div>
            </form>
        </div>
    </div>
@endsection

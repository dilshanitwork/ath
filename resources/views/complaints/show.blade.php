@extends('layouts.app')
@section('title','Complaints- '. $complaint->id)
@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
         <h1 class="mb-0"><i class="bi bi-exclamation-octagon me-1"></i> Under Complaint Details</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('complaints.edit', $complaint->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('complaints.index') }}" class="btn btn-outline-dark">Back</a>
        </div>
    </div>

    @if (session('success'))
        <div id="autoHideAlert" class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="text-muted">UC Number</div>
                    <div class="fw-semibold">{{ $complaint->uc_number }}</div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="text-muted">Customer</div>
                    <div class="fw-semibold">{{ $complaint->customer->name ?? 'N/A' }} (ID: {{ $complaint->customer_id }})</div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="text-muted">Company</div>
                    <div class="fw-semibold">{{ $complaint->company->company_name ?? 'N/A' }} (ID: {{ $complaint->company_id }})</div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="text-muted">Tire Size</div>
                    <div class="fw-semibold">{{ $complaint->tire_size }}</div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="text-muted">Tyre Serial Number</div>
                    <div class="fw-semibold">{{ $complaint->tyre_serial_number }}</div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="text-muted">Status</div>
                    <div class="fw-semibold">{{ str_replace('_', ' ', $complaint->status) }}</div>
                </div>

                <div class="col-12 col-md-3">
                    <div class="text-muted">Customer Given Date</div>
                    <div class="fw-semibold">{{ $complaint->customer_given_date?->format('Y-m-d') ?? '-' }}</div>
                </div>

                <div class="col-12 col-md-3">
                    <div class="text-muted">Company Sent Date</div>
                    <div class="fw-semibold">{{ $complaint->company_sent_date?->format('Y-m-d') ?? '-' }}</div>
                </div>

                <div class="col-12 col-md-3">
                    <div class="text-muted">Company Received</div>
                    <div class="fw-semibold">{{ $complaint->company_received_date?->format('Y-m-d') ?? '-' }}</div>
                </div>

                <div class="col-12 col-md-3">
                    <div class="text-muted">Customer Hand Over Date</div>
                    <div class="fw-semibold">{{ $complaint->customer_hand_over_date?->format('Y-m-d') ?? '-' }}</div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="text-muted">Amount to Customer</div>
                    <div class="fw-semibold">{{ number_format((float)$complaint->amount_to_customer, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alert = document.getElementById('autoHideAlert');
    if (alert) {
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    }
});
</script>
@endsection

@extends('layouts.app')
@section('title','Company- '.$company->company_name)
@section('content')
<div class="container py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><i class="bi bi-building me-1"></i> Company Details</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('companies.edit', $company->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
           <button type="button" class="btn btn-secondary width-100px" onclick="history.back()">Back to List</button>
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
                <div class="col-12 col-md-6">
                    <div class="text-muted">Company Name</div>
                    <div class="fw-semibold">{{ $company->company_name }}</div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="text-muted">Contact Number</div>
                    <div class="fw-semibold">{{ $company->contact_number ?? '-' }}</div>
                </div>

                <div class="col-12">
                    <div class="text-muted">Notes</div>
                    <div>{{ $company->notes ?? '-' }}</div>
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

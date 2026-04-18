@extends('layouts.app')
@section('title', 'Cheque-' . $cheque->id)
@section('content')
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0"><i class="bi bi-receipt"></i>Cheque Details</h1>
            @if (session('success'))
                <div id="autoHideAlert" class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i>
                    {{ session('success') }}
                </div>
            @endif
            <div class="d-flex gap-2">
                <a href="{{ route('cheques.edit', $cheque->id) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="{{ route('cheques.index') }}" class="btn btn-outline-dark">Back</a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="text-muted">Customer</div>
                        <div class="fw-semibold">
                            {{ $cheque->customer->name ?? 'N/A' }}
                            <small class="text-muted">(ID: {{ $cheque->customer_id }})</small>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="text-muted">Cheque Number</div>
                        <div class="fw-semibold">{{ $cheque->cheque_number }}</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="text-muted">Bank</div>
                        <div class="fw-semibold">
                            {{ $cheque->bankValue->value ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="text-muted">Amount</div>
                        <div class="fw-semibold">{{ number_format((float) $cheque->amount, 2) }}</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="text-muted">Cheque Date</div>
                        <div class="fw-semibold">{{ $cheque->cheque_date?->format('Y-m-d') }}</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="text-muted">Status</div>
                        <div class="fw-semibold text-capitalize">{{ $cheque->status }}</div>
                    </div>

                    <div class="col-12">
                        <div class="text-muted">Note</div>
                        <div>{{ $cheque->note ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('autoHideAlert');

            if (alert) {
                setTimeout(() => {
                    alert.classList.remove('show');
                    alert.classList.add('fade');

                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 3000); // 3 seconds
            }
        });
    </script>

@endsection

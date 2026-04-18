@extends('layouts.app')
@section('title', 'All Complaints')
@section('content')
    <div class="container py-3">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0"><i class="bi bi-exclamation-octagon me-1"></i> Under Complaints (UC)</h1>
            <a href="{{ route('complaints.create') }}" class="btn btn-outline-dark">
                <i class="bi bi-plus-circle me-1"></i> Add UC
            </a>
        </div>

        @if (session('success'))
            <div id="autoHideAlert" class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="GET" class="row g-2 mb-3">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                    placeholder="UC number / tyre serial / tire size">
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="claimed_100" {{ request('status') == 'claimed_100' ? 'selected' : '' }}>100% Claimed</option>
                    <option value="half_claim" {{ request('status') == 'half_claim' ? 'selected' : '' }}>Half Claim</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Reject</option>
                </select>
            </div>

            <div class="col-12 col-md-1">
                <label class="form-label fw-semibold">Per Page</label>
                <select name="per_page" class="form-select" onchange="this.form.submit()">
                    @foreach ([10, 25, 100, 500] as $n)
                        <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>
                            {{ $n }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-2 d-grid align-self-end">
                <button class="btn btn-outline-dark"><i class="bi bi-search me-1"></i> Filter</button>
            </div>

            <div class="col-12 col-md-2 d-grid align-self-end">
                <a href="{{ route('complaints.index') }}" class="btn btn-outline-danger">
                    <i class="bi bi-x-circle me-1"></i> Reset
                </a>
            </div>
        </form>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table-hover mb-0 table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>UC No</th>
                            <th>Customer</th>
                            <th>Company</th>
                            <th>Tyre Serial</th>
                            <th>Tire Size</th>
                            <th>Amount (Rs.)</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $c)
                            <tr>
                                <td class="fw-semibold">{{ $c->uc_number }}</td>
                                 <td> <a href="{{ route('customers.show', $c->customer_id) }}"class="text-decoration-none text-primary">{{ $c->customer->name ?? 'N/A' }}</a></td>
                                <td> <a href="{{ route('companies.show', $c->company_id) }}"class="text-decoration-none text-primary">{{ $c->company->company_name ?? 'N/A' }}</a></td>
                                <td>{{ $c->tyre_serial_number }}</td>
                                <td>{{ $c->tire_size }}</td>
                                <td>{{ number_format((float) $c->amount_to_customer, 2) }}</td>
                                <td>
                                    @php
                                        $badge = match ($c->status) {
                                            'claimed_100' => 'bg-success',
                                            'half_claim' => 'bg-warning text-dark',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ str_replace('_', ' ', $c->status) }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('complaints.show', $c->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('complaints.edit', $c->id) }}"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('complaints.destroy', $c->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Delete this UC complaint?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted py-4 text-center">No UC complaints found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-body">
                {{ $complaints->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

@extends('layouts.app')
@section('title', 'All Cheques')
@section('content')
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0"><i class="bi bi-receipt"></i> Cheques</h1>
            <a href="{{ route('cheques.create') }}" class="btn btn-outline-dark mt-md-0 mt-2">
                <i class="bi bi-plus-circle"></i> Add New Cheque
            </a>
        </div>

        <form method="GET" class="row g-2 mb-3">

            {{-- Search --}}
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                    placeholder="Customer name or ID">
            </div>

            {{-- Status --}}
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="settled" {{ request('status') == 'settled' ? 'selected' : '' }}>Settled</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            {{-- Per Page --}}
            <div class="col-12 col-md-1">
                <label class="form-label fw-semibold">Per Page</label>
                <select name="per_page" class="form-select" onchange="this.form.submit()">
                    @foreach ([10, 25, 100, 500] as $n)
                        <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>
                            {{ $n }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Apply --}}
            <div class="col-12 col-md-2 d-grid align-self-end">
                <button class="btn btn-outline-dark">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
            </div>

            {{-- Reset Search + Status only --}}
            <div class="col-12 col-md-2 d-grid align-self-end">
                <a href="{{ route('cheques.index', request()->except('search', 'status', 'page')) }}"
                    class="btn btn-outline-danger">
                    <i class="bi bi-x-circle me-1"></i> Reset Filters
                </a>
            </div>

        </form>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table-hover mb-0 table">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Cheque No</th>
                            <th>Bank</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cheques as $cheque)
                            <tr>
                                <td>{{ $cheque->id }}</td>
                                <td> <a href="{{ route('customers.show', $cheque->customer_id) }}"class="text-decoration-none text-primary">{{ $cheque->customer->name ?? 'N/A' }}</td>
                                <td>{{ $cheque->cheque_number }}</td>
                                <td>{{ $cheque->bankValue->value ?? 'N/A' }}</td>
                                <td>{{ number_format((float) $cheque->amount, 2) }}</td>
                                <td>{{ $cheque->cheque_date?->format('Y-m-d') }}</td>
                                <td class="text-capitalize">
                                    @php
                                        $badge = match ($cheque->status) {
                                            'settled' => 'bg-success',
                                            'cancelled' => 'bg-danger',
                                            default => 'bg-warning text-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $cheque->status }}</span>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary"
                                        href="{{ route('cheques.show', $cheque->id) }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a class="btn btn-sm btn-outline-secondary"
                                        href="{{ route('cheques.edit', $cheque->id) }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    {{-- <form action="{{ route('cheques.destroy', $cheque->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Delete this cheque?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form> --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-muted py-4 text-center">No cheques found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">
                {{ $cheques->links() }}
            </div>
        </div>
    </div>
@endsection

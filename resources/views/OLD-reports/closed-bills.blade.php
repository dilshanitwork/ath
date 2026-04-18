@extends('layouts.app')

@section('title', 'Closed Bills Report')

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <h1 class="mb-0 card-title"><i class="bi bi-check-circle me-2"></i>Closed Bills Report</h1>
        <div class="gap-2 mt-2 d-flex flex-column flex-sm-row mt-md-0">
            <a href="{{ route('reports.closedBillsExport', request()->all()) }}" class="btn btn-outline-dark">
                <i class="bi bi-download me-1"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mb-4 card">
        <div class="card-header bg-light">
            <i class="bi bi-funnel me-2"></i>Filter Closed Bills
        </div>
        <div class="card-body">
            <form action="{{ route('reports.closedBills') }}" method="GET">
                <div class="row g-3">
                    <!-- Date Range -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                            value="{{ request('end_date') }}">
                    </div>
                    <!-- Bill Number -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="bill_number" class="form-label">Bill Number</label>
                        <input type="text" name="bill_number" id="bill_number" class="form-control"
                            value="{{ request('bill_number') }}" placeholder="Enter bill number">
                    </div>
                    <!-- Customer Name -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="customer_name" class="form-label">Customer Name</label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control"
                            value="{{ request('customer_name') }}" placeholder="Enter customer name">
                    </div>
                    <!-- Sale Type -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="category" class="form-label">Sale Type</label>
                        <select name="category" id="category" class="form-select">
                            @foreach($categories as $value => $label)
                                <option value="{{ $value }}" {{ request('category') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Payment Type -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="type" class="form-label">Payment Method</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">All Methods</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- User -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="user_id" class="form-label">Created By</label>
                        <select name="user_id" id="user_id" class="form-select">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Filter Actions -->
                <div class="mt-3 row">
                    <div class="col-12 col-md-8">
                        <div class="flex-wrap gap-2 d-flex align-items-center">
                            @if(request()->hasAny(['start_date', 'end_date', 'bill_number', 'customer_name', 'category', 'type', 'user_id']))
                                <span class="badge bg-danger">Filters Applied</span>
                                @if(request()->filled('start_date'))
                                    <span class="badge bg-secondary">From: {{ request('start_date') }}</span>
                                @endif
                                @if(request()->filled('end_date'))
                                    <span class="badge bg-secondary">To: {{ request('end_date') }}</span>
                                @endif
                            @endif
                        </div>
                        <p class="mt-2 mb-0 card-title text-dark">
                            <strong>Total Amount:</strong> Rs.{{ number_format($totalAmount, 2) }}
                        </p>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="gap-2 d-flex flex-column flex-md-row justify-content-md-end">
                            <button type="submit" class="btn btn-outline-dark w-100 w-md-auto">
                                <i class="bi bi-search me-1"></i>Apply Filters
                            </button>
                            <a href="{{ route('reports.closedBills') }}" class="btn btn-outline-danger w-100 w-md-auto">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bills Table -->
    <div class="table-responsive">
        <table class="table align-middle table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th class="table-header">
                <a href="{{ route('reports.closedBills', array_merge(request()->all(), ['sort_by' => 'bill_number', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}"
                    class="text-decoration-none text-dark">
                    Bill Number <i class="bi bi-sort-down"></i>
                </a>
            </th>
            <th class="table-header"><i class="bi bi-person me-2"></i>Customer</th>
            <th class="table-header"><i class="bi bi-tag me-2"></i>Sale Type</th>
            <th class="table-header"><i class="bi bi-currency-dollar me-2"></i>Total Amount</th>
            <th class="table-header"><i class="bi bi-credit-card me-2"></i>Payment Method</th>
            <th class="table-header"><i class="bi bi-calendar-check me-2"></i>Date Closed</th>
            <th class="table-header"><i class="bi bi-person-badge me-2"></i>Created By</th>
            <th class="table-header text-end"><i class="bi bi-gear-fill"></i> Actions</th>
        </tr>
    </thead>
            <tbody>
                @forelse($bills as $bill)
                    <tr>
                        <!-- Bill Number with clickable link -->
                            <td>
                                <b class="{{ $bill->category == 0 ? 'text-primary' : 'text-success' }}">
                                    <a href="{{ route('bills.show', $bill) }}" class="text-decoration-none text-reset">
                                        {{ $bill->bill_number }}
                                    </a>
                                </b>
                            </td>
                            <!-- Customer Name with clickable link -->
                            <td>
                                <b>
                                    <a href="{{ route('customers.show', $bill->customer->id) }}"
                                        class="text-decoration-none text-reset">
                                        {{ $bill->customer->name }}
                                    </a>
                                </b>
                            </td>
                        <td>{{ $bill->category == 0 ? 'Showroom Sale' : 'Van Sale' }}</td>
                        <td>{{ number_format($bill->total_price, 2) }}</td>
                        <td>{{ ucfirst($bill->payment_type ?? 'N/A') }}</td>
                        <td>{{ $bill->collections->sortByDesc('date')->first()->date ?? 'N/A' }}</td>
                        <td>{{ $bill->user->name ?? 'N/A' }}</td>
                        <td class="text-nowrap text-end">
                            <a href="{{ route('bills.show', $bill) }}" class="btn btn-info btn-sm" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No closed bills found matching the criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $bills->appends(request()->all())->links() }}
    </div>
</div>

<style>
    /* Responsive table styles */
    @media (max-width: 767.98px) {
        .table-responsive {
            font-size: 0.95rem;
        }

        .table thead {
            display: none;
        }

        .table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .5rem 0;
            border: none;
        }

        .table tbody td:before {
            content: attr(data-label);
            flex-basis: 45%;
            font-weight: 700;
            color: #495057;
            text-align: left;
        }

        .table tbody td:last-child {
            justify-content: flex-end;
        }
    }
</style>

<script>
    // Add data-label attribute for mobile table view
    document.addEventListener('DOMContentLoaded', function() {
        if (window.innerWidth < 768) {
            const headers = Array.from(document.querySelectorAll('.table thead th')).map(th => th.innerText.trim());
            document.querySelectorAll('.table tbody tr').forEach(tr => {
                tr.querySelectorAll('td').forEach((td, i) => {
                    td.setAttribute('data-label', headers[i] || '');
                });
            });
        }
    });
</script>
@endsection
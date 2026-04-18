@extends('layouts.app')

@section('title', 'Overdue Bills Report')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h1 class="mb-0 card-title"><i class="bi bi-clock-history me-2"></i>Overdue Bills</h1>
            <div class="gap-2 mt-2 d-flex flex-column flex-sm-row mt-md-0">
                <a href="{{ route('reports.printOverdue', request()->all()) }}"
                    class="btn btn-info">
                    <i class="bi bi-printer"></i> Print
                </a>
                <a href="{{ route('reports.exportOverdue', request()->all()) }}"
                    class="btn btn-outline-dark">
                    <i class="bi bi-download me-1"></i> Export CSV
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="mb-4 card">
            <div class="card-header bg-light">
                <i class="bi bi-funnel me-2"></i>Filter Overdue Bills
            </div>
            <div class="card-body">
                <form action="{{ route('reports.overdue') }}" method="GET">
                    <div class="row g-3">
                        <!-- Overdue Days Input -->
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="overdue_days" class="form-label">Overdue Days</label>
                            <input type="number" min="1" name="overdue_days" id="overdue_days" class="form-control"
                                value="{{ old('overdue_days', request('overdue_days')) }}" placeholder="e.g. 7"
                                autocomplete="off">
                            <small class="text-muted">Bills overdue by this many days</small>
                        </div>
                        <!-- Customer Name -->
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="customer_name" class="form-label">Customer Name</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control"
                                value="{{ request('customer_name') }}" placeholder="Enter customer name" autocomplete="off">
                        </div>
                        <!-- Bill Number -->
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="bill_number" class="form-label">Bill Number</label>
                            <input type="text" name="bill_number" id="bill_number" class="form-control"
                                value="{{ request('bill_number') }}" placeholder="Enter bill number" autocomplete="off">
                        </div>
                        <!-- Hometown -->
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="hometown_value" class="form-label">Hometown</label>
                            <select name="hometown_value" id="hometown_value" class="form-select">
                                <option value="">Select Hometown</option>
                                @foreach ($hometowns as $hometown)
                                    <option value="{{ $hometown->value }}"
                                        {{ request('hometown_value') == $hometown->value ? 'selected' : '' }}>
                                        {{ $hometown->value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-3 row align-items-end gy-2">
                        <div class="col-12 col-md-8">
                            <div class="flex-wrap gap-2 mb-2 d-flex align-items-center">
                                @if (request()->filled('overdue_days'))
                                    <span class="badge bg-secondary">Overdue by: {{ request('overdue_days') }} days</span>
                                @endif
                                @if (request()->filled('start_date'))
                                    <span class="badge bg-secondary">Start Date: {{ request('start_date') }}</span>
                                @endif
                                @if (request()->filled('end_date'))
                                    <span class="badge bg-secondary">End Date: {{ request('end_date') }}</span>
                                @endif
                                @if (request()->filled('customer_name') || request()->filled('bill_number'))
                                    <span class="badge bg-danger">Filters Applied:</span>
                                    @if (request()->filled('customer_name'))
                                        <span class="badge bg-danger">Customer Name: {{ request('customer_name') }}</span>
                                    @endif
                                    @if (request()->filled('bill_number'))
                                        <span class="badge bg-danger">Bill Number: {{ request('bill_number') }}</span>
                                    @endif
                                @endif
                                @if (request()->filled('hometown_value'))
                                    <span class="badge bg-danger">Hometown: {{ request('hometown_value') }}</span>
                                @endif
                            </div>
                            <p class="mb-0 card-title text-dark">
                                <strong>Total Overdue :</strong> <b>Rs.{{ number_format($totalOverdue, 2) }}</b>
                            </p>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="gap-2 d-flex flex-column flex-md-row justify-content-md-end">
                                <button type="submit" class="btn btn-outline-dark w-100 w-md-auto">
                                    <i class="bi bi-search me-1"></i>Apply Filters
                                </button>
                                <a href="{{ route('reports.overdue') }}" class="btn btn-outline-danger w-100 w-md-auto">
                                    <i class="bi bi-x-circle me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Installments Table -->
        <div class="table-responsive">
            <table class="table align-middle table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="table-header">
                            <a href="{{ route('reports.overdue', array_merge(request()->all(), ['sort_by' => 'bill_number', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}"
                                class="text-decoration-none text-dark">
                                Bill Number <i class="bi bi-sort-down"></i>
                            </a>
                        </th>
                        <th class="table-header"><i class="bi bi-person me-2"></i>Customer</th>
                        <th class="table-header"><i class="bi bi-phone me-2"></i>Mobile</th>
                        <th class="table-header"><i class="bi bi-telephone me-2"></i>Contact</th>
                        <th class="table-header"><i class="bi bi-currency-dollar me-2"></i>Total Price</th>
                        <th class="table-header"><i class="bi bi-currency-dollar me-2"></i>Balance</th>
                        <th class="table-header"><i class="bi bi-house me-2"></i>Home Town</th>
                        <th class="table-header"><i class="bi bi-calendar-date me-2"></i>Overdue Date</th>
                        <th class="table-header text-end"><i class="bi bi-gear-fill"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($installments as $bill)
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
                            <td>{{ $bill->customer->mobile }}</td>
                            <td>{{ $bill->customer->mobile_2 }}</td>
                            <td>{{ number_format($bill->total_price, 0) }}</td>
                            <td>{{ number_format($bill->balance, 0) }}</td>
                            <td>{{ $bill->customer->hometownValue->value ?? 'N/A' }}</td>
                            <td>
                                {{ $bill->next_bill }}
                                <b class="text-danger">
                                    @php
                                        $daysOverdue = \Carbon\Carbon::parse($bill->next_bill)->diffInDays(
                                            now(),
                                            false,
                                        );
                                    @endphp
                                    @if ($daysOverdue > 0)
                                        ({{ round($daysOverdue) }} days overdue)
                                    @elseif ($daysOverdue < 0)
                                        ({{ round(abs($daysOverdue)) }} days left)
                                    @else
                                        (Due today)
                                    @endif
                                </b>
                            </td>
                            <td class="text-nowrap text-end">
                                <a href="{{ route('bills.paymentPage', $bill) }}" class="btn btn-success btn-sm" title="Pay Now">
                                    <i class="bi bi-cash"></i> Pay Now
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No overdue bills found for the selected date
                                range.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4 overflow-auto d-flex justify-content-center">
            {{ $installments->appends(request()->all())->links() }}
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tooltip initialization
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Add data-label attribute for mobile table view
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
    
    <!-- Tooltip Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>

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
@endsection

@extends('layouts.app')

@section('title', 'To be Collected Details Report')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h1 class="mb-0 card-title"><i class="bi bi-collection me-2"></i>To be Collected Details</h1>
            <div class="gap-2 mt-2 d-flex flex-column flex-sm-row mt-md-0">
                <a href="{{ route('reports.printCollections', request()->all()) }}"
                    class="btn btn-info">
                    <i class="bi bi-printer"></i> Print
                </a>
                <a href="{{ route('reports.exportCollections', request()->all()) }}"
                    class="btn btn-outline-dark">
                    <i class="bi bi-download me-1"></i> Export CSV
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="mb-4 card">
            <div class="card-header bg-light">
                <i class="bi bi-funnel me-2"></i>Filter Collection Details
            </div>
            <div class="card-body">
                <form action="{{ route('reports.collections') }}" method="GET">
                    <div class="row g-3">
                        <!-- Date Filters -->
                        <div class="col-12 col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ $startDate }}">
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="{{ $endDate }}">
                        </div>

                        <!-- Search Filters -->
                        <div class="col-12 col-md-3">
                            <label for="customer_name" class="form-label">Customer Name</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control"
                                value="{{ request('customer_name') }}" placeholder="Enter customer name">
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="bill_number" class="form-label">Bill Number</label>
                            <input type="text" name="bill_number" id="bill_number" class="form-control"
                                value="{{ request('bill_number') }}" placeholder="Enter bill number">
                        </div>
                        <div class="col-12 col-md-3">
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
                    <div class="mt-3 row">
                        <div
                            class="gap-2 col-12 d-flex flex-column flex-md-row justify-content-between align-items-stretch">
                            <div class="mb-2 mb-md-0">
                                @if (request()->filled('start_date'))
                                    <span class="mb-1 badge bg-secondary mb-md-0">Start Date:
                                        {{ request('start_date') }}</span>
                                @endif
                                @if (request()->filled('end_date'))
                                    <span class="mb-1 badge bg-secondary mb-md-0">End Date:
                                        {{ request('end_date') }}</span>
                                @endif
                                @if (request()->filled('customer_name') || request()->filled('bill_number') || request()->filled('hometown_value'))
                                    <span class="badge bg-danger me-1">Filters Applied:</span>
                                    @if (request()->filled('customer_name'))
                                        <span class="badge bg-danger">Customer Name: {{ request('customer_name') }}</span>
                                    @endif
                                    @if (request()->filled('bill_number'))
                                        <span class="badge bg-danger">Bill Number: {{ request('bill_number') }}</span>
                                    @endif
                                    @if (request()->filled('hometown_value'))
                                        <span class="badge bg-danger">Hometown: {{ request('hometown_value') }}</span>
                                    @endif
                                @endif
                            </div>
                            <div class="gap-2 d-flex">
                                <button type="submit" class="btn btn-outline-dark w-200 w-md-auto">
                                    <i class="bi bi-search me-1"></i>Apply Filters
                                </button>
                                <a href="{{ route('reports.collections') }}" class="btn btn-outline-danger w-200 w-md-auto">
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
                            <a href="{{ route('reports.collections', array_merge(request()->all(), ['sort_by' => 'bill_number', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}"
                                class="text-decoration-none text-dark">
                                Bill Number <i class="bi bi-sort-down"></i>
                            </a>
                        </th>
                        <th class="table-header"><i class="bi bi-person me-2"></i>Customer</th>
                        <th class="table-header"><i class="bi bi-phone me-2"></i>Mobile</th>
                        <th class="table-header"><i class="bi bi-telephone me-2"></i>Contact</th>
                        <th class="table-header"><i class="bi bi-house me-2"></i>Home Town</th>
                        <th class="table-header"><i class="bi bi-currency-dollar me-2"></i>Total Price</th>
                        <th class="table-header"><i class="bi bi-currency-dollar me-2"></i>Balance</th>
                        <th class="table-header"><i class="bi bi-calendar-date me-2"></i>Next Date</th>
                        <th class="table-header"><i class="bi bi-cash-stack me-2"></i>Amount</th>
                        <th class="table-header text-end"><i class="bi bi-gear-fill"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($installments as $bill)
                        <tr class="@if ($startDate > $bill->next_bill) table-danger @endif">
                            <td><b class="{{ $bill->category == 0 ? 'text-primary' : 'text-success' }}">
                                    <a href="{{ route('bills.show', $bill) }}" class="text-decoration-none text-reset">
                                        {{ $bill->bill_number }}
                                    </a>
                                </b>
                            </td>
                            <td>
                                <a href="{{ route('customers.show', $bill->customer->id) }}"
                                class="text-decoration-none"
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="{{ $bill->customer->remark ?? 'No remark' }}">
                                    <b>{{ $bill->customer->name }}</b>
                                </a>
                            </td>
                            <td>{{ $bill->customer->mobile }}</td>
                            <td>{{ $bill->customer->mobile_2 }}</td>
                            <td>{{ $bill->customer->hometownValue->value ?? 'N/A' }}</td>
                            <td>{{ number_format($bill->total_price, 2) }}</td>
                            <td>{{ number_format($bill->balance, 2) }}</td>
                            <td>{{ $bill->next_bill }}</td>
                            <td>{{ number_format($bill->next_payment, 2) }}</td>
                            <td class="text-nowrap text-end">
                                <a href="{{ route('bills.paymentPage', $bill) }}" class="btn btn-success btn-sm" title="Pay Now">
                                    <i class="bi bi-cash"></i> Pay Now
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No pending payments found for the selected
                                date range.</td>
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
            });
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

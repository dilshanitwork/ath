@extends('layouts.app')

@section('title', 'All Credit Bills')

@section('content')
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
            <h1 class="card-title mb-0"><i class="bi bi-file-earmark-text me-2"></i>Credit Bills</h1>
            <a href="{{ route('bills.create') }}" class="btn btn-outline-dark mt-md-0 mt-2">
                <i class="bi bi-plus-circle me-1"></i> Create New Credit Bill
            </a>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Filter and Applied Filters Section -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <i class="bi bi-funnel me-2"></i>Filter Bills
            </div>
            <div class="card-body">
                <form action="{{ route('bills.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="customer_name" class="form-label">Customer Name</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control"
                                value="{{ request('customer_name') }}" placeholder="Enter customer name">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="customer_nic" class="form-label">Customer NIC</label>
                            <input type="text" name="customer_nic" id="customer_nic" class="form-control"
                                value="{{ request('customer_nic') }}" placeholder="Enter customer NIC">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="customer_mobile" class="form-label">Customer Mobile</label>
                            <input type="text" name="customer_mobile" id="customer_mobile" class="form-control"
                                value="{{ request('customer_mobile') }}" placeholder="Enter customer mobile">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="bill_number" class="form-label">Bill Number</label>
                            <input type="text" name="bill_number" id="bill_number" class="form-control"
                                value="{{ request('bill_number') }}" placeholder="Enter bill number">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 col-md-8">
                            <!-- Applied Filters Display -->
                            @if (request()->filled('customer_name') ||
                                    request()->filled('customer_nic') ||
                                    request()->filled('customer_mobile') ||
                                    request()->filled('bill_number'))
                                <div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge bg-danger">Filters Applied</span>
                                        @if (request()->filled('customer_name'))
                                            <span class="badge bg-secondary">Customer: {{ request('customer_name') }}</span>
                                        @endif
                                        @if (request()->filled('customer_nic'))
                                            <span class="badge bg-secondary">NIC: {{ request('customer_nic') }}</span>
                                        @endif
                                        @if (request()->filled('customer_mobile'))
                                            <span class="badge bg-secondary">Mobile: {{ request('customer_mobile') }}</span>
                                        @endif
                                        @if (request()->filled('bill_number'))
                                            <span class="badge bg-secondary">Bill #: {{ request('bill_number') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2">
                                <button type="submit" class="btn btn-outline-dark w-100 w-md-auto">
                                    <i class="bi bi-search me-1"></i>Apply Filters
                                </button>
                                <a href="{{ route('bills.index') }}" class="btn btn-outline-danger w-100 w-md-auto">
                                    <i class="bi bi-x-circle me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-muted">
                <form id="per-page-form" action="{{ route('bills.index') }}" method="GET"
                    class="d-flex align-items-center">
                    <label for="per-page-select" class="form-label mb-0 me-2">Results:</label>
                    <select name="per_page" id="per-page-select" class="form-select-sm form-select"
                        onchange="this.form.submit()">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    @foreach (request()->except('per_page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-bordered table-hover table align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="table-header">Bill Number</th>
                        <th class="table-header"><i class="bi bi-person me-2"></i>Customer</th>
                        <th class="table-header"><i class="bi bi-currency-dollar me-2"></i>Total Price</th>
                        <th class="table-header"><i class="bi bi-currency-dollar me-2"></i>Advance Payment</th>
                        <th class="table-header"><i class="bi bi-currency-dollar me-2"></i>Balance</th>
                        <th class="table-header"><i class="bi bi-box me-2"></i>Items</th>
                        <th class="table-header text-end"><i class="bi bi-gear-fill"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bills as $bill)
                        <tr>
                            <td>
                                <b class="{{ $bill->category == 0 ? 'text-primary' : 'text-success' }}">
                                    <a href="{{ route('bills.show', $bill) }}" class="text-decoration-none text-reset">
                                        {{ $bill->bill_number }}
                                    </a>
                                </b>
                            </td>
                            <td>
                                <b>
                                    <a href="{{ route('customers.show', $bill->customer->id) }}"
                                        class="text-decoration-none text-reset">
                                        {{ $bill->customer->name }}
                                    </a>
                                </b>
                            </td>
                            <td>{{ number_format($bill->total_price, 2) }}</td>
                            <td>{{ number_format($bill->advance_payment, 2) }}</td>
                            <td>{{ number_format($bill->balance, 2) }}</td>
                            <td>
                                <span class="badge bg-info text-white" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="{{ $bill->items->pluck('item_name')->join(', ') }}">
                                    {{ $bill->items->count() }}
                                </span>
                            </td>
                            <td class="text-nowrap text-end">
                                <a href="{{ route('bills.paymentPage', $bill) }}" class="btn btn-success btn-sm"
                                    title="Pay Now">
                                    <i class="bi bi-cash"></i> Pay Now
                                </a>
                                <a href="{{ route('bills.show', $bill) }}" class="btn btn-info btn-sm"
                                    title="View Bill">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('bills.edit', $bill) }}" class="btn btn-warning btn-sm"
                                    title="Edit Bill">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted text-center">No bills found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="d-flex justify-content-center mt-4 overflow-auto">
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

            .table tbody td:last-child .btn {
                margin-left: 0.25rem;
            }
        }
    </style>

    <script>
        // Add data-label attribute for mobile table view
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth < 768) {
                const headers = Array.from(document.querySelectorAll('.table thead th')).map(th => th.innerText
                    .trim());
                document.querySelectorAll('.table tbody tr').forEach(tr => {
                    tr.querySelectorAll('td').forEach((td, i) => {
                        td.setAttribute('data-label', headers[i] || '');
                    });
                });
            }
        });
    </script>

@endsection

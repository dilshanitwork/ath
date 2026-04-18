@extends('layouts.app')

@section('title', 'Customer Credit Report')

@section('content')
    <div class="container py-2 py-md-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 mb-md-4 gap-2">
            <h1 class="h4 h-md-3 text-primary fw-bold mb-0"> <i class="bi bi-person-badge me-2"></i>Customer Bills Report</h1>

            <a href="{{ route('reports.customer_credit_report') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-clockwise me-1"></i> Reset
            </a>
        </div>

        <div class="card mb-3 mb-md-4 border-0 bg-white shadow-sm">
            <div class="card-body p-3 p-md-4">
                <form action="{{ route('reports.customer_credit_report') }}" method="GET" id="filter-form">
                    <div class="row g-2 g-md-3 align-items-end">
                        <div class="col-12 col-md-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Customer Name</label>
                            <div class="position-relative">
                                <input type="text" name="customer_name" id="customer_name" class="form-control"
                                    placeholder="Type customer name..."
                                    value="{{ request('customer_name', $customerName) }}" autocomplete="off">
                                <div id="customer-suggestions" class="list-group position-absolute w-100 d-none shadow-sm"
                                    style="z-index: 1000; top: 100%;"></div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label text-muted small fw-bold text-uppercase">Date Range</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="date" name="date_from" id="date_from" class="form-control"
                                    value="{{ request('date_from', $dateFrom) }}">
                                <span class="text-muted d-none d-md-inline">to</span>
                                <span class="text-muted d-md-none">-</span>
                                <input type="date" name="date_to" id="date_to" class="form-control"
                                    value="{{ request('date_to', $dateTo) }}">
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <label class="form-label text-muted small fw-bold text-uppercase">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-3 d-flex gap-1">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-filter d-none d-md-inline me-1"></i> <span class="d-md-none">🔍</span><span class="d-none d-md-inline">Filter</span>
                            </button>
                            <button type="submit" formaction="{{ route('reports.customer_credit_report.export') }}"
                                class="btn btn-success flex-fill" title="Download CSV">
                                <i class="bi bi-file-earmark-excel d-none d-md-inline me-1"></i> <span class="d-md-none">📊</span><span class="d-none d-md-inline">CSV</span>
                            </button>
                            <button type="submit" name="export" value="pdf" class="btn btn-danger flex-fill"
                                title="Download PDF">
                                <i class="bi bi-file-earmark-pdf d-none d-md-inline"></i> <span class="d-md-none">📄</span><span class="d-none d-md-inline">PDF</span>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mt-3 gap-2">
                        <label for="per_page" class="form-label text-muted small fw-bold text-uppercase mb-0">
                            Per Page
                        </label>
                        <select name="per_page" id="per_page" class="form-select form-select-sm w-auto"
                            onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500</option>
                        </select>
                    </div>
                </form>

                @if (request('date_from') || request('date_to') || request('customer_name') || request('status'))
                    <div class="border-top d-flex align-items-center mt-3 flex-wrap gap-2 pt-3">
                        <span class="text-muted small fw-bold me-2">Active Filters:</span>

                        @if (request('customer_name'))
                            <span class="badge bg-primary">
                                <i class="bi bi-person me-1"></i>
                                {{ request('customer_name') }}
                            </span>
                        @endif

                        @if (request('date_from'))
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}
                                @if (request('date_to') && request('date_to') !== request('date_from'))
                                    &nbsp; - &nbsp; {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                                @endif
                            </span>
                        @endif

                        @if (request('status'))
                            <span class="badge bg-info text-dark">
                                <i class="bi bi-wallet2 me-1"></i> {{ ucfirst(request('status')) }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="row g-2 g-md-3 mb-3 mb-md-4">
            <div class="col-6 col-lg-3">
                <div class="card h-100 border-start border-primary border-0 border-4 shadow-sm">
                    <div class="card-body p-2 p-md-3">
                        <h6 class="text-uppercase text-muted small fw-bold mb-1 mb-md-2">Total Credit</h6>
                        <h4 class="h5 h-md-3 fw-bold text-primary mb-0">Rs. {{ number_format($totalFinal ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card h-100 border-start border-success border-0 border-4 shadow-sm">
                    <div class="card-body p-2 p-md-3">
                        <h6 class="text-uppercase text-muted small fw-bold mb-1 mb-md-2">Total Paid</h6>
                        <h4 class="h5 h-md-3 fw-bold text-success mb-0">Rs. {{ number_format($totalPaid ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card h-100 border-start border-danger border-0 border-4 shadow-sm">
                    <div class="card-body p-2 p-md-3">
                        <h6 class="text-uppercase text-muted small fw-bold mb-1 mb-md-2">Total Balance</h6>
                        <h4 class="h5 h-md-3 fw-bold text-danger mb-0">Rs. {{ number_format($totalBalance ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card h-100 bg-light border-0 shadow-sm">
                    <div class="card-body p-2 p-md-3 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted small fw-bold mb-1">Total Bills</h6>
                            <h4 class="h5 h-md-3 fw-bold text-dark mb-0">{{ $bills->total() }}</h4>
                        </div>
                        <i class="bi bi-receipt-cutoff fs-4 fs-md-1 text-secondary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center bg-white py-3">
                <h6 class="fw-bold text-secondary mb-0">
                    <i class="bi bi-list-ul me-2"></i>Transactions List
                </h6>
            </div>

            <div class="table-responsive">
                <table class="table-hover mb-0 table align-middle">
                    <thead class="bg-light text-secondary d-none d-md-table-header-group">
                        <tr>
                            <th class="ps-3 ps-md-4">Date</th>
                            <th>Bill #</th>
                            <th>Customer</th>
                            <th class="text-end">Status</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Balance</th>
                            <th class="pe-3 pe-md-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bills as $bill)
                            <tr>
                                <td class="ps-3 ps-md-4">
                                    <div class="d-md-none">
                                        <small class="text-muted">Date</small><br>
                                        <strong>{{ $bill->created_at->format('M d, Y') }}</strong>
                                    </div>
                                    <div class="d-none d-md-block">{{ $bill->created_at->format('Y-m-d H:i') }}</div>
                                </td>
                                <td>
                                    <div class="d-md-none">
                                        <small class="text-muted">Bill #</small><br>
                                        <a href="{{ route('direct_bills.show', $bill->id) }}"
                                            class="fw-bold text-decoration-none">
                                            {{ $bill->bill_number }}
                                        </a>
                                    </div>
                                    <div class="d-none d-md-block">
                                        <a href="{{ route('direct_bills.show', $bill->id) }}"
                                            class="fw-bold text-decoration-none">
                                            {{ $bill->bill_number }}
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-md-none">
                                        <small class="text-muted">Customer</small><br>
                                        <div class="fw-medium text-dark">{{ $bill->customer_name }}</div>
                                        @if($bill->contact_number)<small class="text-muted">{{ $bill->contact_number }}</small>@endif
                                    </div>
                                    <div class="d-none d-md-block">
                                        <div class="fw-medium text-dark">{{ $bill->customer_name }}</div>
                                        <small class="text-muted">{{ $bill->contact_number }}</small>
                                    </div>
                                </td>
                                <td class="text-end">
                                    @php $s = $bill->status ?? ($bill->balance > 0 ? 'open' : 'closed'); @endphp
                                    @if ($s == 'closed')
                                        <span class="badge bg-success">Closed</span>
                                    @elseif($s == 'partial')
                                        <span class="badge bg-warning text-dark">Partial</span>
                                    @else
                                        <span class="badge bg-danger">Open</span>
                                    @endif
                                </td>
                                <td class="fw-medium text-end">
                                    <div class="d-md-none">
                                        <small class="text-muted">Total</small><br>
                                        <strong>{{ number_format($bill->final_amount, 2) }}</strong>
                                    </div>
                                    <div class="d-none d-md-block">{{ number_format($bill->final_amount, 2) }}</div>
                                </td>
                                <td class="text-success text-end">
                                    <div class="d-md-none">
                                        <small class="text-muted">Paid</small><br>
                                        <strong>{{ number_format($bill->paid, 2) }}</strong>
                                    </div>
                                    <div class="d-none d-md-block">{{ number_format($bill->paid, 2) }}</div>
                                </td>
                                <td class="fw-bold {{ $bill->balance > 0 ? 'text-danger' : 'text-secondary' }} text-end">
                                    <div class="d-md-none">
                                        <small class="text-muted">Balance</small><br>
                                        <strong>{{ number_format($bill->balance, 2) }}</strong>
                                    </div>
                                    <div class="d-none d-md-block">{{ number_format($bill->balance, 2) }}</div>
                                </td>
                                <td class="text-muted small pe-3 pe-md-4 text-end">
                                    <div class="d-md-none">
                                        <a href="{{ route('direct_bills.show', $bill->id) }}"
                                            class="btn btn-sm btn-primary w-100">View</a>
                                    </div>
                                    <div class="d-none d-md-block">
                                        <i class="bi bi-person me-1"></i> {{ $bill->user->name ?? 'System' }}
                                        <div class="mt-2">
                                            <a href="{{ route('direct_bills.show', $bill->id) }}"
                                                class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-3 py-md-5 text-center">
                                    <div class="text-muted mb-2 opacity-50">
                                        <i class="bi bi-inbox fs-1"></i>
                                    </div>
                                    <p class="text-muted mb-0">No records found matching your filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center justify-content-md-end p-2 p-md-3">
                    {{ $bills->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Mobile-specific optimizations */
            @media (max-width: 767.98px) {
                /* Reduce container padding on mobile */
                .container.py-2.py-md-4 {
                    padding-left: 0.75rem !important;
                    padding-right: 0.75rem !important;
                }
                
                /* Optimize table for mobile */
                .table td {
                    padding: 0.5rem 0.25rem !important;
                    vertical-align: top !important;
                }
                
                /* Make mobile table rows more compact */
                .table tbody tr {
                    border-bottom: 1px solid #dee2e6 !important;
                }
                
                /* Reduce card spacing on mobile */
                .card-body {
                    padding: 0.75rem !important;
                }
                
                /* Optimize stats cards for mobile */
                .row.g-2.g-md-3 > .col-6 {
                    padding-left: 0.5rem !important;
                    padding-right: 0.5rem !important;
                }
                
                /* Make filter buttons more compact on mobile */
                .btn {
                    font-size: 0.875rem;
                    padding: 0.375rem 0.5rem;
                }
                
                /* Reduce gaps between sections */
                .mb-3.mb-md-4 {
                    margin-bottom: 1rem !important;
                }
                
                /* Optimize form elements */
                .form-control, .form-select {
                    font-size: 0.875rem;
                }
                
                /* Hide unnecessary elements on very small screens */
                @media (max-width: 576px) {
                    .d-flex.justify-content-between.align-items-md-center {
                        flex-direction: column !important;
                        align-items: flex-start !important;
                        gap: 0.5rem !important;
                    }
                }
            }
            
            /* Remove unwanted spaces */
            .table-responsive {
                margin: 0 !important;
            }
            
            /* Ensure consistent spacing */
            .card {
                margin-bottom: 1rem !important;
            }
            
            @media (min-width: 768px) {
                .card {
                    margin-bottom: 1.5rem !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const customerInput = document.getElementById('customer_name');
                const suggestionsContainer = document.getElementById('customer-suggestions');
                const from = document.getElementById('date_from');
                const to = document.getElementById('date_to');

                // Customer Autocomplete
                customerInput.addEventListener('input', function() {
                    const query = this.value;
                    if (query.length < 2) {
                        suggestionsContainer.classList.add('d-none');
                        return;
                    }

                    fetch(`{{ route('customers.suggestions') }}?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            suggestionsContainer.innerHTML = '';
                            if (data.length > 0) {
                                suggestionsContainer.classList.remove('d-none');
                                data.forEach(item => {
                                    const button = document.createElement('button');
                                    button.type = 'button';
                                    button.className =
                                        'list-group-item list-group-item-action border-0 py-2';
                                    button.innerHTML = `
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-bold">${item.name}</div>
                                                <small class="text-muted">${item.mobile || ''}</small>
                                            </div>
                                            <i class="bi bi-plus-circle text-primary"></i>
                                        </div>
                                    `;
                                    button.addEventListener('click', function() {
                                        customerInput.value = item.name;
                                        suggestionsContainer.classList.add('d-none');
                                        document.getElementById('filter-form').submit();
                                    });
                                    suggestionsContainer.appendChild(button);
                                });
                            } else {
                                suggestionsContainer.classList.add('d-none');
                            }
                        });
                });

                // Close suggestions when clicking outside
                document.addEventListener('click', function(e) {
                    if (!customerInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                        suggestionsContainer.classList.add('d-none');
                    }
                });

                // Date Logic
                if (from && to) {
                    let toTouched = false;
                    to.addEventListener('input', () => toTouched = true);
                    to.addEventListener('focus', () => toTouched = true);
                    from.addEventListener('change', () => {
                        if (!toTouched) to.value = from.value;
                    });
                }
            });
        </script>
    @endpush
@endsection

@extends('layouts.app')

@section('title', 'Credit Bill Summary')

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <h1 class="h3 text-primary fw-bold mb-0"> <i class="bi bi-wallet2 me-2"></i>Credit Bill Summary</h1>

            <a href="{{ route('reports.credit_summary') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-clockwise me-1"></i> Reset Filters
            </a>
        </div>

        <div class="card mb-4 border-0 bg-white shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('reports.credit_summary') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5 pe-md-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Date</label>

                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label text-muted small mb-0">From</label>
                                <input type="date" name="date_from" id="date_from" class="form-control"
                                    value="{{ request('date_from', $dateFrom ?? date('Y-m-d')) }}">
                                <label class="form-label text-muted small mb-0">To</label>
                                <input type="date" name="date_to" id="date_to" class="form-control"
                                    value="{{ request('date_to', $dateTo ?? request('date_from', date('Y-m-d'))) }}">
                            </div>
                        </div>

                        <div class="col-md-2 ps-md-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="open"
                                    {{ request('status') == 'open' || ($status ?? '') == 'open' ? 'selected' : '' }}>Open
                                </option>
                                <option value="closed"
                                    {{ request('status') == 'closed' || ($status ?? '') == 'closed' ? 'selected' : '' }}>
                                    Closed</option>
                                <option value="partial"
                                    {{ request('status') == 'partial' || ($status ?? '') == 'partial' ? 'selected' : '' }}>
                                    Partial</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold text-uppercase">Cashier</label>
                            <select name="user_id" class="form-select">
                                <option value="">All Cashiers</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-1">
                            <label class="form-label text-muted small fw-bold text-uppercase">Per Page</label>
                            <select name="per_page" class="form-select" onchange="this.form.submit()">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-filter me-1"></i> Filter
                            </button>
                            <button type="submit" formaction="{{ route('reports.credit_summary.export') }}"
                                class="btn btn-success flex-grow-1" title="Download CSV">
                                <i class="bi bi-file-earmark-excel me-1"></i> Export
                            </button>
                        </div>
                    </div>

                    <br>
                    <div class="d-flex align-items-center gap-2">
                        <label for="per_page" class="form-label mb-0">
                            <i class="fas fa-list"></i> Per Page
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

                @if (request('date_from') || request('date_to') || request('user_id')||request('status'))
                    <div class="border-top d-flex align-items-center mt-3 flex-wrap gap-2 pt-3">
                        <span class="text-muted small fw-bold me-2">Active Filters:</span>

                        @if (request('date_from'))
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}
                                @if (request('date_to') && request('date_to') !== request('date_from'))
                                    &nbsp; - &nbsp; {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                                @endif
                            </span>
                        @endif

                        @if (request('user_id'))
                            <span class="badge bg-secondary">
                                <i class="bi bi-person me-1"></i>
                                {{ $users->firstWhere('id', request('user_id'))->name ?? 'Unknown' }}
                            </span>
                        @endif
                        @if ($status)
                            <span class="badge bg-info text-dark">
                                <i class="bi bi-wallet2 me-1"></i> {{ ucfirst($status) }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 border-start border-primary border-0 border-4 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-uppercase text-muted small fw-bold mb-2">Total Credit Amount</h6>
                        <h3 class="fw-bold text-primary mb-0">Rs. {{ number_format($totalFinal ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 border-start border-success border-0 border-4 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-uppercase text-muted small fw-bold mb-2">Total Paid</h6>
                        <h3 class="fw-bold text-success mb-0">Rs. {{ number_format($totalPaid ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 border-start border-danger border-0 border-4 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-uppercase text-muted small fw-bold mb-2">Total Balance</h6>
                        <h3 class="fw-bold text-danger mb-0">Rs. {{ number_format($totalBalance ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 bg-light border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted small fw-bold mb-1">Total Bills</h6>
                            <h3 class="fw-bold text-dark mb-0">{{ $bills->total() ?? $bills->count() }}</h3>
                        </div>
                        <i class="bi bi-receipt-cutoff fs-1 text-secondary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center bg-white py-3">
                <h6 class="fw-bold text-secondary mb-0">
                    <i class="bi bi-list-ul me-2"></i>Transactions List (Credit)
                </h6>
                <span class="badge bg-light text-dark border">
                    {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('l, F j, Y') : '' }}
                    @if (request('date_to') && request('date_to') !== request('date_from'))
                        &nbsp;-&nbsp; {{ \Carbon\Carbon::parse(request('date_to'))->format('l, F j, Y') }}
                    @endif
                </span>
            </div>

            <div class="table-responsive">
                <table class="table-hover mb-0 table align-middle">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Bill #</th>
                            <th>Customer</th>
                            <th class="text-end">Status</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Balance</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bills as $bill)
                            <tr>
                                <td class="ps-4">{{ $bill->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('direct_bills.show', $bill->id) }}"
                                        class="fw-bold text-decoration-none">
                                        {{ $bill->bill_number }}
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $bill->customer_name }}</div>
                                    <small class="text-muted">{{ $bill->contact_number }}</small>
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
                                <td class="fw-medium text-end">{{ number_format($bill->final_amount, 2) }}</td>
                                <td class="text-success text-end">{{ number_format($bill->paid, 2) }}</td>
                                <td class="fw-bold {{ $bill->balance > 0 ? 'text-danger' : 'text-secondary' }} text-end">
                                    {{ number_format($bill->balance, 2) }}
                                </td>
                                <td class="text-muted small pe-4 text-end">
                                    <i class="bi bi-person me-1"></i> {{ $bill->user->name ?? 'System' }}
                                    <div class="mt-2">
                                        <a href="{{ route('direct_bills.show', $bill->id) }}"
                                            class="btn btn-sm btn-outline-primary">View</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-5 text-center">
                                    <div class="text-muted mb-2 opacity-50">
                                        <i class="bi bi-inbox fs-1"></i>
                                    </div>
                                    <p class="text-muted mb-0">No credit bill records found for this range.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-end mt-3">
                    {{ $bills->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const from = document.getElementById('date_from');
                const to = document.getElementById('date_to');
                if (!from || !to) return;

                let toTouched = false;

                // If user focuses/changes To, mark as touched so auto-fill stops
                to.addEventListener('input', () => {
                    toTouched = true;
                });
                to.addEventListener('focus', () => {
                    toTouched = true;
                });

                // When From changes, auto-copy to To only if To hasn't been manually edited
                from.addEventListener('change', () => {
                    if (!toTouched) {
                        to.value = from.value;
                    }
                });
            });
        </script>
    @endpush
@endsection

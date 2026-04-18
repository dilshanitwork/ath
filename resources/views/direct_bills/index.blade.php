@extends('layouts.app')

@section('title', 'Direct Bills')

@section('content')
    <div class="container">

        {{-- ... (Print Script Block) ... --}}

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-receipt-cutoff me-2"></i>Direct Bills</h1>
            <a href="{{ route('direct_bills.create') }}" class="btn btn-outline-dark mt-md-0 mt-2">
                <i class="bi bi-plus-circle me-1"></i> Create New Bill
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-funnel me-1"></i> Filter Bills</h5>

                {{-- ... (Active Filters Block) ... --}}

                <form action="{{ route('direct_bills.index') }}" method="GET" class="mt-3">
                    <div class="row g-3">
                        <div class="col-md-1">
                            <label for="search" class="form-label">Bill</label>
                            <input type="number" class="form-control" id="search" name="search" placeholder="Bill No."
                                value="{{ request('search') }}">
                        </div>

                        {{-- UPDATE START: Customer Input with Datalist --}}
                        <div class="col-md-3">
                            <label for="customer" class="form-label">Customer</label>
                            <input type="text" class="form-control" id="customer" name="customer"
                                placeholder="Type to search..." value="{{ request('customer') }}" list="customerList"
                                autocomplete="off">

                            {{-- The Data List --}}
                            <datalist id="customerList">
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->name }}">
                                @endforeach
                            </datalist>
                        </div>
                        {{-- UPDATE END --}}

                        <div class="col-md-2">
                            <label for="type" class="form-label">Payment Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All Types</option>
                                <option value="cash" {{ request('type') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Credit</option>
                            </select>
                        </div>

                        {{-- ... (Rest of the form: Status, Dates, Filter Button) ... --}}

                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open (Unpaid)
                                </option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed (Paid)
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to"
                                value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100" title="Apply Filters">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                        @if (request()->hasAny(['customer', 'type', 'status', 'date_from', 'date_to']))
                            <div class="col-md-1 d-flex align-items-end">
                                <a href="{{ route('direct_bills.index') }}" class="btn btn-outline-danger w-100"
                                    title="Reset Filters">
                                    <i class="bi bi-x-circle"></i> Reset
                                </a>
                            </div>
                        @endif
                        @if (request('customer'))
                            <div class="col-md-8">
                                Selected Customer <b class="text-success"> {{ request('customer') }} &nbsp;</b>Total
                                Balance
                                is Rs. <b>
                                    {{ number_format(\App\Models\DirectBill::where('customer_name', request('customer'))->sum('balance'), 2) }}</b>
                            </div>
                        @endif
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="d-flex align-items-end gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <label for="per_page" class="form-label mb-0">
                                        <i class="fas fa-list"></i> Per Page
                                    </label>
                                    <select name="per_page" id="per_page" class="form-select form-select-sm w-auto"
                                        onchange="this.form.submit()">
                                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10
                                        </option>
                                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25
                                        </option>
                                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100
                                        </option>
                                        <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500
                                        </option>
                                    </select>
                                </div>
                                {{-- @if (request()->has('per_page') && (int) request('per_page') > 10)
                                    <div>
                                        <label class="form-label mb-1 d-block">&nbsp;</label>
                                        <a href="{{ route('direct_bills.index', request()->except('per_page', 'page')) }}"
                                            class="btn btn-outline-danger">
                                            <i class="bi bi-x-circle-fill me-2"></i> Reset
                                        </a>
                                    </div>
                                @endif --}}

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ... (Success Message & Table) ... --}}

        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table-bordered table-hover table align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col"><i class="bi bi-receipt me-2"></i> Bill #</th>
                        <th><i class="bi bi-person me-2"></i>Customer</th>
                        <th><i class="bi bi-calendar me-2"></i>Date</th>
                        <th><i class="bi bi-box me-2"></i>Items</th>
                        <th><i class="bi bi-tag me-2"></i>Status</th>
                        <th class="text-end"><i class="bi bi-currency-dollar me-2"></i>Total</th>
                        <th class="text-end"><i class="bi bi-wallet2 me-2"></i>Balance</th>
                        <th class="text-end"><i class="bi bi-gear"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($directBills as $bill)
                        <tr>
                            <td><b><a href="{{ route('direct_bills.show', $bill) }}"
                                        class="text-decoration-none text-primary">{{ $bill->bill_number }}</a></b></td>
                            <td>
                                <div>{{ $bill->customer_name }}</div>
                            </td>
                            <td>{{ $bill->created_at->format('Y-m-d') }}</td>
                            <td>
                                <span class="badge bg-info text-white" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="{{ $bill->items->pluck('item_name')->join(', ') }}">
                                    {{ $bill->items->count() }}
                                </span>
                            </td>
                            <td>
                                @if ($bill->status == 'closed')
                                    <span class="badge bg-success">Closed</span>
                                @else
                                    <span class="badge bg-danger">Open</span>
                                @endif
                            </td>
                            <td class="fw-bold text-end">{{ number_format($bill->final_amount, 2) }}</td>
                            <td class="{{ $bill->balance > 0 ? 'text-danger fw-bold' : 'text-muted' }} text-end">
                                {{ number_format($bill->balance, 2) }}
                            </td>
                            <td class="text-nowrap text-end">
                                <a href="{{ route('direct_bills.show', $bill) }}" class="btn btn-info btn-sm"
                                    title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('direct_bills.edit', $bill) }}" class="btn btn-warning btn-sm"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-muted py-4 text-center">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                No direct bills found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            {{ $directBills->appends(request()->query())->links() }}
        </div>
    </div>

    @push('scripts')
        @if (session('print_bill_id'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Build the print URL server-side and JSON-encode it to be safe in JS
                    const printUrl = {!! json_encode(route('direct_bills.print', session('print_bill_id'))) !!};

                    if (printUrl) {
                        // Open the print view in a new tab/window
                        window.open(printUrl, '_blank');

                        // Optional: focus the newly opened window (may be blocked by some browsers)
                        try {
                            window.focus();
                        } catch (e) {
                            /* ignore */ }
                    }
                });
            </script>
        @endif
    @endpush

@endsection

@extends('layouts.app')
@section('title', 'All Purchase Order')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-card-checklist me-2"></i>Purchase Orders</h1>
            <a href="{{ route('purchase_orders.create') }}" class="btn btn-outline-dark">
                <i class="bi bi-plus-circle me-1"></i> Create New PO
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('purchase_orders.index') }}" method="GET" class="mt-3">
                    <div class="row mb-3 align-items-start">
                        <div class="col-12 col-lg-8">
                            <h5 class="card-title mb-2">
                                <i class="bi bi-funnel me-1"></i>Filter Orders
                            </h5>

                            <!-- Active Filters Display -->
                            @if (request('search') || request('date_from') || request('date_to') || request('status'))
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2" style="font-size: 0.9rem;">Active Filters:</h6>
                                    <div class="d-flex flex-wrap align-items-center gap-1">
                                        @if (request('search'))
                                            <span class="badge bg-primary me-1">Search: "{{ request('search') }}"</span>
                                        @endif
                                        @if (request('date_from'))
                                            <span class="badge bg-secondary me-1">From: {{ request('date_from') }}</span>
                                        @endif
                                        @if (request('date_to'))
                                            <span class="badge bg-secondary me-1">To: {{ request('date_to') }}</span>
                                        @endif
                                        @if (request('status'))
                                            <span class="badge bg-info text-dark me-1">Status:
                                                {{ ucfirst(request('status')) }}</span>
                                        @endif
                                        <a href="{{ route('purchase_orders.index') }}"
                                            class="btn btn-sm btn-outline-danger text-decoration-none ms-2"><i
                                                class="bi bi-x-circle"></i> Clear
                                            All</a>
                                    </div>
                                </div>
                            @endif

                        </div>
                        <div class="col-12 col-lg-4 d-flex justify-content-lg-end mt-3 mt-lg-0">
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
                                        <a href="{{ route('purchase_orders.index', request()->except('per_page', 'page')) }}"
                                            class="btn btn-outline-danger">
                                            <i class="bi bi-x-circle-fill me-2"></i> Reset
                                        </a>
                                    </div>
                                @endif --}}

                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="PO # or Supplier Name" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received
                                </option>
                                <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Canceled
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
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-dark w-100" title="Apply Filters">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-bordered table-hover table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>PO No.</th>
                        <th>Supplier</th>
                        <th>Invoice Number</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Total Amount</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchaseOrders as $po)
                        <tr>
                            <td><a href="{{ route('purchase_orders.show', $po) }}"
                                    class="text-decoration-none text-dark"><b>#{{ $po->id }}</b></a></td>
                            <td><a href="{{ route('suppliers.show', $po->supplier) }}"
                                    class="text-decoration-none">{{ $po->supplier->name ?? 'N/A' }}</a></td>
                            <td>{{ $po->po_number ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($po->order_date)->format('Y-m-d') }}</td>
                            <td>
                                @if ($po->status === 'received')
                                    <span class="badge bg-success">Received</span>
                                @elseif($po->status === 'canceled')
                                    <span class="badge bg-danger">Canceled</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td>{{ number_format($po->total_amount, 2) }}</td>
                            <td class="text-nowrap text-end">
                                <a href="{{ route('purchase_orders.show', $po) }}" class="btn btn-info btn-sm"
                                    title="View">
                                    <i class="bi bi-eye"></i>
                                </a>

                                {{-- Only allow editing if not received/canceled --}}
                                @if ($po->status !== 'received' && $po->status !== 'canceled')
                                    <a href="{{ route('purchase_orders.edit', $po) }}" class="btn btn-warning btn-sm"
                                        title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                @endif

                                <a href="{{ route('purchase_orders.print', $po) }}" class="btn btn-secondary btn-sm"
                                    title="Print" target="_blank">
                                    <i class="bi bi-printer"></i>
                                </a>

                                <a href="{{ route('purchase_orders.download', $po) }}" class="btn btn-success btn-sm"
                                    title="Download" target="_blank">
                                    <i class="bi bi-download"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted py-4 text-center">No purchase orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
            {{ $purchaseOrders->appends(request()->query())->links() }}
        </div>
    </div>
@endsection

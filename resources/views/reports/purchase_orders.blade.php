@extends('layouts.app')

@section('title', 'Purchase Order Report')

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <h1 class="h3 text-primary fw-bold mb-0"><i class="bi bi-cart-check me-2"></i>Purchase Order Report</h1>

            <a href="{{ route('reports.purchase_orders') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-clockwise me-1"></i> Reset Filters
            </a>
        </div>

        <div class="card mb-4 border-0 bg-white shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('reports.purchase_orders') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold text-uppercase">Start Date</label>
                            <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold text-uppercase">End Date</label>
                            <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Supplier</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">All Suppliers</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ $supplierId == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold text-uppercase">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="received" {{ $status == 'received' ? 'selected' : '' }}>Received</option>
                                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="canceled" {{ $status == 'canceled' ? 'selected' : '' }}>Canceled</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-filter me-1"></i> Filter
                            </button>
                            <button type="submit" formaction="{{ route('reports.purchase_orders.export') }}" class="btn btn-success"
                                title="Download Excel">
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
                </form>

                @if ($startDate || $endDate || $supplierId || $status)
                    <div class="border-top d-flex align-items-center mt-3 flex-wrap gap-2 pt-3">
                        <span class="text-muted small fw-bold me-2">Active Filters:</span>

                        @if ($startDate)
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-calendar-event me-1"></i> From:
                                {{ \Carbon\Carbon::parse($startDate)->format('M d') }}
                            </span>
                        @endif
                        @if ($endDate)
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-calendar-event me-1"></i> To:
                                {{ \Carbon\Carbon::parse($endDate)->format('M d') }}
                            </span>
                        @endif

                        @if ($supplierId)
                            <span class="badge bg-secondary">
                                <i class="bi bi-truck me-1"></i>
                                {{ $suppliers->firstWhere('id', $supplierId)->name ?? 'Unknown' }}
                            </span>
                        @endif

                        @if ($status)
                            @php
                                $statusColor = match ($status) {
                                    'received' => 'success',
                                    'pending' => 'warning',
                                    'canceled' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $statusColor }} text-{{ $status == 'pending' ? 'dark' : 'white' }}">
                                <i class="bi bi-info-circle me-1"></i> {{ ucfirst($status) }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card h-100 border-start border-primary border-0 border-4 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-uppercase text-muted small fw-bold mb-2">Total Purchases</h6>
                        <h3 class="fw-bold text-primary mb-0">Rs. {{ number_format($totalPurchases, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-start border-success border-0 border-4 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-uppercase text-muted small fw-bold mb-2">Orders Received</h6>
                        <h3 class="fw-bold text-success mb-0">{{ $receivedCount }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-start border-warning border-0 border-4 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-uppercase text-muted small fw-bold mb-2">Orders Pending</h6>
                        <h3 class="fw-bold text-warning mb-0">{{ $pendingCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center bg-white py-3">
                <h6 class="fw-bold text-secondary mb-0">
                    <i class="bi bi-list-ul me-2"></i>Order History
                </h6>
                <span class="badge bg-light text-dark border">
                    {{ \Carbon\Carbon::parse($startDate)->format('M d') }} -
                    {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                </span>
            </div>
            <div class="table-responsive">
                <table class="table-hover mb-0 table align-middle">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Total Amount</th>
                            <th class="pe-4 text-center">Items</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $po)
                            <tr>
                                <td class="text-muted ps-4">
                                    {{ \Carbon\Carbon::parse($po->order_date)->format('Y-m-d') }}
                                </td>
                                <td>
                                    <a href="{{ route('purchase_orders.show', $po->id) }}"
                                        class="fw-bold text-decoration-none">
                                        {{ $po->po_number }}
                                    </a>
                                </td>
                                <td>
                                    <div class="text-dark">{{ $po->supplier->name ?? 'Unknown' }}</div>
                                </td>
                                <td class="text-center">
                                    @if ($po->status === 'received')
                                        <span
                                            class="badge bg-success-subtle text-success border-success-subtle border px-3">Received</span>
                                    @elseif($po->status === 'pending')
                                        <span
                                            class="badge bg-warning-subtle text-warning-emphasis border-warning-subtle border px-3">Pending</span>
                                    @else
                                        <span
                                            class="badge bg-secondary-subtle text-secondary border-secondary-subtle border px-3">{{ ucfirst($po->status) }}</span>
                                    @endif
                                </td>
                                <td class="fw-medium text-end">
                                    {{ number_format($po->total_amount, 2) }}
                                </td>
                                <td class="pe-4 text-center">
                                    <span class="badge bg-light text-dark border">{{ $po->items->count() }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-5 text-center">
                                    <div class="text-muted mb-2 opacity-50">
                                        <i class="bi bi-cart-x fs-1"></i>
                                    </div>
                                    <p class="text-muted mb-0">No purchase orders found in this date range.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-end mt-3">
                    {{ $purchaseOrders->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

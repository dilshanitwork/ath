@extends('layouts.app')

@section('title', 'Stock Inventory Report')

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <h1 class="h3 text-primary fw-bold mb-0"><i class="bi bi-box-seam me-2"></i>Stock Inventory Report</h1>

            <a href="{{ route('reports.stock_inventory') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-clockwise me-1"></i> Reset Filters
            </a>
        </div>

        <div class="card mb-4 border-0 bg-white shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('reports.stock_inventory') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Search Item</label>
                            <input type="text" name="search" class="form-control" placeholder="Name or Model..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Supplier</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">All Suppliers</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold text-uppercase">Stock Status</label>
                            <select name="stock_status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock (< 5)</option>
                                <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Out of Stock
                                </option>
                                <option value="in" {{ request('stock_status') == 'in' ? 'selected' : '' }}>In Stock
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold text-uppercase">Vehicle Type</label>
                            <select name="vehicle_type" class="form-select">
                                <option value="">All Types</option>
                                @foreach ($vehicleTypes as $type)
                                    <option value="{{ $type }}"
                                        {{ request('vehicle_type') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-filter me-1"></i> Filter
                            </button>
                            <div class="btn-group">
                                <button type="submit" formaction="{{ route('reports.stock_inventory.export') }}"
                                    class="btn btn-success" title="Download Excel (CSV)">
                                    <i class="bi bi-file-earmark-excel"></i>
                                </button>
                                <button type="submit" name="export" value="pdf" class="btn btn-danger"
                                    title="Download PDF">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </button>
                            </div>
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

                @if (request('search') || request('supplier_id') || request('stock_status') || request('vehicle_type'))
                    <div class="border-top d-flex align-items-center mt-3 flex-wrap gap-2 pt-3">
                        <span class="text-muted small fw-bold me-2">Active Filters:</span>

                        @if (request('search'))
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-search me-1"></i> "{{ request('search') }}"
                            </span>
                        @endif

                        @if (request('supplier_id'))
                            <span class="badge bg-secondary">
                                <i class="bi bi-truck me-1"></i>
                                {{ $suppliers->firstWhere('id', request('supplier_id'))->name ?? 'Unknown' }}
                            </span>
                        @endif

                        @if (request('vehicle_type'))
                            <span class="badge bg-info text-dark">
                                <i class="bi bi-car-front me-1"></i>
                                {{ request('vehicle_type') }}
                            </span>
                        @endif

                        @if (request('stock_status'))
                            @php
                                $statusLabel =
                                    request('stock_status') == 'low'
                                        ? 'Low Stock'
                                        : (request('stock_status') == 'out'
                                            ? 'Out of Stock'
                                            : 'In Stock');
                                $statusColor =
                                    request('stock_status') == 'low'
                                        ? 'warning'
                                        : (request('stock_status') == 'out'
                                            ? 'danger'
                                            : 'success');
                            @endphp
                            <span
                                class="badge bg-{{ $statusColor }} text-{{ $statusColor == 'warning' ? 'dark' : 'white' }}">
                                <i class="bi bi-info-circle me-1"></i> {{ $statusLabel }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="alert alert-info d-flex align-items-center bg-info-subtle text-info-emphasis mb-4 border-0 shadow-sm">
            <div class="fs-1 text-info me-3 opacity-75">
                <i class="bi bi-wallet2"></i>
            </div>
            <div>
                <h5 class="alert-heading fw-bold mb-1">
                    Total Inventory Valuation: Rs. {{ number_format($totalInventoryValue, 2) }}
                </h5>
                <p class="small mb-0 opacity-75">
                    <i class="bi bi-info-circle me-1"></i> Estimated based on the cost price of the most recent batch for
                    each item.
                </p>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center bg-white py-3">
                <h6 class="fw-bold text-secondary mb-0">
                    <i class="bi bi-list-ul me-2"></i>Inventory List
                </h6>
                <span class="badge bg-light text-dark border">
                    {{ count($stockItems) }} Items Found
                </span>
            </div>
            <div class="table-responsive">
                <table class="table-hover mb-0 table align-middle">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="ps-4">Item Name</th>
                            <th>Vehicle Type</th>
                            <th>Model/Pattern</th>
                            <th class="text-center">Current Stock</th>
                            <th class="text-end">Est. Unit Cost</th>
                            <th class="pe-4 text-end">Total Value</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stockItems as $item)
                            <tr>
                                <td class="fw-bold text-primary ps-4">
                                    {{ $item->name }}
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $item->vehicle_type ?? '-' }}</span>
                                </td>
                                <td>
                                    @if ($item->model_number)
                                        <span class="badge bg-light text-dark border">{{ $item->model_number }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span
                                        class="fs-6 fw-bold {{ $item->current_stock == 0 ? 'text-danger' : 'text-dark' }}">
                                        {{ $item->current_stock }}
                                    </span>
                                </td>
                                <td class="text-muted small text-end">
                                    {{ number_format($item->avg_cost, 2) }}
                                </td>
                                <td class="fw-bold text-dark pe-4 text-end">
                                    {{ number_format($item->total_value, 2) }}
                                </td>
                                <td class="text-center">
                                    @if ($item->current_stock == 0)
                                        <span
                                            class="badge bg-danger-subtle text-danger border-danger-subtle border px-2">Out
                                            of Stock</span>
                                    @elseif($item->current_stock < 5)
                                        <span
                                            class="badge bg-warning-subtle text-warning-emphasis border-warning-subtle border px-2">Low
                                            Stock</span>
                                    @else
                                        <span
                                            class="badge bg-success-subtle text-success border-success-subtle border px-2">In
                                            Stock</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-5 text-center">
                                    <div class="text-muted mb-2 opacity-50">
                                        <i class="bi bi-box-seam fs-1"></i>
                                    </div>
                                    <p class="text-muted mb-0">No inventory items found matching your filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                 <div class="d-flex justify-content-end mt-3">
                    {{ $stockItems->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

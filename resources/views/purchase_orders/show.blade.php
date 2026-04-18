@extends('layouts.app')
@section('title', 'Purchase Order Details - '. $purchaseOrder->id)
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-journal-text me-2"></i>Purchase Order #{{ $purchaseOrder->id }}
            </h1>
            <div>
                <a href="{{ route('purchase_orders.index') }}" class="btn btn-secondary me-1">
                    <i class="bi bi-arrow-left"></i> Back
                </a>

                <a href="{{ route('purchase_orders.print', $purchaseOrder) }}" class="btn btn-info me-2" title="Print"
                    target="_blank">
                    <i class="bi bi-printer"></i> Print
                </a>

                <a href="{{ route('purchase_orders.edit', $purchaseOrder) }}" class="btn btn-warning me-1">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
                <form action="{{ route('purchase_orders.destroy', $purchaseOrder) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Are you sure you want to delete this Purchase Order? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="card p-4">
            {{-- Order Info Section --}}
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <h5 class="text-primary"><i class="bi bi-info-circle me-2"></i>Order Information</h5>
                    <hr>
                    <dl class="row">
                        <dt class="col-sm-4">Invoice Number:</dt>
                        <dd class="col-sm-8 fw-bold">{{ $purchaseOrder->po_number }}</dd>

                        <dt class="col-sm-4">Supplier:</dt>
                        <dd class="col-sm-8">
                            @if ($purchaseOrder->supplier)
                                <a href="#" class="text-decoration-none">{{ $purchaseOrder->supplier->name }}</a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Order Date:</dt>
                        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('F d, Y') }}</dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            @if ($purchaseOrder->status === 'received')
                                <span class="badge bg-success">Received</span>
                            @elseif($purchaseOrder->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($purchaseOrder->status === 'canceled')
                                <span class="badge bg-info text-dark">Canceled</span>
                            @elseif($purchaseOrder->status === 'cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($purchaseOrder->status) }}</span>
                            @endif
                        </dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <h5 class="text-primary"><i class="bi bi-currency-dollar me-2"></i>Financial Details</h5>
                    <hr>
                    <dl class="row">
                        <dt class="col-sm-4">Total Amount:</dt>
                        <dd class="col-sm-8 fs-5 fw-bold text-success">{{ number_format($purchaseOrder->total_amount, 2) }}
                        </dd>

                        <dt class="col-sm-4">Total Items:</dt>
                        <dd class="col-sm-8">{{ $purchaseOrder->items->count() }}</dd>

                        <dt class="col-sm-4">Created At:</dt>
                        <dd class="col-sm-8 text-muted">{{ $purchaseOrder->created_at->format('Y-m-d H:i') }}</dd>
                    </dl>
                </div>
            </div>

            {{-- ✅ Notes Section --}}
            @if ($purchaseOrder->notes)
                <div class="mb-4">
                    <h5 class="text-primary"><i class="bi bi-sticky me-2"></i>Notes</h5>
                    <hr>
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $purchaseOrder->notes }}</p>
                </div>
            @endif

            {{-- Items Table --}}
            <h5 class="text-primary mt-3"><i class="bi bi-list-check me-2"></i>Order Items</h5>
            <div class="table-responsive">
                <table class="table-bordered table-striped table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 45%">Item Name</th>
                            <th style="width: 15%" class="text-center">Quantity</th>
                            <th style="width: 15%" class="text-end">Unit Cost</th>
                            <th style="width: 20%" class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrder->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('stock_items.show', $item->stockItem->id) }}" class="text-decoration-none">{{ $item->stockItem->name }}</a>
                                    @if (optional($item->stockItem)->model_number)
                                        <br><small class="text-muted">Model: {{ $item->stockItem->model_number }}</small>
                                    @endif
                                </td>
                                <td class="fw-bold text-center">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->unit_cost, 2) }}</td>
                                <td class="fw-bold text-end">{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted py-3 text-center">No items found in this purchase
                                    order.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="4" class="fw-bold text-end">Grand Total:</td>
                            <td class="fw-bold text-success text-end">{{ number_format($purchaseOrder->total_amount, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
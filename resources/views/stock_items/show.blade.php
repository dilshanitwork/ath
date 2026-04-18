@extends('layouts.app')
@section('title','Stock Item - '.$stockItem->name)
@section('title', 'Stock Item Details')

@section('content')
    <div class="container">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="card-title mb-0"><i class="bi bi-box-seam me-2"></i>Stock Item Details</h1>
                <a href="{{ route('stock_items.customer_purchases', $stockItem) }}" class="btn btn-success">
                   Customer Purchases
                </a>
                <a href="{{ route('stock_items.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <h4><i class="bi bi-info-circle me-2"></i>Basic Information</h4>
                    <hr>
                    <dl class="row">
                        <dt class="col-sm-4">ID</dt>
                        <dd class="col-sm-8">{{ $stockItem->id }}</dd>

                        <dt class="col-sm-4">Item Name</dt>
                        <dd class="col-sm-8">{{ $stockItem->name }}</dd>

                        <dt class="col-sm-4">Item Code</dt>
                        <dd class="col-sm-8">{{ $stockItem->model_number ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Supplier</dt>
                        <dd class="col-sm-8">{{ $stockItem->supplier->name ?? 'N/A' }}</dd>

                        {{-- UPDATED: Total Quantity from sum --}}
                        <dt class="col-sm-4">Total Quantity</dt>
                        <dd class="col-sm-8 fw-bold" style="font-size: 1.2rem; color: #0d6efd;">
                            {{ $totalQuantity }}
                        </dd>
                    </dl>
                </div>

                <div class="col-md-6">
                    <h4><i class="bi bi-tags me-2"></i>Latest Pricing</h4>
                    <p class="text-muted">Showing pricing from the most recently added batch.</p>
                    <hr>
                    <dl class="row">
                        {{-- UPDATED: Show latest batch pricing --}}
                        <dt class="col-sm-4">Cost Price</dt>
                        <dd class="col-sm-8">{{ $latestBatch ? number_format($latestBatch->cost_price, 2) : 'N/A' }}</dd>

                        <dt class="col-sm-4">Selling Price</dt>
                        <dd class="col-sm-8">{{ $latestBatch ? number_format($latestBatch->selling_price, 2) : 'N/A' }}</dd>

                        <dt class="col-sm-4">Installment Price</dt>
                        <dd class="col-sm-8">{{ $latestBatch ? number_format($latestBatch->installment_price, 2) : 'N/A' }}
                        </dd>
                    </dl>
                </div>

                <div class="col-md-12">
                    <h4><i class="bi bi-three-dots me-2"></i>Additional Details</h4>
                    <hr>
                    <dl class="row">
                        <dt class="col-sm-2">Color</dt>
                        <dd class="col-sm-10">{{ $stockItem->color ?? 'N/A' }}</dd>

                        <dt class="col-sm-2">Warranty</dt>
                        <dd class="col-sm-10">{{ $stockItem->warranty ?? 'N/A' }}</dd>

                        <dt class="col-sm-2">Other</dt>
                        <dd class="col-sm-10">
                            {!! nl2br(e($stockItem->other ?? 'N/A')) !!}
                        </dd>
                         <dt class="col-sm-2">Vehicle Type</dt>
                        <dd class="col-sm-10">{{ $stockItem->vehicle_type ?? 'N/A' }}</dd>
                        <dt class="col-sm-2">Is this Service Item?</dt>
                        <dd class="col-sm-10">
                            {{ $stockItem->service ? 'Yes' : 'No' }}
                        </dd>
                    </dl>
                </div>

                {{-- NEW SECTION: BATCH HISTORY --}}
                <div class="col-md-12">
                    <h4><i class="bi bi-archive me-2"></i>Stock Batch History</h4>
                    <hr>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table-bordered table-striped table-hover table">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Received Date</th>
                                    <th>Invoice Number</th>
                                    <th>Quantity</th>
                                    <th>Cost Price</th>
                                    <th>Selling Price</th>
                                    <th>Installment Price</th>
                                    <th>Source</th>
                                    <th>Initial Quantity</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stockItem->stockBatches as $batch)
                                    <tr>
                                        <td>{{ $batch->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $batch->invoice_number ?? 'N/A' }}</td>
                                        <td><strong>{{ $batch->quantity }}</strong></td>
                                        <td>{{ number_format($batch->cost_price, 2) }}</td>
                                        <td>{{ number_format($batch->selling_price, 2) }}</td>
                                        <td>{{ number_format($batch->installment_price, 2) }}</td>
                                        <td>
                                            @if ($batch->purchase_order_id)
                                                <a href="{{ route('purchase_orders.show', $batch->purchase_order_id) }}"
                                                    class="text-decoration-none">
                                                    {{ $batch->invoice_number ?? 'PO #' . $batch->purchase_order_id }}
                                                </a>
                                            @else
                                                <span class="text-muted">Initial Batch / Adjustment</span>
                                            @endif
                                        </td>
                                        <td>{{ $batch->initial_quantity }}</td>
                                        <td class="text-nowrap text-center">
                                            <a href="{{ route('stock_adjustments.edit', $batch) }}"
                                                class="btn btn-warning btn-sm" title="Edit Batch">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" title="Delete Batch"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteBatchModal{{ $batch->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                            <!-- Delete Modal for each batch -->
                                            <div class="modal fade" id="deleteBatchModal{{ $batch->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title">
                                                                <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm
                                                                Deletion
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><strong>Delete this stock batch?</strong></p>
                                                            <p>Quantity: <strong>{{ $batch->quantity }}</strong></p>
                                                            <p>Price:
                                                                <strong>{{ number_format($batch->selling_price, 2) }}</strong>
                                                            </p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <form action="{{ route('stock_adjustments.destroy', $batch) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">
                                                                    <i class="bi bi-trash me-1"></i> Delete
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-muted text-center">No stock batches found for this
                                            item.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-12">
                    <hr>
                    <dl class="row text-muted" style="font-size: 0.9em;">
                        <dt class="col-sm-2">Item Registered</dt>
                        <dd class="col-sm-10"><i>{{ $stockItem->created_at->format('F d, Y h:i A') }}</i></dd>

                        <dt class="col-sm-2">Item Last Updated</dt>
                        <dd class="col-sm-10"><i>{{ $stockItem->updated_at->format('F d, Y h:i A') }}</i></dd>
                    </dl>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('stock_adjustments.create', ['stock_item_id' => $stockItem->id]) }}"
                    class="btn btn-info">
                    <i class="bi bi-plus-circle me-1"></i> Add Stock Batch
                </a>
                <a href="{{ route('stock_items.edit', $stockItem) }}" class="btn btn-warning">
                    <i class="bi bi-pencil-square me-1"></i> Edit Item Details
                </a>
            </div>
        </div>
    </div>
@endsection

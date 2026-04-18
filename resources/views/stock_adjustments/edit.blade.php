@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Stock Batch |
                {{ $stockBatch->stockItem->name }}</h1>
            <a href="{{ route('stock_items.show', $stockBatch->stock_item_id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Item
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card p-4">
            <form action="{{ route('stock_adjustments.update', $stockBatch) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">

                    <div class="col-md-12">
                        <label class="form-label">Stock Item</label>
                        <input type="text" class="form-control-plaintext fw-bold"
                            value="{{ $stockBatch->stockItem->name }} (Model: {{ $stockBatch->stockItem->model_number ?? 'N/A' }})"
                            readonly>
                        <input type="hidden" name="stock_item_id" value="{{ $stockBatch->stock_item_id }}">
                    </div>

                    <div class="col-md-2">
                        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity"
                            value="{{ old('quantity', $stockBatch->quantity) }}" required>
                        <small class="text-muted">Enter a positive number for available stock.</small>
                    </div>

                    <div class="col-md-3">
                        <label for="invoice_number" class="form-label">Invoice Number</label>
                        <input type="number" class="form-control" id="invoice_number" name="invoice_number"
                            value="{{ old('invoice_number', $stockBatch->invoice_number) }}" required>
                        <small class="text-muted">Enter the invoice number associated with this batch.</small>
                    </div>

                    <div class="col-md-4">
                        <label for="cost_price" class="form-label">Cost Price <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="cost_price" name="cost_price"
                            value="{{ old('cost_price', $stockBatch->cost_price) }}" step="0.01" required>
                    </div>
                    <div class="col-md-4">
                        <label for="selling_price" class="form-label">Selling Price <span
                                class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="selling_price" name="selling_price"
                            value="{{ old('selling_price', $stockBatch->selling_price) }}" step="0.01" required>
                    </div>
                    <div class="col-md-4">
                        <label for="installment_price" class="form-label">Installment Price</label>
                        <input type="number" class="form-control" id="installment_price" name="installment_price"
                            value="{{ old('installment_price', $stockBatch->installment_price) }}" step="0.01">
                    </div>

                    <div class="col-md-12">
                        <label for="reason" class="form-label">Reason (Optional)</label>
                        <input type="text" class="form-control" id="reason" name="reason"
                            value="{{ old('reason') }}" placeholder="e.g., Price correction, Quantity adjustment">
                    </div>

                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bi bi-trash me-1"></i> Delete Batch
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Update Batch
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2"><strong>Are you sure you want to delete this stock batch?</strong></p>
                    <p class="mb-2">Item: <strong>{{ $stockBatch->stockItem->name }}</strong></p>
                    <p class="mb-2">Quantity: <strong>{{ $stockBatch->quantity }}</strong></p>
                    <p class="mb-2">Selling Price: <strong>{{ number_format($stockBatch->selling_price, 2) }}</strong>
                    </p>
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        This action cannot be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <form action="{{ route('stock_adjustments.destroy', $stockBatch) }}" method="POST"
                        class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i> Yes, Delete Batch
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

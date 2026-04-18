@extends('layouts.app')
@section('title','Edit Stock Item - '.$stockItem->name)
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Stock Item Details</h1>
            <a href="{{ route('stock_items.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
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
            <form action="{{ route('stock_items.update', $stockItem) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Item Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $stockItem->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="model_number" class="form-label">Item Code</label>
                        <input type="text" class="form-control" id="model_number" name="model_number"
                            value="{{ old('model_number', $stockItem->model_number) }}" maxlength="255">
                    </div>

                    <div class="col-md-6">
                        <label for="supplier_id" class="form-label">Supplier</label>
                        <select class="form-select" id="supplier_id" name="supplier_id">
                            <option value="">-- Select Supplier --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id', $stockItem->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="color" class="form-label">Color</label>
                        <input type="text" class="form-control" id="color" name="color"
                            value="{{ old('color', $stockItem->color) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="warranty" class="form-label">Warranty</label>
                        <input type="text" class="form-control" id="warranty" name="warranty"
                            value="{{ old('warranty', $stockItem->warranty) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="other" class="form-label">Other</label>
                        <textarea class="form-control" id="other" name="other" rows="1">{{ old('other', $stockItem->other) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="vehicle_type" class="form-label">Vehicle Type</label>
                        <select class="form-select" id="vehicle_type" name="vehicle_type">
                            <option value="">-- Select Vehicle Type --</option>
                            <option value="Truck" {{ old('vehicle_type', $stockItem->vehicle_type) == 'Truck' ? 'selected' : '' }}>Truck</option>
                            <option value="Light Truck" {{ old('vehicle_type', $stockItem->vehicle_type) == 'Light Truck' ? 'selected' : '' }}>Light Truck</option>
                            <option value="PCR" {{ old('vehicle_type', $stockItem->vehicle_type) == 'PCR' ? 'selected' : '' }}>PCR</option>
                            <option value="Motorcycle" {{ old('vehicle_type', $stockItem->vehicle_type) == 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                            <option value="3 Wheeler" {{ old('vehicle_type', $stockItem->vehicle_type) == '3 Wheeler' ? 'selected' : '' }}>3 Wheeler</option>
                            <option value="Tube" {{ old('vehicle_type', $stockItem->vehicle_type) == 'Tube' ? 'selected' : '' }}>Tube</option>
                            <option value="Battery" {{ old('vehicle_type', $stockItem->vehicle_type) == 'Battery' ? 'selected' : '' }}>Battery</option>
                            <option value="other" {{ old('vehicle_type', $stockItem->vehicle_type) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="other" class="form-label">Is this Service Item?</label>
                        <div class="form-radio">
                            <input class="form-check-input" type="radio" name="service" id="service_yes" value="1"
                                {{ old('service', $stockItem->service) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label me-3" for="service_yes">Yes</label>
                            <input class="form-check-input" type="radio" name="service" id="service_no" value="0"
                                {{ old('service', $stockItem->service) == 0 ? 'checked' : '' }}>
                            <label class="form-check-label" for="service_no">No</label>
                        </div>
                    </div>

                    {{-- 
                        REMOVED:
                        - Cost Price
                        - Selling Price
                        - Installment Price
                        - Stock Quantity
                        These are now managed via Stock Batches, not by editing the item itself.
                    --}}
                    <div class="col-12">
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Pricing and stock quantity are managed via <strong>Stock Batches</strong> (e.g., through
                            Purchase Orders or Stock Adjustments) and cannot be edited here.
                        </div>
                    </div>

                </div>
                <div class="d-flex justify-content-between mt-4">
                    <!-- Delete Button on the Left -->
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bi bi-trash me-1"></i> Delete Item
                    </button>

                    <!-- Update Button on the Right -->
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Update Item Details
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
                    <p class="mb-2"><strong>Are you sure you want to delete this stock item?</strong></p>
                    <p class="mb-2">Item: <strong>{{ $stockItem->name }}</strong></p>
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        This will permanently delete the item and <strong>all its stock batches</strong>. This action cannot
                        be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <form action="{{ route('stock_items.destroy', $stockItem) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i> Yes, Delete Item
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

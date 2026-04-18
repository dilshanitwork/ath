@extends('layouts.app')
@section('title','Add Stock Item')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-plus-circle me-2"></i>Add New Stock Item</h1>
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
            <form action="{{ route('stock_items.store') }}" method="POST">
                @csrf

                {{-- SECTION 1: STOCK ITEM DETAILS --}}
                <h5 class="mb-3">Item Details</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Item Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label for="model_number" class="form-label">Item Code</label>
                        <input type="text" class="form-control" id="model_number" name="model_number"
                            value="{{ old('model_number') }}" maxlength="255">
                    </div>
                    <div class="col-md-6">
                        <label for="supplier_id" class="form-label">Supplier</label>
                        <select class="form-select" id="supplier_id" name="supplier_id">
                            <option value="">-- Select Supplier --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="color" class="form-label">Color</label>
                        <input type="text" class="form-control" id="color" name="color"
                            value="{{ old('color') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="warranty" class="form-label">Warranty</label>
                        <input type="text" class="form-control" id="warranty" name="warranty"
                            value="{{ old('warranty') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="other" class="form-label">Other</label>
                        <textarea class="form-control" id="other" name="other" rows="1">{{ old('other') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="vehicle_type" class="form-label">Vehicle Type</label>
                        <select class="form-select" id="vehicle_type" name="vehicle_type">
                            <option value="">-- Select Vehicle Type --</option>
                            <option value="Truck" {{ old('vehicle_type') == 'Truck' ? 'selected' : '' }}>Truck</option>
                            <option value="Light Truck" {{ old('vehicle_type') == 'Light Truck' ? 'selected' : '' }}>Light Truck</option>
                            <option value="PCR" {{ old('vehicle_type') == 'PCR' ? 'selected' : '' }}>PCR</option>
                            <option value="Motorcycle" {{ old('vehicle_type') == 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                            <option value="3 Wheeler" {{ old('vehicle_type') == '3 Wheeler' ? 'selected' : '' }}>3 Wheeler</option>
                            <option value="Tube" {{ old('vehicle_type') == 'Tube' ? 'selected' : '' }}>Tube</option>
                            <option value="Battery" {{ old('vehicle_type') == 'Battery ' ? 'selected' : '' }}>Battery </option>
                            <option value="other" {{ old('vehicle_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="other" class="form-label">Is this Service Item?</label>
                        <div class="form-radio">
                            <input class="form-check-input" type="radio" name="service" id="service_yes" value="1"
                                {{ old('service') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label me-3" for="service_yes">
                                Yes
                            </label>
                            <input class="form-check-input" type="radio" name="service" id="service_no" value="0"
                                {{ old('service', '0') == '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="service_no">
                                No
                            </label>

                        </div>
                    </div>

                    {{-- SECTION 2: INITIAL BATCH DETAILS --}}
                    <h5 class="mb-3">Initial Stock Batch</h5>
                    <p class="text-muted">Enter the quantity and pricing for the first batch of this item.</p>
                    <div class="row g-3">
                        <div class="col-md-3">
                            {{-- UPDATED: Renamed to initial_quantity --}}
                            <label for="initial_quantity" class="form-label">Initial Quantity <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="initial_quantity" name="initial_quantity"
                                value="{{ old('initial_quantity') }}" required min="0">
                        </div>
                        <div class="col-md-3">
                            <label for="cost_price" class="form-label">Cost Price <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="cost_price" name="cost_price"
                                value="{{ old('cost_price') }}" step="0.01" required min="0">
                        </div>
                        <div class="col-md-3">
                            <label for="selling_price" class="form-label">Selling Price <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="selling_price" name="selling_price"
                                value="{{ old('selling_price') }}" step="0.01" required min="0">
                        </div>
                        <div class="col-md-3">
                            <label for="installment_price" class="form-label">Installment Price</label>
                            <input type="number" class="form-control" id="installment_price" name="installment_price"
                                value="{{ old('installment_price') }}" step="0.01" min="0">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Save Item & Batch
                        </button>
                    </div>
            </form>
        </div>
    </div>
@endsection

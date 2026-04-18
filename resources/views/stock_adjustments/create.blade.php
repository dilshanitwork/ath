@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            {{-- Title now safely assumes $selectedStockItem exists --}}
            <h1 class="card-title mb-0"><i class="bi bi-plus-circle me-2"></i>Add Stock Batch | {{ $selectedStockItem->name }}
            </h1>
            <a href="{{ route('stock_items.show', $selectedStockItem->id) }}" class="btn btn-secondary">
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
            <form action="{{ route('stock_adjustments.store') }}" method="POST">
                @csrf
                <div class="row g-3">

                    {{-- This block replaces the dropdown --}}
                    <div class="col-md-12">
                        <label class="form-label">Stock Item</label>
                        {{-- Display the item name as read-only text --}}
                        <input type="text" class="form-control-plaintext fw-bold"
                            value="{{ $selectedStockItem->name }} (Model: {{ $selectedStockItem->model_number ?? 'N/A' }})"
                            readonly>
                        {{-- Hidden input to send the ID with the form --}}
                        <input type="hidden" name="stock_item_id" value="{{ $selectedStockItem->id }}">
                    </div>

                    {{-- Pre-fill pricing from the $latestBatch if it exists, otherwise from old() --}}
                    @php
                        $prefillCost = old('cost_price', $latestBatch->cost_price ?? '');
                        $prefillSelling = old('selling_price', $latestBatch->selling_price ?? '');
                        $prefillInstallment = old('installment_price', $latestBatch->installment_price ?? '');
                    @endphp

                    <div class="col-md-6">
                        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity"
                            value="{{ old('quantity', 1) }}" required>
                        <small class="text-muted">Enter a positive number to add new stock.
                            stock.</small>
                    </div>

                    <div class="col-md-6">
                        <label for="invoice_number" class="form-label">Invoice Number </label>
                        <input type="text" class="form-control" id="invoice_number" name="invoice_number"
                            value="{{ old('invoice_number') }}">
                        <small class="text-muted">Enter the invoice number associated with this stock adjustment.</small>
                    </div>

                    <div class="col-md-4">
                        <label for="cost_price" class="form-label">Cost Price <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="cost_price" name="cost_price"
                            value="{{ $prefillCost }}" step="0.01" required>
                    </div>
                    <div class="col-md-4">
                        <label for="selling_price" class="form-label">Selling Price <span
                                class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="selling_price" name="selling_price"
                            value="{{ $prefillSelling }}" step="0.01" required>
                    </div>
                    <div class="col-md-4">
                        <label for="installment_price" class="form-label">Installment Price</label>
                        <input type="number" class="form-control" id="installment_price" name="installment_price"
                            value="{{ $prefillInstallment }}" step="0.01">
                    </div>

                    <div class="col-md-12">
                        <label for="reason" class="form-label">Reason (Optional)</label>
                        <input type="text" class="form-control" id="reason" name="reason"
                            value="{{ old('reason') }}"
                            placeholder="e.g., Stock correction, Damaged goods, Found new stock">
                    </div>

                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Batch
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <h4 class="mb-3"><i class="fas fa-eye"></i> View Item</h4>
            <div>
                <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-edit me-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('items.destroy', $item) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"
                        onclick="return confirm('Are you sure you want to delete this item?');">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <div class="card">
            <div class="card-header card-header-custom">
                <i class="fas fa-info-circle"></i> Item Details
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Name</label>
                    </div>
                    <div class="col-md-9">
                        <p class="form-control-plaintext">{{ $item->name }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Price</label>
                    </div>
                    <div class="col-md-9">
                        <p class="form-control-plaintext">{{ $item->price }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Quantity</label>
                    </div>
                    <div class="col-md-9">
                        <p class="form-control-plaintext">{{ $item->quantity }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Purchase Date</label>
                    </div>
                    <div class="col-md-9">
                        <p class="form-control-plaintext">{{ $item->purchase_date }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Advanced Payment</label>
                    </div>
                    <div class="col-md-9">
                        <p class="form-control-plaintext">{{ $item->advanced_payment }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Customer</label>
                    </div>
                    <div class="col-md-9">
                        <p class="form-control-plaintext">{{ $item->customer->name }}</p>
                    </div>
                </div>
                <a href="{{ route('items.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
@endsection
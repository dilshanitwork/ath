@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h4 class="mb-3"><i class="fas fa-edit"></i> Edit Item</h4>
        <a href="{{ route('items.index') }}" class="btn btn-sm btn-back">
            <i class="fas fa-arrow-left"></i> Cancel & Back
        </a>
    </div>
    <!-- Include Alerts -->
    @include('components.alerts')

    <!-- Edit Item Form -->
    <div class="card mb-4">
        <div class="card-header card-header-custom">
            <i class="fas fa-user-edit"></i> Edit Item Details
        </div>
        <div class="card-body">
            <form action="{{ route('items.update', $item) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $item->name) }}" required>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" name="price" id="price" class="form-control" value="{{ old('price', $item->price) }}" required>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" value="{{ old('quantity', $item->quantity) }}" required>
                </div>
                <div class="mb-3">
                    <label for="purchase_date" class="form-label">Purchase Date</label>
                    <input type="date" name="purchase_date" id="purchase_date" class="form-control" value="{{ old('purchase_date', $item->purchase_date) }}">
                </div>
                <div class="mb-3">
                    <label for="advanced_payment" class="form-label">Advanced Payment</label>
                    <input type="number" name="advanced_payment" id="advanced_payment" class="form-control" value="{{ old('advanced_payment', $item->advanced_payment) }}" required>
                </div>
                <div class="mb-3">
                    <label for="customer_id" class="form-label">Customer</label>
                    <select name="customer_id" id="customer_id" class="form-control" required>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $customer->id == $item->customer_id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection
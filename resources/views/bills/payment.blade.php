@extends('layouts.app')

@section('title', 'Make Payment')

@section('content')
    <div class="container">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1 class="mb-0 card-title"><i class="bi bi-currency-dollar me-2"></i>Make a Payment for <span class="text-success">{{ $bill->customer->name }}</span></h1>
            <a href="{{ route('bills.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        @include('components.alerts')

        <div class="p-4 card">
            <form action="{{ route('bills.processPayment') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="bill_number" class="form-label"><i class="bi bi-file-earmark-text me-2"></i>Bill Number</label>
                        <p class="form-control">{{ $billNumber }}</p>
                        <input type="hidden" name="bill_number" id="bill_number" class="form-control"
                            value="{{ $billNumber ?? old('bill_number') }}" placeholder="Enter Bill Number" required>
                        @error('bill_number')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="payment" class="form-label">
                            <i class="bi bi-currency-dollar me-2"></i>Payment Amount 
                            <span class="text-muted">(Due amount : Rs.{{ number_format($bill->next_payment, 2) }})</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Rs.</span>
                            <input type="number" step="0.01" name="payment" id="payment" class="form-control"
                                placeholder="Enter Payment Amount" required>
                        </div>
                        @error('payment')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="type" class="form-label"><i class="bi bi-credit-card me-2"></i>Payment Type</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="cash" selected>Cash</option>
                            <option value="card">Card</option>
                            <option value="online">Online</option>
                        </select>
                        @error('type')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="date" class="form-label"><i class="bi bi-calendar-date me-2"></i>Payment Date</label>
                        <input type="hidden" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}"
                            required>
                        <p class="form-control">{{ date('Y-m-d') }}</p>
                        @error('date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Submit Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection


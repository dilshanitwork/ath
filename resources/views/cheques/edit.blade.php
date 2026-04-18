@extends('layouts.app')
@section('title', 'Edit Cheque-' . $cheque->id)
@section('content')
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Edit Cheque</h1>
            <a href="{{ route('cheques.index') }}" class="btn btn-outline-dark"><i class="bi bi-arrow-left"></i> Back to
                List</a>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('cheques.update', $cheque->id) }}" class="row g-3">
                    @csrf
                    @method('PUT')

                    {{-- <div class="col-12 col-md-6">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-select" required>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}"
                                    {{ old('customer_id', $cheque->customer_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} (ID: {{ $c->id }})
                                </option>
                            @endforeach
                        </select>
                    </div> --}}
                    <div class="col-12 col-md-6">
                        <label for="customer" class="form-label">
                            Customer
                        </label>
                        <input type="text" class="form-control" id="customer" name="customer"
                            placeholder="Type to search..." value="{{ old('customer', optional($cheque->customer)->name) }}"
                            list="customerList" autocomplete="off">

                        <input type="hidden" name="customer_id" id="customer_id"
                            value="{{ old('customer_id', $cheque->customer_id) }}">
                        {{-- Data list --}}
                        <datalist id="customerList">
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->name }}" data-id="{{ $customer->id }}"></option>
                            @endforeach
                        </datalist>

                        @error('customer_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Cheque Number</label>
                        <input type="text" name="cheque_number" class="form-control"
                            value="{{ old('cheque_number', $cheque->cheque_number) }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Bank</label>
                        <select name="bank_value_id" class="form-select" required>
                            <option value="">Select bank</option>

                            @foreach ($banks as $bank)
                                <option value="{{ $bank->id }}"
                                    {{ old('bank_value_id', $cheque->bank_value_id) == $bank->id ? 'selected' : '' }}>
                                    {{ $bank->value }}
                                </option>
                            @endforeach
                        </select>

                        @error('bank_value_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control"
                            value="{{ old('amount', $cheque->amount) }}" required>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label">Cheque Date</label>
                        <input type="date" name="cheque_date" class="form-control"
                            value="{{ old('cheque_date', $cheque->cheque_date?->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ old('status', $cheque->status) == 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>
                            <option value="settled" {{ old('status', $cheque->status) == 'settled' ? 'selected' : '' }}>
                                Settled
                            </option>
                            <option value="cancelled"
                                {{ old('status', $cheque->status) == 'cancelled' ? 'selected' : '' }}>
                                Cancelled</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Note</label>
                        <textarea name="note" class="form-control" rows="3">{{ old('note', $cheque->note) }}</textarea>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary"><i class="bi bi-save"></i> Update</button>
                        <a href="{{ route('cheques.show', $cheque->id) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-top border-info border-4 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="text-primary mb-0"><i class="bi bi-receipt-cutoff me-2"></i>Customer's Bills</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-light d-flex justify-content-between align-items-center mb-3 border">
                    <div class="fw-bold text-success">
                        <i class="bi bi-cash-stack me-1"></i> Total Paid: {{ number_format($totalPaid, 2) }}
                    </div>
                    <div class="fw-bold text-danger">
                        <i class="bi bi-exclamation-circle me-1"></i> Total Balance: {{ number_format($totalBalance, 2) }}
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table-hover table-striped table-bordered mb-0 table align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Bill #</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Bill Total</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Balance</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($directBills as $bill)
                                <tr>
                                    <td class="fw-bold">{{ $bill->bill_number }}</td>
                                    <td>{{ $bill->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <small class="text-muted">{{ $bill->items->count() }} Items</small>
                                    </td>
                                    <td class="text-center">
                                        @if ($bill->status == 'closed')
                                            <span class="badge bg-success">Closed</span>
                                        @else
                                            <span class="badge bg-danger">Open</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-end">{{ number_format($bill->final_amount, 2) }}</td>
                                    <td class="text-success text-end">{{ number_format($bill->paid, 2) }}</td>
                                    <td class="text-danger fw-bold text-end">{{ number_format($bill->balance, 2) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('direct_bills.show', $bill->id) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-muted py-3 text-center">
                                        No direct bills found for this customer.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const input    = document.getElementById('customer');
    const hiddenId = document.getElementById('customer_id');
    const options  = document.querySelectorAll('#customerList option');

    function syncCustomerId() {
        const name = input.value.trim();
        let matched = '';

        options.forEach(opt => {
            if (opt.value === name) matched = opt.dataset.id;
        });

        hiddenId.value = matched ?? '';
    }

    input.addEventListener('change', syncCustomerId);
    input.addEventListener('blur', syncCustomerId);
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'Direct Bill #' . $directBill->bill_number)

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-receipt-cutoff me-2"></i>Bill #{{ $directBill->bill_number }}</h1>
            <div>
                {{-- Only show Add Payment if there is a balance --}}
                @if ($directBill->balance > 0)
                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal"
                        data-bs-target="#addPaymentModal">
                        <i class="bi bi-cash-coin me-1"></i> Add Payment
                    </button>
                @endif

                <a href="{{ route('direct_bills.edit', $directBill) }}" class="btn btn-warning me-2" title="Edit">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
                <a href="{{ route('direct_bills.print', $directBill) }}" class="btn btn-info me-2" title="Print"
                    target="_blank">
                    <i class="bi bi-printer"></i> Print
                </a>
                <button type="button" class="btn btn-secondary width-100px" onclick="history.back()">Back to List</button>
            </div>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <div class="card mb-4 p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <h4>Bill Information</h4>
                    <hr>
                    <dl class="row">
                        <dt class="col-sm-4">Bill Number</dt>
                        <dd class="col-sm-8">{{ $directBill->bill_number }}</dd>

                        <dt class="col-sm-4">Customer</dt>
                        <dd class="col-sm-8">{{ $directBill->customer_name }}</dd>

                        <dt class="col-sm-4">Contact</dt>
                        <dd class="col-sm-8">{{ $directBill->contact_number ?? '-' }}</dd>

                        <dt class="col-sm-4">Vehicle</dt>
                        <dd class="col-sm-8">{{ $directBill->vehicle ?? '-' }}</dd>

                        <dt class="col-sm-4">Date</dt>
                        <dd class="col-sm-8">{{ $directBill->created_at->format('Y-m-d H:i') }}</dd>

                        <dt class="col-sm-4">Type</dt>
                        <dd class="col-sm-8">
                            @if ($directBill->type == 'cash')
                                <span class="badge bg-success-subtle text-success border-success-subtle border">Cash</span>
                            @else
                                <span
                                    class="badge bg-warning-subtle text-warning-emphasis border-warning-subtle border">Credit</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
                            @if ($directBill->status == 'closed')
                                <span class="badge bg-success">Closed</span>
                            @else
                                <span class="badge bg-danger">Open</span>
                            @endif
                        </dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <h4>Financial Summary</h4>
                    <hr>
                    <dl class="row">
                        <dt class="col-sm-4">Subtotal</dt>
                        <dd class="col-sm-8">{{ number_format($directBill->bill_total, 2) }}</dd>

                        <dt class="col-sm-4">Discount</dt>
                        <dd class="col-sm-8">{{ number_format($directBill->discount, 2) }}</dd>

                        <dt class="col-sm-4">Final Amount</dt>
                        <dd class="col-sm-8 fw-bold" style="font-size: 1.1em;">
                            {{ number_format($directBill->final_amount, 2) }}</dd>

                        <dt class="col-sm-4 text-success">Paid</dt>
                        <dd class="col-sm-8 text-success fw-bold">{{ number_format($directBill->paid, 2) }}</dd>

                        <dt class="col-sm-4 text-danger">Balance</dt>
                        <dd class="col-sm-8 text-danger fw-bold">{{ number_format($directBill->balance, 2) }}</dd>

                        <dt class="col-sm-4 mt-2">Billed By</dt>
                        <dd class="col-sm-8 mt-2">{{ $directBill->user->name ?? 'Unknown' }}</dd>
                    </dl>
                </div>
            </div>

            @if ($directBill->note)
                <div class="mt-3">
                    <h4>Note</h4>
                    <hr>
                    <p>{{ $directBill->note }}</p>
                </div>
            @endif
        </div>

        <h4>Bill Items</h4>
        <hr>
        <div class="card mb-4">
            <div class="table-responsive">
                <table class="table-hover table-striped mb-0 table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Item Name</th>
                            <th>Code</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($directBill->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->stockItem->model_number ?? '-' }}</td>
                                <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->item_discount, 2) }}</td>
                                <td class="text-end">{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted text-center">No items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment History Section -->
        @if ($directBill->payments->isNotEmpty())
            <h4 class="mt-4">Payment History</h4>
            <hr>
            <div class="card">
                <div class="table-responsive">
                    <table class="table-hover mb-0 table">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Note</th>
                                <th>Recorded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($directBill->payments as $payment)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($payment->paid_date)->format('Y-m-d') }}</td>
                                    <td class="fw-bold text-success">{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                    <td>{{ $payment->note ?? '-' }}</td>
                                    <td>{{ $payment->user->name ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="d-flex justify-content-end mt-4">
            <form action="{{ route('direct_bills.destroy', $directBill) }}" method="POST" class="d-inline"
                onsubmit="return confirm('Are you sure you want to delete this bill?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-trash me-1"></i> Delete Bill
                </button>
            </form>
        </div>
    </div>

    <!-- Add Payment Modal -->
    @if ($directBill->balance > 0)
        <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('direct_bills.payments.store', $directBill) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addPaymentModalLabel">Record Bill Payment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label text-muted">Current Balance</label>
                                <div class="input-group">
                                    <span class="input-group-text">LKR</span>
                                    <input type="text" class="form-control bg-light"
                                        value="{{ number_format($directBill->balance, 2) }}" readonly disabled>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="payment_amount" class="form-label">Payment Amount <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">LKR</span>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                        id="payment_amount" name="amount" step="0.01" min="0.01"
                                        max="{{ $directBill->balance }}" value="{{ old('amount') }}" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Amount cannot exceed the current balance.</div>
                            </div>

                            <div class="mb-3">
                                <label for="paid_date" class="form-label">Payment Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('paid_date') is-invalid @enderror"
                                    id="paid_date" name="paid_date" value="{{ old('paid_date', date('Y-m-d')) }}"
                                    required>
                                @error('paid_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method <span
                                        class="text-danger">*</span></label>
                                <select class="@error('payment_method') is-invalid @enderror form-select"
                                    id="payment_method" name="payment_method" required>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash
                                    </option>
                                    <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card
                                    </option>
                                    <option value="bank_transfer"
                                        {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer
                                    </option>
                                    <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>
                                        Cheque</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="payment_note" class="form-label">Note (Optional)</label>
                                <textarea class="form-control @error('note') is-invalid @enderror" id="payment_note" name="note" rows="2">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Save
                                Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Re-open modal if there are validation errors --}}
        @if ($errors->any() && old('amount'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var myModal = new bootstrap.Modal(document.getElementById('addPaymentModal'));
                    myModal.show();
                });
            </script>
        @endif
    @endif
@endsection

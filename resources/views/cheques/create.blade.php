@extends('layouts.app')
@section('title', 'Add Cheque')
@section('content')
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0"><i class="bi bi-plus-circle me-2"></i> Add Cheque</h1>
            <a href="{{ route('cheques.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to List</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('cheques.store') }}" class="row g-3">
                    @csrf

                    <div class="col-12 col-md-6">
                        <label for="customer" class="form-label">
                            Customer
                        </label>
                        <input type="text" class="form-control" id="customer" name="customer"
                            placeholder="Type to search..."
                            value="{{ old('customer', optional($customers->firstWhere('id', old('customer_id')))->name) }}"
                            list="customerList" autocomplete="off">

                        <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id') }}">

                        {{-- The Data List --}}
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
                        <input type="text" name="cheque_number" class="form-control" value="{{ old('cheque_number') }}"
                            required>
                        @error('cheque_number')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Bank</label>
                        <select name="bank_value_id" class="form-select" required>
                            <option value="">Select bank</option>

                            @foreach ($banks as $bank)
                                <option value="{{ $bank->id }}"
                                    {{ old('bank_value_id') == $bank->id ? 'selected' : '' }}>
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
                            value="{{ old('amount') }}" required>
                        @error('amount')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label">Cheque Date</label>
                        <input type="date" name="cheque_date" class="form-control" value="{{ old('cheque_date') }}"
                            required>
                        @error('cheque_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                        @error('status')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Note</label>
                        <textarea name="note" class="form-control" rows="3">{{ old('note') }}</textarea>
                        @error('note')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary">
                            <i class="bi bi-save"></i> Save
                        </button>
                        <a href="{{ route('cheques.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
     {{-- Map typed name -> customer_id --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input      = document.getElementById('customer');
    const hiddenId   = document.getElementById('customer_id');
    const dataList   = document.getElementById('customerList');
    const options    = dataList ? dataList.getElementsByTagName('option') : [];

    function syncCustomerId() {
        const value = input.value.trim();
        let foundId = '';

        for (let i = 0; i < options.length; i++) {
            if (options[i].value === value) {
                foundId = options[i].dataset.id || '';
                break;
            }
        }

        hiddenId.value = foundId;
    }

    input.addEventListener('change', syncCustomerId);
    input.addEventListener('blur', syncCustomerId);
});
</script>
@endpush
@endsection

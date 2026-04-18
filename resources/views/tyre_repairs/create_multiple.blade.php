@extends('layouts.app')

@section('title', 'Add Multiple Tyre Repairs')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-collection me-2"></i>Add Multiple Repairs</h1>
            <a href="{{ route('tyre_repairs.index') }}" class="btn btn-secondary">
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

        <form action="{{ route('tyre_repairs.store_multiple') }}" method="POST" id="bulk-form">
            @csrf

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <strong>1. Select Customer</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="customer_input" class="form-label">Customer Name / Mobile <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" name="customer_name" id="customer_input"
                                    list="customerOptions" placeholder="Type to search..."
                                    value="{{ old('customer_name') }}" required autocomplete="off">
                                <datalist id="customerOptions">
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->name }}">{{ $customer->mobile }}</option>
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="form-text">All repairs below will be assigned to this customer.</div>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="bg-light d-inline-block rounded border p-3">
                                <small class="text-muted d-block">Starting Item # (Approx)</small>
                                <span class="fs-4 fw-bold text-primary">{{ $nextItemNumber }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <strong>2. Repair Items</strong>
                    <button type="button" class="btn btn-sm btn-success" id="add-row-btn">
                        <i class="bi bi-plus-lg"></i> Add Row
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-bordered mb-0 table" id="repairs-table">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 15%">Date Rcvd</th>
                                    <th style="width: 15%">Tyre Size</th>
                                    <th style="width: 15%">Make</th>
                                    <th style="width: 15%">Tyre No</th>
                                    <th style="width: 12%">Job No</th>
                                    <th style="width: 12%">Amount</th>
                                    <th>Note</th>
                                    <th style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody id="repairs-body">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-save me-1"></i> Save All Jobs
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tableBody = document.getElementById('repairs-body');
                const addBtn = document.getElementById('add-row-btn');
                let rowIndex = 0;

                // Function to create a new row
                function addRow() {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td>
                    <input type="date" class="form-control form-control-sm" name="repairs[${rowIndex}][received_date]" value="{{ date('Y-m-d') }}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="repairs[${rowIndex}][tyre_size]" placeholder="e.g. 100/90-17" required>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="repairs[${rowIndex}][tyre_make]" placeholder="e.g. CEAT">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="repairs[${rowIndex}][tyre_number]" placeholder="Serial No">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="repairs[${rowIndex}][job_number]" placeholder="Job No">
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rs</span>
                        <input type="number" class="form-control" name="repairs[${rowIndex}][amount]" step="0.01" min="0">
                    </div>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="repairs[${rowIndex}][note]" placeholder="Description/Issues">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
                    tableBody.appendChild(row);
                    rowIndex++;
                }

                // Add initial row
                addRow();

                // Event Listeners
                addBtn.addEventListener('click', addRow);

                tableBody.addEventListener('click', function(e) {
                    if (e.target.closest('.remove-row')) {
                        const row = e.target.closest('tr');
                        // Prevent removing the last remaining row
                        if (tableBody.children.length > 1) {
                            row.remove();
                        } else {
                            alert("You must have at least one repair item.");
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection

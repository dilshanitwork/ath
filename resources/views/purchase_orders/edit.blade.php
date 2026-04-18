@extends('layouts.app')
@section('title', 'Edit Purchase Order - ' .  $purchaseOrder->id)
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Purchase Order</h1>
            <a href="{{ route('purchase_orders.index') }}" class="btn btn-secondary">
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
            <form action="{{ route('purchase_orders.update', $purchaseOrder) }}" method="POST" id="po-form">
                @csrf
                @method('PUT')

                {{-- 1. PO Details Section --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="po_number" class="form-label">Invoice Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="po_number" name="po_number"
                            value="{{ old('po_number', $purchaseOrder->po_number) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" id="supplier_id" class="form-select" required>
                            <option value="">-- Select Supplier --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="order_date" class="form-label">Order Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="order_date" name="order_date"
                            value="{{ old('order_date', $purchaseOrder->order_date) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="pending"
                                {{ old('status', $purchaseOrder->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="received"
                                {{ old('status', $purchaseOrder->status) == 'received' ? 'selected' : '' }}>Received
                            </option>
                        </select>
                        <div class="form-text text-muted" style="font-size: 0.8rem">
                            <strong>Note:</strong> Changing status to/from 'Received' will update stock batches accordingly.
                        </div>
                    </div>

                    {{-- ✅ Optional Notes Field --}}
                    <div class="col-12">
                        <label for="notes" class="form-label">
                            Notes
                            <span class="text-muted fw-normal" style="font-size: 0.85rem;">(Optional)</span>
                        </label>
                        <textarea
                            name="notes"
                            id="notes"
                            class="form-control"
                            rows="3"
                            maxlength="1000"
                            placeholder="Add any additional notes or remarks for this purchase order..."
                        >{{ old('notes', $purchaseOrder->notes) }}</textarea>
                        <div class="form-text text-muted d-flex justify-content-end">
                            <span id="notes-char-count">0</span> / 1000
                        </div>
                    </div>
                </div>

                {{-- 2. Items Section --}}
                <h4>Order Items</h4>
                <hr>
                <div class="table-responsive">
                    <table class="table-bordered table align-middle" id="items-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40%;">Item Name (Search)</th>
                                <th style="width: 15%;">Quantity</th>
                                <th style="width: 20%;">Unit Cost</th>
                                <th style="width: 20%;">Total</th>
                                <th style="width: 5%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="items-container">
                            {{-- Rows will be added here via JS --}}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="fw-bold text-end">Grand Total:</td>
                                <td colspan="2" class="fw-bold fs-5" id="grand-total">0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <button type="button" class="btn btn-outline-primary mb-4" id="add-item-btn">
                    <i class="bi bi-plus-lg me-1"></i> Add Item
                </button>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save me-1"></i> Update Purchase Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JavaScript for Dynamic Rows & Search --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let rowIdx = 0;
                const container = document.getElementById('items-container');
                const addItemBtn = document.getElementById('add-item-btn');
                const grandTotalEl = document.getElementById('grand-total');

                // Pass existing items from controller to JS
                const existingItems = @json($purchaseOrder->items);

                // --- ✅ Notes Character Counter ---
                const notesTextarea = document.getElementById('notes');
                const notesCharCount = document.getElementById('notes-char-count');

                // Initialize count on load (handles existing & old() repopulated values)
                notesCharCount.textContent = notesTextarea.value.length;

                notesTextarea.addEventListener('input', function () {
                    notesCharCount.textContent = this.value.length;
                });

                // --- 1. Add Row Function ---
                function addItemRow(data = null) {
                    // Determine values for inputs
                    const itemName = data && data.stock_item ?
                        `${data.stock_item.name} (${data.stock_item.model_number ?? ''})` : '';
                    const itemId = data ? data.stock_item_id : '';
                    const qty = data ? data.quantity : 1;
                    const cost = data ? data.unit_cost : '';

                    const tr = document.createElement('tr');
                    tr.classList.add('item-row');
                    tr.innerHTML = `
                    <td class="position-relative">
                        <input type="text" class="form-control item-search" placeholder="Type to search item..." autocomplete="off" value="${itemName}">
                        <input type="hidden" name="items[${rowIdx}][stock_item_id]" class="stock-item-id" value="${itemId}">
                        <div class="list-group position-absolute w-100 suggestions-container shadow d-none" style="z-index: 1000; max-height: 200px; overflow-y: auto;"></div>
                    </td>
                    <td>
                        <input type="number" name="items[${rowIdx}][quantity]" class="form-control item-qty" min="1" value="${qty}" required>
                    </td>
                    <td>
                        <input type="number" name="items[${rowIdx}][unit_cost]" class="form-control item-cost" min="0" step="0.01" value="${cost}" required>
                    </td>
                    <td>
                        <input type="text" class="form-control item-total" readonly>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                    </td>
                `;
                    container.appendChild(tr);

                    // Calculate initial total for this row
                    calculateRowTotal(tr);

                    rowIdx++;
                }

                // --- 2. Initialize Rows ---
                if (existingItems && existingItems.length > 0) {
                    existingItems.forEach(item => {
                        addItemRow(item);
                    });
                } else {
                    addItemRow();
                }

                // --- 3. Event Listeners ---
                addItemBtn.addEventListener('click', () => addItemRow());

                container.addEventListener('click', function(e) {
                    // Remove Row
                    if (e.target.closest('.remove-row')) {
                        const rows = container.querySelectorAll('tr');
                        if (rows.length > 1) {
                            e.target.closest('tr').remove();
                            calculateGrandTotal();
                        } else {
                            alert("An order must have at least one item.");
                        }
                    }

                    // Select Item from Suggestions
                    if (e.target.classList.contains('suggestion-item')) {
                        const row = e.target.closest('tr');
                        const item = JSON.parse(e.target.dataset.item);

                        row.querySelector('.item-search').value = item.name;
                        row.querySelector('.stock-item-id').value = item.id;

                        // --- Auto-fill Unit Cost from last batch ---
                        if (item.last_cost_price) {
                            row.querySelector('.item-cost').value = item.last_cost_price;
                        } else {
                            row.querySelector('.item-cost').value = 0;
                        }

                        row.querySelector('.suggestions-container').classList.add('d-none');
                        calculateRowTotal(row);
                    }
                });

                // Input Changes (Calculation & Search)
                container.addEventListener('input', function(e) {
                    const target = e.target;
                    const row = target.closest('tr');

                    // Calculate Totals
                    if (target.classList.contains('item-qty') || target.classList.contains('item-cost')) {
                        calculateRowTotal(row);
                    }

                    // Search Logic
                    if (target.classList.contains('item-search')) {
                        const query = target.value;
                        const suggestionsBox = row.querySelector('.suggestions-container');

                        if (query.length < 2) {
                            suggestionsBox.classList.add('d-none');
                            return;
                        }

                        // AJAX Search
                        fetch(`{{ route('purchase_orders.search_items') }}?query=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                suggestionsBox.innerHTML = '';
                                if (data.length > 0) {
                                    data.forEach(item => {
                                        const btn = document.createElement('button');
                                        btn.type = 'button';
                                        btn.classList.add('list-group-item',
                                            'list-group-item-action', 'suggestion-item');
                                        btn.textContent =
                                            `${item.name} (${item.model_number ?? 'No Model'})`;
                                        btn.dataset.item = JSON.stringify(item);
                                        suggestionsBox.appendChild(btn);
                                    });
                                    suggestionsBox.classList.remove('d-none');
                                } else {
                                    suggestionsBox.classList.add('d-none');
                                }
                            })
                            .catch(err => console.error(err));
                    }
                });

                // Close suggestions when clicking outside
                document.addEventListener('click', function(e) {
                    if (!e.target.classList.contains('item-search')) {
                        document.querySelectorAll('.suggestions-container').forEach(el => el.classList.add(
                            'd-none'));
                    }
                });

                // --- 4. Calculations ---
                function calculateRowTotal(row) {
                    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                    const cost = parseFloat(row.querySelector('.item-cost').value) || 0;
                    const total = qty * cost;
                    row.querySelector('.item-total').value = total.toFixed(2);
                    calculateGrandTotal();
                }

                function calculateGrandTotal() {
                    let total = 0;
                    document.querySelectorAll('.item-total').forEach(input => {
                        total += parseFloat(input.value) || 0;
                    });
                    grandTotalEl.textContent = total.toFixed(2);
                }
            });
        </script>
    @endpush
@endsection
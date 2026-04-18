@extends('layouts.app')

    @push('styles')
        <style>
            /* Price Mode Button Group Styling */
            .btn-group label.btn {
                padding: 0.3rem 1rem;
                transition: all 0.3s ease;
                border-width: 2px;
            }
            
            .btn-group label.btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }
            
            .btn-check:checked + .btn-outline-primary {
                background-color: #0d6efd;
                border-color: #0d6efd;
                color: white;
                box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
            }
            
            .btn-check:checked + .btn-outline-success {
                background-color: #198754;
                border-color: #198754;
                color: white;
                box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
            }
        </style>
    @endpush

@section('title', 'Edit Direct Bill')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Direct Bill</h1>
            <a href="{{ route('direct_bills.index') }}" class="btn btn-secondary">
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
            <form id="bill-form" action="{{ route('direct_bills.update', $directBill) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-4 mb-3">
                    <div class="col-md-3">
                        <label for="bill_number" class="form-label">Bill Number</label>
                        <input type="text" class="form-control" id="bill_number" name="bill_number"
                            value="{{ old('bill_number', $directBill->bill_number) }}" readonly required>
                    </div>
                    <div class="col-md-3">
                        <label for="customer_name" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name"
                            list="customerList" placeholder="Select or Type Customer"
                            value="{{ old('customer_name', $directBill->customer_name) }}" required autocomplete="off">

                        <datalist id="customerList">
                            @foreach ($customers ?? [] as $customer)
                                <option value="{{ $customer->name }}">{{ $customer->mobile }}</option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="col-md-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number"
                            value="{{ old('contact_number', $directBill->contact_number) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="vehicle" class="form-label">Vehicle Number</label>
                        <input type="text" class="form-control" id="vehicle" name="vehicle"
                            value="{{ old('vehicle', $directBill->vehicle) }}">
                    </div>
                </div>

                <!-- Price Mode Selection -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Bill Items</h4>
                    <div class="d-flex flex-column align-items-end">
                        <div class="btn-group" role="group" aria-label="Price Mode">
                            <input type="radio" class="btn-check" name="price_mode" id="priceModeLatest" value="latest" checked autocomplete="off">
                            <label class="btn btn-outline-primary" for="priceModeLatest">
                                <i class="bi bi-clock-history me-1"></i> Latest Price
                               <!-- <br><small style="font-size: 0.7rem;">Newest Batch</small>-->
                            </label>
                          
                            <input type="radio" class="btn-check" name="price_mode" id="priceModeFifo" value="fifo" autocomplete="off">
                            <label class="btn btn-outline-success" for="priceModeFifo">
                               <i class="bi bi-arrow-right-circle me-1"></i> FIFO Price
                                <!--<br><small style="font-size: 0.7rem;">Oldest Batch</small>-->
                            </label>
                        </div>
                        <div class="form-text mt-1 text-end" id="priceModeDescription" style="font-size: 0.8rem;">
                            Using the <strong>newest batch</strong> selling price for all items
                        </div>
                    </div>
                </div>
                <hr>
                <div id="items-container">

                    @php
                        $formOldItems = old('items', $directBill->items->toArray());
                        $maxIndex = -1;
                        foreach ($formOldItems as $k => $v) {
                            $maxIndex = max($maxIndex, (int) $k);
                        }
                        $nextJsIndex = $maxIndex + 1;
                    @endphp

                    @foreach ($formOldItems as $index => $item)
                        @php
                            $item = (array) $item;
                            $currentDiscountAmount = $item['item_discount'] ?? 0;
                            $currentDiscountRate = $item['discount_rate'] ?? $currentDiscountAmount;
                            $currentDiscountType = $item['discount_type'] ?? 'amount';
                            $isStockItem = !empty($item['stock_item_id']);
                        @endphp

                        <div class="row g-3 align-items-end item-row mb-3">
                            <div class="col-md-3">
                                @if ($isStockItem)
                                    <label class="form-label">Item Name (Stock)</label>
                                    <input type="text" class="form-control item-name-input"
                                        name="items[{{ $index }}][item_name]" list="stockItemsList"
                                        placeholder="Select or type item code" value="{{ $item['item_name'] }}" required>

                                    <input type="hidden" class="stock-item-id"
                                        name="items[{{ $index }}][stock_item_id]"
                                        value="{{ $item['stock_item_id'] }}">

                                    <div class="form-text stock-info text-primary fw-bold"></div>
                                    <div class="alert alert-warning batch-warning mb-0 mt-1 px-2 py-1"
                                        style="display: none; font-size: 0.875rem;"></div>
                                @else
                                    <label class="form-label">Item Name (Repair/Job)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-wrench"></i></span>
                                        <input type="text" class="form-control tyre-repair-input"
                                            name="items[{{ $index }}][item_name]" list="tyreRepairJobsList"
                                            placeholder="Search Job No..." value="{{ $item['item_name'] }}" required>
                                    </div>
                                    <input type="hidden" class="stock-item-id"
                                        name="items[{{ $index }}][stock_item_id]" value="">
                                @endif

                                <input type="hidden" class="bill-item-id" name="items[{{ $index }}][id]"
                                    value="{{ $item['id'] ?? '' }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Unit Price</label>
                                <input type="number" class="form-control unit-price"
                                    name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] }}"
                                    step="0.01" min="0" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Quantity</label>
                                <input type="number" class="form-control item-quantity"
                                    name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}"
                                    min="1" required>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Discount</label>
                                <div class="input-group">
                                    <input type="number" class="form-control discount-rate"
                                        name="items[{{ $index }}][discount_rate]"
                                        value="{{ $currentDiscountRate }}" step="0.01" min="0"
                                        placeholder="0">
                                    <select class="discount-type form-select px-1"
                                        name="items[{{ $index }}][discount_type]" style="max-width: 60px;">
                                        <option value="amount" {{ $currentDiscountType === 'amount' ? 'selected' : '' }}>
                                            Rs</option>
                                        <option value="percentage"
                                            {{ $currentDiscountType === 'percentage' ? 'selected' : '' }}>%</option>
                                    </select>
                                </div>
                                <input type="hidden" class="item-discount"
                                    name="items[{{ $index }}][item_discount]"
                                    value="{{ $currentDiscountAmount }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Total</label>
                                <input type="text" class="form-control item-total-price"
                                    value="{{ $item['total_price'] ?? '0.00' }}">
                            </div>

                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger remove-item"><i
                                        class="bi bi-x-circle"></i></button>
                            </div>
                        </div>
                    @endforeach

                </div>

                <div class="d-flex mt-3 gap-2">
                    <button type="button" class="btn btn-secondary" id="add-item">
                        <i class="bi bi-plus-circle me-1"></i> Add Stock Item
                    </button>

                    <button type="button" class="btn btn-info text-white" id="add-tyre-repair-item">
                        <i class="bi bi-wrench me-1"></i> Add Tyre Repair Job
                    </button>
                </div>

                <hr class="my-4">

                <div class="row g-3 bg-light rounded border p-3">
                    <div class="col-12 mb-2">
                        <h6 class="fw-bold text-primary"><i class="bi bi-calculator me-1"></i> Bill Summary (Manual Entry
                            Mode)</h6>
                    </div>

                    <div class="col-md-3">
                        <label for="bill_total" class="form-label small text-muted">Bill Subtotal</label>
                        <input type="number" class="form-control" id="bill_total" name="bill_total"
                            value="{{ old('bill_total', $directBill->bill_total) }}" step="0.01">
                    </div>

                    <div class="col-md-3">
                        <label for="total_item_discount" class="form-label small text-muted">Sum of Item Discounts</label>
                        @php
                            $itemDiscountSum = $directBill->items->sum(function ($item) {
                                return $item->item_discount * $item->quantity;
                            });
                        @endphp
                        <input type="number" class="form-control" id="total_item_discount"
                            value="{{ $itemDiscountSum }}" step="0.01">
                    </div>

                    <div class="col-md-3">
                        <label for="discount_input" class="form-label small text-muted">Additional Discount</label>
                        @php
                            // Calculate existing additional discount from DB logic
                            $additionalDiscount = max(0, $directBill->discount - $itemDiscountSum);
                        @endphp
                        <input type="number" class="form-control" id="discount_input" name="discount"
                            value="{{ old('discount', $additionalDiscount) }}" step="0.01" placeholder="0.00">
                    </div>

                    <div class="col-md-3">
                        <label for="type" class="form-label small text-muted">Payment Type</label>
                        <select class="form-select" id="type" name="type">
                            <option value="cash" {{ old('type', $directBill->type) == 'cash' ? 'selected' : '' }}>Cash
                                (Full Payment)</option>
                            <option value="credit" {{ old('type', $directBill->type) == 'credit' ? 'selected' : '' }}>
                                Credit (Partial/Later)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="final_amount" class="form-label fw-bold">Final Amount</label>
                        <input type="number" class="form-control fw-bold border-primary" id="final_amount"
                            name="final_amount" value="{{ old('final_amount', $directBill->final_amount) }}"
                            step="0.01" style="font-size: 1.1rem;">
                    </div>

                    <div class="col-md-4">
                        <label for="paid" class="form-label fw-bold text-success">Paid Amount</label>
                        <input type="number" class="form-control fw-bold border-success" id="paid" name="paid"
                            value="{{ old('paid', $directBill->paid) }}" step="0.01" style="font-size: 1.1rem;">
                    </div>

                    <div class="col-md-4">
                        <label for="balance" class="form-label fw-bold text-danger">Balance</label>
                        <input type="number" class="form-control fw-bold border-danger" id="balance" name="balance"
                            value="{{ old('balance', $directBill->balance) }}" step="0.01"
                            style="font-size: 1.1rem;">
                    </div>

                    <div class="col-md-12 mt-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea class="form-control" id="note" name="note" rows="3">{{ old('note', $directBill->note) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save me-1"></i> Update Bill
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Price Mode Variables
                let priceMode = 'latest';
                
                // Radio button event listeners
                const priceModeLatest = document.getElementById('priceModeLatest');
                const priceModeFifo = document.getElementById('priceModeFifo');
                const priceModeDescription = document.getElementById('priceModeDescription');

                function updatePriceMode(mode) {
                    priceMode = mode;
                    console.log('🔄 Price mode changed to:', priceMode);
                    
                    // Update description text
                    if (mode === 'latest') {
                        priceModeDescription.innerHTML = 'Using the <strong>newest batch</strong> selling price for all items';
                    } else {
                        priceModeDescription.innerHTML = 'Using the <strong>oldest batch</strong> selling price (FIFO method)';
                    }
                    
                    recalculateAllUnitPrices(mode);
                }

                priceModeLatest.addEventListener('change', function() {
                    if (this.checked) updatePriceMode('latest');
                });
                
                // Explicit click listeners
                priceModeLatest.addEventListener('click', function() {
                    updatePriceMode('latest');
                });

                priceModeFifo.addEventListener('change', function() {
                    if (this.checked) updatePriceMode('fifo');
                });
                
                // Explicit click listeners
                priceModeFifo.addEventListener('click', function() {
                    updatePriceMode('fifo');
                });

                let itemIndex = {{ $nextJsIndex }};

                const itemsContainer = document.getElementById('items-container');
                const billForm = document.getElementById('bill-form');
                const stockItems = @json($stockItems ?? []);
                const customers = @json($customers ?? []);

                const customerMap = new Map(customers.map(c => [c.name, c.mobile]));
                const stockItemsMap = new Map(stockItems.map(item => [item.name, {
                    id: item.id,
                    selling_price: item.selling_price,
                    stock_on_hand: item.stock_on_hand
                }]));

                let tyreRepairMap = new Map();

                // --- 1. Customer Logic ---
                const customerInput = document.getElementById('customer_name');
                const contactInput = document.getElementById('contact_number');
                if (customerInput) {
                    customerInput.addEventListener('input', function(e) {
                        const val = e.target.value;
                        if (customerMap.has(val)) contactInput.value = customerMap.get(val);
                    });
                }

                // --- 2. Datalist for Stock ---
                function createStockDatalist() {
                    if (document.getElementById('stockItemsList') || stockItems.length === 0) return;
                    const dataList = document.createElement('datalist');
                    dataList.id = 'stockItemsList';
                    let options = '';
                    stockItems.forEach(item => {
                        options +=
                            `<option value="${item.name}">Price: ${item.selling_price} | Stock: ${item.stock_on_hand}</option>`;
                    });
                    dataList.innerHTML = options;
                    itemsContainer.appendChild(dataList);
                }

                // --- 3. Datalist for Tyre Repair (Async) ---
                async function fetchAndCreateTyreRepairDatalist() {
                    if (document.getElementById('tyreRepairJobsList')) return;
                    try {
                        const response = await fetch('{{ route('direct_bills.search_repair_jobs') }}');
                        const jobs = await response.json();
                        const dataList = document.createElement('datalist');
                        dataList.id = 'tyreRepairJobsList';
                        let options = '';
                        jobs.forEach(job => {
                            const label =
                                `Tyre Repair - Job: ${job.job_number} | Item Number (${job.item_number})`;
                            tyreRepairMap.set(label, {
                                amount: job.amount,
                                id: job.id,
                                customer: job.customer,
                                job_number: job.job_number
                            });
                            options +=
                                `<option value="${label}">Amount: ${job.amount} | Customer: ${job.customer}</option>`;
                        });
                        dataList.innerHTML = options;
                        itemsContainer.appendChild(dataList);
                    } catch (error) {
                        console.error(error);
                    }
                }

                // --- 4. Add Row Logic ---
                function getCommonRowHtml(index, data) {
                    const oldPrice = data.unit_price || '';
                    const oldQty = data.quantity || '1';
                    const oldDiscountRate = data.discount_rate || '0';
                    const oldDiscountType = data.discount_type || 'amount';
                    const oldItemDiscount = data.item_discount || '0';

                    return `
                        <div class="col-md-2"><label class="form-label">Unit Price</label><input type="number" class="form-control unit-price" name="items[${index}][unit_price]" value="${oldPrice}" step="0.01" min="0" required></div>
                        <div class="col-md-2"><label class="form-label">Quantity</label><input type="number" class="form-control item-quantity" name="items[${index}][quantity]" value="${oldQty}" min="1" required></div>
                        <div class="col-md-2"><label class="form-label">Discount</label>
                            <div class="input-group">
                                <input type="number" class="form-control discount-rate" name="items[${index}][discount_rate]" value="${oldDiscountRate}" step="0.01" min="0" placeholder="0">
                                <select class="form-select discount-type px-1" name="items[${index}][discount_type]" style="max-width: 60px;">
                                    <option value="amount" ${oldDiscountType === 'amount' ? 'selected' : ''}>Rs</option>
                                    <option value="percentage" ${oldDiscountType === 'percentage' ? 'selected' : ''}>%</option>
                                </select>
                            </div>
                            <input type="hidden" class="item-discount" name="items[${index}][item_discount]" value="${oldItemDiscount}">
                        </div>
                        <div class="col-md-2"><label class="form-label">Total</label><input type="text" class="form-control item-total-price" value="0.00" readonly></div>
                        <div class="col-md-1"><button type="button" class="btn btn-danger remove-item"><i class="bi bi-x-circle"></i></button></div>
                    `;
                }

                function addStockItemRow(itemData = {}) {
                    const index = itemIndex++;
                    const row = document.createElement('div');
                    row.classList.add('row', 'g-3', 'mb-3', 'align-items-end', 'item-row');
                    const oldName = itemData.item_name || '';
                    let oldStockId = itemData.stock_item_id || '';
                    let stockInfo = '';

                    if (stockItemsMap.has(oldName) && !itemData.unit_price) {
                        const s = stockItemsMap.get(oldName);
                        // Default to latest price if just adding row, or logic will handle it on input
                        itemData.unit_price = s.selling_price;
                        oldStockId = s.id;
                        stockInfo = `On Hand: ${s.stock_on_hand}`;
                    }

                    row.innerHTML =
                        `<div class="col-md-3"><label class="form-label">Item Name (Stock)</label><input type="text" class="form-control item-name-input" name="items[${index}][item_name]" list="stockItemsList" placeholder="Select or type item code" value="${oldName}" required><input type="hidden" class="stock-item-id" name="items[${index}][stock_item_id]" value="${oldStockId}"><div class="form-text stock-info text-primary fw-bold">${stockInfo}</div><div class="alert alert-warning batch-warning py-1 mt-1 mb-0" style="display:none; font-size:0.8rem"></div></div>${getCommonRowHtml(index, itemData)}`;
                    itemsContainer.appendChild(row);
                    
                    // If we just added a fresh row, we might want to load the correct price immediately
                    // But usually the user types the name. If name is pre-filled:
                    if (oldStockId) loadPriceForRow(row);
                }

                function addTyreRepairRow(itemData = {}) {
                    const index = itemIndex++;
                    const row = document.createElement('div');
                    row.classList.add('row', 'g-3', 'mb-3', 'align-items-end', 'item-row');
                    const oldName = itemData.item_name || '';
                    const oldJobNumber = itemData.job_number || '';
                    
                    row.innerHTML =
                        `<div class="col-md-3"><label class="form-label">Item Name (Repair Job)</label><div class="input-group"><span class="input-group-text"><i class="bi bi-wrench"></i></span><input type="text" class="form-control tyre-repair-input" name="items[${index}][item_name]" list="tyreRepairJobsList" placeholder="Search Job No..." value="${oldName}" required></div><input type="hidden" class="job-number" name="items[${index}][job_number]" value="${oldJobNumber}"><input type="hidden" class="stock-item-id" name="items[${index}][stock_item_id]" value=""></div>${getCommonRowHtml(index, itemData)}`;
                    itemsContainer.appendChild(row);
                    fetchAndCreateTyreRepairDatalist();
                }

                document.getElementById('add-item').addEventListener('click', () => {
                    addStockItemRow();
                    updateTotals();
                });
                
                document.getElementById('add-tyre-repair-item').addEventListener('click', () => {
                    addTyreRepairRow();
                    updateTotals();
                });

                // --- 5. Event Listeners ---
                itemsContainer.addEventListener('input', (e) => {
                    const target = e.target;
                    const row = target.closest('.item-row');
                    if (!row) return;

                    if (target.classList.contains('item-name-input')) {
                        const val = target.value;
                        const stockInfo = row.querySelector('.stock-info');
                        const stockIdIn = row.querySelector('.stock-item-id');
                        const warning = row.querySelector('.batch-warning');
                        const qtyIn = row.querySelector('.item-quantity');

                        if (stockItemsMap.has(val)) {
                            const data = stockItemsMap.get(val);
                            stockIdIn.value = data.id;
                            stockInfo.textContent = `On Hand: ${data.stock_on_hand}`;
                            warning.style.display = 'none';
                            
                            if (qtyIn) {
                                qtyIn.setAttribute('max', data.stock_on_hand);
                                if (parseFloat(qtyIn.value) > parseFloat(data.stock_on_hand)) {
                                    qtyIn.value = data.stock_on_hand;
                                    warning.innerHTML = `<i class="bi bi-exclamation-triangle-fill"></i> Only ${data.stock_on_hand} available.`;
                                    warning.style.display = 'block';
                                }
                            }
                            
                            // Load correct price
                            loadPriceForRow(row);
                        } else {
                            stockIdIn.value = '';
                            stockInfo.textContent = '';
                            warning.style.display = 'none';
                            if (qtyIn) qtyIn.removeAttribute('max');
                        }
                    }

                    if (target.classList.contains('tyre-repair-input')) {
                        const val = target.value;
                        if (tyreRepairMap.has(val)) {
                             const jobData = tyreRepairMap.get(val);
                             row.querySelector('.unit-price').value = jobData.amount || 0;
                             const jobNumberIn = row.querySelector('.job-number');
                             if (jobNumberIn) jobNumberIn.value = jobData.job_number || '';
                        }
                    }
                    
                    // Quantity Check
                     if (target.classList.contains('item-quantity')) {
                        const stockId = row.querySelector('.stock-item-id')?.value;
                        const warning = row.querySelector('.batch-warning');
                        const qtyVal = parseFloat(target.value) || 0;

                        const maxAttr = target.getAttribute('max');
                        if (maxAttr !== null) {
                            const maxVal = parseFloat(maxAttr) || 0;
                            if (qtyVal > maxVal) {
                                target.value = maxVal;
                                if (warning) {
                                    warning.innerHTML = `<i class="bi bi-exclamation-triangle-fill"></i> Only ${maxVal} available.`;
                                    warning.style.display = 'block';
                                }
                            } else {
                                if (warning) warning.style.display = 'none';
                            }
                        }
                        
                        // Optional: Check batch availability if needed (like create)
                        if (stockId) checkBatchAvailability(row, stockId, target.value);
                    }

                    updateTotals();
                });

                itemsContainer.addEventListener('click', (e) => {
                    if (e.target.closest('.remove-item')) {
                        e.target.closest('.item-row').remove();
                        updateTotals();
                    }
                });

                itemsContainer.addEventListener('change', (e) => {
                    if (e.target.classList.contains('discount-type')) updateTotals();
                });
                
                document.getElementById('discount_input').addEventListener('input', updateTotals);

                async function checkBatchAvailability(row, stockItemId, qty) {
                    const warning = row.querySelector('.batch-warning');
                    if (!warning) return;
                    try {
                        const res = await fetch(`/direct-bills/batch-info?stock_item_id=${stockItemId}`);
                        const data = await res.json();
                        if (data.has_stock && parseInt(qty) > data.available_quantity) {
                             warning.innerHTML = `<i class="bi bi-exclamation-triangle-fill"></i> Only ${data.available_quantity} available at Rs ${data.selling_price}.`;
                             warning.style.display = 'block';
                             const qtyInput = row.querySelector('.item-quantity');
                             if (qtyInput) {
                                 if (parseInt(qtyInput.value) > data.available_quantity) qtyInput.value = data.available_quantity;
                                 qtyInput.setAttribute('max', data.available_quantity);
                             }
                        } else {
                            warning.style.display = 'none';
                        }
                    } catch (err) {}
                }

                function recalculateAllUnitPrices(mode = null) {
                    itemsContainer.querySelectorAll('.item-row').forEach(row => {
                        loadPriceForRow(row, mode);
                    });
                }
                
                function loadPriceForRow(row, explicitMode = null) {
                    const stockId = row.querySelector('.stock-item-id')?.value;
                    if (!stockId) return;

                    const priceType = explicitMode || (document.getElementById('priceModeLatest').checked ? 'latest' : 'fifo');
                    console.log('📡 Loading price for stock ID:', stockId, 'using mode:', priceType);

                    const unitPriceIn = row.querySelector('.unit-price');
                    const qtyInput = row.querySelector('.item-quantity');
                    const warning = row.querySelector('.batch-warning');

                    const cacheBuster = new Date().getTime();
                    fetch("{{ route('direct_bills.get_batch_info') }}?stock_item_id=" + encodeURIComponent(stockId) +
                            "&price_type=" + priceType + "&_t=" + cacheBuster)
                        .then(res => res.json())
                        .then(data => {
                            if (unitPriceIn && typeof data.selling_price !== 'undefined') {
                                unitPriceIn.value = data.selling_price;
                            }
                            if (qtyInput && typeof data.available_quantity !== 'undefined') {
                                qtyInput.setAttribute('max', data.available_quantity);
                                const qtyVal = parseFloat(qtyInput.value) || 0;
                                if (qtyVal > data.available_quantity) {
                                    qtyInput.value = data.available_quantity;
                                    if (warning) {
                                        warning.innerHTML = `<i class="bi bi-exclamation-triangle-fill"></i> Only ${data.available_quantity} available.`;
                                        warning.style.display = 'block';
                                    }
                                } else if (warning) warning.style.display = 'none';
                            }
                            updateTotals();
                        })
                        .catch(err => console.error(err));
                }

                function updateTotals() {
                    let subTotal = 0;
                    let totalItemDiscount = 0;

                    itemsContainer.querySelectorAll('.item-row').forEach(row => {
                        const price = parseFloat(row.querySelector('.unit-price').value) || 0;
                        const qty = parseFloat(row.querySelector('.item-quantity').value) || 0;
                        const discRate = parseFloat(row.querySelector('.discount-rate').value) || 0;
                        const discType = row.querySelector('.discount-type').value;

                        let discPerUnit = (discType === 'percentage') ? (price * discRate / 100) : discRate;
                        row.querySelector('.item-discount').value = discPerUnit.toFixed(2);

                        const rowTotal = (price - discPerUnit) * qty;
                        row.querySelector('.item-total-price').value = rowTotal.toFixed(2);

                        subTotal += (price * qty);
                        totalItemDiscount += (discPerUnit * qty);
                    });

                    const addDisc = parseFloat(document.getElementById('discount_input').value) || 0;
                    const final = subTotal - totalItemDiscount - addDisc;

                    document.getElementById('bill_total').value = subTotal.toFixed(2);
                    document.getElementById('total_item_discount').value = totalItemDiscount.toFixed(2);
                    document.getElementById('final_amount').value = final.toFixed(2);
                    
                    // Update balance if needed
                    const paid = parseFloat(document.getElementById('paid').value) || 0;
                    document.getElementById('balance').value = (final - paid).toFixed(2);
                }

                // Init
                createStockDatalist();
                if (itemsContainer.querySelector('.tyre-repair-input')) fetchAndCreateTyreRepairDatalist();
                updateTotals();
            });
        </script>
    @endpush
@endsection

@extends('layouts.app')

@section('title', 'Add New Direct Bill')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-plus-circle me-2"></i>Create New Direct Bill</h1>
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
            <form id="bill-form" action="{{ route('direct_bills.store') }}" method="POST">
                @csrf
                <div class="row g-4 mb-3">
                    <div class="col-md-3">
                        <label for="bill_number" class="form-label">Bill Number</label>
                        <input type="text" class="form-control" id="bill_number" name="bill_number"
                            value="{{ old('bill_number', $newBillNumber ?? 'N/A') }}" readonly required>
                    </div>
                    <div class="col-md-3">
                        <label for="customer_name" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name"
                            list="customerList" placeholder="Select or Type Customer" value="{{ old('customer_name') }}"
                            required autocomplete="off">

                        <datalist id="customerList">
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->name }}">{{ $customer->mobile }}</option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="col-md-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number"
                            value="{{ old('contact_number') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="vehicle" class="form-label">Vehicle Number</label>
                        <input type="text" class="form-control" id="vehicle" name="vehicle"
                            value="{{ old('vehicle') }}">
                    </div>
                </div>
               <div class="row align-items-center">
    <div class="col-auto">
        <div class="btn-group" role="group" aria-label="Price Mode Selection">
            <input type="radio" class="btn-check" name="priceModeRadio" id="priceModeLatest" value="latest" checked>
            <label class="btn btn-outline-primary" for="priceModeLatest">
                <i class="bi bi-clock-history me-1"></i>
                <strong>Latest Price</strong>
            </label>

            <input type="radio" class="btn-check" name="priceModeRadio" id="priceModeFifo" value="fifo">
            <label class="btn btn-outline-success" for="priceModeFifo">
                <i class="bi bi-arrow-right-circle me-1"></i>
                <strong>FIFO Price</strong>
            </label>
        </div>
    </div>
    <div class="col">
        <div class="alert alert-info mb-0 py-2 px-3 d-flex align-items-center" style="font-size: 0.875rem;">
            <i class="bi bi-info-circle-fill me-2"></i>
            <span id="priceModeDescription">Using the <strong>newest batch</strong> selling price for all items</span>
        </div>
    </div>
</div>

                <h4>Bill Items</h4>
                <hr>
                <div id="items-container">
                    {{-- Container for JS-generated item rows --}}
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

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="bill_total" class="form-label">Bill Subtotal</label>
                        <input type="text" class="form-control" id="bill_total" name="bill_total"
                            value="{{ old('bill_total', '0.00') }}" readonly>
                        <small class="text-muted">Total before any discounts</small>
                    </div>

                    <div class="col-md-4">
                        <label for="total_item_discount" class="form-label">Sum of Item Discounts</label>
                        <input type="text" class="form-control" id="total_item_discount" value="0.00" readonly>
                        <small class="text-muted">Calculated from item rows above</small>
                    </div>

                    <div class="col-md-4">
                        <label for="discount_input" class="form-label">Additional Discount</label>
                        <input type="number" class="form-control" id="discount_input" name="discount"
                            value="{{ old('discount', 0) }}" step="0.01" placeholder="0.00">
                        <small class="text-muted">Manual discount for the whole bill</small>
                    </div>

                    <div class="col-md-12">
                        <label for="final_amount" class="form-label fw-bold">Final Amount</label>
                        <input type="text" class="form-control fw-bold" id="final_amount" name="final_amount"
                            value="{{ old('final_amount', '0.00') }}" readonly style="font-size: 1.2rem;">
                    </div>

                    <div class="col-md-12 mt-4">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><i class="bi bi-wallet2 me-2"></i>Payment Details</h5>
                                <hr>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="type" class="form-label">Payment Type</label>
                                        <select class="form-select" id="type" name="type" required>
                                            <option value="">Please Select</option>
                                            <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>Cash
                                                (Full Payment)</option>
                                            <option value="credit" {{ old('type') == 'credit' ? 'selected' : '' }}>Credit
                                                (Partial/Later)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6" id="paid_amount_container" style="display: none;">
                                        <label for="paid_amount" class="form-label">Initial Paid Amount (LKR)</label>
                                        <input type="number" class="form-control" id="paid_amount" name="paid_amount"
                                            value="{{ old('paid_amount', 0) }}" step="0.01" min="0">
                                        <div class="form-text">Leave as 0 if no initial payment is made.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label for="note" class="form-label">Note</label>
                        <textarea class="form-control" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary" id="save-button">
                        <i class="bi bi-save me-1"></i> Save & Print Bill
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
        <style>
            /* Price Mode Button Group Styling */
            .btn-group label.btn {
                padding:0.3rem 1.0rem;
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
            
            .btn-group label.btn small {
                opacity: 0.8;
                font-weight: normal;
            }
            
            .btn-check:checked + label small {
                opacity: 1;
                color: rgba(255, 255, 255, 0.9) !important;
            }
        </style>
    @endpush


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

                // Shared Index for both Stock and Tyre items to avoid collision
                let itemIndex = {{ count(old('items', [])) }};

                const itemsContainer = document.getElementById('items-container');
                const billForm = document.getElementById('bill-form');
                const saveButton = document.getElementById('save-button');

                // --- Data Setup ---
                const stockItems = @json($stockItems ?? []);
                const customers = @json($customers ?? []);
                const oldItems = @json(old('items'));

                // Maps
                const customerMap = new Map(customers.map(c => [c.name, c.mobile]));
                // stockItemsMap now includes stock_on_hand
                const stockItemsMap = new Map(stockItems.map(item => [item.name, {
                    id: item.id,
                    selling_price: item.selling_price,
                    stock_on_hand: item.stock_on_hand
                }]));

                // We will populate this map via AJAX when Tyre Repair is clicked
                let tyreRepairMap = new Map();

                // --- 1. Customer & Payment Logic ---
                const typeSelect = document.getElementById('type');
                const paidAmountContainer = document.getElementById('paid_amount_container');
                const paidAmountInput = document.getElementById('paid_amount');

                function togglePaidAmount() {
                    if (typeSelect.value === 'credit') {
                        paidAmountContainer.style.display = 'block';
                    } else {
                        paidAmountContainer.style.display = 'none';
                        paidAmountInput.value = 0;
                    }
                }
                typeSelect.addEventListener('change', togglePaidAmount);
                togglePaidAmount();

                const customerInput = document.getElementById('customer_name');
                const contactInput = document.getElementById('contact_number');
                if (customerInput) {
                    customerInput.addEventListener('input', function(e) {
                        const val = e.target.value;
                        if (customerMap.has(val)) {
                            contactInput.value = customerMap.get(val);
                        }
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
                    if (document.getElementById('tyreRepairJobsList')) return; // Already loaded

                    try {
                        const response = await fetch('{{ route('direct_bills.search_repair_jobs') }}');
                        const jobs = await response.json();

                        const dataList = document.createElement('datalist');
                        dataList.id = 'tyreRepairJobsList';
                        let options = '';

                        jobs.forEach(job => {
                            // Create a descriptive string for the input
                            const label =
                                `Tyre Repair - Job: ${job.job_number} | Item Number (${job.item_number})`;

                            // Store details in Map for auto-filling price later
                            // UPDATED: Added job_number to the map
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
                        console.error('Error fetching tyre repair jobs:', error);
                    }
                }

                // --- 4. Add Row Logic (Unified) ---

                function getCommonRowHtml(index, data) {
                    const oldPrice = data.unit_price || '';
                    const oldQty = data.quantity || '1';
                    const oldDiscountRate = data.discount_rate || '0';
                    const oldDiscountType = data.discount_type || 'amount';
                    const oldItemDiscount = data.item_discount || '0';

                    return `
                        <div class="col-md-2">
                            <label class="form-label">Unit Price</label>
                            <input type="number" class="form-control unit-price"
                                   name="items[${index}][unit_price]"
                                   value="${oldPrice}"
                                   step="0.01" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control item-quantity"
                                   name="items[${index}][quantity]"
                                   value="${oldQty}" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Discount</label>
                            <div class="input-group">
                                <input type="number" class="form-control discount-rate"
                                       name="items[${index}][discount_rate]"
                                       value="${oldDiscountRate}"
                                       step="0.01" min="0" placeholder="0">
                                <select class="form-select discount-type px-1" name="items[${index}][discount_type]" style="max-width: 60px;">
                                    <option value="amount" ${oldDiscountType === 'amount' ? 'selected' : ''}>Rs</option>
                                    <option value="percentage" ${oldDiscountType === 'percentage' ? 'selected' : ''}>%</option>
                                </select>
                            </div>
                            <input type="hidden" class="item-discount" name="items[${index}][item_discount]" value="${oldItemDiscount}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Total</label>
                            <input type="text" class="form-control item-total-price" value="0.00" readonly>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger remove-item"><i class="bi bi-x-circle"></i></button>
                        </div>
                    `;
                }

                function addStockItemRow(itemData = {}) {
                    const index = itemIndex++;
                    const row = document.createElement('div');
                    row.classList.add('row', 'g-3', 'mb-3', 'align-items-end', 'item-row');

                    const oldName = itemData.item_name || '';
                    let oldStockId = itemData.stock_item_id || '';
                    let stockInfo = '';
                    let stockOnHand = '';

                    if (stockItemsMap.has(oldName) && !itemData.unit_price) {
                        const s = stockItemsMap.get(oldName);
                        itemData.unit_price = s.selling_price;
                        oldStockId = s.id;
                        stockInfo = `On Hand: ${s.stock_on_hand}`;
                        stockOnHand = s.stock_on_hand;
                    }

                    row.innerHTML = `
                        <div class="col-md-3">
                            <label class="form-label">Item Name (Stock)</label> <span class="form-text stock-info text-primary fw-bold">${stockInfo}</span>
                            <input type="text" class="form-control item-name-input"
                                   name="items[${index}][item_name]"
                                   list="stockItemsList"
                                   placeholder="Select or type item code"
                                   value="${oldName}" required>
                            <input type="hidden" class="stock-item-id" name="items[${index}][stock_item_id]" value="${oldStockId}">
                            <div class="alert alert-warning batch-warning py-1 mt-1 mb-0" style="display:none; font-size:0.8rem"></div>
                        </div>
                        ${getCommonRowHtml(index, itemData)}
                    `;
                    itemsContainer.appendChild(row);

                    // Immediately set the max attribute for quantity input if stockOnHand is known
                    if (stockOnHand !== '') {
                        const qtyInput = row.querySelector('.item-quantity');
                        qtyInput.setAttribute('max', stockOnHand);
                        // If existing quantity exceeds max, clamp it and show warning
                        if (parseFloat(qtyInput.value) > parseFloat(stockOnHand)) {
                            qtyInput.value = stockOnHand;
                            const warning = row.querySelector('.batch-warning');
                            warning.innerHTML =
                                `<i class="bi bi-exclamation-triangle-fill"></i> Only ${stockOnHand} available.`;
                            warning.style.display = 'block';
                        }
                    }
                }

                function addTyreRepairRow(itemData = {}) {
                    const index = itemIndex++;
                    const row = document.createElement('div');
                    row.classList.add('row', 'g-3', 'mb-3', 'align-items-end', 'item-row');

                    const oldName = itemData.item_name || ''; // "Tyre Repair - Job..."
                    // UPDATED: Retrieve old job_number if re-rendering form with errors
                    const oldJobNumber = itemData.job_number || '';

                    // UPDATED: Added hidden input for job_number
                    row.innerHTML = `
                        <div class="col-md-3">
                            <label class="form-label">Item Name (Repair Job)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-wrench"></i></span>
                                <input type="text" class="form-control tyre-repair-input"
                                       name="items[${index}][item_name]"
                                       list="tyreRepairJobsList"
                                       placeholder="Search Job No..."
                                       value="${oldName}" required>
                            </div>
                            <input type="hidden" class="job-number" name="items[${index}][job_number]" value="${oldJobNumber}">
                            <div class="form-text text-muted">Select completed job to bill</div>
                        </div>
                        ${getCommonRowHtml(index, itemData)}
                    `;
                    itemsContainer.appendChild(row);

                    fetchAndCreateTyreRepairDatalist();
                }

                // --- 5. Event Listeners for Inputs ---
                itemsContainer.addEventListener('input', (e) => {
                    const target = e.target;
                    const row = target.closest('.item-row');
                    if (!row) return;

                    // A. Stock Item Logic
                    // ... inside itemsContainer.addEventListener('input', (e) => { ... })
                    if (target.classList.contains('item-name-input')) {
                        const val = target.value;
                        const unitPriceIn = row.querySelector('.unit-price');
                        const stockIdIn = row.querySelector('.stock-item-id');
                        const stockInfo = row.querySelector('.stock-info');
                        const qtyIn = row.querySelector('.item-quantity');
                        const warning = row.querySelector('.batch-warning');

                        if (stockItemsMap.has(val)) {
                            const data = stockItemsMap.get(val);

                            stockIdIn.value = data.id;
                            stockInfo.textContent = `On Hand: ${data.stock_on_hand}`;
                            warning.style.display = 'none';

                            if (qtyIn) {
                                qtyIn.setAttribute('max', data.stock_on_hand);
                                if (parseFloat(qtyIn.value) > parseFloat(data.stock_on_hand)) {
                                    qtyIn.value = data.stock_on_hand;
                                    warning.innerHTML =
                                        `<i class="bi bi-exclamation-triangle-fill"></i> Only ${data.stock_on_hand} available.`;
                                    warning.style.display = 'block';
                                } else {
                                    warning.style.display = 'none';
                                }
                            }

                            // get correct price (latest or FIFO) from server
                            loadPriceForRow(row);
                        } else {
                            stockIdIn.value = '';
                            stockInfo.textContent = '';
                            warning.style.display = 'none';
                            if (qtyIn) qtyIn.removeAttribute('max');
                        }
                    }

                    // B. Tyre Repair Logic
                    if (target.classList.contains('tyre-repair-input')) {
                        const val = target.value;
                        if (tyreRepairMap.has(val)) {
                            const jobData = tyreRepairMap.get(val);
                            // Auto-fill price
                            const unitPriceIn = row.querySelector('.unit-price');
                            unitPriceIn.value = jobData.amount || 0;

                            // UPDATED: Auto-fill hidden Job Number
                            const jobNumberIn = row.querySelector('.job-number');
                            if (jobNumberIn) {
                                jobNumberIn.value = jobData.job_number || '';
                            }
                        }
                    }

                    // C. Stock Batch Check
                    if (target.classList.contains('item-quantity')) {
                        const stockId = row.querySelector('.stock-item-id')?.value;
                        const warning = row.querySelector('.batch-warning');
                        const qtyVal = parseFloat(target.value) || 0;

                        // If the row has a max attribute, respect it and warn
                        const maxAttr = target.getAttribute('max');
                        if (maxAttr !== null) {
                            const maxVal = parseFloat(maxAttr) || 0;
                            if (qtyVal > maxVal) {
                                target.value = maxVal;
                                if (warning) {
                                    warning.innerHTML =
                                        `<i class="bi bi-exclamation-triangle-fill"></i> Only ${maxVal} available.`;
                                    warning.style.display = 'block';
                                }
                            } else {
                                if (warning) warning.style.display = 'none';
                            }
                        }

                        if (stockId) {
                            checkBatchAvailability(row, stockId, target.value);
                        }
                    }

                    updateTotals();
                });

                // Remove Row
                itemsContainer.addEventListener('click', (e) => {
                    if (e.target.closest('.remove-item')) {
                        e.target.closest('.item-row').remove();
                        updateTotals();
                    }
                });

                // Discount Type Change
                itemsContainer.addEventListener('change', (e) => {
                    if (e.target.classList.contains('discount-type')) updateTotals();
                });

                // Buttons
                document.getElementById('add-item').addEventListener('click', () => {
                    addStockItemRow();
                    updateTotals();
                });

                document.getElementById('add-tyre-repair-item').addEventListener('click', () => {
                    addTyreRepairRow();
                    updateTotals();
                });

                // Additional Discount Input
                document.getElementById('discount_input').addEventListener('input', updateTotals);

                // FORM SUBMIT: final client-side guard
                billForm.addEventListener('submit', function(e) {
                    const rows = itemsContainer.querySelectorAll('.item-row');
                    let invalid = false;
                    let messages = [];

                    rows.forEach(row => {
                        const stockId = row.querySelector('.stock-item-id')?.value;
                        const qty = parseFloat(row.querySelector('.item-quantity')?.value || 0);
                        if (stockId) {
                            // Prefer client-known stock_on_hand, fallback to checking batch-info endpoint synchronously via fetch (not recommended)
                            const name = row.querySelector('.item-name-input')?.value;
                            const stockEntry = stockItemsMap.get(name);
                            const available = stockEntry ? (parseFloat(stockEntry.stock_on_hand) || 0) :
                                null;
                            if (available !== null && qty > available) {
                                invalid = true;
                                messages.push(
                                    `Requested ${qty} for '${name}' exceeds available ${available}.`
                                );
                                row.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                            }
                        }
                    });

                    if (invalid) {
                        e.preventDefault();
                        alert(messages.join('\n\n'));
                        return false;
                    }

                    // allow submit
                });

                // --- 6. Helper Functions ---

                async function checkBatchAvailability(row, stockItemId, qty) {
                    const warning = row.querySelector('.batch-warning');
                    if (!warning) return;

                    try {
                        const res = await fetch(`/direct-bills/batch-info?stock_item_id=${stockItemId}`);
                        const data = await res.json();

                        if (data.has_stock && parseInt(qty) > data.available_quantity) {
                            warning.innerHTML =
                                `<i class="bi bi-exclamation-triangle-fill"></i> Only ${data.available_quantity} available at Rs ${data.selling_price}.`;
                            warning.style.display = 'block';

                            const qtyInput = row.querySelector('.item-quantity');
                            if (qtyInput) {
                                if (parseInt(qtyInput.value) > data.available_quantity) {
                                    qtyInput.value = data.available_quantity;
                                }
                                qtyInput.setAttribute('max', data.available_quantity);
                            }
                        } else {
                            warning.style.display = 'none';
                        }
                    } catch (err) {
                        console.error(err);
                    }
                }

                function recalculateAllUnitPrices(mode = null) {
                    itemsContainer.querySelectorAll('.item-row').forEach(row => {
                        loadPriceForRow(row, mode);
                    });
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
                }

                // --- 7. Initialization ---
                createStockDatalist();

                if (oldItems && oldItems.length > 0) {
                    oldItems.forEach(item => {
                        if (item.stock_item_id || stockItemsMap.has(item.item_name)) {
                            addStockItemRow(item);
                        } else {
                            addTyreRepairRow(item);
                        }
                    });
                } else {
                    addStockItemRow();
                }

                updateTotals();

                function loadPriceForRow(row, explicitMode = null) {
                    const stockId = row.querySelector('.stock-item-id')?.value;
                    if (!stockId) return;

                    // Use explicit mode if provided, otherwise check DOM (fallback)
                    const priceType = explicitMode || (document.getElementById('priceModeLatest').checked ? 'latest' : 'fifo');
                    
                    console.log('📡 Loading price for stock ID:', stockId, 'using mode:', priceType);

                    const unitPriceIn = row.querySelector('.unit-price');
                    const qtyInput = row.querySelector('.item-quantity');
                    const warning = row.querySelector('.batch-warning');

                    // Add timestamp to prevent caching
                    const cacheBuster = new Date().getTime();
                    fetch("{{ route('direct_bills.get_batch_info') }}?stock_item_id=" + encodeURIComponent(stockId) +
                            "&price_type=" + priceType + "&_t=" + cacheBuster)
                        .then(res => res.json())
                        .then(data => {
                            console.log('✅ Received price data:', data);
                            if (unitPriceIn && typeof data.selling_price !== 'undefined') {
                                console.log('💰 Setting unit price to:', data.selling_price);
                                unitPriceIn.value = data.selling_price;
                            }

                            if (qtyInput && typeof data.available_quantity !== 'undefined') {
                                qtyInput.setAttribute('max', data.available_quantity);
                                const qtyVal = parseFloat(qtyInput.value) || 0;

                                if (qtyVal > data.available_quantity) {
                                    qtyInput.value = data.available_quantity;
                                    if (warning) {
                                        warning.innerHTML =
                                            `<i class="bi bi-exclamation-triangle-fill"></i> Only ${data.available_quantity} available.`;
                                        warning.style.display = 'block';
                                    }
                                } else if (warning) {
                                    warning.style.display = 'none';
                                }
                            }

                            updateTotals();
                        })
                        .catch(err => {
                            console.error('❌ Error loading price:', err);
                        });
                }
            });
        </script>
    @endpush
@endsection

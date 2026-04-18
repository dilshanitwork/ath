@extends('layouts.app')

@section('title', 'Create Credit Bill')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-file-earmark-plus me-2"></i>Create New Credit Bill</h1>
            <a href="{{ route('bills.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Sale Type Highlight -->
        {{-- @if ($isShowroomUser || $isVanUser)
            <div class="alert {{ $isShowroomUser ? 'alert-primary' : 'alert-success' }}">
                <strong>Sale Type:</strong>
                {{ $isShowroomUser ? 'Showroom Sale & Bill category will be automatically applied to Showroom Sales.' : 'Van Sale & Bill category will be automatically applied to Van Sales.' }}
            </div>
        @else
            <div class="alert alert-warning">
                <strong>Sale Type:</strong> Please select the category for this bill.
            </div>
        @endif --}}

        <!-- Create Bill Form -->
        <div class="card p-4">
            <form action="{{ route('bills.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="bill_number" class="form-label"><i class="bi bi-file-earmark-text me-2"></i>Bill
                            Number</label>
                        <input type="text" name="bill_number" id="bill_number" class="form-control"
                            placeholder="Enter Bill Number" required>
                        @error('bill_number')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="customer_id" class="form-label"><i class="bi bi-person me-2"></i>Customer</label>
                        <select name="customer_id" id="customer_id" class="form-control" required>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Category Selection -->
                    @if (!$isShowroomUser && !$isVanUser)
                        <input type="hidden" name="category" value="0">
                        @error('category')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    @endif

                    <div class="col-12">
                        <label class="form-label"><i class="bi bi-box me-2"></i>First Item</label>
                        <div id="items">
                            <div class="item mb-3">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control item-name-input"
                                            name="items[0][item_name]" list="stockItemsList0"
                                            placeholder="Select or type item name" required>
                                        <input type="hidden" class="stock-item-id" name="items[0][stock_item_id]">
                                        <datalist id="stockItemsList0">
                                            @foreach ($stockItems as $item)
                                                <option value="{{ $item->name }}">
                                            @endforeach
                                        </datalist>
                                        <div class="form-text stock-info text-primary fw-bold"></div>
                                        <div class="alert alert-warning batch-warning mb-0 mt-1 px-2 py-1"
                                            style="display: none; font-size: 0.875rem;"></div>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control item-quantity"
                                            name="items[0][item_quantity]" placeholder="Quantity" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" step="0.01" class="form-control item-price"
                                            name="items[0][item_price]" placeholder="Price" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" step="0.01" class="form-control item-total-price"
                                            name="items[0][total_price]" placeholder="Total Price" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary" id="addItem">
                            <i class="bi bi-plus-circle me-1"></i>Add more items
                        </button>
                    </div>

                    <div class="col-md-6">
                        <label for="total_price" class="form-label"><i class="bi bi-currency-dollar me-2"></i>Total
                            Price</label>
                        <input type="number" step="0.01" name="total_price" id="total_price" class="form-control"
                            placeholder="Enter Total Price" readonly>
                        @error('total_price')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <!-- Type Fields -->
                    <div class="col-md-6">
                        <label for="type" class="form-label"><i class="bi bi-calendar me-2"></i>Payment Type</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="1">Daily</option>
                            <option value="2">Weekly</option>
                            <option value="3">Monthly</option>
                        </select>
                        @error('type')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="advance_payment" class="form-label"><i class="bi bi-currency-dollar me-2"></i>Advance
                            Payment</label>
                        <input type="number" step="0.01" name="advance_payment" id="advance_payment"
                            class="form-control" placeholder="Enter Advance Payment" required>
                        @error('advance_payment')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="installments" class="form-label"><i class="bi bi-123 me-2"></i>Installments</label>
                        <input type="number" name="installments" id="installments" class="form-control"
                            placeholder="Enter Number of Installments" required>
                        @error('installments')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="payment_type" class="form-label"><i class="bi bi-credit-card me-2"></i>Payment
                            Type</label>
                        <select name="payment_type" id="payment_type" class="form-control" required>
                            <option value="cash" selected>Cash</option>
                            <option value="card">Card</option>
                            <option value="online">Online</option>
                        </select>
                        @error('payment_type')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="balance" class="form-label"><i
                                class="bi bi-currency-dollar me-2"></i>Balance</label>
                        <input type="number" step="0.01" name="balance" id="balance" class="form-control"
                            placeholder="Enter Balance" readonly>
                        @error('balance')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="next_payment" class="form-label"><i class="bi bi-currency-dollar me-2"></i>Next
                            Payment</label>
                        <input type="number" step="0.01" name="next_payment" id="next_payment" class="form-control"
                            placeholder="Enter Next Payment Amount" readonly>
                        @error('next_payment')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="next_bill_dates" class="form-label"><i class="bi bi-calendar-date me-2"></i>Next Bill
                            Dates</label>
                        <div id="bill-dates" class="row g-2"></div>
                    </div>

                    <!-- Guarantor Information -->
                    <div class="col-md-6">
                        <label for="guarantor_name" class="form-label"><i class="bi bi-person-badge me-2"></i>Guarantor
                            Name</label>
                        <input type="text" name="guarantor_name" id="guarantor_name" class="form-control"
                            placeholder="Enter Guarantor Name">
                        @error('guarantor_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="guarantor_mobile" class="form-label"><i class="bi bi-phone me-2"></i>Guarantor
                            Mobile</label>
                        <input type="text" name="guarantor_mobile" id="guarantor_mobile" class="form-control"
                            placeholder="Enter Guarantor Mobile">
                        @error('guarantor_mobile')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="guarantor_nic" class="form-label"><i class="bi bi-credit-card me-2"></i>Guarantor
                            NIC</label>
                        <input type="text" name="guarantor_nic" id="guarantor_nic" class="form-control"
                            placeholder="Enter Guarantor NIC">
                        @error('guarantor_nic')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-start mt-4">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-save me-1"></i> Create & Save
                    </button>
                    <a href="{{ route('bills.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Stock items data for dynamically added items
        const stockItems = @json($stockItems);

        const stockItemsMap = new Map(stockItems.map(item => [item.name, {
            id: item.id,
            installment_price: item.installment_price,
            stock_on_hand: item.stock_on_hand
        }]));

        // Check batch availability
        async function checkBatchAvailability(itemElement, stockItemId, requestedQuantity) {
            const batchWarning = itemElement.querySelector('.batch-warning');

            try {
                const url = `/bills/batch-info?stock_item_id=${stockItemId}`;
                const response = await fetch(url);

                if (!response.ok) return;

                const data = await response.json();

                if (data.has_stock && requestedQuantity > data.available_quantity) {
                    const itemName = itemElement.querySelector('.item-name-input').value;
                    batchWarning.innerHTML =
                        `<i class="bi bi-exclamation-triangle-fill me-1"></i><strong>Warning:</strong> Only ${data.available_quantity} unit(s) of "${itemName}" available at the current price (LKR ${data.installment_price}). Consider creating separate bills for different price batches.`;
                    batchWarning.style.display = 'block';
                } else {
                    batchWarning.style.display = 'none';
                }
            } catch (error) {
                console.error('Error checking batch availability:', error);
            }
        }

        function calculateTotals() {
            let totalPrice = 0;

            document.querySelectorAll('.item').forEach((item) => {
                const quantity = parseFloat(item.querySelector('.item-quantity').value) || 0;
                const price = parseFloat(item.querySelector('.item-price').value) || 0;
                const total = quantity * price;

                item.querySelector('.item-total-price').value = total.toFixed(2);
                totalPrice += total;
            });

            document.getElementById('total_price').value = totalPrice.toFixed(2);

            const advancePayment = parseFloat(document.getElementById('advance_payment').value) || 0;
            const balance = totalPrice - advancePayment;
            document.getElementById('balance').value = balance.toFixed(2);

            const installments = parseInt(document.getElementById('installments').value) || 1;
            const nextPayment = balance / installments;
            document.getElementById('next_payment').value = nextPayment.toFixed(2);

            generateBillDates();
        }

        function generateBillDates() {
            const billDatesContainer = document.getElementById('bill-dates');

            // Store current user-modified dates
            const existingDates = Array.from(billDatesContainer.querySelectorAll('input')).map(input => input.value);

            const type = parseInt(document.getElementById('type').value);
            const installments = parseInt(document.getElementById('installments').value) || 1;
            const today = new Date();

            // Clear the container for regeneration
            billDatesContainer.innerHTML = '';

            for (let i = 0; i < installments; i++) {
                const nextDate = new Date(today);

                // Calculate the next date based on the payment type
                if (type === 1) {
                    nextDate.setDate(nextDate.getDate() + i + 1); // Daily
                } else if (type === 2) {
                    nextDate.setDate(nextDate.getDate() + (i + 1) * 7); // Weekly
                } else if (type === 3) {
                    nextDate.setMonth(nextDate.getMonth() + (i + 1)); // Monthly
                }

                const formattedDate = nextDate.toISOString().split('T')[0];

                // Preserve user-modified dates if they exist
                const userModifiedDate = existingDates[i] || formattedDate;

                // Create the input field
                const input = document.createElement('input');
                input.type = 'date';
                input.name = `bill_dates[${i}]`;
                input.value = userModifiedDate;
                input.classList.add('form-control', 'mb-2');

                // Append the input to the container
                billDatesContainer.appendChild(input);
            }
        }

        document.addEventListener('input', calculateTotals);

        document.getElementById('addItem').addEventListener('click', function() {
            const items = document.getElementById('items');
            const index = items.children.length;
            const item = document.createElement('div');
            item.classList.add('item', 'mb-3');

            let stockItemsOptions = '';
            stockItems.forEach(stockItem => {
                stockItemsOptions += `<option value="${stockItem.name}">`;
            });

            item.innerHTML = `
        <div class="row g-2">
            <div class="col-md-6">
                <input type="text" class="form-control item-name-input" 
                       name="items[${index}][item_name]"
                       list="stockItemsList${index}"
                       placeholder="Select or type item name" required>
                <input type="hidden" class="stock-item-id" name="items[${index}][stock_item_id]">
                <datalist id="stockItemsList${index}">
                    ${stockItemsOptions}
                </datalist>
                <div class="form-text stock-info text-primary fw-bold"></div>
                <div class="px-2 py-1 mt-1 mb-0 alert alert-warning batch-warning" style="display: none; font-size: 0.875rem;"></div>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control item-quantity" name="items[${index}][item_quantity]" placeholder="Quantity" required>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" class="form-control item-price" name="items[${index}][item_price]" placeholder="Price" required>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" class="form-control item-total-price" name="items[${index}][total_price]" placeholder="Total Price" readonly>
            </div>
        </div>
    `;
            items.appendChild(item);
        });

        document.addEventListener('input', function(e) {
            const itemElement = e.target.closest('.item');
            if (!itemElement) return;

            // Handle item name input
            if (e.target.classList.contains('item-name-input')) {
                const itemName = e.target.value;
                const priceInput = itemElement.querySelector('.item-price');
                const stockIdInput = itemElement.querySelector('.stock-item-id');
                const stockInfo = itemElement.querySelector('.stock-info');
                const batchWarning = itemElement.querySelector('.batch-warning');
                const quantityInput = itemElement.querySelector('.item-quantity');

                const stockItem = stockItemsMap.get(itemName);

                if (stockItem) {
                    priceInput.value = stockItem.installment_price;
                    stockIdInput.value = stockItem.id;
                    stockInfo.textContent = `On Hand: ${stockItem.stock_on_hand}`;
                    batchWarning.style.display = 'none';

                    if (quantityInput.value) {
                        checkBatchAvailability(itemElement, stockItem.id, parseInt(quantityInput.value));
                    }
                } else {
                    stockIdInput.value = '';
                    stockInfo.textContent = '';
                    batchWarning.style.display = 'none';
                }
            }

            // Handle quantity input
            if (e.target.classList.contains('item-quantity')) {
                const stockIdInput = itemElement.querySelector('.stock-item-id');
                const stockItemId = stockIdInput.value;
                const quantity = parseInt(e.target.value) || 0;

                if (stockItemId && quantity > 0) {
                    checkBatchAvailability(itemElement, stockItemId, quantity);
                } else {
                    itemElement.querySelector('.batch-warning').style.display = 'none';
                }
            }

            calculateTotals();
        });
    </script>
@endsection

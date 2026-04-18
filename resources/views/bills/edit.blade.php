@extends('layouts.app')

@section('title', 'Edit Bill')

@section('content')
    <div class="container">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1 class="mb-0 card-title"><i class="bi bi-pencil-square me-2"></i>Edit Bill</h1>
            <a href="{{ route('bills.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Edit Bill Form -->
        <div class="p-4 card">
                <form action="{{ route('bills.update', $bill) }}" method="POST" id="updateBillForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <!-- Bill Number -->
                        <div class="col-md-6">
                            <label for="bill_number" class="form-label"><i class="bi bi-file-earmark-text me-2"></i>Bill Number</label>
                            <input type="text" name="bill_number" id="bill_number" class="form-control"
                                value="{{ $bill->bill_number }}" required @cannot('advance edit bills') readonly @endcannot>
                            @error('bill_number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Customer -->
                        <div class="col-md-6">
                            <label for="customer_id" class="form-label"><i class="bi bi-person me-2"></i>Customer</label>
                            <select name="customer_id" id="customer_id" class="form-control" required
                                @cannot('advance edit bills') disabled @endcannot>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ $customer->id == $bill->customer_id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            @cannot('advance edit bills')
                                <input type="hidden" name="customer_id" value="{{ $bill->customer_id }}">
                            @endcannot
                            @error('customer_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="col-md-6">
                            <label for="category" class="form-label"><i class="bi bi-tags me-2"></i>Category</label>
                            <select name="category" id="category" class="form-control" required>
                                <option value="0" {{ $bill->category == 0 ? 'selected' : '' }}>Showroom Sale</option>
                                <option value="1" {{ $bill->category == 1 ? 'selected' : '' }}>Van Sale</option>
                            </select>
                            @error('type')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Items -->
                        <div class="col-12">
                            <label class="form-label"><i class="bi bi-box me-2"></i>Items</label>
                            <div id="items">
                                @foreach ($bill->items as $index => $item)
                                <div class="mb-3 item">
                                    <div class="row g-2">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control item-name-input"
                                                name="items[{{ $index }}][item_name]" 
                                                value="{{ $item->item_name }}"
                                                list="stockItemsList{{ $index }}"
                                                placeholder="Select or type item name" required 
                                                @cannot('advance edit bills') readonly @endcannot>
                                            
                                            <!-- Hidden fields for tracking -->
                                            <input type="hidden" class="stock-item-id" name="items[{{ $index }}][stock_item_id]" value="{{ $item->stock_item_id }}">
                                            <input type="hidden" class="batch-id" name="items[{{ $index }}][batch_id]" value="{{ $item->batch_id }}">
                                            <input type="hidden" class="bill-item-id" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                            <input type="hidden" class="original-quantity" value="{{ $item->item_quantity }}">
                                            
                                            <datalist id="stockItemsList{{ $index }}">
                                                @foreach ($stockItems as $stockItem)
                                                    <option value="{{ $stockItem->name }}">
                                                @endforeach
                                            </datalist>
                                            
                                            <div class="form-text stock-info text-primary fw-bold">
                                                @if($item->batch_id)
                                                    Batch ID: {{ $item->batch_id }}
                                                @endif
                                            </div>
                                            <div class="px-2 py-1 mt-1 mb-0 alert alert-warning batch-warning" style="display: none; font-size: 0.875rem;"></div>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control item-quantity"
                                                name="items[{{ $index }}][item_quantity]"
                                                value="{{ $item->item_quantity }}" placeholder="Quantity" required
                                                @cannot('advance edit bills') readonly @endcannot>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" step="0.01" class="form-control item-price"
                                                name="items[{{ $index }}][item_price]" value="{{ $item->item_price }}"
                                                placeholder="Price" required @cannot('advance edit bills') readonly @endcannot>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" step="0.01" class="form-control item-total-price"
                                                name="items[{{ $index }}][total_price]" value="{{ $item->total_price }}"
                                                placeholder="Total Price" readonly>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger btn-sm remove-item w-100"
                                                @cannot('advance edit bills') disabled @endcannot>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="addItem"
                                @cannot('advance edit bills') disabled @endcannot>
                                <i class="bi bi-plus-circle me-1"></i>Add more items
                            </button>
                        </div>

                        <!-- Total Price -->
                        <div class="col-md-6">
                            <label for="total_price" class="form-label"><i class="bi bi-currency-dollar me-2"></i>Total Price</label>
                            <input type="number" step="0.01" name="total_price" id="total_price" class="form-control"
                                value="{{ $bill->total_price }}" readonly>
                            @error('total_price')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Advance Payment -->
                        <div class="col-md-6">
                            <label for="advance_payment" class="form-label"><i class="bi bi-currency-dollar me-2"></i>Advance Payment</label>
                            <input type="number" step="0.01" name="advance_payment" id="advance_payment"
                                class="form-control" value="{{ $bill->advance_payment }}" required
                                @cannot('advance edit bills') readonly @endcannot>
                            @error('advance_payment')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Balance -->
                        <div class="col-md-6">
                            <label for="balance" class="form-label"><i class="bi bi-currency-dollar me-2"></i>Balance</label>
                            <input type="number" step="0.01" name="balance" id="balance" class="form-control"
                                value="{{ $bill->balance }}" readonly>
                            @error('balance')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="col-md-6">
                            <label for="type" class="form-label"><i class="bi bi-calendar me-2"></i>Payment Type</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="1" {{ $bill->type == 1 ? 'selected' : '' }}>Daily</option>
                                <option value="2" {{ $bill->type == 2 ? 'selected' : '' }}>Weekly</option>
                                <option value="3" {{ $bill->type == 3 ? 'selected' : '' }}>Monthly</option>
                            </select>
                            @error('type')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Installments -->
                        <div class="col-md-6">
                            <label for="installments" class="form-label"><i class="bi bi-123 me-2"></i>Installments</label>
                            <input type="number" name="installments" id="installments" class="form-control"
                                value="{{ $bill->installments }}">
                        </div>

                        <!-- Next Payment -->
                        <div class="col-md-6">
                            <label for="next_payment" class="form-label"><i class="bi bi-currency-dollar me-2"></i>Next Payment</label>
                            <input type="number" step="0.01" name="next_payment" id="next_payment" class="form-control"
                                value="{{ $bill->next_payment }}" readonly>
                            @error('next_payment')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Next Bill Date -->
                        <div class="col-md-6">
                            <label for="next_bill" class="form-label"><i class="bi bi-calendar-date me-2"></i>Next Bill Date</label>
                            <input type="date" name="next_bill" id="next_bill" class="form-control"
                                value="{{ $bill->next_bill }}">
                            @error('next_bill')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Editable Payment Schedule -->
                        <div class="col-12">
                            <label for="payment_schedule" class="form-label"><i class="bi bi-calendar-week me-2"></i>Payment Schedule</label>
                            <div id="payment-schedule" class="row g-2">
                                @foreach ($bill->paymentSchedules as $index => $schedule)
                                    <div class="col-md-6">
                                        <div class="gap-2 mb-2 schedule d-flex">
                                            <input type="date" class="form-control"
                                                name="payment_schedule[{{ $index }}]"
                                                value="{{ $schedule->payment_date }}" required>
                                            <button type="button" class="btn btn-danger btn-sm remove-schedule">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="mt-2 btn btn-outline-primary" id="addSchedule">
                                <i class="bi bi-plus-circle me-1"></i>Add Payment Date
                            </button>
                        </div>
                    </div>
    </form> 

    <!-- Button Row with all three buttons -->
    <div class="gap-2 mt-4 d-flex justify-content-between align-items-center">
        <div class="gap-2 d-flex">
            <button type="submit" form="updateBillForm" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Update & Save
            </button>
            <a href="{{ route('bills.index') }}" class="btn btn-secondary">
                <i class="bi bi-x-circle me-1"></i> Cancel
            </a>
        </div>
        
        <!-- Delete form separate but on same row -->
        <form action="{{ route('bills.destroy', $bill) }}" method="POST" 
            onsubmit="return confirm('Are you sure you want to delete this bill?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </form>
    </div>
        </div>

    <script>
        function calculateItemTotal(itemElement) {
            // Get the quantity and price fields within the item element
            const quantity = parseFloat(itemElement.querySelector('.item-quantity').value) || 0;
            const price = parseFloat(itemElement.querySelector('.item-price').value) || 0;

            // Calculate the total price for the item
            const total = quantity * price;

            // Set the calculated total price in the total_price field
            itemElement.querySelector('.item-total-price').value = total.toFixed(2);
        }

        function calculateAllTotals() {
            const items = document.querySelectorAll('.item');
            let grandTotal = 0;

            // Loop through each item and calculate its total
            items.forEach((item) => {
                calculateItemTotal(item);
                const totalPrice = parseFloat(item.querySelector('.item-total-price').value) || 0;
                grandTotal += totalPrice;
            });

            // Update the total price field for the entire form
            document.getElementById('total_price').value = grandTotal.toFixed(2);

            // Update the balance and next payment fields based on the new total
            updateBalanceAndNextPayment();
        }

        function updateBalanceAndNextPayment() {
            const totalPrice = parseFloat(document.getElementById('total_price').value) || 0;
            const advancePayment = parseFloat(document.getElementById('advance_payment').value) || 0;
            const installments = parseInt(document.getElementById('installments').value) || 1;

            // Calculate balance and next payment
            const balance = totalPrice - advancePayment;
            const nextPayment = installments > 0 ? balance / installments : 0;

            // Update the respective fields
            document.getElementById('balance').value = balance.toFixed(2);
            document.getElementById('next_payment').value = nextPayment.toFixed(2);
        }

        // Attach event listeners to dynamically calculate totals
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('item-quantity') || e.target.classList.contains('item-price')) {
                const itemElement = e.target.closest('.item');
                calculateItemTotal(itemElement);
                calculateAllTotals();
            } else if (e.target.id === 'advance_payment' || e.target.id === 'installments') {
                updateBalanceAndNextPayment();
            }
        });

        // Add new item functionality
            document.getElementById('addItem').addEventListener('click', function () {
            const itemsContainer = document.getElementById('items');
            const index = itemsContainer.querySelectorAll('.item').length;

            const newItem = document.createElement('div');
            newItem.classList.add('mb-3', 'item');
            newItem.innerHTML = `
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text" class="form-control item-name-input"
                            name="items[${index}][item_name]"
                            placeholder="Select or type item name" list="stockItemsList${index}" required>
                        
                        <input type="hidden" class="stock-item-id" name="items[${index}][stock_item_id]">
                        <input type="hidden" class="batch-id" name="items[${index}][batch_id]">
                        <input type="hidden" class="bill-item-id" name="items[${index}][id]">
                        <input type="hidden" class="original-quantity" value="0">

                        <datalist id="stockItemsList${index}">
                            ${stockItems.map(stock => `<option value="${stock.name}">`).join('')}
                        </datalist>

                        <div class="form-text stock-info text-primary fw-bold"></div>
                        <div class="px-2 py-1 mt-1 mb-0 alert alert-warning batch-warning"
                            style="display: none; font-size: 0.875rem;"></div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control item-quantity"
                            name="items[${index}][item_quantity]" placeholder="Quantity" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" class="form-control item-price"
                            name="items[${index}][item_price]" placeholder="Price" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" class="form-control item-total-price"
                            name="items[${index}][total_price]" placeholder="Total Price" readonly>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-item w-100">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            itemsContainer.appendChild(newItem);
        });


        // Remove item functionality
        document.getElementById('items').addEventListener('click', function(e) {
            if (e.target.closest('.remove-item')) {
                const itemToRemove = e.target.closest('.item');
                itemToRemove.remove(); // Remove the item element
                calculateAllTotals(); // Recalculate all totals after removal
            }
        });

        document.getElementById('addSchedule').addEventListener('click', function() {
            const schedules = document.getElementById('payment-schedule');
            const index = schedules.children.length;
            const schedule = document.createElement('div');
            schedule.classList.add('schedule', 'mb-3');
            schedule.innerHTML = `
                <input type="date" class="mb-2 form-control" name="payment_schedule[${index}]" required>
                <button type="button" class="btn btn-danger btn-sm remove-schedule"><i class="fas fa-trash"></i> Remove</button>
            `;
            schedules.appendChild(schedule);
        });

        document.getElementById('payment-schedule').addEventListener('click', function(e) {
            if (e.target.closest('.remove-schedule')) {
                e.target.closest('.schedule').remove();
            }
        });

        // Stock items data for dynamic functionality
        const stockItems = @json($stockItems);

        const stockItemsMap = new Map(stockItems.map(item => [item.name, {
            id: item.id,
            installment_price: item.installment_price,
            stock_on_hand: item.stock_on_hand
        }]));

        // Check batch availability
        // Check batch availability
        async function checkBatchAvailability(itemElement, stockItemId, requestedQuantity) {
            const batchWarning = itemElement.querySelector('.batch-warning');
            
            // Get the original quantity (if editing existing item)
            const originalQuantityInput = itemElement.querySelector('.original-quantity');
            const originalQuantity = originalQuantityInput ? parseInt(originalQuantityInput.value) || 0 : 0;
            
            // Calculate how much MORE stock we need (not total)
            const additionalQuantityNeeded = requestedQuantity - originalQuantity;
            
            try {
                const url = `/bills/batch-info?stock_item_id=${stockItemId}`;
                const response = await fetch(url);
                
                if (!response.ok) return;
                
                const data = await response.json();
                
                if (data.has_stock && additionalQuantityNeeded > data.available_quantity) {
                    const itemName = itemElement.querySelector('.item-name-input').value;
                    batchWarning.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i><strong>Warning:</strong> Only ${data.available_quantity} unit(s) of "${itemName}" available at the current price (LKR ${data.installment_price}). You're trying to add ${additionalQuantityNeeded} more. Consider creating separate bills for different price batches.`;
                    batchWarning.style.display = 'block';
                } else {
                    batchWarning.style.display = 'none';
                }
            } catch (error) {
                console.error('Error checking batch availability:', error);
            }
        }

        // Enhanced input handler for stock item selection
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
                    const batchWarning = itemElement.querySelector('.batch-warning');
                    if (batchWarning) {
                        batchWarning.style.display = 'none';
                    }
                }
            }
            
            // Recalculate totals for price/quantity changes
            if (e.target.classList.contains('item-quantity') || e.target.classList.contains('item-price')) {
                calculateItemTotal(itemElement);
                calculateAllTotals();
            } else if (e.target.id === 'advance_payment' || e.target.id === 'installments') {
                updateBalanceAndNextPayment();
            }
        });
    </script>
@endsection


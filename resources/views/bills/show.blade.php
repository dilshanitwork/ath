@extends('layouts.app')

@section('title', 'Bill Details')

@section('content')

    <div class="container">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
            <h1 class="card-title mb-0"><i class="bi bi-file-earmark-text me-2"></i>Bill Details</h1>
            <div class="mt-md-0 mt-2">
                <a href="{{ route('bills.paymentPage', $bill) }}" class="btn btn-success">
                    <i class="bi bi-cash"></i> Pay Now
                </a>
                <a href="{{ route('bills.print', $bill) }}" class="btn btn-info" target="_blank">
                    <i class="bi bi-printer"></i> Print
                </a>
                <a href="{{ route('bills.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Bill Information and Bill Items Side by Side (Stacks on mobile) -->
        <div class="row g-3">
            <!-- Bill Information -->
            <div class="col-12 col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <i class="bi bi-info-circle me-2"></i>Bill Information
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6"><strong>Bill Number:</strong> {{ $bill->bill_number }}</div>
                            <div class="col-6"><strong>Customer:</strong> {{ $bill->customer->name }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Total Price:</strong> {{ number_format($bill->total_price, 2) }}
                            </div>
                            <div class="col-6"><strong>Advance Payment:</strong>
                                {{ number_format($bill->advance_payment, 2) }}
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Balance:</strong> {{ number_format($bill->balance, 2) }}</div>
                            <div class="col-6"><strong>Billed By:</strong> {{ $bill->user->name }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-12 col-md-6"><strong>Installment Payment:</strong>
                                {{ number_format($bill->installment_payment, 2) }} <b>x</b> {{ $bill->installments }}
                            </div>
                            <div class="col-12 col-md-6"><strong>Type:</strong>
                                @if ($bill->type == 1)
                                    Daily
                                @elseif ($bill->type == 2)
                                    Weekly
                                @else
                                    Monthly
                                @endif
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-12 col-md-6"><strong>Next Bill Date:</strong>
                                @if ($bill->balance != 0)
                                    {{ $bill->next_bill }}
                                @else
                                    <b class="text-danger">Bill Closed</b>
                                @endif
                            </div>
                            <div class="col-12 col-md-6"><strong>Next Payment:</strong>
                                {{ number_format($bill->next_payment, 2) }}
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-12 col-md-6"><strong>Bill Category:</strong>
                                {{ $bill->category == 0 ? 'Showroom Sale' : 'Van Sale' }}
                            </div>
                            <div class="col-12 col-md-6"><strong>Bill Created Date:</strong>
                                {{ $bill->created_at->format('Y-m-d') }}
                            </div>
                        </div>
                        @if (!empty($bill->guarantor_name))
                            <div class="row mb-2">
                                <div class="col-6"><strong>Guarantor:</strong> {{ $bill->guarantor_name }}</div>
                                <div class="col-6"><strong>Guarantor NIC:</strong> {{ $bill->guarantor_nic }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6"><strong>Guarantor Mobile:</strong> {{ $bill->guarantor_mobile }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bill Items -->
            <div class="col-12 col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <i class="bi bi-box me-2"></i>Bill Items
                    </div>
                    <div class="card-body p-0">
                        @if ($bill->items->isEmpty())
                            <p class="text-muted m-3">No items have been added to this bill.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table-bordered table-hover table align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="table-header text-center" width="60"><i
                                                    class="bi bi-hash me-2"></i>#</th>
                                            <th class="table-header"><i class="bi bi-box me-2"></i>Item Name</th>
                                            <th class="table-header text-end"><i
                                                    class="bi bi-currency-dollar me-2"></i>Price</th>
                                            <th class="table-header text-end"><i class="bi bi-123 me-2"></i>Quantity</th>
                                            <th class="table-header text-end"><i class="bi bi-calculator me-2"></i>Total
                                                Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bill->items as $index => $item)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>{{ $item->item_name }}</td>
                                                <td class="text-end">{{ number_format($item->item_price, 2) }}</td>
                                                <td class="text-end">{{ $item->item_quantity }}</td>
                                                <td class="text-end">{{ number_format($item->total_price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Schedule and Collections Side by Side (Stacks on mobile) -->
        <div class="row g-3">
            <!-- Payment Schedule -->
            <div class="col-12 col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <i class="bi bi-calendar-date me-2"></i>Payment Schedule
                    </div>
                    <div class="card-body p-0">
                        @if ($bill->paymentSchedules->isEmpty())
                            <p class="text-muted m-3">No scheduled payments found for this bill.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table-bordered table-hover table align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="table-header text-center" width="60"><i
                                                    class="bi bi-hash me-2"></i>#</th>
                                            <th class="table-header"><i class="bi bi-calendar me-2"></i>Payment Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bill->paymentSchedules as $index => $schedule)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>{{ $schedule->payment_date }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Collections -->
            <div class="col-12 col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <i class="bi bi-cash-stack me-2"></i>Collections
                    </div>
                    <div class="card-body p-0">
                        @if ($bill->collections->isEmpty())
                            <p class="text-muted m-3">No payments have been made yet.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table-bordered table-hover d-none d-md-table table align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="table-header"><i class="bi bi-currency-dollar me-2"></i>Amount</th>
                                            <th class="table-header"><i class="bi bi-credit-card me-2"></i>Type</th>
                                            <th class="table-header"><i class="bi bi-calendar me-2"></i>Date</th>
                                            <th class="table-header"><i class="bi bi-person me-2"></i>Added By</th>
                                            @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('ATH Admin'))
                                                <th class="table-header" width="150px"><i
                                                        class="bi bi-gear-fill me-2"></i>Actions</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bill->collections as $collection)
                                            <tr>
                                                <td>
                                                    @if (session('editing_collection') == $collection->id)
                                                        <form action="{{ route('collections.update', $collection) }}"
                                                            method="POST" class="d-flex align-items-center gap-1">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="number" name="payment"
                                                                value="{{ $collection->payment }}" step="0.01"
                                                                class="form-control form-control-sm" style="width: 90px;"
                                                                required>
                                                        @else
                                                            {{ number_format($collection->payment, 2) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('editing_collection') == $collection->id)
                                                        <select name="type" class="form-control form-control-sm"
                                                            required>
                                                            <option value="cash"
                                                                {{ $collection->type == 'cash' ? 'selected' : '' }}>
                                                                Cash</option>
                                                            <option value="card"
                                                                {{ $collection->type == 'card' ? 'selected' : '' }}>
                                                                Card</option>
                                                            <option value="online"
                                                                {{ $collection->type == 'online' ? 'selected' : '' }}>
                                                                Online</option>
                                                        </select>
                                                    @else
                                                        {{ ucfirst($collection->type) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('editing_collection') == $collection->id)
                                                        <input type="date" name="date"
                                                            value="{{ $collection->date }}"
                                                            class="form-control form-control-sm" required>
                                                    @else
                                                        {{ $collection->date }}
                                                    @endif
                                                </td>
                                                <td>{{ $collection->user->name }}</td>
                                                @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('ATH Admin'))
                                                    <td>
                                                        @if (session('editing_collection') == $collection->id)
                                                            <button type="submit"
                                                                class="btn btn-sm btn-success">Save</button>
                                                            </form>
                                                            <form action="{{ route('collections.cancelEdit') }}"
                                                                method="POST" style="display:inline;">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-secondary">Cancel</button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('collections.edit', $collection) }}"
                                                                method="POST" style="display:inline;">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-primary">Edit</button>
                                                            </form>
                                                            <form
                                                                action="{{ route('collections.destroy', $collection->id) }}"
                                                                method="POST" style="display:inline-block;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm"
                                                                    onclick="return confirm('Are you sure you want to delete this collection?')">Delete</button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Mobile Cards --}}
                            <div class="d-block d-md-none">
                                @foreach ($bill->collections as $collection)
                                    <div class="card mb-2">
                                        <div class="card-body px-3 py-2">
                                            <div class="d-flex justify-content-between">
                                                <span><strong>Amount:</strong></span>
                                                <span>
                                                    @if (session('editing_collection') == $collection->id)
                                                        <form action="{{ route('collections.update', $collection) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="number" name="payment"
                                                                value="{{ $collection->payment }}" step="0.01"
                                                                class="form-control form-control-sm mb-1" required>
                                                        @else
                                                            {{ number_format($collection->payment, 2) }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span><strong>Type:</strong></span>
                                                <span>
                                                    @if (session('editing_collection') == $collection->id)
                                                        <select name="type" class="form-control form-control-sm mb-1"
                                                            required>
                                                            <option value="cash"
                                                                {{ $collection->type == 'cash' ? 'selected' : '' }}>
                                                                Cash</option>
                                                            <option value="card"
                                                                {{ $collection->type == 'card' ? 'selected' : '' }}>
                                                                Card</option>
                                                            <option value="online"
                                                                {{ $collection->type == 'online' ? 'selected' : '' }}>
                                                                Online</option>
                                                        </select>
                                                    @else
                                                        {{ ucfirst($collection->type) }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span><strong>Date:</strong></span>
                                                <span>
                                                    @if (session('editing_collection') == $collection->id)
                                                        <input type="date" name="date"
                                                            value="{{ $collection->date }}"
                                                            class="form-control form-control-sm mb-1" required>
                                                    @else
                                                        {{ $collection->date }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span><strong>Added By:</strong></span>
                                                <span>{{ $collection->user->name }}</span>
                                            </div>
                                            @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('ATH Admin'))
                                                <div class="mt-2 text-end">
                                                    @if (session('editing_collection') == $collection->id)
                                                        <button type="submit"
                                                            class="btn btn-sm btn-success">Save</button>
                                                        </form>
                                                        <form action="{{ route('collections.cancelEdit') }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-secondary">Cancel</button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('collections.edit', $collection) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-primary">Edit</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Collection (commented out) -->
        {{-- Add Collection form here if needed --}}
    </div>

    <!-- Success Popup -->
    @if (session('success'))
        <div id="popup-overlay" class="popup-overlay">
            <div id="popup-message" class="popup-message">
                <p>{{ session('success') }}</p>
                <div class="popup-buttons d-flex gap-2">
                    <button id="print-bill" class="btn btn-info">
                        <i class="fas fa-print"></i> Print Receipt
                    </button>
                    <button id="close-popup" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('styles')
    <style>
        /* Popup Styles */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .popup-message {
            background: #fff;
            padding: 2rem 1.5rem;
            border-radius: 10px;
            text-align: center;
            min-width: 240px;
        }

        .popup-buttons button {
            margin: 0 8px;
        }

        @media (max-width: 767.98px) {
            h4 {
                font-size: 1.1rem;
            }

            .card-header {
                font-size: 1rem;
            }

            .popup-message {
                padding: 1.2rem 0.5rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById('close-popup')?.addEventListener('click', function() {
            // Hide the popup overlay when the close button is clicked
            document.getElementById('popup-overlay').style.display = 'none';
        });

        document.getElementById('print-bill')?.addEventListener('click', function() {
            // Redirect the user to the print page for the bill
            window.open("{{ route('bills.printCollection', $bill->id) }}", "_blank");
        });
    </script>
@endpush

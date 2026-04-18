@extends('layouts.app')

@section('title', 'All Bills Details')

@section('content')
    <div class="container">
        <div class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h1 class="mb-0 card-title"><i class="bi bi-file-earmark-text me-2"></i>All Bills</h1>
            <div class="mt-2 mt-md-0">
                <a href="{{ route('reports.allBillsExport', request()->all()) }}"
                    class="btn btn-outline-dark">
                    <i class="bi bi-download me-1"></i> Export Data to CSV
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="mb-4 card">
            <div class="card-header bg-light">
                <i class="bi bi-funnel me-2"></i>Filter Bills
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.allBills') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="user_id" class="form-label">User</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">-- All Users --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="type" class="form-label">Payment Method</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">-- All Types --</option>
                                @foreach ($types as $t)
                                    <option value="{{ $t }}" @selected(request('type') == $t)>
                                        {{ ucfirst($t) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="category" class="form-label">Sale Type</label>
                            <select name="category" id="category" class="form-select">
                                <option value="">-- All Types --</option>
                                <option value="0" @selected(request('category') === '0')>Showroom Sale</option>
                                <option value="1" @selected(request('category') === '1')>Van Sale</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="bill" class="form-label">Bill</label>
                            <input type="text" name="bill" id="bill" class="form-control"
                                value="{{ request('bill') }}" placeholder="Enter bill number">
                        </div>
                    </div>
                    <div class="mt-3 row">
                        <div class="col-md-12 d-flex justify-content-between align-items-center">
                            <div>
                                <!-- Applied Filters Display is handled separately -->
                            </div>
                            <div>
                                <button type="submit" class="btn btn-outline-dark me-2">
                                    <i class="bi bi-search me-1"></i>Apply Filters
                                </button>
                                <a href="{{ route('reports.allBills') }}" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Applied Filters Display -->
        @if (request()->anyFilled(['start_date', 'end_date', 'user_id', 'type', 'category', 'bill']))
            <div class="mb-3">
                <p class="mb-1"><strong>Active Filters:</strong></p>
                <div class="flex-wrap gap-2 d-flex">
                    @if (request('start_date'))
                        <span class="badge bg-info">Start Date: {{ request('start_date') }}</span>
                    @endif
                    @if (request('end_date'))
                        <span class="badge bg-info">End Date: {{ request('end_date') }}</span>
                    @endif
                    @if (request('user_id'))
                        <span class="badge bg-info">
                            User: {{ $users->firstWhere('id', request('user_id'))->name ?? 'Unknown' }}
                        </span>
                    @endif
                    @if (request('type'))
                        <span class="badge bg-info">Payment Method: {{ ucfirst(request('type')) }}</span>
                    @endif
                    @if (request()->filled('category'))
                        <span class="badge bg-info">
                            Sale Type: {{ request('category') == 0 ? 'Showroom Sale' : 'Van Sale' }}
                        </span>
                    @endif
                    @if (request('bill'))
                        <span class="badge bg-info">Bill: {{ request('bill') }}</span>
                    @endif
                </div>
            </div>
        @endif

        <!-- Data Table -->
        <div class="table-responsive">
            <table class="table align-middle table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="table-header">Bill No</th>
                        <th class="table-header">Sale Type</th>
                        <th class="table-header"><i class="bi bi-person me-2"></i>Customer</th>
                        <th class="table-header"><i class="bi bi-currency-dollar me-2"></i>Total Price</th>
                        <th class="table-header"><i class="bi bi-currency-dollar me-2"></i>Advance Payment</th>
                        <th class="table-header"><i class="bi bi-currency-dollar me-2"></i>Balance</th>
                        <th class="table-header"><i class="bi bi-credit-card me-2"></i>Payment Method</th>
                        <th class="table-header"><i class="bi bi-calendar me-2"></i>Installment</th>
                        <th class="table-header"><i class="bi bi-calendar-date me-2"></i>Date</th>
                        <th class="table-header"><i class="bi bi-person me-2"></i>User</th>
                        <th class="table-header"><i class="bi bi-box me-2"></i>Items</th>
                        <th class="table-header"><i class="bi bi-cash me-2"></i>Payments Made</th>
                        <th class="table-header"><i class="bi bi-calendar-check me-2"></i>Payment Schedule</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grandTotal = 0;
                        $grandAdvance = 0;
                        $grandBalance = 0;
                    @endphp
                    @forelse($bills as $bill)
                        @php
                            $grandTotal += $bill->total_price;
                            $grandAdvance += $bill->advance_payment;
                            $grandBalance += $bill->balance;
                        @endphp
                        <tr>
                            <td>
                                <b><a href="{{ route('bills.show', $bill->id) }}"
                                        class="text-reset text-decoration-none">{{ $bill->bill_number }}</a></b>
                            </td>
                            <td>
                                @if ($bill->category === 0 || $bill->category === '0')
                                    <span class="badge bg-primary">Showroom Sale</span>
                                @elseif($bill->category === 1 || $bill->category === '1')
                                    <span class="badge bg-success">Van Sale</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $bill->customer->name ?? '-' }}</td>
                            <td>{{ number_format($bill->total_price, 2) }}</td>
                            <td>{{ number_format($bill->advance_payment, 2) }}</td>
                            <td>{{ number_format($bill->balance, 2) }}</td>
                            <td>{{ ucfirst($bill->payment_type ?? '-') }}</td>
                            <td>
                                {{ number_format($bill->installment_payment, 2) }} x {{ $bill->installments }}
                            </td>
                            <td>{{ $bill->created_at->format('Y-m-d') }}</td>
                            <td>{{ $bill->user->name ?? '-' }}</td>
                            <td>
                                @if ($bill->items->isNotEmpty())
                                    <ul class="mb-0 ps-3">
                                        @foreach ($bill->items as $item)
                                            <li>{{ $item->item_name }} ({{ $item->item_quantity }} x
                                                {{ number_format($item->item_price, 2) }})</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">No Items</span>
                                @endif
                            </td>
                            <td>
                                @if ($bill->collections->isNotEmpty())
                                    <ul class="mb-0 ps-3">
                                        @foreach ($bill->collections as $collection)
                                            <li>
                                                {{ number_format($collection->payment, 2) }}
                                                ({{ ucfirst($collection->type) }})
                                                - {{ $collection->date }} by
                                                {{ $collection->user->name ?? '' }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">No Payments</span>
                                @endif
                            </td>
                            <td>
                                @if ($bill->paymentSchedules->isNotEmpty())
                                    <ul class="mb-0 ps-3">
                                        @foreach ($bill->paymentSchedules as $ps)
                                            <li>{{ $ps->payment_date }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">No Schedule</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center text-muted">No bills found for selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end">Total:</td>
                        <td>{{ number_format($grandTotal, 2) }}</td>
                        <td>{{ number_format($grandAdvance, 2) }}</td>
                        <td>{{ number_format($grandBalance, 2) }}</td>
                        <td colspan="8"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @media (max-width: 767.98px) {
            .table-responsive {
                font-size: 0.96rem;
            }
            .table th,
            .table td {
                padding: 0.5rem;
            }
        }
        .table {
            width: 100%;
            min-width: 1200px; /* Set a minimum width for the table */
        }
        .table-header {
            white-space: nowrap;
        }
    </style>
@endpush

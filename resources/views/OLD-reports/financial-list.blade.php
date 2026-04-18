@extends('layouts.app')

@section('title', 'Financial Data Report')

@section('content')
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
            <h1 class="card-title mb-0"><i class="bi bi-bar-chart-line me-2"></i>Financial Report</h1>
            <div class="d-flex flex-column flex-sm-row mt-md-0 mt-2 gap-2">
                <a href="{{ route('reports.printFinancialList', request()->all()) }}" class="btn btn-info" target="_blank">
                    <i class="bi bi-printer"></i> Print
                </a>
                <a href="{{ route('reports.exportFinancialList', request()->all()) }}" class="btn btn-outline-dark">
                    <i class="bi bi-download me-1"></i> Export CSV
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <i class="bi bi-funnel me-2"></i>Filter Financial Data
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.financialList') }}">
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
                            <label for="source" class="form-label">Source</label>
                            <select name="source" id="source" class="form-select">
                                <option value="">-- All --</option>
                                <option value="collection" @selected(request('source') == 'collection')>Collection Only</option>
                                <option value="bill" @selected(request('source') == 'bill')>Bill Only</option>
                                <option value="direct_bill" @selected(request('source') == 'direct_bill')>Direct Bill Only</option>
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
                    <div class="col-12">
                        <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2">
                            <button type="submit" class="btn btn-outline-dark">
                                <i class="bi bi-search me-1"></i>Apply Filters
                            </button>
                            <a href="{{ route('reports.financialList') }}" class="btn btn-outline-danger">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filtered summary info -->
        @if (request()->anyFilled(['start_date', 'end_date', 'user_id', 'type', 'payment_method', 'bill']))
            <div class="mb-3">
                <span class="badge bg-info">Filters:</span>
                @if (request('start_date'))
                    <span class="badge bg-secondary">Start Date: {{ request('start_date') }}</span>
                @endif
                @if (request('end_date'))
                    <span class="badge bg-secondary">End Date: {{ request('end_date') }}</span>
                @endif
                @if (request('user_id'))
                    <span class="badge bg-secondary">
                        User: {{ $users->firstWhere('id', request('user_id'))->name ?? 'Unknown' }}
                    </span>
                @endif
                @if (request('type'))
                    <span class="badge bg-secondary">Payment Method : {{ ucfirst(request('type')) }}</span>
                @endif
                @if (request('source'))
                    <span class="badge bg-secondary">
                        Source:
                        @if (request('source') == 'collection')
                            Collection Only
                        @elseif (request('source') == 'bill')
                            Bill Only
                        @elseif (request('source') == 'direct_bill')
                            Direct Bill Only
                        @endif
                    </span>
                @endif
                @if (request()->filled('category'))
                    <span class="badge bg-secondary">
                        Sale Type: {{ request('category') == 0 ? 'Showroom Sale' : 'Van Sale' }}
                    </span>
                @endif
                @if (request('bill'))
                    <span class="badge bg-secondary">Bill: {{ request('bill') }}</span>
                @endif
            </div>
        @endif

        <!-- Data Table -->
        <div class="table-responsive">
            <table class="table-bordered table-hover mb-0 table align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="table-header"><i class="bi bi-diagram-3 me-2"></i>Source</th>
                        <th class="table-header"><i class="bi bi-tag me-2"></i>Sale Type</th>
                        <th class="table-header"><i class="bi bi-file-text me-2"></i>Bill No</th>
                        <th class="table-header"><i class="bi bi-person me-2"></i>Customer</th>
                        <th class="table-header"><i class="bi bi-currency-dollar me-2"></i>Amount</th>
                        <th class="table-header"><i class="bi bi-credit-card me-2"></i>Payment Method</th>
                        <th class="table-header"><i class="bi bi-calendar-date me-2"></i>Date</th>
                        <th class="table-header"><i class="bi bi-person-badge me-2"></i>User</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; @endphp
                    @forelse($allData as $row)
                        @php $grandTotal += $row['amount']; @endphp
                        <tr>
                            <td>
                                @if ($row['source'] === 'collection')
                                    <span class="badge bg-info">Collection</span>
                                @elseif ($row['source'] === 'direct_bill')
                                    <span class="badge bg-success">Direct Bill</span>
                                @else
                                    <span class="badge bg-warning">Bill</span>
                                @endif
                            </td>
                            <td>
                                @if ($row['category'] === 0 || $row['category'] === '0')
                                    @if ($row['source'] === 'direct_bill')
                                        @if ($row['user']?->hasRole('Showroom User'))
                                            <span class="badge bg-primary">Showroom 1</span>
                                        @else
                                            <span class="badge bg-info">Showroom 2</span>
                                        @endif
                                    @else
                                        <span class="badge bg-primary">Showroom Sale</span>
                                    @endif
                                @elseif($row['category'] === 1 || $row['category'] === '1')
                                    <span class="badge bg-success">Van Sale</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if ($row['bill_number'])
                                    <b>
                                        @if ($row['source'] === 'direct_bill')
                                            {{ $row['bill_number'] }}
                                        @else
                                            <a href="{{ route('bills.show', $row['bill_id']) }}"
                                                class="text-reset text-decoration-none">{{ $row['bill_number'] }}</a>
                                        @endif
                                    </b>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $row['customer_name'] ?? '-' }}</td>
                            <td>{{ number_format($row['amount'], 2) }}</td>
                            <td>{{ ucfirst($row['payment_method'] ?? 'N/A') }}</td>
                            <td>{{ $row['date'] ?? '-' }}</td>
                            <td>{{ $row['user_name'] ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-muted text-center">No data found for selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="4" class="text-end">Total:</td>
                        <td>{{ number_format($grandTotal, 2) }}</td>
                        <td colspan="3"></td>
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
    </style>
@endpush

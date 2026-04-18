@extends('layouts.app')

@section('title', 'Tyre Repair Status Report')

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <h1 class="h3 text-primary fw-bold mb-0"><i class="bi bi-tools me-2"></i>Tyre Repair Status Report</h1>

            <a href="{{ route('reports.tyre_repairs') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-clockwise me-1"></i> Reset Filters
            </a>
        </div>

        <div class="card mb-4 border-0 bg-white shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('reports.tyre_repairs') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Customer</label>
                            <select name="customer_id" class="form-select">
                                <option value="">All Customers</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ $customerId == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold text-uppercase">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="sent" {{ $status == 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="received" {{ $status == 'received' ? 'selected' : '' }}>Received</option>
                                <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold text-uppercase">Date From</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold text-uppercase">Date To</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-filter me-1"></i> Filter
                            </button>
                            <button type="submit" formaction="{{ route('reports.tyre_repairs.export') }}" class="btn btn-success"
                                title="Download Excel">
                                <i class="bi bi-file-earmark-excel me-1"></i> Export
                            </button>
                        </div>
                    </div>
                    <br>
                    <div class="d-flex align-items-center gap-2">
                        <label for="per_page" class="form-label mb-0">
                            <i class="fas fa-list"></i> Per Page
                        </label>

                        <select name="per_page" id="per_page" class="form-select form-select-sm w-auto"
                            onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10
                            </option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25
                            </option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100
                            </option>
                            <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500
                            </option>
                        </select>
                    </div>
                </form>

                @if ($status || $customerId || $startDate || $endDate)
                    <div class="border-top d-flex align-items-center mt-3 flex-wrap gap-2 pt-3">
                        <span class="text-muted small fw-bold me-2">Active Filters:</span>

                        @if ($status)
                            @php
                                $statusColor = match ($status) {
                                    'completed' => 'success',
                                    'received' => 'info',
                                    'sent' => 'warning',
                                    default => 'secondary',
                                };
                            @endphp
                            <span
                                class="badge bg-{{ $statusColor }} text-{{ $status == 'sent' || $status == 'received' ? 'dark' : 'white' }}">
                                <i class="bi bi-info-circle me-1"></i> {{ ucfirst($status) }}
                            </span>
                        @endif

                        @if ($customerId)
                            <span class="badge bg-secondary">
                                <i class="bi bi-person me-1"></i>
                                {{ $customers->firstWhere('id', $customerId)->name ?? 'Unknown' }}
                            </span>
                        @endif

                        @if ($startDate)
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-calendar-event me-1"></i> From:
                                {{ \Carbon\Carbon::parse($startDate)->format('M d') }}
                            </span>
                        @endif

                        @if ($endDate)
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-calendar-event me-1"></i> To:
                                {{ \Carbon\Carbon::parse($endDate)->format('M d') }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('reports.tyre_repairs', ['status' => 'pending']) }}" class="text-decoration-none">
                    <div
                        class="card h-100 border-start border-secondary {{ $status == 'pending' ? 'bg-secondary-subtle' : 'bg-white' }} border-0 border-4 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted text-uppercase small fw-bold mb-1">Pending (In Shop)</h6>
                                    <h2 class="fw-bold text-secondary mb-0">{{ $stats['pending'] }}</h2>
                                </div>
                                <i class="bi bi-shop fs-1 text-secondary opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('reports.tyre_repairs', ['status' => 'sent']) }}" class="text-decoration-none">
                    <div
                        class="card h-100 border-start border-warning {{ $status == 'sent' ? 'bg-warning-subtle' : 'bg-white' }} border-0 border-4 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted text-uppercase small fw-bold mb-1">Sent to Company</h6>
                                    <h2 class="fw-bold text-warning mb-0">{{ $stats['sent'] }}</h2>
                                </div>
                                <i class="bi bi-truck fs-1 text-warning opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('reports.tyre_repairs', ['status' => 'received']) }}" class="text-decoration-none">
                    <div
                        class="card h-100 border-start border-info {{ $status == 'received' ? 'bg-info-subtle' : 'bg-white' }} border-0 border-4 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted text-uppercase small fw-bold mb-1">Received (Ready)</h6>
                                    <h2 class="fw-bold text-info mb-0">{{ $stats['received'] }}</h2>
                                </div>
                                <i class="bi bi-box-seam fs-1 text-info opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('reports.tyre_repairs', ['status' => 'completed']) }}" class="text-decoration-none">
                    <div
                        class="card h-100 border-start border-success {{ $status == 'completed' ? 'bg-success-subtle' : 'bg-white' }} border-0 border-4 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted text-uppercase small fw-bold mb-1">Completed (Issued)</h6>
                                    <h2 class="fw-bold text-success mb-0">{{ $stats['completed'] }}</h2>
                                </div>
                                <i class="bi bi-check-circle fs-1 text-success opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center bg-white py-3">
                <h6 class="fw-bold text-secondary mb-0">
                    <i class="bi bi-list-ul me-2"></i>Job List
                </h6>
                <span class="badge bg-light text-dark border">
                    {{ count($repairs) }} Jobs Found
                </span>
            </div>
            <div class="table-responsive">
                <table class="table-hover mb-0 table align-middle">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4">Item #</th>
                            <th>Job #</th>
                            <th>Customer</th>
                            <th>Tyre Info</th>
                            <th>Status / Date</th>
                            <th class="pe-4 text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($repairs as $repair)
                            <tr>
                                <td class="fw-bold text-primary ps-4">{{ $repair->item_number }}</td>
                                <td>
                                    @if ($repair->job_number)
                                        <span class="badge bg-light text-dark border">{{ $repair->job_number }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $repair->customer->name }}</div>
                                    <small class="text-muted">{{ $repair->customer->mobile }}</small>
                                </td>
                                <td>
                                    <div>{{ $repair->tyre_size }} <span class="text-muted small">|</span>
                                        {{ $repair->tyre_make }}</div>
                                    <small class="text-muted">No: {{ $repair->tyre_number }}</small>
                                </td>
                                <td>
                                    @if ($repair->issued_date)
                                        <span
                                            class="badge bg-success-subtle text-success border-success-subtle border px-2">Completed</span>
                                        <div class="small text-muted mt-1">Issued:
                                            {{ \Carbon\Carbon::parse($repair->issued_date)->format('Y-m-d') }}</div>
                                    @elseif($repair->received_from_company_date)
                                        <span
                                            class="badge bg-info-subtle text-info-emphasis border-info-subtle border px-2">Ready</span>
                                        <div class="small text-muted mt-1">Rcvd:
                                            {{ \Carbon\Carbon::parse($repair->received_from_company_date)->format('Y-m-d') }}
                                        </div>
                                    @elseif($repair->sent_date)
                                        <span
                                            class="badge bg-warning-subtle text-warning-emphasis border-warning-subtle border px-2">Sent</span>
                                        <div class="small text-muted mt-1">Sent:
                                            {{ \Carbon\Carbon::parse($repair->sent_date)->format('Y-m-d') }}</div>
                                    @else
                                        <span
                                            class="badge bg-secondary-subtle text-secondary border-secondary-subtle border px-2">Pending</span>
                                        <div class="small text-muted mt-1">In:
                                            {{ \Carbon\Carbon::parse($repair->received_date)->format('Y-m-d') }}</div>
                                    @endif
                                </td>
                                <td class="fw-bold pe-4 text-end">
                                    @if ($repair->amount)
                                        Rs. {{ number_format($repair->amount, 2) }}
                                    @else
                                        <span class="text-muted fw-normal">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-5 text-center">
                                    <div class="text-muted mb-2 opacity-50">
                                        <i class="bi bi-clipboard-x fs-1"></i>
                                    </div>
                                    <p class="text-muted mb-0">No repair jobs found matching the current filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                 <div class="d-flex justify-content-end mt-3">
                    {{ $repairs->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

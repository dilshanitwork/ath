@extends('layouts.app')

@section('title', 'Tyre Repairs')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-tools me-2"></i>Tyre Repairs</h1>
            <a href="{{ route('tyre_repairs.create') }}" class="btn btn-outline-dark mt-md-0 mt-2">
                <i class="bi bi-plus-circle me-1"></i> New Repair Job
            </a>
            <a href="{{ route('tyre_repairs.create_multiple') }}" class="btn btn-outline-secondary mt-md-0 mt-2">
                <i class="bi bi-plus-circle me-1"></i> New Bulk Repair Jobs
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('tyre_repairs.index') }}" method="GET" class="mt-3">
                    <div class="row align-items-start mb-3">
                        <div class="col-12 col-lg-8">
                            <h5 class="card-title mb-2">
                                <i class="bi bi-funnel me-1"></i>Filter Repairs
                            </h5>

                            {{-- Active Filters Display --}}
                            @if (request('search') ||
                                    request('status') ||
                                    request('job_number') ||
                                    request('received_from') ||
                                    request('received_to') ||
                                    request('customer_name'))
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2" style="font-size: 0.9rem;">Active Filters:</h6>
                                    <div class="d-flex align-items-center flex-wrap gap-1">
                                        @if (request('customer_name'))
                                            <span class="badge bg-primary me-1">Customer:
                                                {{ ucwords(str_replace('_', ' ', request('customer_name'))) }}</span>
                                        @endif
                                        @if (request('status'))
                                            <span class="badge bg-info text-dark me-1">Status:
                                                {{ ucwords(str_replace('_', ' ', request('status'))) }}</span>
                                        @endif
                                        @if (request('job_number'))
                                            <span class="badge bg-secondary me-1">Job #: {{ request('job_number') }}</span>
                                        @endif
                                        @if (request('received_from'))
                                            <span class="badge bg-light text-dark me-1 border">From:
                                                {{ request('received_from') }}</span>
                                        @endif
                                        @if (request('received_to'))
                                            <span class="badge bg-light text-dark me-1 border">To:
                                                {{ request('received_to') }}</span>
                                        @endif
                                        <a href="{{ route('tyre_repairs.index') }}"
                                            class="btn btn-sm btn-outline-danger text-decoration-none ms-2">
                                            <i class="bi bi-x-circle me-1"></i> Clear All
                                        </a>
                                    </div>
                                </div>
                            @endif

                        </div>
                        <div class="col-12 col-lg-4 d-flex justify-content-lg-end mt-lg-0 mt-3">
                            <div class="d-flex align-items-end gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <label for="per_page" class="form-label mb-0">
                                        <i class="fas fa-list"></i> Per Page
                                    </label>
                                    <select name="per_page" id="per_page" class="form-select-sm form-select w-auto"
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
                                {{-- @if (request()->has('per_page') && (int) request('per_page') > 10)
                                    <div>
                                        <label class="form-label mb-1 d-block">&nbsp;</label>
                                        <a href="{{ route('tyre_repairs.index', request()->except('per_page', 'page')) }}"
                                            class="btn btn-outline-danger">
                                            <i class="bi bi-x-circle-fill me-2"></i> Reset
                                        </a>
                                    </div>
                                @endif --}}

                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="customer_input" class="form-label">Customer</label>
                            <input type="text" class="form-control" name="customer_name" id="customer_input"
                                list="customerOptions" placeholder="Type to search..."
                                value="{{ request('customer_name') }}" autocomplete="off">
                            <datalist id="customerOptions">
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->name }}">{{ $customer->mobile }}</option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="sent_to_company"
                                    {{ request('status') == 'sent_to_company' ? 'selected' : '' }}>Sent to Company</option>
                                <option value="received_from_company"
                                    {{ request('status') == 'received_from_company' ? 'selected' : '' }}>Available</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                    Completed</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="job_number" class="form-label">Job Number</label>
                            <input type="text" class="form-control" name="job_number" id="job_number"
                                placeholder="Search Job #" value="{{ request('job_number') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="received_from" class="form-label">Received From</label>
                            <input type="date" class="form-control" name="received_from" id="received_from"
                                value="{{ request('received_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="received_to" class="form-label">Received To</label>
                            <input type="date" class="form-control" name="received_to" id="received_to"
                                value="{{ request('received_to') }}">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button class="btn btn-primary w-100" type="submit" title="Apply Filters">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-bordered table-hover table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Item #</th>
                        <th>Customer</th>
                        <th>Date Rcvd</th>
                        <th>Tyre Info</th>
                        <th>Job #</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($repairs as $repair)
                        <tr>
                            <td class="fw-bold"><a href="{{ route('tyre_repairs.show', $repair) }}"
                                    class="text-decoration-none">{{ $repair->item_number }}</a></td>
                            <td>
                                <div>{{ $repair->customer->name }}</div>
                                <small class="text-muted">{{ $repair->customer->mobile }}</small>
                            </td>
                            <td>{{ $repair->received_date ? \Carbon\Carbon::parse($repair->received_date)->format('Y-m-d') : '-' }}
                            </td>
                            <td>
                                <div class="small">
                                    @if ($repair->tyre_size)
                                        <span class="text-muted">Size:</span> {{ $repair->tyre_size }}<br>
                                    @endif
                                    @if ($repair->tyre_make)
                                        <span class="text-muted">Make:</span> {{ $repair->tyre_make }}
                                    @endif
                                </div>
                            </td>
                            <td>{{ $repair->job_number ?? '-' }}</td>
                            <td>
                                @if ($repair->status === 4)
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    @if ($repair->issued_date)
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($repair->received_from_company_date)
                                        <span class="badge bg-info text-dark">Available</span>
                                    @elseif($repair->sent_date)
                                        <span class="badge bg-warning text-dark">Sent to Company</span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                @endif
                            </td>
                            <td class="text-nowrap text-end">
                                <a href="{{ route('tyre_repairs.show', $repair) }}" class="btn btn-info btn-sm"
                                    title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('tyre_repairs.edit', $repair) }}" class="btn btn-warning btn-sm"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted py-4 text-center">No repair jobs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            {{ $repairs->appends(request()->query())->links() }}
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <h4 class="mb-3"><i class="fas fa-users"></i> Employees</h4>
            <a href="{{ route('employees.create') }}" class="btn btn-sm btn-create">
                <i class="fas fa-plus"></i> Add New Employee
            </a>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Filters Collapse Button -->
        <div class="mb-3">
            <button class="btn btn-outline-secondary w-100 d-md-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#filtersCollapse" aria-expanded="false" aria-controls="filtersCollapse">
                <i class="fas fa-filter"></i> View Filter Options
            </button>
        </div>

        <!-- Filters Form -->
        <div id="filtersCollapse" class="collapse d-md-block">
            <form action="{{ route('employees.index') }}" method="GET" class="mb-4">
                <div class="row g-2 align-items-end">
                    <!-- Search Field -->
                    <div class="col-md-4">
                        <label for="search" class="form-label"><i class="fas fa-search"></i> Search</label>
                        <input type="text" name="search" id="search" class="form-control"
                            placeholder="Search employees..." value="{{ request('search') }}">
                    </div>

                    <!-- Start Date Field -->
                    <div class="col-md-3">
                        <label for="start_date" class="form-label"><i class="fas fa-calendar-alt"></i> Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="{{ request('start_date') }}">
                    </div>

                    <!-- End Date Field -->
                    <div class="col-md-3">
                        <label for="end_date" class="form-label"><i class="fas fa-calendar-alt"></i> End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                            value="{{ request('end_date') }}">
                    </div>

                    <!-- Filter Buttons -->
                    <div class="col-md-2 d-flex justify-content-end">
                        <button type="submit" class="btn btn-search me-2">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Filter Details -->
        @if (request('search') || request('start_date') || request('end_date'))
            <div class="mb-4">
                <p><strong>Active Filters:</strong></p>
                <ul class="list-inline">
                    @if (request('search'))
                        <li class="list-inline-item">
                            <span class="badge bg-info text-white">Search: "{{ request('search') }}"</span>
                        </li>
                    @endif
                    @if (request('start_date'))
                        <li class="list-inline-item">
                            <span class="badge bg-secondary text-white">Start Date: {{ request('start_date') }}</span>
                        </li>
                    @endif
                    @if (request('end_date'))
                        <li class="list-inline-item">
                            <span class="badge bg-secondary text-white">End Date: {{ request('end_date') }}</span>
                        </li>
                    @endif
                </ul>
            </div>
        @endif

        <!-- Employees Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="table-header">ID</th>
                        <th class="table-header"><i class="fas fa-user"></i> Name</th>
                        <th class="table-header"><i class="fas fa-phone"></i> Mobile</th>
                        <th class="table-header"><i class="fas fa-calendar-alt"></i> Joined Date</th>
                        <th class="table-header text-end"><i class="fas fa-tools"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>{{ $employee->id }}</td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->mobile }}</td>
                            <td>{{ $employee->joined_date }}</td>
                            <td class="text-end">
                                            <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="d-flex justify-content-center mt-4">
            {{ $employees->appends(request()->all())->links() }}
        </div>
    </div>
@endsection

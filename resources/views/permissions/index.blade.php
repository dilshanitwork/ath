@extends('layouts.app')

@section('title', 'Permissions')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h1 class="mb-0 card-title"><i class="bi bi-key me-2"></i>Permissions</h1>
            <a href="{{ route('permissions.create') }}" class="mt-2 btn btn-outline-dark mt-md-0">
                <i class="bi bi-plus-circle me-1"></i> Create New Permission
            </a>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Filter Section -->
        <div class="mb-4 card">
            <div class="card-header bg-light">
                <i class="bi bi-funnel me-2"></i>Filter Permissions
            </div>
            <div class="card-body">
                <form action="{{ route('permissions.index') }}" method="GET">
                    <div class="row g-3">
                        <!-- Search Field -->
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Search permissions..." value="{{ request('search') }}">
                        </div>

                        <!-- Start Date Field -->
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ request('start_date') }}">
                        </div>

                        <!-- End Date Field -->
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="{{ request('end_date') }}">
                        </div>
                    </div>

                    <div class="mt-3 row">
                        <div class="col-md-12 d-flex justify-content-between align-items-center">
                            <div>
                                <!-- Applied Filters Display -->
                                @if (request()->filled('search') || request()->filled('start_date') || request()->filled('end_date'))
                                    <div>
                                        <p class="mb-1"><strong>Active Filters:</strong></p>
                                        <div class="flex-wrap gap-2 d-flex">
                                            @if (request()->filled('search'))
                                                <span class="badge bg-info">Search: {{ request('search') }}</span>
                                            @endif
                                            @if (request()->filled('start_date'))
                                                <span class="badge bg-info">Start Date: {{ request('start_date') }}</span>
                                            @endif
                                            @if (request()->filled('end_date'))
                                                <span class="badge bg-info">End Date: {{ request('end_date') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-12 col-md-12">
                                <div class="d-flex align-items-center justify-content-between gap-2">

                                    {{-- LEFT: Per Page --}}
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

                                    {{-- RIGHT: Action buttons --}}
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-outline-dark btn-sm">
                                            <i class="bi bi-search me-1"></i> Apply Filters
                                        </button>

                                        <a href="{{ route('permissions.index') }}" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center"
                                       style="min-width: 140px;">
                                            <i class="bi bi-x-circle me-1"></i> Reset
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Permissions Table -->
        <div class="table-responsive">
            <table class="table align-middle table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="table-header"><i class="bi bi-hash me-2"></i>ID</th>
                        <th class="table-header"><i class="bi bi-tag me-2"></i>Name</th>
                        <th class="table-header text-end"><i class="bi bi-gear-fill"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permissions as $permission)
                        <tr>
                            <td>{{ $permission->id }}</td>
                            <td><b>{{ $permission->name }}</b></td>
                            <td class="text-nowrap text-end">
                                <a href="{{ route('permissions.show', $permission) }}" class="btn btn-info btn-sm" title="View Permission">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No permissions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4 d-flex justify-content-center">
            {{ $permissions->appends(request()->all())->links() }}
        </div>
    </div>
@endsection
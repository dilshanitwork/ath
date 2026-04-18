@extends('layouts.app')

@section('title', 'Roles')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h1 class="mb-0 card-title"><i class="bi bi-shield-check me-2"></i>Roles</h1>
            <a href="{{ route('roles.create') }}" class="mt-2 btn btn-outline-dark mt-md-0">
                <i class="bi bi-plus-circle me-1"></i> Create New Role
            </a>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Filter and Search Section -->
        <div class="mb-4 card">
            <div class="card-header bg-light">
                <i class="bi bi-funnel me-2"></i>Filter Roles
            </div>
            <div class="card-body">
                <form action="{{ route('roles.index') }}" method="GET">
                    <div class="row g-3">
                        <!-- Search Field -->
                        <div class="col-md-6">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                placeholder="Search roles..." value="{{ request('search') }}">
                        </div>

                        <!-- Sort Options -->
                        <div class="col-md-6">
                            <label for="sort" class="form-label">Sort By</label>
                            <select name="sort" id="sort" class="form-control">
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3 row">
                        <div class="col-md-12 d-flex justify-content-between align-items-center">
                            <div>
                                <!-- Applied Filters Display -->
                                @if (request()->filled('search'))
                                    <div>
                                        <p class="mb-1"><strong>Active Filters:</strong></p>
                                        <div class="flex-wrap gap-2 d-flex">
                                            <span class="badge bg-info">Search: {{ request('search') }}</span>
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

                                        <a href="{{ route('roles.index') }}" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center"
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

        <!-- Roles Table -->
        <div class="table-responsive">
            <table class="table align-middle table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="table-header"><i class="bi bi-hash me-2"></i>ID</th>
                        <th class="table-header"><i class="bi bi-shield-check me-2"></i>Name</th>
                        <th class="table-header text-end"><i class="bi bi-gear-fill"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td><b>{{ $role->name }}</b></td>
                            <td class="text-nowrap text-end">
                                <a href="{{ route('roles.show', $role) }}" class="btn btn-info btn-sm" title="View Role">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No roles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4 d-flex justify-content-center">
            {{ $roles->appends(request()->all())->links() }}
        </div>
    </div>
@endsection
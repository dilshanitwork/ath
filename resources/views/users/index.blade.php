@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h1 class="mb-0 card-title"><i class="bi bi-people me-2"></i>Users</h1>
            <a href="{{ route('users.create') }}" class="mt-2 btn btn-outline-dark mt-md-0">
                <i class="bi bi-plus-circle me-1"></i> Create New User
            </a>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Filter Section -->
        <div class="mb-4 card">
            <div class="card-header bg-light">
                <i class="bi bi-funnel me-2"></i>Filter Users
            </div>
            <div class="card-body">
                <form action="{{ route('users.index') }}" method="GET">
                    <div class="row g-3">
                        <!-- Search Field -->
                        <div class="col-12 col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" name="search" id="search" class="form-control"
                                value="{{ request('search') }}" placeholder="Search users...">
                        </div>

                        <!-- Start Date Field -->
                        <div class="col-12 col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ request('start_date') }}">
                        </div>

                        <!-- End Date Field -->
                        <div class="col-12 col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="{{ request('end_date') }}">
                        </div>
                    </div>

                    <div class="mt-3 row">
                        <div class="gap-2 col-12 d-flex flex-column flex-md-row justify-content-between align-items-stretch">
                            <div class="mb-2 mb-md-0">
                                @if (request()->filled('start_date'))
                                    <span class="mb-1 badge bg-secondary mb-md-0">Start Date: {{ request('start_date') }}</span>
                                @endif
                                @if (request()->filled('end_date'))
                                    <span class="mb-1 badge bg-secondary mb-md-0">End Date: {{ request('end_date') }}</span>
                                @endif
                                @if (request()->filled('search'))
                                    <span class="badge bg-danger me-1">Search Term:</span>
                                    <span class="badge bg-danger">{{ request('search') }}</span>
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

                                        <a href="{{ route('users.index') }}" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center"
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

        <!-- Users Table -->
        <div class="table-responsive">
            <table class="table align-middle table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="table-header"><i class="bi bi-hash me-2"></i>ID</th>
                        <th class="table-header"><i class="bi bi-person me-2"></i>Name</th>
                        <th class="table-header"><i class="bi bi-envelope me-2"></i>Email</th>
                        <th class="table-header"><i class="bi bi-tags me-2"></i>Roles</th>
                        <th class="table-header text-end"><i class="bi bi-gear-fill me-2"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td><b>{{ $user->name }}</b></td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if ($user->roles->isNotEmpty())
                                    @foreach ($user->roles as $role)
                                        <span class="badge bg-primary">{{ $role->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">No Roles Assigned</span>
                                @endif
                            </td>
                            <td class="text-nowrap text-end">
                                <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-sm" title="View User">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('users.edit-password', $user) }}" class="btn btn-warning btn-sm" title="Update Password">
                                    <i class="bi bi-key"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No users found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4 overflow-auto d-flex justify-content-center">
            {{ $users->appends(request()->all())->links() }}
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add data-label attribute for mobile table view
        if (window.innerWidth < 768) {
            const headers = Array.from(document.querySelectorAll('.table thead th')).map(th => th.innerText.trim());
            document.querySelectorAll('.table tbody tr').forEach(tr => {
                tr.querySelectorAll('td').forEach((td, i) => {
                    td.setAttribute('data-label', headers[i] || '');
                });
            });
        }
    });
</script>

    <style>
    /* Responsive table styles */
    @media (max-width: 767.98px) {
        .table-responsive {
            font-size: 0.95rem;
        }

        .table thead {
            display: none;
        }

        .table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .5rem 0;
            border: none;
        }

        .table tbody td:before {
            content: attr(data-label);
            flex-basis: 45%;
            font-weight: 700;
            color: #495057;
            text-align: left;
        }

        .table tbody td:last-child {
            justify-content: flex-end;
        }

        .table tbody td:last-child .btn {
    margin-left: 0.5rem;
}
    }
</style>

@endsection

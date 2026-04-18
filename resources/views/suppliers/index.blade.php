@extends('layouts.app')
@section('title', 'All Suppliers')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-truck me-2"></i>Suppliers</h1>
            <a href="{{ route('suppliers.create') }}" class="btn btn-outline-dark">
                <i class="bi bi-plus-circle me-1"></i> Add New Supplier
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('suppliers.index') }}" method="GET" class="row g-3 align-items-center">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search"
                                placeholder="Search by Name, Contact Person, Phone or Email..."
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-center flex-nowrap">
                        <label for="per_page" class="form-label mb-0 me-2">
                            <i class="fas fa-list me-1"></i> Per Page
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
                    <div class="col-md-2">
                        <div class="d-grid d-md-flex justify-content-md-end gap-2">
                            <button type="submit" class="btn btn-outline-dark w-100">Search</button>
                            @if (request('search'))
                                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-bordered table-hover table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Contact Person</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->id }}</td>
                            <td class="fw-bold">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="text-decoration-none text-dark">
                                    {{ $supplier->name }}
                                </a>
                            </td>
                            <td>{{ $supplier->contact_person ?? 'N/A' }}</td>
                            <td>{{ $supplier->email ?? 'N/A' }}</td>
                            <td>{{ $supplier->phone ?? 'N/A' }}</td>
                            <td class="text-nowrap text-end">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-info btn-sm"
                                    title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning btn-sm"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted py-4 text-center">No suppliers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
            {{ $suppliers->appends(request()->query())->links() }}
        </div>
    </div>
@endsection

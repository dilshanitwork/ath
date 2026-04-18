@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <h4 class="mb-3"><i class="fas fa-box"></i> Items</h4>
            <a href="{{ route('items.create') }}" class="btn btn-sm btn-create">
                <i class="fas fa-plus"></i> Add New Item
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
            <form action="{{ route('items.index') }}" method="GET" class="mb-4">
                <div class="row g-2 align-items-end">
                    <!-- Search Field -->
                    <div class="col-md-4">
                        <label for="search" class="form-label"><i class="fas fa-search"></i> Search</label>
                        <input type="text" name="search" id="search" class="form-control"
                            placeholder="Search Items..." value="{{ request('search') }}">
                    </div>

                    <!-- Filter Buttons -->
                    <div class="col-md-2 d-flex justify-content-end">
                        <button type="submit" class="btn btn-search me-2">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Filter Details -->
        @if (request('search'))
            <div class="mb-4">
                <p><strong>Active Filters:</strong></p>
                <ul class="list-inline">
                    @if (request('search'))
                        <li class="list-inline-item">
                            <span class="badge bg-info text-white">Search: "{{ request('search') }}"</span>
                        </li>
                    @endif
                </ul>
            </div>
        @endif

        <!-- Items Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="table-header">ID</th>
                        <th class="table-header"><i class="fas fa-box"></i> Name</th>
                        <th class="table-header"><i class="fas fa-tag"></i> Price</th>
                        <th class="table-header"><i class="fas fa-cubes"></i> Quantity</th>
                        <th class="table-header"><i class="fas fa-user"></i> Customer Name</th>
                        <th class="table-header text-end"><i class="fas fa-tools"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->price }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->customer->name }}</td>
                            <td class="text-end">
                                <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="d-flex justify-content-center mt-4">
            {{ $items->appends(request()->all())->links() }}
        </div>
    </div>
@endsection
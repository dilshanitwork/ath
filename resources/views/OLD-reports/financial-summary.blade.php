@extends('layouts.app')

@section('title', 'Financial Summary Report')

@section('content')
    <div class="container">
        <div class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h1 class="mb-0 card-title"><i class="bi bi-graph-up me-2"></i>Financial Summary</h1>
            <a href="{{ route('home') }}" class="mt-2 btn btn-outline-dark mt-md-0">
                <i class="bi bi-house-door me-1"></i> Back to Home
            </a>
        </div>

        <!-- Filter Form -->
        <div class="mb-4 card">
            <div class="card-header bg-light">
                <i class="bi bi-funnel me-2"></i>Filter Collections
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.financialSummary') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ $startDate ?? \Carbon\Carbon::now()->toDateString() }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="{{ $endDate ?? \Carbon\Carbon::now()->toDateString() }}">
                        </div>
                        <div class="col-md-3">
                            <label for="user_id" class="form-label">User</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">-- All Users --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @if ($userId == $user->id) selected @endif>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">-- All Types --</option>
                                @foreach ($types as $t)
                                    <option value="{{ $t }}" @if ($type == $t) selected @endif>
                                        {{ ucfirst($t) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-3 row">
                        <div class="col-md-12 d-flex justify-content-end align-items-center">
                            <button type="submit" class="btn btn-outline-dark me-2">
                                <i class="bi bi-search me-1"></i>Apply Filters
                            </button>
                            <a href="{{ route('reports.financialSummary') }}" class="btn btn-outline-danger">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filtered summary info -->
        @if ($startDate || $endDate || $userId || $type)
            <div class="mb-4">
                <span class="badge bg-info">Filters:</span>
                @if ($startDate)
                    <span class="badge bg-secondary">Start Date: {{ $startDate }}</span>
                @endif
                @if ($endDate)
                    <span class="badge bg-secondary">End Date: {{ $endDate }}</span>
                @endif
                @if ($userId)
                    <span class="badge bg-secondary">
                        User: {{ $users->firstWhere('id', $userId)->name ?? 'Unknown' }}
                    </span>
                @endif
                @if ($type)
                    <span class="badge bg-secondary">Type: {{ ucfirst($type) }}</span>
                @endif
            </div>
        @endif

        <div class="row g-3 row-cols-1 row-cols-md-2 row-cols-xl-5">
            <div class="col">
                <div class="card border-primary">
                    <div class="card-body">
                        <h6 class="card-title text-primary">Advance Payments</h6>
                        <p class="card-text fs-4">{{ number_format($totalAdvance, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-success">
                    <div class="card-body">
                        <h6 class="card-title text-success">Total Collections</h6>
                        <p class="card-text fs-4">{{ number_format($totalCollections, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-info">
                    <div class="card-body">
                        <h6 class="card-title text-info">Grand Total</h6>
                        <p class="card-text fs-4">{{ number_format($total, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-warning">
                    <div class="card-body">
                        <h6 class="card-title text-warning">Total Sales</h6>
                        <p class="card-text fs-4">{{ number_format($totalSales, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger">Total Direct Bills</h6>
                        <p class="card-text fs-4">{{ number_format($totalDirectBills, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

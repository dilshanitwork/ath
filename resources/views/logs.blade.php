@extends('layouts.app')

@section('title', 'Logs')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h1 class="mb-0 card-title"><i class="bi bi-file-text me-2"></i>Logs</h1>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Filter Section -->
        <div class="mb-4 card">
            <div class="card-header bg-light">
                <i class="bi bi-funnel me-2"></i>Filter Logs
            </div>
            <div class="card-body">
                <form action="{{ route('logs.index') }}" method="GET">
                    <div class="row g-3">
                        <!-- Search Field -->
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Search logs..." value="{{ request('search') }}">
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
                            <div>
                                <button type="submit" class="btn btn-outline-dark me-2">
                                    <i class="bi bi-search me-1"></i>Apply Filters
                                </button>
                                <a href="{{ route('logs.index') }}" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="table-responsive">
            <table class="table align-middle table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="table-header"><i class="bi bi-hash me-2"></i>ID</th>
                        <th class="table-header"><i class="bi bi-person me-2"></i>User</th>
                        <th class="table-header"><i class="bi bi-chat-left-text me-2"></i>Message</th>
                        <th class="table-header"><i class="bi bi-clock me-2"></i>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td><b>{{ $log->user ? $log->user->name : 'N/A' }}</b></td>
                            <td>{{ $log->message }}</td>
                            <td>{{ $log->created_at }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4 overflow-auto d-flex justify-content-center">
            {{ $logs->appends(request()->all())->links() }}
        </div>
    </div>
@endsection
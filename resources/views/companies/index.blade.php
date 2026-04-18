@extends('layouts.app')
@section('title','All Companies')
@section('content')
<div class="container py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><i class="bi bi-building me-1"></i> Companies</h1>
        <a href="{{ route('companies.create') }}" class="btn btn-outline-dark">
            <i class="bi bi-plus-circle me-1"></i> Add Company
        </a>
    </div>

    @if (session('success'))
        <div id="autoHideAlert" class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="GET" class="row g-2 mb-3">
        <div class="col-12 col-md-7">
            <label class="form-label fw-semibold">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="Company name or contact number">
        </div>
         <div class="col-12 col-md-1">
            <label class="form-label fw-semibold">Per Page</label>
            <select name="per_page" class="form-select" onchange="this.form.submit()">
                @foreach([10,25,100,500] as $n)
                    <option value="{{ $n }}" {{ (int)request('per_page',10)===$n ? 'selected':'' }}>{{ $n }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2 d-grid align-self-end">
            <button class="btn btn-outline-dark">
                <i class="bi bi-search me-1"></i> Search
            </button>
        </div>

        <div class="col-12 col-md-2 d-grid align-self-end">
            <a href="{{ route('companies.index') }}" class="btn btn-outline-danger">
                <i class="bi bi-x-circle me-1"></i> Reset
            </a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Company Name</th>
                        <th>Contact</th>
                        <th>Notes</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($companies as $company)
                        <tr>
                            <td>{{ $company->id }}</td>
                            <td class="fw-semibold">{{ $company->company_name }}</td>
                            <td>{{ $company->contact_number ?? '-' }}</td>
                            <td class="text-truncate" style="max-width: 280px;">
                                {{ $company->notes ?? '-' }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('companies.show', $company->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('companies.edit', $company->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('companies.destroy', $company->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this company?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No companies found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-body">
            {{ $companies->links() }}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alert = document.getElementById('autoHideAlert');
    if (alert) {
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    }
});
</script>
@endsection

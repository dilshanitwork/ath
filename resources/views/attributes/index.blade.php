@extends('layouts.app')

@section('title', 'Attributes')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h1 class="mb-0 card-title"><i class="bi bi-list-ul me-2"></i>Attributes</h1>
            <a href="{{ route('attributes.create') }}" class="mt-2 btn btn-outline-dark mt-md-0">
                <i class="bi bi-plus-circle me-1"></i> Add Attribute
            </a>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Attributes Table -->
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
                    @forelse ($attributes as $attribute)
                        <tr>
                            <td>{{ $attribute->id }}</td>
                            <td><b>{{ $attribute->name }}</b></td>
                            <td class="text-nowrap text-end">
                                <a href="{{ route('attributes.edit', $attribute) }}" class="btn btn-warning btn-sm" title="Edit Attribute">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('attributes.destroy', $attribute) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete Attribute"
                                        onclick="return confirm('Are you sure you want to delete this attribute?');">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No attributes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links (if applicable) -->
        @if(method_exists($attributes, 'links'))
            <div class="mt-4 d-flex justify-content-center">
                {{ $attributes->links() }}
            </div>
        @endif
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Attribute Values')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h1 class="mb-0 card-title"><i class="bi bi-list-check me-2"></i>Attribute Values</h1>
            <a href="{{ route('attribute-values.create') }}" class="mt-2 btn btn-outline-dark mt-md-0">
                <i class="bi bi-plus-circle me-1"></i> Add Attribute Value
            </a>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Attribute Values Table -->
        <div class="table-responsive">
            <table class="table align-middle table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="table-header"><i class="bi bi-hash me-2"></i>ID</th>
                        <th class="table-header"><i class="bi bi-tag me-2"></i>Attribute</th>
                        <th class="table-header"><i class="bi bi-diagram-3 me-2"></i>Value</th>
                        <th class="table-header text-end"><i class="bi bi-gear-fill"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attributeValues as $value)
                        <tr>
                            <td>{{ $value->id }}</td>
                            <td><b>{{ $value->attribute->name }}</b></td>
                            <td>{{ $value->value }}</td>
                            <td class="text-nowrap text-end">
                                <a href="{{ route('attribute-values.edit', $value) }}" class="btn btn-warning btn-sm" title="Edit Attribute Value">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                {{-- <form action="{{ route('attribute-values.destroy', $value) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete Attribute Value"
                                        onclick="return confirm('Are you sure you want to delete this attribute value?');">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form> --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No attribute values found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links (if applicable) -->
        @if(method_exists($attributeValues, 'links'))
            <div class="mt-4 d-flex justify-content-center">
                {{ $attributeValues->links() }}
            </div>
        @endif
    </div>
@endsection
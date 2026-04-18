@extends('layouts.app')

@section('title', 'Create Attribute Value')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1 class="mb-0 card-title"><i class="bi bi-plus-circle-fill me-2"></i>Create Attribute Value</h1>
            <a href="{{ route('attribute-values.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        <!-- Include Alerts -->
        @include('components.alerts')

        <!-- Create Attribute Value Form -->
        <div class="card">
            <div class="card-header bg-light">
                <strong><i class="bi bi-diagram-3 me-2"></i>New Attribute Value Details</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('attribute-values.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="attribute_id" class="form-label"><i class="bi bi-tag me-2"></i>Attribute</label>
                            <select name="attribute_id" id="attribute_id" class="form-control" required>
                                <option value="">Select Attribute</option>
                                @foreach ($attributes as $attribute)
                                    <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                                @endforeach
                            </select>
                            @error('attribute_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="value" class="form-label"><i class="bi bi-list-ul me-2"></i>Value</label>
                            <input type="text" name="value" id="value" class="form-control" 
                                placeholder="Enter attribute value" required>
                            @error('value')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-start">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-save me-1"></i> Create & Save
                        </button>
                        <a href="{{ route('attribute-values.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
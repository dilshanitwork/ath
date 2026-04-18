@extends('layouts.app')

@section('title', 'Edit Attribute')

@section('content')
    <div class="container">
        <h4 class="mb-4"><i class="fas fa-edit"></i> Edit Attribute</h4>

        <form action="{{ route('attributes.update', $attribute) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label"><i class="fas fa-tag"></i> Attribute Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $attribute->name) }}" placeholder="Enter attribute name" required>
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Update Attribute
            </button>
            <a href="{{ route('attributes.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </form>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container text-center">
        <h1 class="display-4">403</h1>
        <p class="lead">You do not have permission to access this page.</p>
        <a href="{{ url('/') }}" class="btn btn-success">Go Back to Home</a>
    </div>
@endsection

@extends('layouts.app')
@section('title', 'View Customer - ' . $customer->name)
@section('content')
    <div class="container">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="card-title mb-0"><i class="bi bi-person-circle me-2"></i>Customer Details</h1>
                <button type="button" class="btn btn-secondary width-100px" onclick="history.back()">Back to List</button>
            </div>

            <div class="row g-4">
                <div class="col-md-2 text-center">
                    @if ($customer->photo)
                        <img src="{{ asset($customer->photo) }}" alt="{{ $customer->name }}"
                            class="img-fluid rounded-circle shadow-sm"
                            style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center rounded-circle border"
                            style="width: 150px; height: 150px; object-fit: cover;">
                            <i class="bi bi-person-circle text-muted" style="font-size: 5rem;"></i>
                        </div>
                    @endif
                    <h5 class="text-success mt-3">{{ $customer->name }}</h5>
                    <p class="text-muted">{{ $customer->email }}</p>
                </div>

                <div class="col-md-10">
                    <h4>Basic Information</h4>
                    <hr>
                    <dl class="row">
                        <dt class="col-sm-4">ID</dt>
                        <dd class="col-sm-8">{{ $customer->id }}</dd>

                        <dt class="col-sm-4">Name</dt>
                        <dd class="col-sm-8">{{ $customer->name }}</dd>

                        {{-- <dt class="col-sm-4">Category</dt>
                        <dd class="col-sm-8">{{ $customer->category === 0 ? 'Showroom Sale' : 'Van Sale' }}</dd> --}}

                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $customer->email }}</dd>

                        <dt class="col-sm-4">Mobile</dt>
                        <dd class="col-sm-8">{{ $customer->mobile }}</dd>

                        <dt class="col-sm-4">Alternate Contact</dt>
                        <dd class="col-sm-8">{{ $customer->mobile_2 }}</dd>

                        <dt class="col-sm-4">Address</dt>
                        <dd class="col-sm-8">{{ $customer->address }}</dd>

                        <dt class="col-sm-4">NIC</dt>
                        <dd class="col-sm-8">{{ $customer->nic }}</dd>

                        <dt class="col-sm-4">Gender</dt>
                        <dd class="col-sm-8">{{ ucfirst($customer->gender) }}</dd>

                        <dt class="col-sm-4">Total Paid</dt>
                        <dd class="col-sm-8">LKR {{ number_format($totalPaid ?? 0, 2) }}</dd>

                        <dt class="col-sm-4">Current Balance</dt>
                        <dd class="col-sm-8 text-{{ $totalBalance > 0 ? 'danger' : 'success' }}"><b>LKR
                                {{ number_format($totalBalance ?? 0, 2) }}</b></dd>

                        <dt class="col-sm-4">Credit Limit</dt>
                        <dd class="col-sm-8 text-{{ $customer->credit_limit > 0 ? 'danger' : 'success' }}">
                            {{ $customer->credit_limit }}</dd>

                        <dt class="col-sm-4">Hometown</dt>
                        <dd class="col-sm-8">{{ $customer->hometownValue ? $customer->hometownValue->value : 'N/A' }}</dd>

                        <dt class="col-sm-4">Remark</dt>
                        <dd class="col-sm-8">{{ $customer->remark }}</dd>

                        <dt class="col-sm-4">Registered</dt>
                        <dd class="col-sm-8"><i>{{ $customer->created_at->format('F d, Y') }}</i></dd>
                        <dt class="col-sm-4">Last Updated</dt>
                        <dd class="col-sm-8"><i>{{ $customer->updated_at->format('F d, Y') }}</i></dd>
                    </dl>

                    <h4 class="mt-4">Customer Bills</h4>
                    <hr>

                    @if ($customer->directBills->isNotEmpty())
                        <ul class="list-group">
                            @foreach ($customer->directBills as $bill)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ route('direct_bills.show', $bill->id) }}">
                                        <i class="bi bi-file-earmark me-2"></i>Bill #{{ $bill->bill_number }}
                                        - {{ $bill->created_at->format('d M Y') }}
                                    </a>
                                    <span class="badge bg-primary text-white">
                                        {{ $bill->status ?? 'N/A' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No bills available for this customer.</p>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4 gap-2">
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning">
                    <i class="bi bi-pencil-square me-1"></i> Edit Customer
                </a>
                {{-- <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                    <i class="bi bi-trash me-1"></i> Delete Customer
                </button> --}}
            </div>
        </div>
    </div>

    {{-- <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this customer? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="delete-form" action="{{ route('customers.destroy', $customer) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

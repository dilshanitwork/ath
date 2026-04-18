@extends('layouts.app')

@section('title', 'Supplier Details - ' . $supplier->name)

@section('content')
    <div class="container">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="card-title mb-0"><i class="bi bi-truck me-2"></i>Supplier Details</h1>
                <div>
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning me-2">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <div class="row g-4">
                <!-- Profile Image / Icon -->
                <div class="col-md-2 text-center">
                    <div class="bg-light d-flex align-items-center justify-content-center rounded-circle mx-auto border"
                        style="width: 150px; height: 150px; object-fit: cover;">
                        <i class="bi bi-building text-muted" style="font-size: 5rem;"></i>
                    </div>
                    <h5 class="text-success mt-3">{{ $supplier->name }}</h5>
                    <p class="text-muted">{{ $supplier->email ?? 'No Email' }}</p>
                </div>

                <div class="col-md-10">
                    <!-- Basic Info -->
                    <h4 class="text-primary">Basic Information</h4>
                    <hr>
                    <dl class="row">
                        <dt class="col-sm-3 text-muted">Supplier ID</dt>
                        <dd class="col-sm-9">{{ $supplier->id }}</dd>

                        <dt class="col-sm-3 text-muted">Contact Person</dt>
                        <dd class="col-sm-9">{{ $supplier->contact_person ?? '-' }}</dd>

                        <dt class="col-sm-3 text-muted">Phone Number</dt>
                        <dd class="col-sm-9">{{ $supplier->phone ?? '-' }}</dd>

                        <dt class="col-sm-3 text-muted">Email Address</dt>
                        <dd class="col-sm-9">{{ $supplier->email ?? '-' }}</dd>

                        <dt class="col-sm-3 text-muted">Address</dt>
                        <dd class="col-sm-9">{{ $supplier->address ?? '-' }}</dd>

                        <dt class="col-sm-3 text-muted">Registered On</dt>
                        <dd class="col-sm-9">{{ $supplier->created_at->format('F d, Y') }}</dd>
                    </dl>

                    <!-- Purchase Orders Section -->
                    <h4 class="text-primary mt-5"><i class="bi bi-cart-check me-2"></i>Purchase Order History</h4>
                    <hr>
                    @if ($supplier->purchaseOrders && $supplier->purchaseOrders->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table-hover table-sm table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>PO Number</th>
                                        <th>Order Date</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($supplier->purchaseOrders as $po)
                                        <tr>
                                            <td>{{ $po->po_number }}</td>
                                            <td>{{ \Carbon\Carbon::parse($po->order_date)->format('Y-m-d') }}</td>
                                            <td>{{ number_format($po->total_amount, 2) }}</td>
                                            <td>
                                                @if ($po->status === 'received')
                                                    <span class="badge bg-success">Received</span>
                                                @elseif($po->status === 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($po->status === 'canceled')
                                                    <span class="badge bg-danger">Canceled</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($po->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('purchase_orders.show', $po) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-light border">No purchase orders found for this supplier.</div>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Are you sure you want to delete this supplier? Associated Purchase Orders and Items might be affected.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Delete Supplier
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

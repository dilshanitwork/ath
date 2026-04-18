@extends('layouts.app')

@section('title', 'Repair Job #' . $tyreRepair->item_number)

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-tools me-2"></i>Repair Job #{{ $tyreRepair->item_number }}</h1>
            <div>
                <a href="{{ route('tyre_repairs.edit', $tyreRepair) }}" class="btn btn-warning me-2"><i
                        class="bi bi-pencil-square"></i> Edit</a>
                <a href="{{ route('tyre_repairs.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> List</a>
            </div>
        </div>

        <div class="row">
            <!-- Customer & Tyre Info -->
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-light fw-bold">Customer & Tyre Details</div>
                    <div class="card-body">
                        <table class="table-borderless mb-0 table">
                            <tr>
                                <td class="text-muted" style="width: 40%">Customer:</td>
                                <td class="fw-bold">{{ $tyreRepair->customer->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Contact:</td>
                                <td>{{ $tyreRepair->customer->mobile }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Received Date:</td>
                                <td>{{ $tyreRepair->received_date ? \Carbon\Carbon::parse($tyreRepair->received_date)->format('Y-m-d') : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr class="my-1">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tyre Size:</td>
                                <td>{{ $tyreRepair->tyre_size ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tyre Make:</td>
                                <td>{{ $tyreRepair->tyre_make ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tyre Number:</td>
                                <td>{{ $tyreRepair->tyre_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status:</td>
                                <td>
                                    @if ($tyreRepair->status === 4)
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        @if ($tyreRepair->issued_date)
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($tyreRepair->sent_date)
                                            @if ($tyreRepair->received_from_company_date)
                                                <span class="badge bg-info text-dark">Available</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Sent to Company</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Job Status & Billing -->
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-light fw-bold">Job Status & Billing</div>
                    <div class="card-body">
                        <table class="table-borderless mb-0 table">
                            <tr>
                                <td class="text-muted" style="width: 40%">Sent to Company:</td>
                                <td>{{ $tyreRepair->sent_date ? \Carbon\Carbon::parse($tyreRepair->sent_date)->format('Y-m-d') : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Rep Receipt #:</td>
                                <td>{{ $tyreRepair->rep_receipt_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Job Number:</td>
                                <td>{{ $tyreRepair->job_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Rcvd from Company:</td>
                                <td>{{ $tyreRepair->received_from_company_date ? \Carbon\Carbon::parse($tyreRepair->received_from_company_date)->format('Y-m-d') : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr class="my-1">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Issued to Customer:</td>
                                <td>{{ $tyreRepair->issued_date ? \Carbon\Carbon::parse($tyreRepair->issued_date)->format('Y-m-d') : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Bill Number:</td>
                                <td>{{ $tyreRepair->bill_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Amount:</td>
                                <td class="fw-bold text-success fs-5">
                                    {{ $tyreRepair->amount ? 'Rs. ' . number_format($tyreRepair->amount, 2) : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            @if ($tyreRepair->note)
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light fw-bold">Notes</div>
                        <div class="card-body">
                            {{ $tyreRepair->note }}
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-12 mt-4 text-end">
                <form action="{{ route('tyre_repairs.destroy', $tyreRepair) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Are you sure you want to delete this record? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i> Delete
                        Record</button>
                </form>
            </div>
        </div>
    </div>
@endsection

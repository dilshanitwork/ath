@extends('layouts.app')
@section('title', 'Customers')
@section('content')
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
            <h1 class="card-title mb-0"><i class="bi bi-people me-2"></i>Customers</h1>
            <a href="{{ route('customers.create') }}" class="btn btn-outline-dark mt-md-0 mt-2">
                <i class="bi bi-person-add me-1"></i> Add New Customer
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <form action="{{ route('customers.index') }}" method="GET" class="row g-2 align-items-end mb-3">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, NIC, or mobile"
                        value="{{ request('search') }}" id="search">
                    <button type="submit" class="btn btn-secondary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <div id="suggestions" class="list-group d-none position-absolute mt-1"
                    style="z-index: 1000; width: calc(100% - 1.5rem);"></div>
            </div>
            <div class="col-md-1">
                <label for="per_page" class="form-label"><i class="fas fa-list"></i> Per Page</label>
                <select name="per_page" id="per_page" class="form-select" onchange="this.form.submit()">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500</option>
                </select>
            </div>
            @if (request()->has('search'))
                <div class="col-12 col-md-2 d-flex justify-content-end">
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-danger w-100">
                        <i class="bi bi-x-circle-fill me-1"></i> Reset
                    </a>
                </div>
            @endif
        </form>

        <!-- Filter Details -->
        @if (request('search') || request('start_date') || request('end_date'))
            <div class="mb-4">
                <p><strong>Active Filters:</strong></p>
                <ul class="list-inline">
                    @if (request('search'))
                        <li class="list-inline-item">
                            <span class="badge bg-info text-white">Search: "{{ request('search') }}"</span>
                        </li>
                    @endif
                    @if (request('start_date'))
                        <li class="list-inline-item">
                            <span class="badge bg-secondary text-white">Start Date: {{ request('start_date') }}</span>
                        </li>
                    @endif
                    @if (request('end_date'))
                        <li class="list-inline-item">
                            <span class="badge bg-secondary text-white">End Date: {{ request('end_date') }}</span>
                        </li>
                    @endif
                </ul>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table-bordered table-hover table align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="table-header">ID</th>
                        <th class="table-header"><i class="bi bi-person me-2"></i>Name</th>
                        <th class="table-header"><i class="bi bi-phone me-2"></i>Mobile</th>
                        <th class="table-header"><i class="bi bi-cash-stack me-2"></i>Credit Limit</th>
                        {{-- <th class="table-header"><i class="bi bi-check-circle me-2"></i>Category</th> --}}
                        <th class="table-header text-end"><i class="bi bi-gear-fill"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>
                                <b class="{{ $customer->category == 0 ? 'text-primary' : 'text-success' }}">
                                    <a href="{{ route('customers.show', $customer) }}"
                                        class="text-decoration-none text-reset">
                                        {{ $customer->name }}
                                    </a>
                                </b>
                            </td>
                            <td>{{ $customer->mobile }}</td>
                            <td>{{ $customer->credit_limit ?: 'N/A' }}</td>
                            {{-- <td>
                                <span class="badge {{ $customer->category == 0 ? 'bg-primary' : 'bg-success' }} text-white">
                                    {{ $customer->category == 0 ? 'Showroom Sale' : 'Van Sale' }}
                                </span>
                            </td> --}}
                            <td class="text-nowrap text-end">
                                <a href="{{ route('direct_bills.create', ['customer_name' => $customer->name, 'contact_number' => $customer->mobile]) }}"
                                    class="btn btn-success btn-sm" title="Add New Bill">
                                    <i class="bi bi-file-earmark-plus"></i>
                                </a>
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-info btn-sm"
                                    title="View Customer">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted text-center">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4 overflow-auto">
            {{ $customers->appends(request()->all())->links() }}
        </div>
    </div>

    <style>
        /* Responsive table styles */
        @media (max-width: 767.98px) {
            .table-responsive {
                font-size: 0.95rem;
            }

            .table thead {
                display: none;
            }

            .table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border-bottom: 2px solid #dee2e6;
            }

            .table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: .5rem 0;
                border: none;
            }

            .table tbody td:before {
                content: attr(data-label);
                flex-basis: 45%;
                font-weight: 700;
                color: #495057;
                text-align: left;
            }

            .table tbody td:last-child {
                justify-content: flex-end;
            }

            .table tbody td:last-child .btn {
                margin-left: 0.25rem;
            }
        }
    </style>

    <script>
        // Add data-label attribute for mobile table view
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth < 768) {
                const headers = Array.from(document.querySelectorAll('.table thead th')).map(th => th.innerText
                    .trim());
                document.querySelectorAll('.table tbody tr').forEach(tr => {
                    tr.querySelectorAll('td').forEach((td, i) => {
                        td.setAttribute('data-label', headers[i] || '');
                    });
                });
            }
        });
    </script>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const suggestionsContainer = document.getElementById('suggestions');

            searchInput.addEventListener('input', function() {
                const query = this.value.trim();

                if (query.length >= 2) {
                    fetchSuggestions(query);
                } else {
                    suggestionsContainer.style.display = 'none';
                }
            });

            function fetchSuggestions(query) {
                fetch(`{{ route('customers.suggestions') }}?query=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            const suggestionsHtml = data.map(customer => `
                            <a href="#" class="list-group-item list-group-item-action" data-name="${customer.name}">
                                ${customer.name} (${customer.mobile})
                            </a>
                        `).join('');

                            suggestionsContainer.innerHTML = suggestionsHtml;
                            suggestionsContainer.style.display = 'block';
                        } else {
                            suggestionsContainer.innerHTML = '';
                            suggestionsContainer.style.display = 'none';
                        }
                    });
            }

            suggestionsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('list-group-item')) {
                    searchInput.value = e.target.getAttribute('data-name');
                    suggestionsContainer.style.display = 'none';
                }
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                    suggestionsContainer.style.display = 'none';
                }
            });
        });
    </script>
@endpush

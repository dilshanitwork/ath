@extends('layouts.app')
@section('title', 'Customer Purchases - ' . $stockItem->name)
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Purchases for: <strong>{{ $stockItem->name }}</strong></h3>
            <a href="{{ route('stock_items.show', $stockItem) }}" class="btn btn-secondary">Back</a>
        </div>

        <form method="GET" class="row g-2 mb-3">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Customer</label>
                <input type="text" name="customer" id="customer_search" value="{{ old('customer', $customerSearch) }}"
                    class="form-control" placeholder="Type to search..." autocomplete="off">
                <div id="customer_suggestions" class="mt-1 list-group d-none position-absolute"
                    style="z-index:1000; width: calc(100% - 1.5rem);"></div>
            </div>

            <div class="col-12 col-md-1">
                <label class="form-label fw-semibold">Per Page</label>
                <select name="per_page" class="form-select" onchange="this.form.submit()">
                    @foreach ([10, 25, 100, 500] as $n)
                        <option value="{{ $n }}" {{ (int) request('per_page', 15) === $n ? 'selected' : '' }}>
                            {{ $n }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-auto align-self-end">
                <button class="btn btn-outline-dark"><i class="bi bi-search me-1"></i> Filter</button>
            </div>
            <div class="col-auto align-self-end">
                <a href="{{ route('stock_items.customer_purchases', $stockItem) }}" class="btn btn-outline-danger">Reset</a>
            </div>
        </form>

        @if (!is_null($totalForCustomer))
            <div class="alert alert-info">
                <strong>Total quantity for matching customer(s):</strong>
                <span class="fw-bold">{{ $totalForCustomer }}</span>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Customer</th>
                        <th>Bill Number</th>
                        <th class="text-end">Quantity</th>
                        <th>Sold At</th>
                        <th>Bill Link</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customerPurchases as $row)
                        <tr>
                            <td> {{ $row->customer_name ?? 'Unknown' }}</td>
                            <td> <a href="{{ route('direct_bills.show', $row->bill_id) }}"class="text-decoration-none text-primary">{{ $row->bill_number }}</a></td>
                            <td class="text-end">{{ $row->quantity }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->sold_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                @if (isset($row->bill_id))
                                    <a href="{{ route('direct_bills.show', $row->bill_id) }}"
                                        class="btn btn-sm btn-outline-primary">View Bill</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No purchase records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            {{ $customerPurchases->appends(request()->except('customer_page'))->links() }}
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('customer_search');
                const suggestionsContainer = document.getElementById('customer_suggestions');

                if (!searchInput) return;

                let debounceTimer = null;
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    if (debounceTimer) clearTimeout(debounceTimer);
                    if (query.length >= 2) {
                        debounceTimer = setTimeout(() => fetchSuggestions(query), 250);
                    } else {
                        suggestionsContainer.innerHTML = '';
                        suggestionsContainer.classList.add('d-none');
                    }
                });

                function fetchSuggestions(query) {
                    fetch(`{{ route('customers.suggestions') }}?query=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.length > 0) {
                                const html = data.map(c => `
                        <a href="#" class="list-group-item list-group-item-action" data-name="${c.name}">
                            ${c.name} ${c.mobile ? '(' + c.mobile + ')' : ''}
                        </a>
                    `).join('');
                                suggestionsContainer.innerHTML = html;
                                suggestionsContainer.classList.remove('d-none');
                            } else {
                                suggestionsContainer.innerHTML = '';
                                suggestionsContainer.classList.add('d-none');
                            }
                        });
                }

                suggestionsContainer.addEventListener('click', function(e) {
                    const item = e.target.closest('.list-group-item');
                    if (!item) return;
                    searchInput.value = item.getAttribute('data-name');
                    suggestionsContainer.classList.add('d-none');
                });

                document.addEventListener('click', function(e) {
                    if (!suggestionsContainer.contains(e.target) && e.target !== searchInput) {
                        suggestionsContainer.classList.add('d-none');
                    }
                });
            });
        </script>
    @endpush
@endsection

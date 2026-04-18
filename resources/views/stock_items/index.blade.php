@extends('layouts.app')
@section('title', 'All Stock Items')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="card-title mb-0"><i class="bi bi-boxes me-2"></i>Stock Items</h1>
            <a href="{{ route('stock_items.create') }}" class="btn btn-outline-dark">
                <i class="bi bi-plus-circle me-1"></i> Add New Item
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if ($lowStockCount > 0)
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div>
                    {{ $lowStockCount }} item(s) are low in stock (≤ 3).
                    <a href="{{ route('stock_items.index', array_merge(request()->query(), ['quantity' => '3', 'quantity_operator' => 'lte'])) }}"
                        class="alert-link">View low stock</a>
                </div>
            </div>
        @endif

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <i class="bi bi-funnel me-2"></i>Filter Stock Items
            </div>
            <div class="card-body">
                <form action="{{ route('stock_items.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-3 position-relative" id="stock-item-name-wrapper">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ request('name') }}" placeholder="Enter item name" autocomplete="off"
                                aria-autocomplete="list" aria-controls="name-suggestions">
                            <input type="hidden" name="name_id" id="name_id" value="{{ request('name_id') }}">
                            <div id="name-suggestions" class="list-group position-absolute w-100"
                                style="z-index: 1000; display: none; max-height: 240px; overflow:auto;"></div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label for="quantity" class="form-label">Quantity</label>
                            <div class="input-group">
                                <select name="quantity_operator" id="quantity_operator" class="form-select"
                                    style="max-width: 6rem;">
                                    <option value="eq"
                                        {{ request('quantity_operator', 'eq') == 'eq' ? 'selected' : '' }}>=</option>
                                    <option value="lte" {{ request('quantity_operator') == 'lte' ? 'selected' : '' }}>
                                        &le;</option>
                                    <option value="gte" {{ request('quantity_operator') == 'gte' ? 'selected' : '' }}>
                                        &ge;</option>
                                </select>
                                <input type="number" name="quantity" id="quantity" class="form-control"
                                    value="{{ request('quantity') }}" placeholder="Enter quantity">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="cost_price" class="form-label">Cost Price</label>
                            <input type="number" step="0.01" name="cost_price" id="cost_price" class="form-control"
                                value="{{ request('cost_price') }}" placeholder="Enter cost price">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="invoice_number" class="form-label">Invoice Number</label>
                            <input type="text" name="invoice_number" id="invoice_number" class="form-control"
                                value="{{ request('invoice_number') }}" placeholder="Enter invoice number">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 col-md-8">
                            <!-- Applied Filters Display -->
                            @if (request()->filled(['name', 'quantity', 'cost_price']))
                                <div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge bg-danger">Filters Applied</span>
                                        @if (request()->filled('name'))
                                            <span class="badge bg-secondary">Name: {{ request('name') }}</span>
                                        @endif
                                        @if (request()->filled('quantity'))
                                            <span class="badge bg-secondary">Qty: {{ request('quantity') }}</span>
                                        @endif
                                        @if (request()->filled('cost_price'))
                                            <span class="badge bg-secondary">Cost: {{ request('cost_price') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-12 col-md-12">
                            <div class="d-flex align-items-center justify-content-between gap-2">

                                {{-- LEFT: Per Page --}}
                                <div class="d-flex align-items-center gap-2">
                                    <label for="per_page" class="form-label mb-0">
                                        <i class="fas fa-list"></i> Per Page
                                    </label>

                                    <select name="per_page" id="per_page" class="form-select-sm form-select w-auto"
                                        onchange="this.form.submit()">
                                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10
                                        </option>
                                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25
                                        </option>
                                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100
                                        </option>
                                        <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500
                                        </option>
                                    </select>
                                </div>

                                {{-- RIGHT: Action buttons --}}
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-dark btn-sm">
                                        <i class="bi bi-search me-1"></i> Apply Filters
                                    </button>

                                    <a href="{{ route('stock_items.index') }}" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center"
                                       style="min-width: 140px;">
                                        <i class="bi bi-x-circle me-1"></i> Reset
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-bordered table-hover table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Item Code</th>
                        <th>Total Quantity</th>
                        <th>Latest Cost Price</th>
                        <th>Latest Selling Price</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stockItems as $item)
                        {{-- Get the latest batch (relation is ordered desc) --}}
                        @php $latestBatch = $item->stockBatches->first(); @endphp
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td><b><a class="text-decoration-none @if ($item->service == 1) text-success @endif"
                                        href="{{ route('stock_items.show', $item) }}">{{ $item->name }}</a></b></td>
                            <td>{{ $item->model_number }}</td>
                            {{-- Use the calculated sum, default to 0 if null --}}
                            <td><strong>{{ $item->total_quantity ?? 0 }}</strong></td>

                            {{-- Show pricing from the latest batch, or N/A --}}
                            <td>{{ $latestBatch ? number_format($latestBatch->cost_price, 2) : 'N/A' }}</td>
                            <td>{{ $latestBatch ? number_format($latestBatch->selling_price, 2) : 'N/A' }}</td>

                            <td class="text-nowrap text-end">
                                <a href="{{ route('stock_adjustments.create', ['stock_item_id' => $item->id]) }}"
                                    class="btn btn-info btn-sm" title="Add Stock Batch">
                                    <i class="bi bi-plus-circle"></i>
                                </a>
                                <a href="{{ route('stock_items.edit', $item) }}" class="btn btn-warning btn-sm"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted text-center">No stock items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            {{ $stockItems->appends(request()->query())->links() }}
        </div>
    </div>

    <script>
        (function() {
            const input = document.getElementById('name');
            const suggestions = document.getElementById('name-suggestions');
            const hiddenId = document.getElementById('name_id');
            let controller = null; // for aborting fetch
            let timeout = null;

            function debounce(fn, wait) {
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => fn.apply(this, args), wait);
                };
            }

            function hideSuggestions() {
                suggestions.style.display = 'none';
                suggestions.innerHTML = '';
            }

            function showSuggestions(items) {
                suggestions.innerHTML = '';
                if (!items.length) {
                    hideSuggestions();
                    return;
                }

                items.forEach(item => {
                    const el = document.createElement('a');
                    el.href = '#';
                    el.className = 'list-group-item list-group-item-action';
                    el.textContent = item.name;
                    el.dataset.id = item.id;
                    el.addEventListener('click', function(e) {
                        e.preventDefault();
                        input.value = item.name;
                        if (hiddenId) hiddenId.value = item.id;
                        hideSuggestions();
                    });
                    suggestions.appendChild(el);
                });
                suggestions.style.display = 'block';
            }

            async function fetchSuggestions(q) {
                if (controller) controller.abort();
                controller = new AbortController();
                const url = new URL("{{ route('stock-items.autocomplete') }}", window.location.origin);
                url.searchParams.set('q', q);

                try {
                    const res = await fetch(url.toString(), {
                        signal: controller.signal
                    });
                    if (!res.ok) return [];
                    return await res.json();
                } catch (err) {
                    if (err.name === 'AbortError') return [];
                    console.error(err);
                    return [];
                }
            }

            const onInput = debounce(async function(e) {
                const q = e.target.value.trim();
                if (q.length < 1) {
                    hideSuggestions();
                    return;
                }
                const items = await fetchSuggestions(q);
                showSuggestions(items);
            }, 250); // 250ms debounce

            input.addEventListener('input', onInput);

            // hide suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!document.getElementById('stock-item-name-wrapper').contains(e.target)) {
                    hideSuggestions();
                }
            });

            // optional: handle keyboard navigation
            input.addEventListener('keydown', function(e) {
                const visible = suggestions.style.display === 'block';
                if (!visible) return;
                const active = suggestions.querySelector('.active');
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    const next = active ? active.nextElementSibling : suggestions.firstElementChild;
                    if (active) active.classList.remove('active');
                    if (next) next.classList.add('active');
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prev = active ? active.previousElementSibling : null;
                    if (active) active.classList.remove('active');
                    if (prev) prev.classList.add('active');
                } else if (e.key === 'Enter') {
                    const toSelect = suggestions.querySelector('.active') || suggestions.firstElementChild;
                    if (toSelect) {
                        e.preventDefault();
                        toSelect.click();
                    }
                } else if (e.key === 'Escape') {
                    hideSuggestions();
                }
            });
        })();
    </script>

@endsection

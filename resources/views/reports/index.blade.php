@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
    <div class="container">
        <h2 class="text-primary mb-4"><i class="bi bi-graph-up-arrow me-2"></i>Reports & Analytics</h2>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-start border-primary border-4 shadow-sm">
                    <div class="card-body text-center">
                        <div class="display-4 text-primary mb-2"><i class="bi bi-cash-stack"></i></div>
                        <h5 class="card-title">Daily Sales</h5>
                        <p class="card-text text-muted small">Daily revenue summary, cash vs credit breakdown.</p>
                        <a href="{{ route('reports.daily_sales') }}"
                            class="btn btn-outline-primary btn-sm stretched-link">View Report</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-start border-success border-4 shadow-sm">
                    <div class="card-body text-center">
                        <div class="display-4 text-success mb-2"><i class="bi bi-box-seam"></i></div>
                        <h5 class="card-title">Stock Inventory</h5>
                        <p class="card-text text-muted small">Current stock levels, valuation, and low stock alerts.</p>
                        <a href="{{ route('reports.stock_inventory') }}"
                            class="btn btn-outline-success btn-sm stretched-link">View Report</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-start border-info border-4 shadow-sm">
                    <div class="card-body text-center">
                        <div class="display-4 text-info mb-2"><i class="bi bi-cart-check"></i></div>
                        <h5 class="card-title">Purchase Orders</h5>
                        <p class="card-text text-muted small">Supplier purchases summary by date range.</p>
                        <a href="{{ route('reports.purchase_orders') }}"
                            class="btn btn-outline-info btn-sm stretched-link">View Report</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-start border-warning border-4 shadow-sm">
                    <div class="card-body text-center">
                        <div class="display-4 text-warning mb-2"><i class="bi bi-tools"></i></div>
                        <h5 class="card-title">Tyre Repairs</h5>
                        <p class="card-text text-muted small">Tracking for pending, sent, and completed repair jobs.</p>
                        <a href="{{ route('reports.tyre_repairs') }}"
                            class="btn btn-outline-warning text-dark btn-sm stretched-link">View Report</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-start border-secondary border-4 shadow-sm">
                    <div class="card-body text-center">
                        <div class="display-4 text-secondary mb-2"><i class="bi bi-wallet2"></i></div>
                        <h5 class="card-title">Credit Summary</h5>
                        <p class="card-text text-muted small">Summary of all credit bills by date range.</p>
                        <a href="{{ route('reports.credit_summary') }}"
                            class="btn btn-outline-secondary btn-sm stretched-link">View Report</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-start border-danger border-4 shadow-sm">
                    <div class="card-body text-center">
                        <div class="display-4 text-danger mb-2"><i class="bi bi-person-badge"></i></div>
                        <h5 class="card-title">Customer Credit</h5>
                        <p class="card-text text-muted small">Track credit, payments and balance per customer.</p>
                        <a href="{{ route('reports.customer_credit_report') }}"
                            class="btn btn-outline-danger btn-sm stretched-link">View Report</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

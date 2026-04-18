<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} — Home</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon.png') }}">

        <style>
            :root {
                --ath-dark:       #1a2332;   /* deep badge background   */
                --ath-steel:      #2c3e50;   /* mid steel-blue          */
                --ath-orange:     #c0622a;   /* ATH letter orange       */
                --ath-orange-lt:  #d4733a;   /* lighter orange hover    */
                --ath-chrome:     #8a9bb0;   /* rim / chrome silver     */
                --ath-light-bg:   #f4f5f7;   /* page background         */
            }

            * { font-family: 'Figtree', sans-serif; }

            body {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                background-color: var(--ath-light-bg);
            }

            main { flex: 1; }

            /* ── Navbar ── */
            .top-navbar {
                background: #fff;
                border-bottom: 3px solid var(--ath-orange);
                padding: 0.75rem 1.5rem;
            }

            .top-navbar .brand-name {
                color: var(--ath-dark);
            }

            /* ── Login button ── */
            .btn-ath {
                background: var(--ath-orange);
                border-color: var(--ath-orange);
                color: #fff;
                font-weight: 600;
            }

            .btn-ath:hover {
                background: var(--ath-orange-lt);
                border-color: var(--ath-orange-lt);
                color: #fff;
            }

            .btn-outline-ath {
                border: 1.5px solid var(--ath-orange);
                color: var(--ath-orange);
                font-weight: 600;
                background: transparent;
            }

            .btn-outline-ath:hover {
                background: var(--ath-orange);
                color: #fff;
            }

            /* ── Guest Hero ── */
            .hero-section {
                background: linear-gradient(135deg, var(--ath-dark) 0%, var(--ath-steel) 60%, #3d5166 100%);
                color: #fff;
                padding: 6rem 0 5rem;
                position: relative;
                overflow: hidden;
            }

            /* Subtle tyre-tread texture overlay */
            .hero-section::before {
                content: '';
                position: absolute;
                inset: 0;
                background: url('{{ asset('img/background.jpg') }}') no-repeat center center / cover;
                opacity: 0.07;
            }

            /* Orange accent line at the bottom of hero */
            .hero-section::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, var(--ath-orange), var(--ath-orange-lt), var(--ath-chrome));
            }

            .hero-section .container { position: relative; z-index: 1; }

            .hero-badge {
                display: inline-block;
                background: rgba(192,98,42,0.18);
                border: 1px solid rgba(192,98,42,0.5);
                color: #f0a070;
                border-radius: 50px;
                padding: 0.3rem 1rem;
                font-size: 0.82rem;
                letter-spacing: 0.5px;
                margin-bottom: 1.2rem;
            }

            /* ── Feature cards (guest) ── */
            .feature-card {
                border: none;
                border-radius: 16px;
                box-shadow: 0 2px 16px rgba(0,0,0,0.07);
                transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
                border-bottom: 3px solid transparent;
            }

            .feature-card:hover {
                transform: translateY(-6px);
                box-shadow: 0 8px 28px rgba(0,0,0,0.12);
                border-bottom-color: var(--ath-orange);
            }

            .feature-icon-wrap {
                width: 64px;
                height: 64px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.8rem;
                margin: 0 auto 1rem;
            }

            /* ── Dashboard header (auth) ── */
            .dashboard-header {
                background: linear-gradient(135deg, var(--ath-dark) 0%, var(--ath-steel) 100%);
                color: #fff;
                padding: 2.2rem 2rem 2.8rem;
                border-radius: 0 0 28px 28px;
                margin-bottom: -1.5rem;
                border-bottom: 4px solid var(--ath-orange);
                position: relative;
                overflow: hidden;
            }

            /* Subtle chrome shimmer on dashboard header */
            .dashboard-header::before {
                content: '';
                position: absolute;
                top: -60px;
                right: -60px;
                width: 220px;
                height: 220px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(138,155,176,0.15) 0%, transparent 70%);
                pointer-events: none;
            }

            .dashboard-header .btn-new-bill {
                background: var(--ath-orange);
                border-color: var(--ath-orange);
                color: #fff;
                font-weight: 600;
            }

            .dashboard-header .btn-new-bill:hover {
                background: var(--ath-orange-lt);
                border-color: var(--ath-orange-lt);
            }

            /* ── Shortcut cards ── */
            .shortcut-card {
                border: none;
                border-radius: 16px;
                box-shadow: 0 2px 12px rgba(0,0,0,0.07);
                transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
                text-decoration: none;
                color: inherit;
                display: block;
                border-bottom: 3px solid transparent;
            }

            .shortcut-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 24px rgba(0,0,0,0.13);
                color: inherit;
                border-bottom-color: var(--ath-orange);
            }

            .shortcut-icon {
                width: 52px;
                height: 52px;
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.4rem;
                flex-shrink: 0;
            }

            .section-label {
                font-size: 0.72rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: var(--ath-chrome);
                margin-bottom: 0.75rem;
            }

            /* Avatar circle uses brand orange */
            .user-avatar {
                width: 30px;
                height: 30px;
                border-radius: 50%;
                background: var(--ath-orange);
                color: #fff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 0.75rem;
                font-weight: 600;
            }

            /* ── Footer ── */
            footer {
                background: var(--ath-dark);
                color: var(--ath-chrome);
                font-size: 0.85rem;
                border-top: 3px solid var(--ath-orange);
            }

            footer a { color: var(--ath-orange-lt); }
            footer a:hover { color: #fff; }
        </style>
    </head>

    <body>

        {{-- ══════════════════════════════════════════════
             TOP NAVBAR
        ══════════════════════════════════════════════ --}}
        <nav class="top-navbar d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="d-flex align-items-center text-decoration-none gap-2">
                <img src="{{ asset('img/favicon.png') }}" alt="Logo" height="40">
                <span class="fw-bold fs-5 brand-name">{{ config('app.name', 'Laravel') }}</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-ath btn-sm px-3">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </a>
                @else
                    <a href="{{ route('direct_bills.index') }}" class="btn btn-outline-ath btn-sm d-none d-sm-inline-flex">
                        <i class="bi bi-receipt me-1"></i> Bills
                    </a>
                    <a href="{{ route('stock_items.index') }}" class="btn btn-outline-ath btn-sm d-none d-sm-inline-flex">
                        <i class="bi bi-boxes me-1"></i> Stock Items
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2"
                            data-bs-toggle="dropdown">
                            <span class="user-avatar">
                                @php
                                    $n = Auth::user()->name ?? 'A';
                                    $parts = explode(' ', $n);
                                    $ini = '';
                                    foreach ($parts as $p) $ini .= strtoupper(substr($p,0,1));
                                    echo strlen($ini) > 2 ? substr($ini,0,1).substr($ini,-1,1) : $ini;
                                @endphp
                            </span>
                            <span class="d-none d-sm-inline">{{ Auth::user()->name ?? 'Admin' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm mt-1">
                            <li>
                                <div class="dropdown-header">
                                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                                    <div class="text-muted small">{{ Auth::user()->email }}</div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="bi bi-person-circle me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('logs.index') }}"><i class="bi bi-card-checklist me-2"></i>Logs</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                            </li>
                        </ul>
                    </div>
                @endguest
            </div>
        </nav>

        <main>
            @guest
            {{-- ══════════════════════════════════════════════
                 GUEST — Hero + Features
            ══════════════════════════════════════════════ --}}
            <section class="hero-section text-center">
                <div class="container">
                    <div class="hero-badge"><i class="bi bi-stars me-1"></i> Business Management System</div>
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" height="90" class="mb-3">
                    <h1 class="display-5 fw-bold mb-3">{{ config('app.name', 'Laravel') }}</h1>
                    <p class="lead mb-4" style="opacity:0.75;">
                        Manage bills, stock, customers, and reports — all in one place.
                    </p>
                    <div class="d-flex justify-content-center flex-wrap gap-3">
                        <a href="{{ route('login') }}" class="btn btn-ath btn-lg px-4 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Login to Dashboard
                        </a>
                        <a href="https://webclanka.com" target="_blank" class="btn btn-outline-light btn-lg px-4">
                            <i class="bi bi-headset me-2"></i> Support & Help
                        </a>
                    </div>
                </div>
            </section>

            <section class="py-5">
                <div class="container">
                    <p class="section-label text-center mb-4">What you can do</p>
                    <div class="row g-4 justify-content-center">
                        <div class="col-sm-6 col-md-4">
                            <div class="card feature-card h-100 p-4 text-center">
                                <div class="feature-icon-wrap mx-auto" style="background:rgba(192,98,42,0.12);color:var(--ath-orange);">
                                    <i class="bi bi-receipt"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Bill Management</h5>
                                <p class="text-muted small mb-0">Generate and manage invoices effortlessly. Track payments, dues, and financial records in one place.</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="card feature-card h-100 p-4 text-center">
                                <div class="feature-icon-wrap mx-auto" style="background:rgba(44,62,80,0.10);color:var(--ath-steel);">
                                    <i class="bi bi-boxes"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Stock Management</h5>
                                <p class="text-muted small mb-0">Monitor your inventory in real-time. Manage suppliers and purchase orders effectively.</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="card feature-card h-100 p-4 text-center">
                                <div class="feature-icon-wrap mx-auto" style="background:rgba(138,155,176,0.15);color:var(--ath-chrome);">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <h5 class="fw-bold mb-2">People Management</h5>
                                <p class="text-muted small mb-0">Manage employees and customers. Track roles, permissions, and customer interactions seamlessly.</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="card feature-card h-100 p-4 text-center">
                                <div class="feature-icon-wrap mx-auto" style="background:rgba(192,98,42,0.08);color:var(--ath-orange-lt);">
                                    <i class="bi bi-tools"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Tyre Repairs</h5>
                                <p class="text-muted small mb-0">Log and track tyre repair jobs with ease. Keep service records organized and accessible.</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="card feature-card h-100 p-4 text-center">
                                <div class="feature-icon-wrap mx-auto" style="background:rgba(26,35,50,0.08);color:var(--ath-dark);">
                                    <i class="bi bi-speedometer2"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Reports & Analytics</h5>
                                <p class="text-muted small mb-0">Get daily sales, stock inventory, credit summaries, and more — at a glance.</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="card feature-card h-100 p-4 text-center">
                                <div class="feature-icon-wrap mx-auto" style="background:rgba(138,155,176,0.12);color:#5a6e82;">
                                    <i class="bi bi-bank"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Cheque Tracking</h5>
                                <p class="text-muted small mb-0">Track cheques and company transactions. Stay on top of all pending and cleared payments.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            @else
            {{-- ══════════════════════════════════════════════
                 AUTH — Dashboard with Shortcuts
            ══════════════════════════════════════════════ --}}
            <div class="dashboard-header">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <p class="mb-1 opacity-75" style="font-size:0.85rem;">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ \Carbon\Carbon::now()->format('l, F j, Y') }}
                            </p>
                            <h2 class="fw-bold mb-0">
                                Good {{ \Carbon\Carbon::now()->hour < 12 ? 'Morning' : (\Carbon\Carbon::now()->hour < 17 ? 'Afternoon' : 'Evening') }},
                                {{ explode(' ', Auth::user()->name)[0] }}! 👋
                            </h2>
                            <p class="mb-0 mt-1 opacity-75" style="font-size:0.9rem;">Here's a quick overview of your workspace.</p>
                        </div>
                        <a href="{{ route('direct_bills.create') }}" class="btn btn-new-bill fw-semibold px-4">
                            <i class="bi bi-plus-lg me-2"></i> New Bill
                        </a>
                    </div>
                </div>
            </div>

            <div class="container-fluid px-4 pt-5 pb-4">

                {{-- Quick Shortcuts --}}
                <p class="section-label">Quick Shortcuts</p>
                <div class="row g-3 mb-4">

                    {{-- Bills --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('direct_bills.index') }}" class="shortcut-card bg-white p-3 text-center">
                            <div class="shortcut-icon mx-auto mb-2" style="background:rgba(192,98,42,0.12);color:var(--ath-orange);">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <div class="fw-semibold" style="font-size:0.88rem;">Bills</div>
                        </a>
                    </div>

                    {{-- New Bill --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('direct_bills.create') }}" class="shortcut-card bg-white p-3 text-center">
                            <div class="shortcut-icon mx-auto mb-2" style="background:rgba(192,98,42,0.08);color:var(--ath-orange-lt);">
                                <i class="bi bi-plus-circle"></i>
                            </div>
                            <div class="fw-semibold" style="font-size:0.88rem;">New Bill</div>
                        </a>
                    </div>

                    {{-- Stock Items --}}
                    @can('view stock items')
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('stock_items.index') }}" class="shortcut-card bg-white p-3 text-center">
                            <div class="shortcut-icon mx-auto mb-2" style="background:rgba(44,62,80,0.10);color:var(--ath-steel);">
                                <i class="bi bi-boxes"></i>
                            </div>
                            <div class="fw-semibold" style="font-size:0.88rem;">Stock Items</div>
                        </a>
                    </div>
                    @endcan

                    {{-- Tyre Repairs --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('tyre_repairs.index') }}" class="shortcut-card bg-white p-3 text-center">
                            <div class="shortcut-icon mx-auto mb-2" style="background:rgba(192,98,42,0.08);color:#a0522d;">
                                <i class="bi bi-tools"></i>
                            </div>
                            <div class="fw-semibold" style="font-size:0.88rem;">Tyre Repairs</div>
                        </a>
                    </div>

                    {{-- Customers --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('customers.index') }}" class="shortcut-card bg-white p-3 text-center">
                            <div class="shortcut-icon mx-auto mb-2" style="background:rgba(138,155,176,0.15);color:var(--ath-chrome);">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="fw-semibold" style="font-size:0.88rem;">Customers</div>
                        </a>
                    </div>

                    {{-- Purchase Orders --}}
                    @can('view purchase orders')
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('purchase_orders.index') }}" class="shortcut-card bg-white p-3 text-center">
                            <div class="shortcut-icon mx-auto mb-2" style="background:rgba(26,35,50,0.10);color:var(--ath-dark);">
                                <i class="bi bi-journal-text"></i>
                            </div>
                            <div class="fw-semibold" style="font-size:0.88rem;">Purchase Orders</div>
                        </a>
                    </div>
                    @endcan

                    {{-- Suppliers --}}
                    @can('view suppliers')
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('suppliers.index') }}" class="shortcut-card bg-white p-3 text-center">
                            <div class="shortcut-icon mx-auto mb-2" style="background:rgba(44,62,80,0.08);color:#5a7a8a;">
                                <i class="bi bi-truck"></i>
                            </div>
                            <div class="fw-semibold" style="font-size:0.88rem;">Suppliers</div>
                        </a>
                    </div>
                    @endcan

                    {{-- Reports --}}
                    @can('view reports')
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('reports.index') }}" class="shortcut-card bg-white p-3 text-center">
                            <div class="shortcut-icon mx-auto mb-2" style="background:rgba(192,98,42,0.10);color:var(--ath-orange);">
                                <i class="bi bi-speedometer2"></i>
                            </div>
                            <div class="fw-semibold" style="font-size:0.88rem;">Reports</div>
                        </a>
                    </div>
                    @endcan

                    {{-- Daily Sales --}}
                    @can('view reports')
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('reports.daily_sales') }}" class="shortcut-card bg-white p-3 text-center">
                            <div class="shortcut-icon mx-auto mb-2" style="background:rgba(192,98,42,0.08);color:var(--ath-orange-lt);">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div class="fw-semibold" style="font-size:0.88rem;">Daily Sales</div>
                        </a>
                    </div>
                    @endcan

                    {{-- Cheques --}}
                    @can('view attributes')
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('cheques.index') }}" class="shortcut-card bg-white p-3 text-center">
                            <div class="shortcut-icon mx-auto mb-2" style="background:rgba(138,155,176,0.12);color:#5a6e82;">
                                <i class="bi bi-bank"></i>
                            </div>
                            <div class="fw-semibold" style="font-size:0.88rem;">Cheques</div>
                        </a>
                    </div>
                    @endcan

                    {{-- Users (admin only) --}}
                    @can('view users')
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('users.index') }}" class="shortcut-card bg-white p-3 text-center">
                            <div class="shortcut-icon mx-auto mb-2" style="background:rgba(26,35,50,0.08);color:var(--ath-dark);">
                                <i class="bi bi-shield-person"></i>
                            </div>
                            <div class="fw-semibold" style="font-size:0.88rem;">Users</div>
                        </a>
                    </div>
                    @endcan

                </div>

                {{-- Quick Actions --}}
                <p class="section-label mt-2">Quick Actions</p>
                <div class="row g-3 mb-2">
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <a href="{{ route('direct_bills.create') }}"
                            class="btn btn-ath w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                            <i class="bi bi-plus-circle-fill"></i> Create New Bill
                        </a>
                    </div>
                    @can('view reports')
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <a href="{{ route('reports.daily_sales') }}"
                            class="btn btn-outline-ath w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                            <i class="bi bi-bar-chart-line"></i> Today's Sales Report
                        </a>
                    </div>
                    @endcan
                    @can('view stock items')
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <a href="{{ route('stock_items.index') }}"
                            class="btn w-100 d-flex align-items-center justify-content-center gap-2 py-2"
                            style="border:1.5px solid var(--ath-steel);color:var(--ath-steel);">
                            <i class="bi bi-boxes"></i> View Stock Items
                        </a>
                    </div>
                    @endcan
                    @can('view purchase orders')
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <a href="{{ route('purchase_orders.create') }}"
                            class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                            <i class="bi bi-journal-plus"></i> New Purchase Order
                        </a>
                    </div>
                    @endcan
                </div>

            </div>
            @endauth
        </main>

        <footer class="py-3 text-center">
            <p class="mb-0">
                <strong style="color:#fff;">Powered by</strong>:
                <a href="https://webclanka.com" target="_blank" class="text-decoration-none">Web C Lanka</a>
            </p>
            <p class="mb-0">Developed in Sri Lanka &nbsp;•&nbsp; &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>

</html>
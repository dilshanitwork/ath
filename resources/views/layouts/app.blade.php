<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/Tire_logo.png') }}">

        @stack('styles')
    </head>

    <body>
        <div class="d-flex" id="wrapper">

            {{-- ══════════════════════════════════════════════
                 SIDEBAR — Light Theme
            ══════════════════════════════════════════════ --}}
            <nav id="sidebar-wrapper" class="border-end">

                {{-- Logo --}}
                <div class="sidebar-heading d-flex justify-content-center align-items-center py-3">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('img/Tire_logo.png') }}" alt="{{ config('app.name', 'Laravel') }} Logo"
                            class="img-fluid" style="max-height: 56px;">
                    </a>
                </div>

                {{-- Orange accent divider under logo --}}
                <div style="height: 3px; background: linear-gradient(90deg, #c0622a, #d4733a, #8a9bb0);"></div>

                <div class="list-group list-group-flush mt-1">

                    {{-- 1. Home --}}
                    <a href="{{ route('home') }}"
                        class="list-group-item list-group-item-action {{ Request::is('/') ? 'active' : '' }}">
                        <i class="bi bi-house-door me-2"></i> Home
                    </a>

                    {{-- 2. Bills --}}
                    <a href="{{ route('direct_bills.index') }}"
                        class="list-group-item list-group-item-action {{ Request::is('direct_bills*') ? 'active' : '' }}">
                        <i class="bi bi-receipt me-2"></i> Bills
                    </a>

                    {{-- 3. Stock Items --}}
                    @can('view stock items')
                        <a href="{{ route('stock_items.index') }}"
                            class="list-group-item list-group-item-action {{ Request::is('stock_items*') ? 'active' : '' }}">
                            <i class="bi bi-boxes me-2"></i> Stock Items
                        </a>
                    @endcan

                    {{-- 4. Tyre Repairs --}}
                    <a href="{{ route('tyre_repairs.index') }}"
                        class="list-group-item list-group-item-action {{ Request::is('tyre_repairs*') ? 'active' : '' }}">
                        <i class="bi bi-tools me-2"></i> Tyre Repairs
                    </a>

                    {{-- 5. Customers --}}
                    <a href="{{ route('customers.index') }}"
                        class="list-group-item list-group-item-action {{ Request::is('customers*') ? 'active' : '' }}">
                        <i class="bi bi-person me-2"></i> Customers
                    </a>

                    {{-- 6. Purchasing Dropdown --}}
                    @if (Auth::user()->can('view purchase orders') || Auth::user()->can('view suppliers'))
                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                            data-bs-toggle="collapse" href="#stockManagementCollapse" role="button"
                            aria-expanded="{{ Request::is(['purchase_orders*', 'suppliers*']) ? 'true' : 'false' }}">
                            <div><i class="bi bi-box-seam me-2"></i> Purchasing</div>
                            <i class="bi bi-chevron-down ms-auto sidebar-chevron" style="font-size: 0.8rem;"></i>
                        </a>
                        <div class="{{ Request::is(['purchase_orders*', 'suppliers*']) ? 'show' : '' }} collapse"
                            id="stockManagementCollapse">
                            @can('view suppliers')
                                <a href="{{ route('suppliers.index') }}"
                                    class="list-group-item list-group-item-action {{ Request::is('suppliers*') ? 'active' : '' }} ps-5">
                                    <i class="bi bi-truck me-2"></i> Suppliers
                                </a>
                            @endcan
                            @can('view purchase orders')
                                <a href="{{ route('purchase_orders.index') }}"
                                    class="list-group-item list-group-item-action {{ Request::is('purchase_orders*') ? 'active' : '' }} ps-5">
                                    <i class="bi bi-journal-text me-2"></i> Purchase Orders
                                </a>
                            @endcan
                        </div>
                    @endif

                    {{-- 7. Reports --}}
                    @if (Auth::user()->can('view reports'))
                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                            data-bs-toggle="collapse" href="#reportsCollapse" role="button"
                            aria-expanded="{{ Request::is(['reports*']) ? 'true' : 'false' }}">
                            <div><i class="bi bi-speedometer2 me-2"></i> Reports</div>
                            <i class="bi bi-chevron-down ms-auto sidebar-chevron" style="font-size: 0.8rem;"></i>
                        </a>
                        <div class="{{ Request::is(['reports*']) ? 'show' : '' }} collapse" id="reportsCollapse">
                            @can('view reports')
                                <a href="{{ route('reports.index') }}"
                                    class="list-group-item list-group-item-action {{ Request::is('reports') ? 'active' : '' }} ps-5">
                                    <i class="bi bi-file-earmark-text me-2"></i> All Reports
                                </a>
                            @endcan
                            @can('view reports')
                                <a href="{{ route('reports.daily_sales') }}"
                                    class="list-group-item list-group-item-action {{ Request::is('reports/daily-sales*') ? 'active' : '' }} ps-5">
                                    <i class="bi bi-currency-dollar me-2"></i> Daily Sales
                                </a>
                            @endcan
                            @can('view reports')
                                <a href="{{ route('reports.stock_inventory') }}"
                                    class="list-group-item list-group-item-action {{ Request::is('reports/stock-inventory*') ? 'active' : '' }} ps-5">
                                    <i class="bi bi-boxes me-2"></i> Stock Inventory
                                </a>
                            @endcan
                            @can('view reports')
                                <a href="{{ route('reports.purchase_orders') }}"
                                    class="list-group-item list-group-item-action {{ Request::is('reports/purchase-orders*') ? 'active' : '' }} ps-5">
                                    <i class="bi bi-journal-text me-2"></i> Purchase Orders
                                </a>
                            @endcan
                            @can('view reports')
                                <a href="{{ route('reports.tyre_repairs') }}"
                                    class="list-group-item list-group-item-action {{ Request::is('reports/tyre-repairs*') ? 'active' : '' }} ps-5">
                                    <i class="bi bi-wrench me-2"></i> Tyre Repairs
                                </a>
                            @endcan
                            @can('view reports')
                                <a href="{{ route('reports.credit_summary') }}"
                                    class="list-group-item list-group-item-action {{ Request::is('reports/credit_summary*') ? 'active' : '' }} ps-5">
                                    <i class="bi bi-wallet2 me-2"></i> Credit Summary
                                </a>
                            @endcan
                            @can('view reports')
                                <a href="{{ route('reports.customer_credit_report') }}"
                                    class="list-group-item list-group-item-action {{ Request::is('reports/customer_credit_report*') ? 'active' : '' }} ps-5">
                                    <i class="bi bi-people me-2"></i>  Customer Report
                                </a>
                            @endcan
                        </div>
                    @endif

                    {{-- 8. Cheques --}}
                    @can('view attributes')
                        <a href="{{ route('cheques.index') }}"
                            class="list-group-item list-group-item-action {{ Request::is('cheques*') ? 'active' : '' }}">
                            <i class="bi bi-bank me-2"></i> Cheques
                        </a>
                    @endcan

                    {{-- 9. Companies --}}
                    @can('view attributes')
                        <a href="{{ route('companies.index') }}"
                            class="list-group-item list-group-item-action {{ Request::is('Companies*') ? 'active' : '' }}">
                            <i class="bi bi-building me-2"></i> Companies
                        </a>
                    @endcan

                    {{-- 10. Complaints (UC) --}}
                    @can('view attributes')
                        <a href="{{ route('complaints.index') }}"
                            class="list-group-item list-group-item-action {{ Request::is('complaints*') ? 'active' : '' }}">
                            <i class="bi bi-exclamation-octagon me-2"></i> UC
                        </a>
                    @endcan

                    {{-- 11. User Management --}}
                    @if (Auth::user()->can('view users') || Auth::user()->can('view roles') || Auth::user()->can('view permissions'))
                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                            data-bs-toggle="collapse" href="#userManagementCollapse" role="button"
                            aria-expanded="{{ Request::is(['users*', 'roles*', 'permissions*']) ? 'true' : 'false' }}">
                            <div><i class="bi bi-people me-2"></i> Users</div>
                            <i class="bi bi-chevron-down ms-auto sidebar-chevron" style="font-size: 0.8rem;"></i>
                        </a>
                        <div class="{{ Request::is(['users*', 'roles*', 'permissions*']) ? 'show' : '' }} collapse"
                            id="userManagementCollapse">
                            @can('view users')
                                <a href="{{ route('users.index') }}"
                                    class="list-group-item list-group-item-action {{ Request::is('users*') ? 'active' : '' }} ps-5">
                                    <i class="bi bi-person me-2"></i> Users
                                </a>
                            @endcan
                            @can('view roles')
                                <a href="{{ route('roles.index') }}"
                                    class="list-group-item list-group-item-action {{ Request::is('roles*') ? 'active' : '' }} ps-5">
                                    <i class="bi bi-shield-lock me-2"></i> Roles
                                </a>
                            @endcan
                            @can('view permissions')
                                <a href="{{ route('permissions.index') }}"
                                    class="list-group-item list-group-item-action {{ Request::is('permissions*') ? 'active' : '' }} ps-5">
                                    <i class="bi bi-key me-2"></i> Permissions
                                </a>
                            @endcan
                        </div>
                    @endif

                    {{-- 12. Admin / Config --}}
                    @can('view attributes')
                        <a href="{{ route('attributes.index') }}"
                            class="list-group-item list-group-item-action {{ Request::is('attribute*') ? 'active' : '' }}">
                            <i class="bi bi-tags me-2"></i> Attributes
                        </a>
                    @endcan

                    @can('view attributes')
                        <a href="{{ route('attribute-values.index') }}"
                            class="list-group-item list-group-item-action {{ Request::is('attribute-values*') ? 'active' : '' }}">
                            <i class="bi bi-list me-2"></i> Attr Values
                        </a>
                    @endcan

                </div>
            </nav>

            {{-- ══════════════════════════════════════════════
                 MAIN CONTENT
            ══════════════════════════════════════════════ --}}
            <div id="page-content-wrapper" class="w-100">

                {{-- Top Navbar --}}
                <nav class="navbar navbar-expand-lg border-bottom bg-white">
                    <div class="container-fluid">
                        <button class="btn btn-ath-outline d-none d-lg-inline me-2" id="sidebarCollapse">☰</button>
                        <button class="btn btn-ath-outline d-lg-none me-2" id="mobileSidebarToggle">☰</button>

                        <span class="navbar-brand fw-bold mb-0" style="color: #1a2332;">
                            {{ config('app.name', 'Laravel') }}
                        </span>

                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="navbar-collapse justify-content-end collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ms-auto align-items-center gap-1">
                                @guest
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}">
                                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                                        </a>
                                    </li>
                                    @if (Route::has('register'))
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('register') }}">
                                                <i class="bi bi-person-plus me-1"></i> Register
                                            </a>
                                        </li>
                                    @endif
                                @else
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#"
                                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="avatar avatar-sm">
                                                @php
                                                    $name = Auth::user()->name ?? 'Admin';
                                                    $initials = '';
                                                    $parts = explode(' ', $name);
                                                    foreach ($parts as $part) {
                                                        $initials .= strtoupper(substr($part, 0, 1));
                                                    }
                                                    if (strlen($initials) > 2) {
                                                        $initials = substr($initials, 0, 1) . substr($initials, -1, 1);
                                                    } elseif (strlen($initials) == 1 && strlen($name) > 1) {
                                                        $initials .= strtoupper(substr($name, 1, 1));
                                                    }
                                                @endphp
                                                {{ $initials }}
                                            </span>
                                            <span class="d-none d-sm-inline text-dark">{{ Auth::user()->name ?? 'Admin' }}</span>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end mt-2 border-0 shadow">
                                            <li>
                                                <div class="dropdown-header px-3 py-2">
                                                    <div class="fw-bold">{{ Auth::user()->name ?? 'Admin' }}</div>
                                                    <div class="text-muted small">{{ Auth::user()->email ?? '' }}</div>
                                                </div>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('profile.index') }}">
                                                    <i class="bi bi-person-circle me-2"></i> My Profile
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('logs.index') }}">
                                                    <i class="bi bi-card-checklist me-2"></i> Logs
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                                </a>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                    class="d-none">
                                                    @csrf
                                                </form>
                                            </li>
                                        </ul>
                                    </li>
                                @endguest
                            </ul>
                        </div>
                    </div>
                </nav>

                {{-- Breadcrumb --}}
                <header class="border-bottom px-4 py-2" style="background: #f4f5f7;">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 rounded p-1" style="background: transparent;">
                            <li class="breadcrumb-item">
                                <a href="{{ url('/') }}" class="text-decoration-none" style="color: #c0622a;">
                                    <i class="bi bi-house-door"></i> Home
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"
                                style="color: #2c3e50; font-weight: 500;">
                                @yield('title')
                            </li>
                        </ol>
                    </nav>
                </header>

                {{-- Page Content --}}
                <div class="container-fluid py-4">
                    @yield('content')
                </div>

                {{-- Footer --}}
                <div class="sidebar-footer border-top py-3 text-center"
                    style="background: #fff; border-top: 3px solid #c0622a !important;">
                    <p class="mb-0 text-muted">
                        <strong class="text-dark">Powered by</strong>:
                        <a href="https://webclanka.com" target="_blank"
                            class="text-decoration-none" style="color: #c0622a;">Web C Lanka</a>
                    </p>
                    <p class="mb-0 text-muted">Developed in Sri Lanka.</p>
                    <p class="mb-0 text-muted">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
                </div>

            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.tiny.cloud/1/y6inz4fbe866bo5buh9ygn882aquc5lnk33k0put0ucvpb6u/tinymce/8/tinymce.min.js"
            referrerpolicy="origin" crossorigin="anonymous"></script>

        <script>
            const sidebar = document.getElementById('sidebar-wrapper');
            const wrapper = document.getElementById('wrapper');

            const desktopToggle = document.getElementById('sidebarCollapse');
            if (desktopToggle) {
                desktopToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('collapsed');
                    wrapper.classList.toggle('desktop-collapsed');
                });
            }

            const mobileToggle = document.getElementById('mobileSidebarToggle');
            if (mobileToggle) {
                mobileToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('mobile-show');
                });
            }

            const sidebarMenuToggle = document.getElementById('sidebarMenuToggle');
            if (sidebarMenuToggle) {
                sidebarMenuToggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (window.innerWidth >= 992) {
                        sidebar.classList.toggle('collapsed');
                        wrapper.classList.toggle('desktop-collapsed');
                    } else {
                        sidebar.classList.toggle('mobile-show');
                    }
                });
            }
        </script>

        <style>
            /* ── ATH Brand Variables ── */
            :root {
                --ath-dark:      #1a2332;
                --ath-steel:     #2c3e50;
                --ath-orange:    #c0622a;
                --ath-orange-lt: #d4733a;
                --ath-chrome:    #8a9bb0;
                --ath-light-bg:  #f4f5f7;
            }

            * { font-family: 'Figtree', sans-serif; }

            body { background-color: var(--ath-light-bg); }

            /* ══════════════════════════════════════════════
               SIDEBAR — Light Theme
            ══════════════════════════════════════════════ */
            #sidebar-wrapper {
                width: 220px;
                min-height: 100vh;
                transition: width 0.3s, margin 0.3s;
                z-index: 1040;
                background-color: #fff;          /* ✅ White sidebar */
                border-right: 1px solid #e3e6ea;
            }

            #sidebar-wrapper.collapsed { width: 60px; }

            /* Logo area */
            #sidebar-wrapper .sidebar-heading {
                background-color: #fff;
            }

            /* All sidebar links — light text on white */
            #sidebar-wrapper .list-group-item {
                background-color: #fff;
                color: #4a5568;                  /* ✅ Dark grey text */
                border-color: #f0f2f4;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                font-size: 0.9rem;
                padding: 0.65rem 1rem;
                border-left: 3px solid transparent;
                transition: background-color 0.15s ease, color 0.15s ease, border-left-color 0.15s ease;
            }

            /* Hover */
            #sidebar-wrapper .list-group-item:hover {
                background-color: rgba(192, 98, 42, 0.07) !important;
                color: var(--ath-orange) !important;
                border-left-color: var(--ath-orange) !important;
            }

            /* Active */
            #sidebar-wrapper .list-group-item.active {
                background-color: rgba(192, 98, 42, 0.10) !important;
                border-left: 3px solid var(--ath-orange) !important;
                color: var(--ath-orange) !important;
                font-weight: 600;
            }

            /* Nested items */
            #sidebar-wrapper .ps-5 {
                background-color: #f8f9fa !important;
                font-size: 0.85rem;
                color: #6c757d;
            }

            #sidebar-wrapper .ps-5:hover {
                background-color: rgba(192, 98, 42, 0.07) !important;
                color: var(--ath-orange) !important;
            }

            #sidebar-wrapper .ps-5.active {
                background-color: rgba(192, 98, 42, 0.10) !important;
                color: var(--ath-orange) !important;
            }

            /* Chevron */
            .sidebar-chevron { color: #adb5bd; }

            /* ══════════════════════════════════════════════
               TOP NAVBAR
            ══════════════════════════════════════════════ */
            .navbar.bg-white {
                border-bottom: 3px solid var(--ath-orange) !important;
            }

            .btn-ath-outline {
                background: transparent;
                border: 1.5px solid var(--ath-orange);
                color: var(--ath-orange);
                border-radius: 6px;
                padding: 0.3rem 0.6rem;
                transition: background 0.15s, color 0.15s;
            }

            .btn-ath-outline:hover {
                background: var(--ath-orange);
                color: #fff;
            }

            /* ══════════════════════════════════════════════
               AVATAR
            ══════════════════════════════════════════════ */
            .avatar {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background-color: var(--ath-orange);
                color: #fff;
                font-weight: 600;
                font-size: 0.78rem;
                flex-shrink: 0;
            }

            .dropdown-item i { width: 20px; text-align: center; }

            /* Breadcrumb divider */
            .breadcrumb-item + .breadcrumb-item::before { color: var(--ath-chrome); }

            /* Desktop collapsed */
            #wrapper.desktop-collapsed #page-content-wrapper { margin-left: 0; }

            /* ══════════════════════════════════════════════
               MOBILE
            ══════════════════════════════════════════════ */
            @media (max-width: 991.98px) {
                #sidebar-wrapper {
                    position: fixed;
                    left: -220px;
                    top: 0;
                    height: 100%;
                    width: 220px;
                    transition: left 0.3s;
                    box-shadow: none;
                }

                #sidebar-wrapper.mobile-show {
                    left: 0;
                    box-shadow: 4px 0 20px rgba(0, 0, 0, 0.12);
                }

                #page-content-wrapper { margin-left: 0 !important; }
            }
        </style>

        <script>
            tinymce.init({
                selector: 'textarea.webclanka-editor',
                plugins: [
                    'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists', 'media',
                    'searchreplace', 'table', 'visualblocks', 'wordcount',
                    'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker',
                    'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'advtemplate', 'ai',
                    'uploadcare', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags',
                    'autocorrect', 'typography', 'inlinecss', 'markdown', 'importword', 'exportword', 'exportpdf'
                ],
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography uploadcare | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
                tinycomments_mode: 'embedded',
                tinycomments_author: 'Author name',
                mergetags_list: [
                    { value: 'First.Name', title: 'First Name' },
                    { value: 'Email',      title: 'Email'      },
                ],
                ai_request: (request, respondWith) => respondWith.string(() => Promise.reject(
                    'See docs to implement AI Assistant')),
                uploadcare_public_key: 'c80fa72321a0c9c2af47',
            });
        </script>

        @stack('scripts')
    </body>

</html>
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

        <style>
            body {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }

            main {
                flex: 1;
            }

            .hero-section {
                background: linear-gradient(rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.8)), url('{{ asset('img/background.jpg') }}') no-repeat center center;
                background-size: cover;
                padding: 5rem 0;
            }

            .feature-card {
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s ease;
            }

            .feature-card:hover {
                transform: translateY(-5px);
            }

            .feature-icon {
                font-size: 3rem;
                color: #0d6efd;
            }
        </style>

    </head>

    <body>
        <main>
            <div class="content text-center">
                <section class="hero-section">
                    <div class="container mt-5">
                        <h1 class="display-1">
                            <img src="{{ asset('img/Tire_logo.png') }}" alt="KE" width="100">
                            {{ config('app.name', 'Laravel') }}
                        </h1>
                        <img src="{{ asset('img/Tire_logo.png') }}" alt="Web C Lanka" style="max-width: 300px;">
                        <hr class="my-4">
                        <p>Please log in or register to access the full features.</p>

                        <div class="links mt-4">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-2">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Login
                            </a>
                            @if (Route::has('register'))
                                <a href="https://webclanka.com" target="_blank" class="btn btn-secondary btn-lg">
                                    <i class="bi bi-headset me-2"></i> Support & Help
                                </a>
                            @endif
                        </div>
                    </div>
                </section>

                <section id="features" class="py-5">
                    <div class="container">
                        <div class="row text-center">
                            <div class="col-md-4 mb-4">
                                <div class="card feature-card h-100 p-4">
                                    <div class="card-body">
                                        <i class="bi bi-people-fill feature-icon"></i>
                                        <h3 class="card-title mt-3"><i class="bi bi-person-circle"></i> People
                                            Management</h3>
                                        <p class="card-text">
                                            Efficiently manage your employees and customers. Track roles, permissions,
                                            and customer
                                            interactions seamlessly.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card feature-card h-100 p-4">
                                    <div class="card-body">
                                        <i class="bi bi-file-earmark-text-fill feature-icon"></i>
                                        <h3 class="card-title mt-3"><i class="bi bi-file-earmark-text"></i> Bill
                                            Management</h3>
                                        <p class="card-text">
                                            Generate and manage invoices effortlessly. Keep track of payments, dues, and
                                            financial
                                            records in one place.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card feature-card h-100 p-4">
                                    <div class="card-body">
                                        <i class="bi bi-box-seam-fill feature-icon"></i>
                                        <h3 class="card-title mt-3"><i class="bi bi-box"></i> Stock Management</h3>
                                        <p class="card-text">
                                            Monitor your inventory in real-time. Receive alerts for low stock and manage
                                            suppliers
                                            effectively.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <footer class="bg-light text-dark border-top py-4 text-center">
            <p class="mb-0">
                <strong>Powered by</strong>: <a href="https://webclanka.com" target="_blank"
                    class="text-decoration-none">Web C Lanka</a>
            </p>
            <p class="mb-0">Developed in Sri Lanka.</p>
            <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>

</html>

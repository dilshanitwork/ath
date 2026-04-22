<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>
            body {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                background-color: #f8fafc;
                /* A light gray background similar to the original Tailwind config */
            }

            .guest-container {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem 1rem;
            }

            .auth-card {
                width: 100%;
                max-width: 50rem;
                /* Equivalent to sm:max-w-md */
            }

            .footer {
                background-color: #f8f9fa;
                /* Light background for the footer */
                border-top: 1px solid #e9ecef;
            }
        </style>
    </head>

    <body>
        <div class="guest-container">
            <div class="auth-card">
                <div class="mb-4 text-center">
                    <a href="{{ url('home') }}" class="text-decoration-none">
                        <img src="{{ asset('img/Tire_logo.png') }}" alt="{{ config('app.name', 'Laravel') }} Logo"
                            class="img-fluid" style="max-height: 80px;">
                        <h1 class="h1 fw-bold text-dark mt-2">{{ config('app.name', 'Laravel') }}</h1>
                    </a>
                </div>

                <div class="p-4 shadow-sm">
                    @yield('content')
                </div>
            </div>
        </div>

        <footer class="footer text-dark py-3 text-center">
            <div class="container">
                <p class="mb-0">
                    <strong>Powered by</strong>: <a href="https://webclanka.com" target="_blank"
                        class="text-decoration-none">Web C Lanka</a>
                </p>
                <p class="mb-0">Developed in Sri Lanka.</p>
                <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>

</html>

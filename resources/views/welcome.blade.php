<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">{{ config('app.name', 'Laravel') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/dashboard') }}">Dashboard</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Log in</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="p-5 mb-4 bg-light rounded-3 shadow-sm">
            <div class="container-fluid py-5">
                <h1 class="display-5 fw-bold">Selamat Datang di Laravel</h1>
                <p class="col-md-8 fs-4">Proyek ini sekarang sudah bersih menggunakan <strong>Bootstrap 5</strong>. Semua dependensi Tailwind CSS telah dihapus.</p>
                <a class="btn btn-primary btn-lg" href="https://getbootstrap.com/docs/5.3/" target="_blank">Pelajari Bootstrap 5</a>
            </div>
        </div>

        <div class="row align-items-md-stretch text-center">
            <div class="col-md-4 mb-3">
                <div class="h-100 p-5 text-white bg-dark rounded-3">
                    <h2>Dokumentasi</h2>
                    <p>Laravel memiliki dokumentasi yang lengkap dan mudah dipahami. Silakan pelajari lebih lanjut.</p>
                    <a class="btn btn-outline-light" href="https://laravel.com/docs" target="_blank">Baca Dokumen</a>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="h-100 p-5 bg-light border rounded-3">
                    <h2>Laracasts</h2>
                    <p>Tonton tutorial video melalui Laracasts untuk memperdalam pemahaman Laravel Anda.</p>
                    <a class="btn btn-outline-secondary" href="https://laracasts.com" target="_blank">Tonton Video</a>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="h-100 p-5 bg-light border rounded-3">
                    <h2>Ekosistem</h2>
                    <p>Jelajahi berbagai paket dan alat bantu keren yang disediakan oleh komunitas Laravel.</p>
                    <a class="btn btn-outline-secondary" href="https://laravel.com/ecosystem" target="_blank">Lihat Ekosistem</a>
                </div>
            </div>
        </div>

        <footer class="pt-3 mt-4 text-muted border-top text-center">
            &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }} &middot; Bootstrap 5 Enabled
        </footer>
    </div>
</body>
</html>

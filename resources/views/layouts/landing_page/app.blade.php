<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Resumify | The Curated Manuscript')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,700&family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen flex flex-col bg-surface font-body text-primary">
    {{-- Path: resources/views/layouts/landing_page/navbar.blade.php --}}
    @include('layouts.landing_page.navbar')

    <main class="flex-grow">
        @yield('content')
    </main>

    @include('layouts.landing_page.footer')
    @livewireScripts
</body>
</html>
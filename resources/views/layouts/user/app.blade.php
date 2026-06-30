<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Resumify')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&family=Manrope:wght@200..800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-surface text-primary font-body antialiased @yield('body_class', 'min-h-screen flex')">
    
    @include('layouts.user.sidenavbar')

    <div class="flex-1 w-full md:pl-64 min-h-screen flex flex-col">
        @yield('content')
    </div>

    @include('layouts.user.mobilenavbar')

    <x-user.toast />
    @stack('scripts')
    @livewireScripts

</body>
</html>

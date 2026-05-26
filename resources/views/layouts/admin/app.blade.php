<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title', 'Admin Dashboard - Resumify')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&family=Manrope:wght@200..800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-surface text-primary font-body antialiased min-h-screen flex">
    
    @include('layouts.admin.sidenavbar')

    <div class="flex-1 flex flex-col min-h-screen overflow-hidden bg-surface">
        <!-- Top Header -->
        <header class="h-20 flex items-center justify-between px-10 border-b border-primary/10 bg-surface/80 backdrop-blur-md sticky top-0 z-10">
            <!-- Search -->
            <div class="flex items-center text-primary/40 flex-1">
                <span class="material-symbols-outlined text-[20px] mr-2">search</span>
                <input type="text" placeholder="Search analytics or users..." class="bg-transparent border-none focus:outline-none text-sm font-label w-[300px] text-primary placeholder:text-primary/40">
            </div>

            <!-- Right Actions -->
            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-3 text-primary/60">
                    <button class="hover:text-primary transition-colors flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">notifications</span>
                    </button>
                    <button class="hover:text-primary transition-colors flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">settings</span>
                    </button>
                </div>
                
                <div class="h-6 w-px bg-primary/20"></div>

                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <p class="text-sm font-headline font-bold text-primary">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] font-label text-primary/50 uppercase tracking-widest">RESUMIFY HQ</p>
                    </div>
                    <div class="w-9 h-9 rounded-full overflow-hidden bg-primary/10 flex items-center justify-center text-sm font-bold">
                        <img alt="Admin Avatar" class="w-full h-full object-cover" src="{{ auth()->user()->avatar_url }}" onerror="this.outerHTML='{{ substr(auth()->user()->name, 0, 2) }}'"/>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto custom-scrollbar p-10">
            @yield('content')
        </main>
    </div>
    
</body>
</html>

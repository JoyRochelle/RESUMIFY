<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard - Resumify')</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&family=Manrope:wght@200..800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-surface text-primary font-body antialiased min-h-screen flex">
    @php
        $admin = auth()->user();
        $adminUnreadNotificationsCount = $admin?->unreadNotifications()->count() ?? 0;
        $adminNotifications = $admin?->notifications()->latest()->limit(10)->get() ?? collect();
    @endphp
    
    @include('layouts.admin.sidenavbar')

    <div class="flex-1 flex flex-col min-h-screen overflow-hidden bg-surface md:pl-64">
        <header class="sticky top-0 z-10 flex h-16 items-center justify-between border-b border-primary/10 bg-surface/85 px-4 backdrop-blur-md md:h-20 md:px-10">
            <div class="hidden md:block flex-1"></div>
            <div class="md:hidden flex items-center gap-2">
                <span class="text-base font-headline font-bold text-primary">Resumify Admin</span>
            </div>

            <div class="flex items-center gap-4 md:gap-6">
                <div class="flex items-center gap-2 text-primary/60">
                    <div x-data="{ open: false }" class="relative">
                        <button
                            type="button"
                            class="admin-icon-action relative"
                            aria-label="Notifications"
                            aria-haspopup="true"
                            :aria-expanded="open.toString()"
                            @click="open = !open"
                            @click.away="open = false"
                        >
                            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">notifications</span>
                            @if($adminUnreadNotificationsCount > 0)
                                <span class="absolute right-2 top-2 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-none text-white">
                                    {{ $adminUnreadNotificationsCount > 9 ? '9+' : $adminUnreadNotificationsCount }}
                                </span>
                            @endif
                        </button>

                        <div
                            x-show="open"
                            x-transition.origin.top.right
                            style="display: none;"
                            class="absolute right-0 mt-2 w-[min(20rem,calc(100vw-2rem))] overflow-hidden rounded-xl border border-primary/10 bg-surface shadow-lg z-50"
                        >
                            <div class="flex items-center justify-between gap-3 border-b border-primary/10 bg-surface-container-low p-4">
                                <h3 class="text-sm font-headline font-bold text-primary">Notifications</h3>
                                @if($adminUnreadNotificationsCount > 0)
                                    <form action="{{ route('notifications.readAll') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-secondary transition hover:text-secondary/80">
                                            Mark all as read
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div class="max-h-80 overflow-y-auto custom-scrollbar">
                                @forelse($adminNotifications as $notification)
                                    <a
                                        href="{{ route('notifications.read', $notification->id) }}"
                                        class="block border-b border-primary/5 p-4 transition hover:bg-surface-container-low {{ $notification->read_at ? 'opacity-75' : 'bg-secondary/5' }}"
                                    >
                                        <p class="text-sm font-label leading-5 text-primary">
                                            {{ $notification->data['message'] ?? 'You have a new notification.' }}
                                        </p>
                                        <span class="mt-1 block text-[10px] font-label uppercase tracking-wider text-primary/40">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </a>
                                @empty
                                    <div class="p-6 text-center text-sm font-label text-primary/60">
                                        No notifications yet.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="hidden sm:block">
                        <a href="{{ Route::has('admin.settings') ? route('admin.settings') : '#' }}" class="admin-icon-action" aria-label="Admin settings">
                            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">settings</span>
                        </a>
                    </div>
                </div>

                <div class="hidden h-6 w-px bg-primary/20 sm:block"></div>

                <div class="flex items-center gap-3">
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-headline font-bold text-primary">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] font-label uppercase tracking-widest text-primary/50">RESUMIFY HQ</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full bg-primary/10 text-sm font-bold text-primary">
                        <img alt="{{ auth()->user()->name }} avatar" class="h-full w-full object-cover" src="{{ auth()->user()->avatar_url }}" onerror="this.outerHTML='{{ substr(auth()->user()->name, 0, 2) }}'"/>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto custom-scrollbar p-4 pb-24 md:pb-10 sm:p-6 md:p-10">
            @yield('content')
        </main>
    </div>

    @include('layouts.admin.mobiletabbar')

    <x-ui.toast-region />
    @livewireScripts
    @stack('scripts')
</body>
</html>

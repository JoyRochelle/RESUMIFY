@php
    $navItems = config('navigation.admin', []);
@endphp

<aside class="fixed left-0 top-0 z-40 hidden h-screen w-64 shrink-0 flex-col space-y-8 border-r border-primary/10 bg-nav-footer p-6 md:flex">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3" aria-label="Resumify admin dashboard">
        <img src="{{ asset('images/logo.jpg') }}" alt="Resumify" class="h-10 w-10 rounded-lg object-cover shadow-sm">
        <span class="text-xl font-bold font-headline text-primary tracking-tight">Resumify</span>
    </a>

    <div class="rounded-lg border border-primary/10 bg-tertiary p-4 shadow-sm">
        <p class="text-xs font-label font-bold uppercase tracking-widest text-primary/40">Admin Console</p>
        <p class="mt-1 text-sm font-headline font-bold text-primary">{{ auth()->user()->name }}</p>
        <p class="mt-0.5 text-xs font-label text-primary/60">The Editorial Architect</p>
    </div>

    <nav class="flex-1 flex flex-col space-y-2" aria-label="Admin navigation">
        @foreach($navItems as $item)
            @php($active = request()->routeIs(...$item['match']))
            <a href="{{ route($item['route']) }}"
               class="flex min-h-11 items-center space-x-3 rounded-lg p-3 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-secondary/40 {{ $active ? 'bg-tertiary text-primary font-bold shadow-sm' : 'text-primary/60 hover:bg-tertiary/60 hover:text-primary' }}"
               @if($active) aria-current="page" @endif>
                <span class="material-symbols-outlined {{ $active ? 'icon-filled' : '' }} text-[20px]" aria-hidden="true">{{ $item['icon'] }}</span>
                <span class="font-label text-sm tracking-wide">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="mt-auto border-t border-primary/10 pt-6">
        <form action="{{ route('logout') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" class="flex min-h-11 w-full items-center space-x-3 rounded-lg p-3 text-primary/60 transition-colors hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                <span class="material-symbols-outlined text-[20px]" aria-hidden="true">logout</span>
                <span class="font-label text-sm tracking-wide">Log Out</span>
            </button>
        </form>
    </div>
</aside>

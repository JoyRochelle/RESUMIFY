@php
    $navItems = config('navigation.user', []);
@endphp

<aside class="hidden md:flex flex-col h-screen w-64 border-r border-primary/10 bg-nav-footer p-6 space-y-8 fixed top-0 left-0 z-40 shrink-0">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3" aria-label="Resumify dashboard">
        <img src="{{ asset('images/logo.jpg') }}" alt="Resumify" class="h-10 w-10 rounded-xl object-cover shadow-sm">
        <span class="text-xl font-bold font-headline text-primary tracking-tight">Resumify</span>
    </a>

    <div class="flex items-center space-x-3 mb-4">
        <div class="w-10 h-10 rounded-full overflow-hidden bg-primary/10">
            <img alt="{{ auth()->user()->name }} avatar" class="w-full h-full object-cover" src="{{ auth()->user()->avatar_url }}"/>
        </div>
        <div>
            <p class="text-sm font-headline font-bold text-primary">{{ auth()->user()->name }}</p>
            <p class="text-xs font-label text-primary/60 uppercase tracking-wider">{{ auth()->user()->isPremium() ? 'Premium Member' : 'Basic Member' }}</p>
        </div>
    </div>

    <nav class="flex-1 flex flex-col space-y-2" aria-label="User navigation">
        @foreach($navItems as $item)
            @php
                $active = request()->routeIs(...$item['match']);
                $emphasis = $item['emphasis'] ?? false;
            @endphp
            <a href="{{ route($item['route']) }}"
               class="flex min-h-11 items-center space-x-3 rounded-lg p-3 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-secondary/40 {{ $active ? 'text-primary font-bold bg-tertiary shadow-sm' : ($emphasis ? 'text-secondary bg-secondary/10 hover:bg-secondary/20 font-bold' : 'text-primary/60 hover:text-primary hover:bg-tertiary/60') }}"
               @if($active) aria-current="page" @endif>
                <span class="material-symbols-outlined {{ $active ? 'icon-filled' : '' }}" aria-hidden="true">{{ $item['icon'] }}</span>
                <span class="font-label tracking-wide">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="pt-6 border-t border-primary/10">
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="flex min-h-11 w-full items-center space-x-3 rounded-lg p-3 text-primary/60 hover:bg-red-50 hover:text-red-600 transition-colors focus:outline-none focus:ring-2 focus:ring-red-300">
                <span class="material-symbols-outlined" aria-hidden="true">logout</span>
                <span class="font-label tracking-wide">Log Out</span>
            </button>
        </form>
    </div>
</aside>

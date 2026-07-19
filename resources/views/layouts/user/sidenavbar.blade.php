@php
    $navItems = config('navigation.user', []);
@endphp

<aside class="hidden md:flex flex-col h-screen w-64 border-r border-primary/10 bg-nav-footer p-6 space-y-8 fixed top-0 left-0 z-40 shrink-0">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3" aria-label="Resumify dashboard">
        <img src="{{ asset('images/logo.jpg') }}" alt="Resumify" class="h-10 w-10 rounded-xl object-cover shadow-sm">
        <span class="text-xl font-bold font-headline text-primary tracking-tight">Resumify</span>
    </a>

    <div class="flex items-center space-x-3 mb-4">
        <div class="relative w-10 h-10 rounded-full bg-primary/10 {{ auth()->user()->isPremium() ? 'ring-2 ring-[#A16207]/40 ring-offset-2 ring-offset-nav-footer' : '' }}">
            <div class="h-full w-full overflow-hidden rounded-full">
            <img alt="{{ auth()->user()->name }} avatar" class="w-full h-full object-cover" src="{{ auth()->user()->avatar_url }}"/>
            </div>
            @if(auth()->user()->isPremium())
                <span class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full border-2 border-nav-footer bg-[#A16207] text-white" aria-label="Premium member">
                    <span class="material-symbols-outlined text-[12px] icon-filled" aria-hidden="true">workspace_premium</span>
                </span>
            @endif
        </div>
        <div class="min-w-0">
            <p class="text-sm font-headline font-bold text-primary">{{ auth()->user()->name }}</p>
            <x-user.plan-badge :user="auth()->user()" size="xs" />
        </div>
    </div>

    <nav class="flex-1 flex flex-col space-y-2" aria-label="User navigation">
        @foreach($navItems as $item)
            @php
                $active = request()->routeIs(...$item['match']);
                $emphasis = $item['emphasis'] ?? false;
            @endphp
            <a href="{{ route($item['route']) }}"
               class="flex min-h-11 items-center space-x-3 rounded-lg p-3 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-secondary/40 {{ $active ? 'text-primary font-bold bg-tertiary shadow-sm' : ($emphasis ? 'text-secondary bg-secondary/10 hover:bg-secondary/20 font-bold' : 'text-primary/60 hover:text-primary hover:bg-tertiary/60') }}"
               @if($active) aria-current="page" @endif>
                <span class="material-symbols-outlined {{ $active ? 'icon-filled' : '' }}" aria-hidden="true">{{ $item['icon'] }}</span>
                <span class="font-label tracking-wide">{{ __('messages.nav.' . $item['label_key']) }}</span>
            </a>
        @endforeach
    </nav>

    <div class="pt-6 border-t border-primary/10 space-y-4">
        <x-ui.locale-switcher />

        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="flex min-h-11 w-full items-center space-x-3 rounded-lg p-3 text-primary/60 hover:bg-red-50 hover:text-red-600 transition-colors focus:outline-none focus:ring-2 focus:ring-red-300">
                <span class="material-symbols-outlined" aria-hidden="true">logout</span>
                <span class="font-label tracking-wide">{{ __('messages.nav.logout') }}</span>
            </button>
        </form>
    </div>
</aside>

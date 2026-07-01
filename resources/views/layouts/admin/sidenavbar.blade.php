@php
    $navItems = config('navigation.admin', []);
@endphp

<aside class="hidden md:flex flex-col h-screen w-64 border-r border-primary/10 bg-surface-container-low p-6 space-y-8 sticky top-0 shrink-0">
    <div>
        <h1 class="text-xl font-headline font-bold text-primary">Resumify Admin</h1>
        <p class="text-xs font-label text-primary/60">The Editorial Architect</p>
    </div>

    <nav class="flex-1 flex flex-col space-y-1 relative -mx-6" aria-label="Admin navigation">
        @foreach($navItems as $item)
            @php($active = request()->routeIs(...$item['match']))
            <a href="{{ route($item['route']) }}"
               class="flex min-h-11 items-center space-x-3 px-6 py-3 transition-all duration-300 relative group focus:outline-none focus:ring-2 focus:ring-inset focus:ring-secondary/40 {{ $active ? 'text-primary font-bold bg-primary/5' : 'text-primary/60 hover:text-primary hover:bg-primary/5' }}"
               @if($active) aria-current="page" @endif>
                <span class="material-symbols-outlined {{ $active ? 'icon-filled' : '' }} text-[20px]" aria-hidden="true">{{ $item['icon'] }}</span>
                <span class="font-label text-sm tracking-wide">{{ $item['label'] }}</span>
                @if($active)
                    <span class="absolute right-0 top-0 bottom-0 w-1 bg-secondary" aria-hidden="true"></span>
                @endif
            </a>
        @endforeach
    </nav>

    <div class="pt-6 mt-auto space-y-3">
        <form action="{{ route('logout') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" class="flex min-h-11 w-full items-center space-x-3 rounded-lg px-4 py-2 text-primary/60 hover:bg-red-50 hover:text-red-600 transition-colors focus:outline-none focus:ring-2 focus:ring-red-300">
                <span class="material-symbols-outlined text-[20px]" aria-hidden="true">logout</span>
                <span class="font-label text-sm tracking-wide">Log Out</span>
            </button>
        </form>
    </div>
</aside>

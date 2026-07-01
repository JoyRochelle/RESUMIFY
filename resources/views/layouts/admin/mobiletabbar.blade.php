@php
    $navItems = config('navigation.admin', []);
    $primaryItems = array_slice($navItems, 0, 4);
    $moreItems = array_slice($navItems, 4);
    $moreActive = collect($moreItems)->contains(fn ($item) => request()->routeIs(...$item['match']));
@endphp

<div x-data="{ moreOpen: false }" @keydown.escape.window="moreOpen = false">
    <div id="admin-more-navigation"
         x-show="moreOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         @click.away="moreOpen = false"
         style="display:none"
         class="fixed bottom-[4.75rem] right-2 bg-white border border-primary/10 rounded-lg shadow-2xl p-2 z-50 w-60"
         role="menu"
         aria-label="More admin navigation">
        @foreach($moreItems as $item)
            @php($active = request()->routeIs(...$item['match']))
            <a href="{{ route($item['route']) }}"
               role="menuitem"
               class="flex min-h-11 items-center gap-3 rounded-lg px-4 py-3 text-sm font-label transition-colors focus:outline-none focus:ring-2 focus:ring-secondary/40 {{ $active ? 'text-primary font-bold bg-primary/5' : 'text-primary/60 hover:text-primary hover:bg-primary/5' }}"
               @if($active) aria-current="page" @endif>
                <span class="material-symbols-outlined text-[18px] {{ $active ? 'icon-filled' : '' }}" aria-hidden="true">{{ $item['icon'] }}</span>
                {{ $item['label'] }}
            </a>
        @endforeach
        <div class="h-px bg-primary/10 my-1 mx-4"></div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="flex min-h-11 w-full items-center gap-3 rounded-lg px-4 py-3 text-sm font-label text-red-600 hover:bg-red-50 transition-colors focus:outline-none focus:ring-2 focus:ring-red-300">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">logout</span>
                Log Out
            </button>
        </form>
    </div>

    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-tertiary border-t border-primary/10 flex justify-around items-center py-2 px-1 z-40 pb-[env(safe-area-inset-bottom)]" aria-label="Admin navigation">
        @foreach($primaryItems as $item)
            @php($active = request()->routeIs(...$item['match']))
            <a href="{{ route($item['route']) }}"
               class="flex min-h-14 min-w-0 flex-1 flex-col items-center justify-center gap-0.5 rounded-lg px-2 py-1.5 transition-colors focus:outline-none focus:ring-2 focus:ring-secondary/40 {{ $active ? 'text-primary bg-primary/5' : 'text-primary/40 hover:text-primary' }}"
               @if($active) aria-current="page" @endif>
                <span class="material-symbols-outlined text-[22px] {{ $active ? 'icon-filled' : '' }}" aria-hidden="true">{{ $item['icon'] }}</span>
                <span class="text-[9px] font-label font-bold uppercase tracking-wide">{{ $item['short_label'] ?? $item['label'] }}</span>
            </a>
        @endforeach

        <button type="button"
                @click="moreOpen = !moreOpen"
                class="flex min-h-14 min-w-0 flex-1 flex-col items-center justify-center gap-0.5 rounded-lg px-2 py-1.5 transition-colors focus:outline-none focus:ring-2 focus:ring-secondary/40 {{ $moreActive ? 'text-primary bg-primary/5' : 'text-primary/40 hover:text-primary' }}"
                aria-controls="admin-more-navigation"
                x-bind:aria-expanded="moreOpen.toString()"
                aria-label="More admin navigation">
            <span class="material-symbols-outlined text-[22px] {{ $moreActive ? 'icon-filled' : '' }}" aria-hidden="true">more_horiz</span>
            <span class="text-[9px] font-label font-bold uppercase tracking-wide">More</span>
        </button>
    </nav>
</div>

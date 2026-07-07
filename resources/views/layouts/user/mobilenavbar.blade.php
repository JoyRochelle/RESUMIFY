@php
    $navItems = config('navigation.user', []);
    $primaryRoutes = ['dashboard', 'user.manuscript', 'user.ai-assistant', 'interview.index', 'user.settings'];
    $primaryItems = collect($navItems)->filter(fn ($item) => in_array($item['route'], $primaryRoutes, true))->values();
    $moreItems = collect($navItems)->reject(fn ($item) => in_array($item['route'], $primaryRoutes, true))->values();
    $moreActive = $moreItems->contains(fn ($item) => request()->routeIs(...$item['match']));
@endphp

<div x-data="{ moreOpen: false }" @keydown.escape.window="moreOpen = false">
    <div id="user-more-navigation"
         x-show="moreOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         @click.away="moreOpen = false"
         style="display:none"
         class="md:hidden fixed bottom-[4.75rem] right-2 z-50 w-60 rounded-lg border border-primary/10 bg-tertiary p-2 shadow-2xl"
         role="menu"
         aria-label="More user navigation">
        <div class="flex items-center justify-between px-4 py-2 mb-1">
            <span class="text-xs font-label font-bold text-primary/50">{{ __('messages.nav.language') }}</span>
            <x-ui.locale-switcher />
        </div>
        @foreach($moreItems as $item)
            @php
                $active = request()->routeIs(...$item['match']);
                $emphasis = $item['emphasis'] ?? false;
            @endphp
            <a href="{{ route($item['route']) }}"
               role="menuitem"
               class="flex min-h-11 items-center gap-3 rounded-lg px-4 py-3 text-sm font-label transition-colors focus:outline-none focus:ring-2 focus:ring-secondary/40 {{ $active ? 'text-primary font-bold bg-primary/5' : ($emphasis ? 'text-secondary bg-secondary/10 hover:bg-secondary/20 font-bold' : 'text-primary/60 hover:text-primary hover:bg-primary/5') }}"
               @if($active) aria-current="page" @endif>
                <span class="material-symbols-outlined text-[18px] {{ $active ? 'icon-filled' : '' }}" aria-hidden="true">{{ $item['icon'] }}</span>
                {{ __('messages.nav.' . $item['label_key']) }}
            </a>
        @endforeach

        <div class="mt-1 border-t border-primary/10 pt-1">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        role="menuitem"
                        class="flex min-h-11 w-full items-center gap-3 rounded-lg px-4 py-3 text-sm font-label text-primary/60 transition-colors hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">logout</span>
                    {{ __('messages.nav.logout') }}
                </button>
            </form>
        </div>
    </div>

    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 border-t border-primary/10 bg-tertiary py-2 pl-[max(0.25rem,env(safe-area-inset-left))] pr-[max(0.25rem,env(safe-area-inset-right))] pb-[env(safe-area-inset-bottom)] shadow-[0_-8px_24px_rgba(79,59,47,0.08)]" aria-label="User navigation">
        <div class="grid w-full grid-cols-6 gap-0.5">
            @foreach($primaryItems as $item)
                @php
                    $active = request()->routeIs(...$item['match']);
                    $label = match($item['route']) {
                        'user.manuscript' => __('messages.nav.manuscripts'),
                        'user.ai-assistant' => __('messages.nav.ats_short'),
                        default => __('messages.nav.' . $item['label_key']),
                    };
                @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex min-h-14 min-w-0 flex-col items-center justify-center gap-0.5 rounded-lg px-1 text-center transition-colors focus:outline-none focus:ring-2 focus:ring-secondary/40 {{ $active ? 'text-primary bg-primary/5' : 'text-primary/50 hover:text-primary hover:bg-primary/5' }}"
                   @if($active) aria-current="page" @endif>
                    <span class="material-symbols-outlined text-[22px] {{ $active ? 'icon-filled' : '' }}" aria-hidden="true">{{ $item['icon'] }}</span>
                    <span class="w-full truncate text-[9px] font-label font-bold leading-tight">{{ $label }}</span>
                </a>
            @endforeach

            <button type="button"
                    @click="moreOpen = !moreOpen"
                    class="flex min-h-14 min-w-0 flex-col items-center justify-center gap-0.5 rounded-lg px-1 text-center transition-colors focus:outline-none focus:ring-2 focus:ring-secondary/40 {{ $moreActive ? 'text-primary bg-primary/5' : 'text-primary/50 hover:text-primary hover:bg-primary/5' }}"
                    aria-controls="user-more-navigation"
                    x-bind:aria-expanded="moreOpen.toString()"
                    aria-label="More user navigation">
                <span class="material-symbols-outlined text-[22px] {{ $moreActive ? 'icon-filled' : '' }}" aria-hidden="true">more_horiz</span>
                <span class="w-full truncate text-[9px] font-label font-bold leading-tight">{{ __('messages.nav.more') }}</span>
            </button>
        </div>
    </nav>
</div>

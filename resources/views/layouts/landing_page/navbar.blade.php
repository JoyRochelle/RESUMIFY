<nav class="bg-nav-footer border-b border-primary/15 sticky top-0 z-50" x-data="{ open: false }">
    <div class="flex justify-between items-center w-full px-4 sm:px-8 py-4 max-w-7xl mx-auto relative">
        {{-- Brand / Logo --}}
        <div class="text-2xl font-bold text-primary flex items-center gap-2.5 font-headline tracking-tighter">
            <a href="/" wire:navigate class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo.jpg') }}" alt="Resumify Logo" class="h-9 w-9 rounded-xl object-cover shadow-sm">
                <span>Resumify</span>
            </a>
        </div>

        {{-- Desktop Nav Links (centered) --}}
        <div class="hidden md:flex absolute left-1/2 -translate-x-1/2 items-center gap-8 text-sm">
            <a href="/" wire:navigate
                class="pb-1 border-b-2 rounded-sm transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40 {{ request()->is('/') ? 'text-primary font-bold border-secondary' : 'text-outline border-transparent hover:text-secondary' }}"
                @if(request()->is('/')) aria-current="page" @endif>{{ __('messages.landing.navbar.features') }}</a>
            <a href="/templates" wire:navigate
                class="pb-1 border-b-2 rounded-sm transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40 {{ request()->is('templates') ? 'text-primary font-bold border-secondary' : 'text-outline border-transparent hover:text-secondary' }}"
                @if(request()->is('templates')) aria-current="page" @endif>{{ __('messages.landing.navbar.templates') }}</a>
            <a href="/pricing" wire:navigate
                class="pb-1 border-b-2 rounded-sm transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40 {{ request()->is('pricing') ? 'text-primary font-bold border-secondary' : 'text-outline border-transparent hover:text-secondary' }}"
                @if(request()->is('pricing')) aria-current="page" @endif>{{ __('messages.landing.navbar.pricing') }}</a>
        </div>

        {{-- Desktop CTA --}}
        <div class="hidden md:flex items-center gap-6 text-sm">
            <x-ui.locale-switcher />
            <a class="inline-flex min-h-11 items-center rounded-lg px-2 text-primary font-semibold hover:text-secondary transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40"
                href="{{ route('login') }}">{{ __('messages.landing.navbar.login') }}</a>
            <x-landing_page.button variant="primary" class="!py-2 !px-4 !text-sm" href="{{ route('register') }}">
                {{ __('messages.landing.navbar.cta_create_resume') }}
            </x-landing_page.button>
        </div>

        {{-- Mobile: Login link + Hamburger --}}
        <div class="flex md:hidden items-center gap-3">
            <a class="inline-flex min-h-11 items-center rounded-lg px-2 text-sm text-primary font-semibold hover:text-secondary transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40"
                href="{{ route('login') }}">{{ __('messages.landing.navbar.login') }}</a>
            <button @click="open = !open"
                    class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg hover:bg-primary/5 transition-colors duration-200 text-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40"
                    aria-label="Toggle menu"
                    x-bind:aria-expanded="open.toString()">
                <span class="material-symbols-outlined text-2xl" aria-hidden="true" x-text="open ? 'close' : 'menu'">menu</span>
            </button>
        </div>
    </div>

    {{-- Mobile Dropdown Drawer --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden border-t border-primary/15 bg-nav-footer px-4 py-4 space-y-1"
         x-cloak>
        <a href="/" wire:navigate @click="open = false"
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold {{ request()->is('/') ? 'text-primary bg-primary/5' : 'text-outline hover:text-primary hover:bg-primary/5' }} transition-colors">
            <span class="material-symbols-outlined text-base">star</span>
            {{ __('messages.landing.navbar.features') }}
        </a>
        <a href="/templates" wire:navigate @click="open = false"
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold {{ request()->is('templates') ? 'text-primary bg-primary/5' : 'text-outline hover:text-primary hover:bg-primary/5' }} transition-colors">
            <span class="material-symbols-outlined text-base">article</span>
            {{ __('messages.landing.navbar.templates') }}
        </a>
        <a href="/pricing" wire:navigate @click="open = false"
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold {{ request()->is('pricing') ? 'text-primary bg-primary/5' : 'text-outline hover:text-primary hover:bg-primary/5' }} transition-colors">
            <span class="material-symbols-outlined text-base">sell</span>
            {{ __('messages.landing.navbar.pricing') }}
        </a>
        <div class="flex items-center justify-between px-4 py-3 border-t border-primary/10 mt-2">
            <span class="text-sm font-semibold text-outline">{{ __('messages.landing.navbar.language') }}</span>
            <x-ui.locale-switcher />
        </div>
        <div class="pb-1">
            <a href="{{ route('register') }}"
               class="flex items-center justify-center gap-2 w-full py-3 rounded-lg bg-secondary text-white text-sm font-bold hover:bg-secondary/90 transition-colors shadow-sm">
                {{ __('messages.landing.navbar.cta_create_resume') }}
            </a>
        </div>
    </div>
</nav>

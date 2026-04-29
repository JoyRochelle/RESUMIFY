<section
    class="w-full md:w-2/5 flex-1 bg-surface-container-lowest flex flex-col items-center p-6 md:p-8 lg:p-12 relative md:overflow-y-auto">
    <div class="w-full max-w-md">
        {{-- Brand Anchor --}}
        <x-auth.brand class="mb-6" />

        {{-- Auth Card --}}
        <div
            class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 p-6 md:p-8 shadow-sm transition-auth-card">
            {{-- Tabs --}}
            <nav aria-label="Auth Tabs" class="flex border-b border-surface-variant mb-6">
                <a href="{{ route('login') }}" wire:navigate
                    class="flex-1 text-center pb-4 text-sm font-bold {{ request()->routeIs('login') ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary' }} transition-all">
                    Login
                </a>
                <a href="{{ route('register') }}" wire:navigate
                    class="flex-1 text-center pb-4 text-sm font-bold {{ request()->routeIs('register') ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary' }} transition-all">
                    Sign Up
                </a>
            </nav>

            {{-- Form Header --}}
            <div class="mb-6">
                <h3 class="text-2xl font-headline font-bold text-on-surface">{{ $title ?? 'Login to Your Account' }}
                </h3>
                <p class="text-sm text-on-surface-variant mt-1">
                    {{ $subtitle ?? 'Welcome back to your career journey.' }}
                </p>
            </div>

            {{-- Form Content --}}
            {{ $slot }}

            {{-- Social Auth --}}
            <x-auth.social-buttons class="mt-6" />
        </div>

        {{-- Footer Info --}}
        <div class="text-center mt-6">
            {{ $footer ?? '' }}
        </div>

        {{-- Secondary Footer --}}
        <div
            class="mt-8 flex justify-center gap-6 text-[10px] uppercase tracking-widest text-on-surface-variant/60 font-bold">
            <a href="#" class="hover:text-primary transition-colors">TERMS & CONDITIONS</a>
            <a href="#" class="hover:text-primary transition-colors">PRIVACY POLICY</a>
            <a href="#" class="hover:text-primary transition-colors">HELP CENTER</a>
        </div>
    </div>
</section>

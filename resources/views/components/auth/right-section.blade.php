<section
    class="w-full md:w-2/5 flex-1 bg-surface-container-lowest flex flex-col items-center  p-6 md:p-8 lg:p-12 relative md:overflow-y-auto">
    <div class="w-full max-w-md">
        {{-- Brand Anchor --}}
        <div class="text-center mb-6 transition-auth-brand">
            <h2 class="text-4xl font-headline font-bold text-primary tracking-tighter">Resumify</h2>
            <div class="h-1 w-8 bg-secondary mx-auto mt-1 rounded-full"></div>
        </div>

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

            {{-- Social Auth Separator --}}
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-surface-variant"></div>
                </div>
                <div class="relative flex justify-center text-[10px] font-bold tracking-widest uppercase">
                    <span class="bg-surface-container-lowest px-2 text-on-surface-variant">OR CONTINUE WITH</span>
                </div>
            </div>

            {{-- Social Buttons --}}
            <div class="grid grid-cols-2 gap-4">
                <button
                    class="flex items-center justify-center gap-2 py-3 px-4 rounded-lg border border-outline-variant hover:bg-surface-container-low transition-colors group">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                            fill="#4285F4"></path>
                        <path
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            fill="#34A853"></path>
                        <path
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                            fill="#FBBC05"></path>
                        <path
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.66l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                            fill="#EA4335"></path>
                    </svg>
                    <span class="text-sm font-medium text-primary">Google</span>
                </button>
                <button
                    class="flex items-center justify-center gap-2 py-3 px-4 rounded-lg border border-outline-variant hover:bg-surface-container-low transition-colors">
                    <svg class="w-4 h-4 fill-[#0077b5]" viewBox="0 0 24 24">
                        <path
                            d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z">
                        </path>
                    </svg>
                    <span class="text-sm font-medium text-primary">LinkedIn</span>
                </button>
            </div>
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

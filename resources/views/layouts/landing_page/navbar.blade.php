<nav class="bg-surface border-b border-primary/10 sticky top-0 z-50">
    <div class="flex justify-between items-center w-full px-8 py-4 max-w-7xl mx-auto relative">
        <div class="text-2xl font-bold text-primary flex items-center gap-1 font-headline tracking-tighter">
            <a href="/" wire:navigate>Resumify</a>
        </div>
        <div class="hidden md:flex absolute left-1/2 -translate-x-1/2 items-center gap-8 text-sm">
            <a href="/" wire:navigate
                class="{{ request()->is('/') ? 'text-primary font-bold border-b-2 border-secondary pb-1' : 'text-outline hover:text-secondary' }} transition-colors">Features</a>
            <a href="/templates" wire:navigate
                class="{{ request()->is('templates') ? 'text-primary font-bold border-b-2 border-secondary pb-1' : 'text-outline hover:text-secondary' }} transition-colors">Templates</a>
            <a href="/pricing" wire:navigate
                class="{{ request()->is('pricing') ? 'text-primary font-bold border-b-2 border-secondary pb-1' : 'text-outline hover:text-secondary' }} transition-colors">Pricing</a>
        </div>
        <div class="flex items-center gap-6 text-sm">
            <a class="text-primary font-semibold hover:text-secondary transition-colors"
                href="{{ route('login') }}">Login</a>
            <x-landing_page.button variant="primary" class="!py-2 !px-4 !text-sm" href="{{ route('register') }}">
                Create Free Resume ✨
            </x-landing_page.button>
        </div>
    </div>
</nav>

<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-tertiary border-t border-primary/10 flex justify-around items-center py-3 px-6 z-50">
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-primary/50 hover:text-primary' }} transition-colors">
        <span class="material-symbols-outlined" style="{{ request()->routeIs('dashboard') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">dashboard</span>
        <span class="text-[10px] font-label font-medium mt-1">Dashboard</span>
    </a>
    <a href="{{ route('manuscript') }}" class="flex flex-col items-center {{ request()->routeIs('manuscript') ? 'text-primary' : 'text-primary/50 hover:text-primary' }} transition-colors">
        <span class="material-symbols-outlined" style="{{ request()->routeIs('manuscript') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">description</span>
        <span class="text-[10px] font-label font-medium mt-1">Manuscripts</span>
    </a>
    <a href="{{ route('manuscript') }}" class="flex flex-col items-center justify-center -translate-y-6 bg-primary text-tertiary w-14 h-14 rounded-full shadow-lg">
        <span class="material-symbols-outlined">add</span>
    </a>
    <a href="{{ route('ai-assistant') }}" class="flex flex-col items-center {{ request()->routeIs('ai-assistant') ? 'text-primary' : 'text-primary/50 hover:text-primary' }} transition-colors">
        <span class="material-symbols-outlined" style="{{ request()->routeIs('ai-assistant') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">auto_fix_high</span>
        <span class="text-[10px] font-label font-medium mt-1">AI</span>
    </a>
    <a href="{{ route('settings') }}" class="flex flex-col items-center {{ request()->routeIs('settings') ? 'text-primary' : 'text-primary/50 hover:text-primary' }} transition-colors">
        <span class="material-symbols-outlined" style="{{ request()->routeIs('settings') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">settings</span>
        <span class="text-[10px] font-label font-medium mt-1">Account</span>
    </a>
</nav>
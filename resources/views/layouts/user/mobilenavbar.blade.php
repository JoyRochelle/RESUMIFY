<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-tertiary border-t border-primary/10 flex justify-around items-center py-3 px-6 z-50">
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-primary/50 hover:text-primary' }} transition-colors">
        <span class="material-symbols-outlined {{ request()->routeIs('dashboard') ? 'icon-filled' : '' }}">dashboard</span>
        <span class="text-[10px] font-label font-medium mt-1">Dashboard</span>
    </a>
    <a href="{{ route('user.manuscript') }}" class="flex flex-col items-center {{ request()->routeIs('user.manuscript') ? 'text-primary' : 'text-primary/50 hover:text-primary' }} transition-colors">
        <span class="material-symbols-outlined {{ request()->routeIs('user.manuscript') ? 'icon-filled' : '' }}">description</span>
        <span class="text-[10px] font-label font-medium mt-1">Manuscripts</span>
    </a>
    <a href="{{ route('resumes.create') }}" class="flex flex-col items-center justify-center -translate-y-6 bg-primary text-tertiary w-14 h-14 rounded-full shadow-lg">
        <span class="material-symbols-outlined">add</span>
    </a>
    <a href="{{ route('user.ai-assistant') }}" class="flex flex-col items-center {{ request()->routeIs('user.ai-assistant') ? 'text-primary' : 'text-primary/50 hover:text-primary' }}">
        <span class="material-symbols-outlined {{ request()->routeIs('user.ai-assistant') ? 'icon-filled' : '' }}">analytics</span>
        <span class="text-[10px] font-label font-medium mt-1">ATS</span>
    </a>
    <a href="{{ route('user.settings') }}" class="flex flex-col items-center {{ request()->routeIs('user.settings') ? 'text-primary' : 'text-primary/50 hover:text-primary' }}">
        <span class="material-symbols-outlined {{ request()->routeIs('user.settings') ? 'icon-filled' : '' }}">settings</span>
        <span class="text-[10px] font-label font-medium mt-1">Account</span>
    </a>
</nav>
<aside class="hidden md:flex flex-col h-screen w-64 border-r border-primary/10 bg-surface-container-low p-6 space-y-8 sticky top-0 shrink-0">
    <div>
        <h1 class="text-xl font-headline font-bold text-primary">Resumify Admin</h1>
        <p class="text-xs font-label text-primary/60">The Editorial Architect</p>
    </div>

    <nav class="flex-1 flex flex-col space-y-1 relative -mx-6">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-6 py-3 transition-all duration-300 relative group {{ request()->routeIs('admin.dashboard') ? 'text-primary font-bold bg-primary/5' : 'text-primary/60 hover:text-primary hover:bg-primary/5' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('admin.dashboard') ? 'icon-filled' : '' }} text-[20px]">grid_view</span>
            <span class="font-label text-sm tracking-wide">Core Statistics</span>
            @if(request()->routeIs('admin.dashboard'))
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-secondary"></div>
            @endif
        </a>
        
        <a href="{{ route('admin.users') }}" class="flex items-center space-x-3 px-6 py-3 transition-all duration-300 relative group {{ request()->routeIs('admin.users') ? 'text-primary font-bold bg-primary/5' : 'text-primary/60 hover:text-primary hover:bg-primary/5' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('admin.users') ? 'icon-filled' : '' }} text-[20px]">group</span>
            <span class="font-label text-sm tracking-wide">User Management</span>
            @if(request()->routeIs('admin.users'))
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-secondary"></div>
            @endif
        </a>
        
        <a href="{{ route('admin.support') }}" class="flex items-center space-x-3 px-6 py-3 transition-all duration-300 relative group {{ request()->routeIs('admin.support') ? 'text-primary font-bold bg-primary/5' : 'text-primary/60 hover:text-primary hover:bg-primary/5' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('admin.support') ? 'icon-filled' : '' }} text-[20px]">help_outline</span>
            <span class="font-label text-sm tracking-wide">Support Center</span>
            @if(request()->routeIs('admin.support'))
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-secondary"></div>
            @endif
        </a>

        <a href="{{ route('admin.templates.index') }}" class="flex items-center space-x-3 px-6 py-3 transition-all duration-300 relative group {{ request()->routeIs('admin.templates.*') ? 'text-primary font-bold bg-primary/5' : 'text-primary/60 hover:text-primary hover:bg-primary/5' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('admin.templates.*') ? 'icon-filled' : '' }} text-[20px]">style</span>
            <span class="font-label text-sm tracking-wide">Template Catalog</span>
            @if(request()->routeIs('admin.templates.*'))
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-secondary"></div>
            @endif
        </a>

        <a href="{{ route('admin.logs') }}" class="flex items-center space-x-3 px-6 py-3 transition-all duration-300 relative group {{ request()->routeIs('admin.logs') ? 'text-primary font-bold bg-primary/5' : 'text-primary/60 hover:text-primary hover:bg-primary/5' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('admin.logs') ? 'icon-filled' : '' }} text-[20px]">science</span>
            <span class="font-label text-sm tracking-wide">AI & Finance Logs</span>
            @if(request()->routeIs('admin.logs'))
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-secondary"></div>
            @endif
        </a>

        <a href="{{ route('admin.settings') }}" class="flex items-center space-x-3 px-6 py-3 transition-all duration-300 relative group {{ request()->routeIs('admin.settings') ? 'text-primary font-bold bg-primary/5' : 'text-primary/60 hover:text-primary hover:bg-primary/5' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('admin.settings') ? 'icon-filled' : '' }} text-[20px]">settings</span>
            <span class="font-label text-sm tracking-wide">System Settings</span>
            @if(request()->routeIs('admin.settings'))
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-secondary"></div>
            @endif
        </a>

    </nav>

    <div class="pt-6 mt-auto space-y-3">
        <button class="w-full flex items-center justify-center space-x-2 py-3 px-4 rounded-lg bg-[#3f3129] text-[#fdf8f4] hover:bg-opacity-90 transition-all duration-300 font-label text-sm shadow-sm">
            <span class="material-symbols-outlined text-[18px]">add</span>
            <span>New Report</span>
        </button>

        <!-- Logout Link -->
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();" class="flex items-center space-x-3 px-4 py-2 text-primary/60 hover:text-red-500 transition-colors w-full mt-2">
            <span class="material-symbols-outlined text-[20px]">logout</span>
            <span class="font-label text-sm tracking-wide">Log Out</span>
        </a>
        <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="hidden" style="display: none;">
            @csrf
        </form>
    </div>
</aside>

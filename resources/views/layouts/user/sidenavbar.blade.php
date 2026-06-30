<aside class="hidden md:flex flex-col h-screen w-64 border-r border-primary/10 bg-surface-container-low p-6 space-y-8 fixed top-0 left-0 z-40 shrink-0">
    <a href="{{ route('dashboard') }}" class="block">
        <img src="{{ asset('images/logo.jpg') }}" alt="Resumify" class="h-10 w-10 rounded-xl object-cover shadow-sm">
    </a>
    
    <div class="flex items-center space-x-3 mb-4">
        <div class="w-10 h-10 rounded-full overflow-hidden bg-primary/10">
            <img alt="User Avatar" class="w-full h-full object-cover" src="{{ auth()->user()->avatar_url }}"/>
        </div>
        <div>
            <p class="text-sm font-headline font-bold text-primary">{{ auth()->user()->name }}</p>
            <p class="text-xs font-label text-primary/60 uppercase tracking-wider">{{ auth()->user()->isPremium() ? 'Premium Member' : 'Basic Member' }}</p>
        </div>
    </div>

    <nav class="flex-1 flex flex-col space-y-2">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 p-3 transition-all duration-300 {{ request()->routeIs('dashboard') ? 'text-primary font-bold bg-tertiary rounded-lg shadow-sm' : 'text-primary/60 hover:text-primary hover:translate-x-1' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('dashboard') ? 'icon-filled' : '' }}">dashboard</span>
            <span class="font-label tracking-wide">Dashboard</span>
        </a>
        
        <a href="{{ route('user.manuscript') }}" class="flex items-center space-x-3 p-3 transition-all duration-300 {{ request()->routeIs('user.manuscript') ? 'text-primary font-bold bg-tertiary rounded-lg shadow-sm' : 'text-primary/60 hover:text-primary hover:translate-x-1' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('user.manuscript') ? 'icon-filled' : '' }}">description</span>
            <span class="font-label tracking-wide">Manuscripts</span>
        </a>
        
        <a href="{{ route('user.ai-assistant') }}" class="flex items-center space-x-3 p-3 transition-all duration-300 {{ request()->routeIs('user.ai-assistant') ? 'text-primary font-bold bg-tertiary rounded-lg shadow-sm' : 'text-primary/60 hover:text-primary hover:translate-x-1' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('user.ai-assistant') ? 'icon-filled' : '' }}" data-icon="analytics">analytics</span>
            <span class="font-label tracking-wide">ATS Analyzer</span>
        </a>
        <a href="{{ route('interview.history') }}" class="flex items-center space-x-3 p-3 transition-all duration-300 {{ request()->routeIs('interview.*') ? 'text-primary font-bold bg-tertiary rounded-lg shadow-sm' : 'text-primary/60 hover:text-primary hover:translate-x-1' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('interview.*') ? 'icon-filled' : '' }}">record_voice_over</span>
            <span class="font-label tracking-wide">Interview</span>
        </a>
        <a href="{{ route('user.settings') }}" class="flex items-center space-x-3 p-3 transition-all duration-300 {{ request()->routeIs('user.settings') ? 'text-primary font-bold bg-tertiary rounded-lg shadow-sm' : 'text-primary/60 hover:text-primary hover:translate-x-1' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('user.settings') ? 'icon-filled' : '' }}" data-icon="settings">settings</span>
            <span class="font-label tracking-wide">Settings</span>
        </a>
        <a href="{{ route('user.help') }}" class="flex items-center space-x-3 p-3 transition-all duration-300 {{ request()->routeIs('user.help') ? 'text-primary font-bold bg-tertiary rounded-lg shadow-sm' : 'text-primary/60 hover:text-primary hover:translate-x-1' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('user.help') ? 'icon-filled' : '' }}" data-icon="help_outline">help_outline</span>
            <span class="font-label tracking-wide">Help</span>
        </a>
    </nav>

    <div class="pt-6 border-t border-primary/10">
        <a href="{{ route('user.upgrade-quota') }}" class="block w-full py-2 px-4 rounded-lg text-sm font-label font-bold text-secondary bg-secondary/10 hover:bg-secondary/20 transition-all duration-300 text-center">
            Upgrade Quota
        </a>
        <form method="POST" action="{{ route('logout') }}" class="w-full mt-4">
            @csrf
            <button type="submit" class="flex items-center space-x-3 p-3 text-primary/60 hover:text-red-500 transition-colors w-full">
                <span class="material-symbols-outlined" data-icon="logout">logout</span>
                <span class="font-label tracking-wide">Log Out</span>
            </button>
        </form>
    </div>
</aside>
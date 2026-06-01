<div x-data="{ moreOpen: false }" @keydown.escape.window="moreOpen = false">

    {{-- More pop-up menu (opens upward above tab bar) --}}
    <div x-show="moreOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         @click.away="moreOpen = false"
         style="display:none"
         class="fixed bottom-[4.5rem] right-2 bg-white border border-primary/10 rounded-2xl shadow-2xl p-2 z-50 w-52">
        <a href="{{ route('admin.templates.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-label transition-colors {{ request()->routeIs('admin.templates.*') ? 'text-primary font-bold bg-primary/5' : 'text-primary/60 hover:text-primary hover:bg-primary/5' }}">
            <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.templates.*') ? 'icon-filled' : '' }}">style</span>
            Templates
        </a>
        <a href="{{ route('admin.monitor') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-label transition-colors {{ request()->routeIs('admin.monitor') ? 'text-primary font-bold bg-primary/5' : 'text-primary/60 hover:text-primary hover:bg-primary/5' }}">
            <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.monitor') ? 'icon-filled' : '' }}">monitor_heart</span>
            Monitor
        </a>
        <a href="{{ route('admin.reports') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-label transition-colors {{ request()->routeIs('admin.reports*') ? 'text-primary font-bold bg-primary/5' : 'text-primary/60 hover:text-primary hover:bg-primary/5' }}">
            <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.reports*') ? 'icon-filled' : '' }}">bar_chart_4_bars</span>
            Reports
        </a>
        <div class="h-px bg-primary/10 my-1 mx-4"></div>
        <button onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();"
                class="flex w-full items-center gap-3 px-4 py-3 rounded-xl text-sm font-label text-red-500 hover:bg-red-50 transition-colors">
            <span class="material-symbols-outlined text-[18px]">logout</span>
            Log Out
        </button>
    </div>

    {{-- Bottom Tab Bar --}}
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-tertiary border-t border-primary/10 flex justify-around items-center py-2 px-1 z-40 safe-area-inset-bottom">
        <a href="{{ route('admin.dashboard') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl min-w-0 transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-primary' : 'text-primary/40 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[22px] {{ request()->routeIs('admin.dashboard') ? 'icon-filled' : '' }}">grid_view</span>
            <span class="text-[9px] font-label font-bold uppercase tracking-wide">Stats</span>
        </a>

        <a href="{{ route('admin.users') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl min-w-0 transition-colors {{ request()->routeIs('admin.users*') ? 'text-primary' : 'text-primary/40 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[22px] {{ request()->routeIs('admin.users*') ? 'icon-filled' : '' }}">group</span>
            <span class="text-[9px] font-label font-bold uppercase tracking-wide">Users</span>
        </a>

        <a href="{{ route('admin.support') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl min-w-0 transition-colors {{ request()->routeIs('admin.support*') ? 'text-primary' : 'text-primary/40 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[22px] {{ request()->routeIs('admin.support*') ? 'icon-filled' : '' }}">help_outline</span>
            <span class="text-[9px] font-label font-bold uppercase tracking-wide">Support</span>
        </a>

        <a href="{{ route('admin.logs') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl min-w-0 transition-colors {{ request()->routeIs('admin.logs*') ? 'text-primary' : 'text-primary/40 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[22px] {{ request()->routeIs('admin.logs*') ? 'icon-filled' : '' }}">science</span>
            <span class="text-[9px] font-label font-bold uppercase tracking-wide">Logs</span>
        </a>

        <button @click="moreOpen = !moreOpen"
                class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl min-w-0 transition-colors {{ request()->routeIs('admin.templates.*') || request()->routeIs('admin.monitor') || request()->routeIs('admin.reports*') ? 'text-primary' : 'text-primary/40' }}">
            <span class="material-symbols-outlined text-[22px] {{ request()->routeIs('admin.templates.*') || request()->routeIs('admin.monitor') || request()->routeIs('admin.reports*') ? 'icon-filled' : '' }}">more_horiz</span>
            <span class="text-[9px] font-label font-bold uppercase tracking-wide">More</span>
        </button>
    </nav>
</div>

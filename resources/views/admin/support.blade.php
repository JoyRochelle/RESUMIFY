@extends('layouts.admin.app')

@section('title', 'Support Tickets - Admin Dashboard')

@section('content')
<div class="admin-shell">

    <!-- Header -->
    <div>
        <h1 class="text-3xl font-headline font-bold text-primary mb-2">Support Tickets</h1>
        <p class="text-sm font-label text-primary/60">Manage user support requests.</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-6">
        <div class="admin-card-pad">
            <h3 class="admin-section-title mb-3">OPEN</h3>
            <p class="text-3xl font-headline text-red-500">{{ number_format($openCount) }}</p>
        </div>
        <div class="admin-card-pad">
            <h3 class="admin-section-title mb-3">PENDING</h3>
            <p class="text-3xl font-headline text-amber-500">{{ number_format($pendingCount) }}</p>
        </div>
        <div class="admin-card-pad">
            <h3 class="admin-section-title mb-3">CLOSED</h3>
            <p class="text-3xl font-headline text-primary">{{ number_format($closedCount) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.support') }}" class="flex flex-wrap items-center gap-4">
        <div class="flex h-11 min-w-[200px] flex-1 items-center rounded-lg border border-primary/10 bg-tertiary px-4 shadow-sm transition focus-within:ring-2 focus-within:ring-secondary/30">
            <span class="material-symbols-outlined text-primary/40 mr-3 text-[18px]">search</span>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Search by subject or user..."
                   class="bg-transparent border-none focus:outline-none text-sm font-label w-full text-primary placeholder:text-primary/40">
        </div>

        <select name="status"
                class="admin-filter-field h-11 cursor-pointer">
            <option value="">All Status</option>
            <option value="open"    {{ $status === 'open'    ? 'selected' : '' }}>Open</option>
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="closed"  {{ $status === 'closed'  ? 'selected' : '' }}>Closed</option>
        </select>

        <x-ui.loading-button loading-text="Filtering..." icon="filter_list">Filter</x-ui.loading-button>

        @if($search || $status)
            <a href="{{ route('admin.support') }}"
               class="text-sm font-label text-primary/50 hover:text-primary transition">Clear</a>
        @endif
    </form>

    <!-- Ticket Table -->
    <div class="admin-card overflow-hidden">

        {{-- Mobile card layout --}}
        <div class="md:hidden divide-y divide-primary/5">
            @forelse($tickets as $ticket)
            @php
                $badgeMap = ['open' => 'bg-red-100 text-red-600', 'pending' => 'bg-amber-100 text-amber-600', 'closed' => 'bg-primary/10 text-primary/50'];
            @endphp
            <a href="{{ route('admin.support.show', $ticket) }}"
               class="block p-4 hover:bg-surface/40 transition-colors">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <p class="text-sm font-label font-bold text-primary truncate flex-1">{{ $ticket->subject }}</p>
                    <span class="admin-badge shrink-0 {{ $badgeMap[$ticket->status] ?? '' }}">
                        {{ $ticket->status }}
                    </span>
                </div>
                <p class="text-xs text-primary/50 font-label">
                    {{ $ticket->user?->name ?? '—' }} &middot; {{ $ticket->created_at->diffForHumans() }}
                </p>
            </a>
            @empty
            <x-ui.empty-state title="No tickets found" description="Try clearing the status or search filter." icon="support_agent" class="m-4" />
            @endforelse
        </div>

        {{-- Desktop table --}}
        <div class="hidden md:block overflow-x-auto">
        <table class="admin-table">
            <thead class="admin-table-head">
                <tr>
                    <th class="admin-th text-left">User</th>
                    <th class="admin-th text-left">Subject</th>
                    <th class="admin-th text-left">Status</th>
                    <th class="admin-th text-left">Assigned</th>
                    <th class="admin-th text-left">Date</th>
                    <th class="admin-th text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary/10">
                @forelse($tickets as $ticket)
                <tr class="hover:bg-surface/40 transition-colors">
                    <td class="admin-td">
                        <p class="text-sm font-label font-bold text-primary">{{ $ticket->user?->name ?? '—' }}</p>
                        <p class="text-[11px] font-label text-primary/40">{{ $ticket->user?->email ?? '' }}</p>
                    </td>
                    <td class="admin-td max-w-xs">
                        <p class="text-sm font-label text-primary truncate">{{ $ticket->subject }}</p>
                    </td>
                    <td class="admin-td">
                        @php
                            $badgeMap = ['open' => 'bg-red-100 text-red-600', 'pending' => 'bg-amber-100 text-amber-600', 'closed' => 'bg-primary/10 text-primary/50'];
                        @endphp
                        <span class="admin-badge {{ $badgeMap[$ticket->status] ?? '' }}">
                            {{ $ticket->status }}
                        </span>
                    </td>
                    <td class="admin-td">
                        <p class="text-sm font-label text-primary/60">{{ $ticket->assignedAdmin?->name ?? '—' }}</p>
                    </td>
                    <td class="admin-td font-label text-primary/50 whitespace-nowrap">
                        {{ $ticket->created_at->diffForHumans() }}
                    </td>
                    <td class="admin-td text-right">
                        <a href="{{ route('admin.support.show', $ticket) }}"
                           class="admin-btn-secondary min-h-0 px-3 py-1.5 text-xs">
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-4">
                        <x-ui.empty-state title="No tickets found" description="Try clearing the status or search filter." icon="support_agent" />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>{{-- end desktop table --}}

        @if($tickets->hasPages())
            <div class="px-6 py-4 border-t border-primary/5">{{ $tickets->links() }}</div>
        @endif
    </div>

</div>
@endsection

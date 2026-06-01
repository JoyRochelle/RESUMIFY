@extends('layouts.admin.app')

@section('title', 'Support Tickets - Admin Dashboard')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-10">

    <!-- Header -->
    <div>
        <h1 class="text-3xl font-headline font-bold text-primary mb-2">Support Tickets</h1>
        <p class="text-sm font-label text-primary/60">Manage user support requests.</p>
    </div>

    @if(session('success'))
        <div class="bg-secondary/10 border border-secondary/20 text-secondary text-sm font-label px-5 py-3 rounded-xl flex items-center space-x-2">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-6">
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-3">OPEN</h3>
            <p class="text-3xl font-headline text-red-500">{{ number_format($openCount) }}</p>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-3">PENDING</h3>
            <p class="text-3xl font-headline text-amber-500">{{ number_format($pendingCount) }}</p>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-3">CLOSED</h3>
            <p class="text-3xl font-headline text-primary">{{ number_format($closedCount) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.support') }}" class="flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[200px] bg-white rounded-xl border border-primary/10 flex items-center px-4 h-11 shadow-sm">
            <span class="material-symbols-outlined text-primary/40 mr-3 text-[18px]">search</span>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Search by subject or user..."
                   class="bg-transparent border-none focus:outline-none text-sm font-label w-full text-primary placeholder:text-primary/40">
        </div>

        <select name="status"
                class="bg-white rounded-xl border border-primary/10 px-4 h-11 text-sm font-label text-primary shadow-sm focus:outline-none cursor-pointer">
            <option value="">All Status</option>
            <option value="open"    {{ $status === 'open'    ? 'selected' : '' }}>Open</option>
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="closed"  {{ $status === 'closed'  ? 'selected' : '' }}>Closed</option>
        </select>

        <button type="submit"
                class="bg-primary text-white px-5 h-11 rounded-xl text-sm font-label hover:bg-primary/90 transition shadow-sm">
            Filter
        </button>

        @if($search || $status)
            <a href="{{ route('admin.support') }}"
               class="text-sm font-label text-primary/50 hover:text-primary transition">Clear</a>
        @endif
    </form>

    <!-- Ticket Table -->
    <div class="bg-white rounded-3xl shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 overflow-hidden">

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
                    <span class="inline-block shrink-0 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeMap[$ticket->status] ?? '' }}">
                        {{ $ticket->status }}
                    </span>
                </div>
                <p class="text-xs text-primary/50 font-label">
                    {{ $ticket->user?->name ?? '—' }} &middot; {{ $ticket->created_at->diffForHumans() }}
                </p>
            </a>
            @empty
            <div class="py-16 text-center">
                <span class="material-symbols-outlined text-primary/20 text-[48px] block mb-2">support_agent</span>
                <p class="text-sm font-label text-primary/40">No tickets found</p>
            </div>
            @endforelse
        </div>

        {{-- Desktop table --}}
        <div class="hidden md:block">
        <table class="w-full">
            <thead>
                <tr class="border-b border-primary/5 bg-surface/50">
                    <th class="text-left text-[9px] font-label text-primary/40 uppercase tracking-widest py-3 px-6">User</th>
                    <th class="text-left text-[9px] font-label text-primary/40 uppercase tracking-widest py-3 px-4">Subject</th>
                    <th class="text-left text-[9px] font-label text-primary/40 uppercase tracking-widest py-3 px-4">Status</th>
                    <th class="text-left text-[9px] font-label text-primary/40 uppercase tracking-widest py-3 px-4">Assigned</th>
                    <th class="text-left text-[9px] font-label text-primary/40 uppercase tracking-widest py-3 px-6">Date</th>
                    <th class="py-3 px-6"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary/5">
                @forelse($tickets as $ticket)
                <tr class="hover:bg-surface/40 transition-colors">
                    <td class="py-4 px-6">
                        <p class="text-sm font-label font-bold text-primary">{{ $ticket->user?->name ?? '—' }}</p>
                        <p class="text-[11px] font-label text-primary/40">{{ $ticket->user?->email ?? '' }}</p>
                    </td>
                    <td class="py-4 px-4 max-w-xs">
                        <p class="text-sm font-label text-primary truncate">{{ $ticket->subject }}</p>
                    </td>
                    <td class="py-4 px-4">
                        @php
                            $badgeMap = ['open' => 'bg-red-100 text-red-600', 'pending' => 'bg-amber-100 text-amber-600', 'closed' => 'bg-primary/10 text-primary/50'];
                        @endphp
                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeMap[$ticket->status] ?? '' }}">
                            {{ $ticket->status }}
                        </span>
                    </td>
                    <td class="py-4 px-4">
                        <p class="text-sm font-label text-primary/60">{{ $ticket->assignedAdmin?->name ?? '—' }}</p>
                    </td>
                    <td class="py-4 px-6 text-sm font-label text-primary/50 whitespace-nowrap">
                        {{ $ticket->created_at->diffForHumans() }}
                    </td>
                    <td class="py-4 px-6 text-right">
                        <a href="{{ route('admin.support.show', $ticket) }}"
                           class="text-xs font-label text-primary/60 hover:text-primary bg-primary/5 hover:bg-primary/10 px-3 py-1.5 rounded-lg transition">
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center">
                        <span class="material-symbols-outlined text-primary/20 text-[48px] block mb-2">support_agent</span>
                        <p class="text-sm font-label text-primary/40">No tickets found</p>
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

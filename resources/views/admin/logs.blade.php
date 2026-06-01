@extends('layouts.admin.app')

@section('title', 'AI & Finance Logs - Admin Dashboard')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-10" x-data="{ tab: '{{ $tab }}' }">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-headline font-bold text-primary mb-2">AI & Finance Logs</h1>
            <p class="text-sm font-label text-primary/60">Full audit trail of AI usage and payment transactions.</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-3">AI COST (MTD)</h3>
            <p class="text-2xl font-headline text-primary">${{ number_format($totalAiCostMtd, 4) }}</p>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-3">REVENUE (MTD)</h3>
            <p class="text-2xl font-headline text-secondary">Rp {{ number_format($totalRevenueMtd, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-3">TOTAL AI ACTIONS</h3>
            <p class="text-2xl font-headline text-primary">{{ number_format($totalAiActions) }}</p>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-3">TOTAL TRANSACTIONS</h3>
            <p class="text-2xl font-headline text-primary">{{ number_format($totalTxCount) }}</p>
        </div>
    </div>

    <!-- Date Range Filter -->
    <form method="GET" action="{{ route('admin.logs') }}" class="flex flex-wrap items-center gap-4">
        <input type="hidden" name="tab" :value="tab">

        <div class="flex items-center space-x-2 bg-white border border-primary/10 rounded-xl px-4 h-11 shadow-sm">
            <span class="text-xs font-label text-primary/50">From</span>
            <input type="date" name="from" value="{{ $from }}"
                   class="bg-transparent border-none focus:outline-none text-sm font-label text-primary">
        </div>

        <div class="flex items-center space-x-2 bg-white border border-primary/10 rounded-xl px-4 h-11 shadow-sm">
            <span class="text-xs font-label text-primary/50">To</span>
            <input type="date" name="to" value="{{ $to }}"
                   class="bg-transparent border-none focus:outline-none text-sm font-label text-primary">
        </div>

        <button type="submit"
                class="bg-primary text-white px-5 h-11 rounded-xl text-sm font-label hover:bg-primary/90 transition shadow-sm">
            Apply
        </button>

        @if($from || $to)
            <a href="{{ route('admin.logs', ['tab' => $tab]) }}"
               class="text-sm font-label text-primary/50 hover:text-primary transition">Clear</a>
        @endif
    </form>

    <!-- Tabs -->
    <div class="bg-white rounded-3xl shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 overflow-hidden">

        <!-- Tab header -->
        <div class="flex items-center border-b border-primary/5 px-6">
            <button @click="tab = 'ai'"
                    :class="tab === 'ai'
                        ? 'border-b-2 border-primary text-primary font-bold'
                        : 'text-primary/50 hover:text-primary'"
                    class="flex items-center space-x-2 py-5 pr-8 text-sm font-label transition-colors">
                <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
                <span>AI Usage</span>
                <span class="ml-1 text-[11px] bg-primary/8 text-primary/60 px-2 py-0.5 rounded-full">
                    {{ $aiLogs->total() }}
                </span>
            </button>

            <button @click="tab = 'finance'"
                    :class="tab === 'finance'
                        ? 'border-b-2 border-primary text-primary font-bold'
                        : 'text-primary/50 hover:text-primary'"
                    class="flex items-center space-x-2 py-5 pr-8 text-sm font-label transition-colors">
                <span class="material-symbols-outlined text-[18px]">payments</span>
                <span>Finance</span>
                <span class="ml-1 text-[11px] bg-primary/8 text-primary/60 px-2 py-0.5 rounded-full">
                    {{ $transactions->total() }}
                </span>
            </button>

            <div class="ml-auto flex items-center space-x-3 py-4">
                <!-- Export AI -->
                <a x-show="tab === 'ai'"
                   href="{{ route('admin.logs.export.ai', array_filter(['from' => $from, 'to' => $to])) }}"
                   class="flex items-center space-x-1.5 text-xs font-label text-secondary hover:text-secondary/80 bg-secondary/10 px-3 py-2 rounded-xl transition">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                    <span>Export CSV</span>
                </a>
                <!-- Export Finance -->
                <a x-show="tab === 'finance'"
                   href="{{ route('admin.logs.export.finance', array_filter(['from' => $from, 'to' => $to])) }}"
                   class="flex items-center space-x-1.5 text-xs font-label text-secondary hover:text-secondary/80 bg-secondary/10 px-3 py-2 rounded-xl transition">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                    <span>Export CSV</span>
                </a>
            </div>
        </div>

        <!-- AI Usage Tab -->
        <div x-show="tab === 'ai'" x-cloak>

            {{-- Mobile card layout --}}
            <div class="md:hidden divide-y divide-primary/5">
                @forelse($aiLogs as $log)
                @php
                    $actionColors = [
                        'ats_analyze'       => 'bg-primary/10 text-primary',
                        'bullet_optimize'   => 'bg-secondary/10 text-secondary',
                        'generate_versions' => 'bg-amber-100 text-amber-700',
                        'manuscript_ats'    => 'bg-blue-100 text-blue-600',
                    ];
                    $colorClass = $actionColors[$log->action_type] ?? 'bg-primary/5 text-primary/60';
                @endphp
                <div class="p-4 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-primary/40 font-label">{{ $log->created_at?->format('d M Y, H:i') }}</span>
                        <span class="font-headline font-bold text-primary text-sm">${{ number_format($log->cost_usd, 4) }}</span>
                    </div>
                    <p class="text-sm font-bold text-primary">{{ $log->user?->name ?? '—' }}</p>
                    <div class="flex items-center gap-2">
                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $colorClass }}">
                            {{ str_replace('_', ' ', $log->action_type) }}
                        </span>
                        <span class="text-xs text-primary/40">{{ number_format($log->tokens_used) }} tokens</span>
                    </div>
                </div>
                @empty
                <div class="py-16 text-center">
                    <span class="material-symbols-outlined text-primary/20 text-[48px] block mb-2">auto_awesome</span>
                    <p class="text-sm font-label text-primary/40">No AI usage logs found</p>
                </div>
                @endforelse
            </div>

            {{-- Desktop table --}}
            <div class="hidden md:block overflow-x-auto">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-surface/50 border-b border-primary/5">
                            <th class="text-left text-[9px] font-label text-primary/40 uppercase tracking-widest py-3 px-6">Timestamp</th>
                            <th class="text-left text-[9px] font-label text-primary/40 uppercase tracking-widest py-3 px-4">User</th>
                            <th class="text-left text-[9px] font-label text-primary/40 uppercase tracking-widest py-3 px-4">Action</th>
                            <th class="text-right text-[9px] font-label text-primary/40 uppercase tracking-widest py-3 px-4">Tokens</th>
                            <th class="text-right text-[9px] font-label text-primary/40 uppercase tracking-widest py-3 px-6">Cost (USD)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/5">
                        @forelse($aiLogs as $log)
                        <tr class="hover:bg-surface/40 transition-colors text-xs font-label">
                            <td class="py-4 px-6 text-primary/50 whitespace-nowrap">
                                {{ $log->created_at?->format('d M Y, H:i') }}
                            </td>
                            <td class="py-4 px-4">
                                <p class="font-bold text-primary">{{ $log->user?->name ?? '—' }}</p>
                                <p class="text-primary/40 text-[11px]">{{ $log->user?->email ?? '—' }}</p>
                            </td>
                            <td class="py-4 px-4">
                                @php
                                    $actionColors = [
                                        'ats_analyze'        => 'bg-primary/10 text-primary',
                                        'bullet_optimize'    => 'bg-secondary/10 text-secondary',
                                        'generate_versions'  => 'bg-amber-100 text-amber-700',
                                        'manuscript_ats'     => 'bg-blue-100 text-blue-600',
                                    ];
                                    $colorClass = $actionColors[$log->action_type] ?? 'bg-primary/5 text-primary/60';
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $colorClass }}">
                                    {{ str_replace('_', ' ', $log->action_type) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right text-primary font-headline">
                                {{ number_format($log->tokens_used) }}
                            </td>
                            <td class="py-4 px-6 text-right font-headline text-primary font-bold">
                                ${{ number_format($log->cost_usd, 4) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <span class="material-symbols-outlined text-primary/20 text-[48px] block mb-2">auto_awesome</span>
                                <p class="text-sm font-label text-primary/40">No AI usage logs found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>{{-- end desktop table --}}
            @if($aiLogs->hasPages())
                <div class="px-6 py-4 border-t border-primary/5">
                    {{ $aiLogs->links() }}
                </div>
            @endif
        </div>

        <!-- Finance Tab -->
        <div x-show="tab === 'finance'" x-cloak>

            {{-- Mobile card layout --}}
            <div class="md:hidden divide-y divide-primary/5">
                @forelse($transactions as $tx)
                @php
                    $statusMap = [
                        'success' => 'bg-secondary/10 text-secondary',
                        'pending' => 'bg-amber-100 text-amber-600',
                        'failed'  => 'bg-red-100 text-red-500',
                        'expired' => 'bg-primary/10 text-primary/40',
                    ];
                @endphp
                <div class="p-4 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="font-headline font-bold text-primary">Rp {{ number_format($tx->amount, 0, ',', '.') }}</span>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusMap[$tx->status] ?? 'bg-primary/5 text-primary/50' }}">
                            {{ $tx->status }}
                        </span>
                    </div>
                    <p class="text-sm font-bold text-primary">{{ $tx->user?->name ?? '—' }}</p>
                    <p class="text-xs text-primary/40">{{ $tx->user?->email ?? '—' }}</p>
                    <p class="text-[10px] text-primary/40">{{ $tx->created_at?->format('d M Y, H:i') }}</p>
                </div>
                @empty
                <div class="py-16 text-center">
                    <span class="material-symbols-outlined text-primary/20 text-[48px] block mb-2">payments</span>
                    <p class="text-sm font-label text-primary/40">No transactions found</p>
                </div>
                @endforelse
            </div>

            {{-- Desktop table --}}
            <div class="hidden md:block overflow-x-auto">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-surface/50 border-b border-primary/5">
                            <th class="text-left text-[9px] font-label text-primary/40 uppercase tracking-widest py-3 px-6">Date</th>
                            <th class="text-left text-[9px] font-label text-primary/40 uppercase tracking-widest py-3 px-4">User</th>
                            <th class="text-left text-[9px] font-label text-primary/40 uppercase tracking-widest py-3 px-4">Order ID</th>
                            <th class="text-right text-[9px] font-label text-primary/40 uppercase tracking-widest py-3 px-4">Amount</th>
                            <th class="text-left text-[9px] font-label text-primary/40 uppercase tracking-widest py-3 px-4">Method</th>
                            <th class="text-left text-[9px] font-label text-primary/40 uppercase tracking-widest py-3 px-6">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/5">
                        @forelse($transactions as $tx)
                        <tr class="hover:bg-surface/40 transition-colors text-xs font-label">
                            <td class="py-4 px-6 text-primary/50 whitespace-nowrap">
                                {{ $tx->created_at?->format('d M Y, H:i') }}
                            </td>
                            <td class="py-4 px-4">
                                <p class="font-bold text-primary">{{ $tx->user?->name ?? '—' }}</p>
                                <p class="text-primary/40 text-[11px]">{{ $tx->user?->email ?? '—' }}</p>
                            </td>
                            <td class="py-4 px-4 text-primary/60 font-mono text-[11px]">
                                {{ $tx->midtrans_order_id ?? '—' }}
                            </td>
                            <td class="py-4 px-4 text-right font-headline text-primary font-bold">
                                Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4 text-primary/60 capitalize">
                                {{ $tx->payment_method ?? '—' }}
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $statusMap = [
                                        'success'  => 'bg-secondary/10 text-secondary',
                                        'pending'  => 'bg-amber-100 text-amber-600',
                                        'failed'   => 'bg-red-100 text-red-500',
                                        'expired'  => 'bg-primary/10 text-primary/40',
                                    ];
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusMap[$tx->status] ?? 'bg-primary/5 text-primary/50' }}">
                                    {{ $tx->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <span class="material-symbols-outlined text-primary/20 text-[48px] block mb-2">payments</span>
                                <p class="text-sm font-label text-primary/40">No transactions found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>{{-- end desktop table --}}
            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-primary/5">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

    </div>

</div>
@endsection

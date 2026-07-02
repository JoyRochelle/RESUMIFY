@extends('layouts.admin.app')

@section('title', 'Revenue Report - Admin Dashboard')

@section('content')
<div class="admin-shell">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-headline font-bold text-primary mb-2">Revenue Report</h1>
            <p class="text-sm font-label text-primary/60">Aggregate stats for any date range.</p>
        </div>
    </div>

    <!-- Date Range Filter -->
    <form method="GET" action="{{ route('admin.reports') }}" class="admin-card-pad">
        <p class="admin-section-title mb-4">Date Range</p>
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center space-x-2 bg-surface border border-primary/10 rounded-lg px-4 h-11">
                <span class="text-xs font-label text-primary/50">From</span>
                <input type="date" name="from" value="{{ $from->toDateString() }}"
                       class="bg-transparent border-none focus:outline-none text-sm font-label text-primary">
            </div>

            <div class="flex items-center space-x-2 bg-surface border border-primary/10 rounded-lg px-4 h-11">
                <span class="text-xs font-label text-primary/50">To</span>
                <input type="date" name="to" value="{{ $to->toDateString() }}"
                       class="bg-transparent border-none focus:outline-none text-sm font-label text-primary">
            </div>

            <x-ui.loading-button loading-text="Generating..." icon="bar_chart">Generate</x-ui.loading-button>

            <a href="{{ route('admin.reports') }}"
               class="text-sm font-label text-primary/50 hover:text-primary transition">Reset</a>

            <div class="ml-auto flex items-center space-x-3">
                <a href="{{ route('admin.reports.export.csv', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}"
                   class="flex items-center space-x-1.5 text-xs font-label text-secondary hover:text-secondary/80 bg-secondary/10 px-4 py-2.5 rounded-lg transition">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                    <span>Export CSV</span>
                </a>
                <a href="{{ route('admin.reports.export.pdf', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}"
                   class="flex items-center space-x-1.5 text-xs font-label text-primary hover:text-primary/80 bg-primary/10 px-4 py-2.5 rounded-lg transition">
                    <span class="material-symbols-outlined text-[16px]">picture_as_pdf</span>
                    <span>Export PDF</span>
                </a>
            </div>
        </div>
        <p class="text-xs font-label text-primary/40 mt-3">
            Showing data from
            <strong>{{ $from->format('d M Y') }}</strong>
            to
            <strong>{{ $to->format('d M Y') }}</strong>
        </p>
    </form>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <div class="admin-card-pad">
            <div class="flex items-center justify-between mb-3">
                <span class="material-symbols-outlined text-secondary text-[20px]">payments</span>
                <span class="text-[9px] font-label text-primary/40 uppercase tracking-widest">Revenue</span>
            </div>
            <p class="text-[10px] font-label text-primary/50 uppercase tracking-widest mb-1">Total Revenue</p>
            <p class="text-3xl font-headline text-secondary">
                Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
            </p>
        </div>

        <div class="admin-card-pad">
            <div class="flex items-center justify-between mb-3">
                <span class="material-symbols-outlined text-primary/50 text-[20px]">person_add</span>
                <span class="text-[9px] font-label text-primary/40 uppercase tracking-widest">Users</span>
            </div>
            <p class="text-[10px] font-label text-primary/50 uppercase tracking-widest mb-1">New Users</p>
            <p class="text-3xl font-headline text-primary">{{ number_format($stats['new_users']) }}</p>
        </div>

        <div class="admin-card-pad">
            <div class="flex items-center justify-between mb-3">
                <span class="material-symbols-outlined text-amber-500 text-[20px]">star</span>
                <span class="text-[9px] font-label text-primary/40 uppercase tracking-widest">Conversions</span>
            </div>
            <p class="text-[10px] font-label text-primary/50 uppercase tracking-widest mb-1">Premium Conversions</p>
            <p class="text-3xl font-headline text-primary">{{ number_format($stats['premium_conversions']) }}</p>
        </div>

        <div class="admin-card-pad">
            <div class="flex items-center justify-between mb-3">
                <span class="material-symbols-outlined text-primary/50 text-[20px]">auto_awesome</span>
                <span class="text-[9px] font-label text-primary/40 uppercase tracking-widest">AI</span>
            </div>
            <p class="text-[10px] font-label text-primary/50 uppercase tracking-widest mb-1">Total AI Calls</p>
            <p class="text-3xl font-headline text-primary">{{ number_format($stats['total_ai_calls']) }}</p>
        </div>

        <div class="admin-card-pad">
            <div class="flex items-center justify-between mb-3">
                <span class="material-symbols-outlined text-red-400 text-[20px]">account_balance_wallet</span>
                <span class="text-[9px] font-label text-primary/40 uppercase tracking-widest">AI Cost</span>
            </div>
            <p class="text-[10px] font-label text-primary/50 uppercase tracking-widest mb-1">Total AI Cost</p>
            <p class="text-3xl font-headline text-primary">${{ number_format($stats['total_ai_cost'], 4) }}</p>
        </div>

        <div class="admin-card-pad">
            <div class="flex items-center justify-between mb-3">
                <span class="material-symbols-outlined text-primary/50 text-[20px]">trending_up</span>
                <span class="text-[9px] font-label text-primary/40 uppercase tracking-widest">Margin</span>
            </div>
            <p class="text-[10px] font-label text-primary/50 uppercase tracking-widest mb-1">Net Margin</p>
            @php
                $netMargin = $stats['total_revenue'] > 0
                    ? round((($stats['total_revenue'] - ($stats['total_ai_cost'] * 15000)) / $stats['total_revenue']) * 100, 1)
                    : null;
            @endphp
            <p class="text-3xl font-headline {{ $netMargin !== null && $netMargin >= 0 ? 'text-secondary' : 'text-red-500' }}">
                {{ $netMargin !== null ? $netMargin . '%' : 'N/A' }}
            </p>
        </div>

    </div>

    <!-- Daily Revenue Breakdown -->
    @if($dailyRevenue->isNotEmpty())
    <div class="admin-card-pad">
        <h3 class="admin-section-title mb-5">Daily Revenue Breakdown</h3>
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead class="admin-table-head">
                    <tr>
                        <th class="admin-th text-left">Date</th>
                        <th class="admin-th text-right">Transactions</th>
                        <th class="admin-th text-right">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/10">
                    @foreach($dailyRevenue as $day)
                    <tr class="hover:bg-surface/40 transition-colors">
                        <td class="admin-td font-label text-primary">
                            {{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}
                        </td>
                        <td class="admin-td font-headline text-primary text-right">
                            {{ number_format($day->count) }}
                        </td>
                        <td class="admin-td font-headline font-bold text-secondary text-right">
                            Rp {{ number_format($day->total, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-primary/10">
                        <td class="admin-td font-label font-bold text-primary">Total</td>
                        <td class="admin-td font-headline font-bold text-primary text-right">
                            {{ number_format($dailyRevenue->sum('count')) }}
                        </td>
                        <td class="admin-td font-headline font-bold text-secondary text-right">
                            Rp {{ number_format($dailyRevenue->sum('total'), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection

@extends('layouts.admin.app')

@section('title', 'Admin Dashboard - Resumify')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    <!-- Top Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        <!-- Total Revenue -->
        <div class="admin-card-pad">
            <h3 class="admin-section-title mb-4">Total Revenue</h3>
            <div class="text-3xl font-headline text-primary mb-4">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
            <div class="flex items-center text-sm font-label">
                @if($revenueGrowth !== null)
                    @if($revenueGrowth >= 0)
                        <span class="material-symbols-outlined text-[16px] mr-1 text-secondary">trending_up</span>
                        <span class="text-secondary">+{{ $revenueGrowth }}%</span>
                    @else
                        <span class="material-symbols-outlined text-[16px] mr-1 text-red-500">trending_down</span>
                        <span class="text-red-500">{{ $revenueGrowth }}%</span>
                    @endif
                    <span class="text-primary/40 ml-2">vs last month</span>
                @else
                    <span class="text-primary/40">No data for previous month</span>
                @endif
            </div>
        </div>

        <!-- AI API Costs -->
        <div class="admin-card-pad">
            <h3 class="admin-section-title mb-4">AI API Costs</h3>
            <div class="flex items-end mb-4">
                <span class="text-3xl font-headline text-primary mr-2">${{ number_format($aiCostUsd, 2) }}</span>
                <span class="text-sm font-label text-primary/40 mb-1">this month</span>
            </div>
            @php $aiCostLimit = 200; $aiPct = min(100, round($aiCostUsd / $aiCostLimit * 100)); @endphp
            <div class="w-full bg-surface-container-low h-1.5 rounded-full overflow-hidden mb-2">
                <div class="h-full rounded-full {{ $aiPct >= 80 ? 'bg-red-400' : 'bg-primary' }}"
                     style="width: {{ $aiPct }}%"></div>
            </div>
            <p class="text-[10px] font-label text-primary/40">{{ $aiPct }}% of ${{ number_format($aiCostLimit) }} monthly limit</p>
        </div>

        <!-- Open Support Tickets -->
        <div class="admin-card-pad">
            <h3 class="admin-section-title mb-4">Open Support Tickets</h3>
            <div class="text-3xl font-headline text-primary mb-4">{{ $openTickets }}</div>
            <div class="flex items-center">
                @if($openTickets === 0)
                    <span class="text-[10px] font-label text-secondary">All clear — no open tickets</span>
                @else
                    <span class="material-symbols-outlined text-[16px] mr-1 text-primary/40">inbox</span>
                    <span class="text-[10px] font-label text-primary/40">awaiting response</span>
                @endif
            </div>
        </div>

        <!-- System Health -->
        <div class="admin-card-pad">
            <h3 class="text-[10px] font-label text-secondary uppercase tracking-widest mb-4">System Health</h3>
            @if($sentryErrors === null)
                <div class="flex items-center text-3xl font-headline text-primary mb-4">
                    <div class="w-3 h-3 bg-secondary rounded-full mr-3 shadow-[0_0_8px_rgba(16,185,129,0.4)]"></div>
                    Healthy
                </div>
                <p class="text-[10px] font-label text-primary/40 leading-tight">Sentry not configured — no error data</p>
            @elseif($sentryErrors === 0)
                <div class="flex items-center text-3xl font-headline text-primary mb-4">
                    <div class="w-3 h-3 bg-secondary rounded-full mr-3 shadow-[0_0_8px_rgba(16,185,129,0.4)]"></div>
                    Optimal
                </div>
                <p class="text-[10px] font-label text-secondary leading-tight">0 unresolved errors in the last 24h</p>
            @else
                <div class="flex items-center text-3xl font-headline text-primary mb-4">
                    <div class="w-3 h-3 bg-red-400 rounded-full mr-3 shadow-[0_0_8px_rgba(248,113,113,0.4)]"></div>
                    {{ $sentryErrors }} Error{{ $sentryErrors > 1 ? 's' : '' }}
                </div>
                <p class="text-[10px] font-label text-red-400 leading-tight">Unresolved issues in last 24h</p>
            @endif
        </div>

    </div>

    <!-- Middle Row: Chart & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main Chart -->
        <div class="lg:col-span-2 bg-tertiary rounded-lg p-8 shadow-sm border border-primary/10">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-xl font-headline font-bold text-primary">New Users vs AI Calls</h2>
                    <p class="text-[10px] font-label text-primary/40 mt-1">Last 30 days</p>
                </div>
                <div class="flex space-x-4">
                    <div class="flex items-center">
                        <div class="w-2 h-2 rounded-full bg-primary mr-2"></div>
                        <span class="text-[10px] font-label font-bold text-primary">New Users</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-2 h-2 rounded-full bg-secondary mr-2"></div>
                        <span class="text-[10px] font-label font-bold text-primary">AI Calls</span>
                    </div>
                </div>
            </div>
            <div class="relative h-[250px] w-full">
                <canvas id="trafficChart"></canvas>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-surface-container-low rounded-lg p-8 border border-primary/10">
            <h2 class="text-xl font-headline text-primary mb-6">Quick Actions</h2>
            <div class="space-y-4 mb-8">
                <a href="{{ Route::has('admin.reports') ? route('admin.reports') : '#' }}"
                   class="w-full bg-tertiary rounded-lg p-4 flex items-center justify-between hover:shadow-md transition-shadow group">
                    <div class="flex items-center space-x-3 text-primary">
                        <span class="material-symbols-outlined text-[20px]">description</span>
                        <span class="text-sm font-label font-bold">Generate Report</span>
                    </div>
                    <span class="material-symbols-outlined text-primary/40 group-hover:text-primary transition-colors text-[20px]">chevron_right</span>
                </a>
                <a href="{{ route('admin.templates.create') }}"
                   class="w-full bg-tertiary rounded-lg p-4 flex items-center justify-between hover:shadow-md transition-shadow group">
                    <div class="flex items-center space-x-3 text-primary">
                        <span class="material-symbols-outlined text-[20px]">add_circle</span>
                        <span class="text-sm font-label font-bold">Add New Template</span>
                    </div>
                    <span class="material-symbols-outlined text-primary/40 group-hover:text-primary transition-colors text-[20px]">chevron_right</span>
                </a>
                <a href="{{ Route::has('admin.monitor') ? route('admin.monitor') : '#' }}"
                   class="w-full bg-tertiary rounded-lg p-4 flex items-center justify-between hover:shadow-md transition-shadow group">
                    <div class="flex items-center space-x-3 text-primary">
                        <span class="material-symbols-outlined text-[20px]">monitor_heart</span>
                        <span class="text-sm font-label font-bold">System Monitor</span>
                    </div>
                    <span class="material-symbols-outlined text-primary/40 group-hover:text-primary transition-colors text-[20px]">chevron_right</span>
                </a>
            </div>
            <div class="bg-tertiary p-5 rounded-lg text-[11px] font-headline text-primary/60 italic leading-relaxed border border-primary/10">
                "{{ $premiumUsers }} premium users are trusting Resumify right now."
            </div>
        </div>

    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-10">

        <!-- Recent User Activity -->
        <div class="bg-tertiary rounded-lg p-8 shadow-sm border border-primary/10">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-headline text-primary">Recent Signups</h2>
                <a href="{{ route('admin.users') }}" class="text-[10px] font-label text-secondary hover:underline">View all</a>
            </div>
            <div class="space-y-5">
                @forelse($recentUsers as $user)
                    @php $initials = collect(explode(' ', $user->name))->take(2)->map(fn($w) => strtoupper($w[0]))->implode(''); @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-full bg-surface-container-low flex items-center justify-center text-primary font-bold text-xs shrink-0">
                                {{ $initials }}
                            </div>
                            <div>
                                <p class="text-sm font-label font-bold text-primary leading-tight">{{ $user->name }}</p>
                                <p class="text-[10px] font-label text-primary/40 truncate max-w-[140px]">{{ $user->email }}</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-label px-2 py-0.5 rounded-full shrink-0
                            {{ $user->role === 'premium' ? 'bg-secondary/10 text-secondary' : 'bg-primary/5 text-primary/50' }}">
                            {{ strtoupper($user->role) }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm font-label text-primary/40">No users yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Support Queue -->
        <div class="bg-tertiary rounded-lg p-8 shadow-sm border border-primary/10">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-headline text-primary">Support Queue</h2>
                <a href="{{ route('admin.support') }}" class="text-[10px] font-label text-secondary hover:underline">View all</a>
            </div>
            <div class="space-y-4">
                @forelse($recentTickets as $ticket)
                    <a href="{{ Route::has('admin.support.show') ? route('admin.support.show', $ticket) : '#' }}"
                       class="block bg-surface p-4 rounded-lg border border-primary/10 hover:border-primary/20 transition-colors">
                        <div class="flex justify-between items-start mb-1">
                            <p class="text-xs font-label font-bold text-primary truncate max-w-[160px]">{{ $ticket->subject }}</p>
                            <span class="text-[9px] text-primary/40 ml-2 shrink-0">{{ $ticket->created_at->diffForHumans(null, true) }}</span>
                        </div>
                        <p class="text-[10px] font-label text-primary/50">{{ $ticket->user->name }}</p>
                    </a>
                @empty
                    <div class="text-center py-6">
                        <span class="material-symbols-outlined text-[32px] text-secondary">check_circle</span>
                        <p class="text-sm font-label text-primary/40 mt-2">No open tickets</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Template Performance -->
        <div class="bg-tertiary rounded-lg p-8 shadow-sm border border-primary/10">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-headline text-primary">Template Usage</h2>
                <a href="{{ route('admin.templates.index') }}" class="text-[10px] font-label text-secondary hover:underline">Manage</a>
            </div>
            <div class="space-y-5">
                @forelse($templatePerformance as $template)
                    @php $pct = $maxCvCount > 0 ? round($template->cvs_count / $maxCvCount * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between text-[10px] font-label uppercase tracking-widest font-bold mb-2">
                            <span class="text-primary truncate max-w-[150px]">{{ $template->name }}</span>
                            <span class="text-primary/60 text-[9px] shrink-0 ml-2">{{ number_format($template->cvs_count) }} CVs</span>
                        </div>
                        <div class="w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
                            <div class="bg-primary h-full rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm font-label text-primary/40">No templates in use yet.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const data = @json($chartData);
    const ctx  = document.getElementById('trafficChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'New Users',
                    data: data.activeUsers,
                    borderColor: '#4f3b2f',
                    backgroundColor: 'rgba(79,59,47,0.06)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#4f3b2f',
                    tension: 0.35,
                    fill: true,
                },
                {
                    label: 'AI Calls',
                    data: data.aiCalls,
                    borderColor: '#0F6E56',
                    backgroundColor: 'rgba(15,110,86,0.06)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#0F6E56',
                    tension: 0.35,
                    fill: true,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#4f3b2f',
                    bodyColor: '#4f3b2f',
                    borderColor: 'rgba(79,59,47,0.1)',
                    borderWidth: 1,
                    padding: 10,
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: 'rgba(79,59,47,0.3)',
                        font: { size: 10 },
                        maxTicksLimit: 7,
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(79,59,47,0.05)' },
                    ticks: {
                        color: 'rgba(79,59,47,0.3)',
                        font: { size: 10 },
                        precision: 0,
                    }
                }
            }
        }
    });
})();
</script>
@endpush

@extends('layouts.admin.app')

@section('title', 'System Monitor - Admin Dashboard')

@section('content')
<div class="admin-shell">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-headline font-bold text-primary mb-2">System Monitor</h1>
            <p class="text-sm font-label text-primary/60">Queue health, disk usage, and error tracking.</p>
        </div>
        <a href="{{ route('admin.monitor') }}"
           class="flex items-center space-x-2 text-sm font-label text-primary/60 hover:text-primary bg-tertiary border border-primary/10 px-4 py-2 rounded-lg shadow-sm transition-colors">
            <span class="material-symbols-outlined text-[18px]">refresh</span>
            <span>Refresh</span>
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        <!-- Pending Jobs -->
        <div class="admin-card-pad">
            <div class="flex items-center justify-between mb-3">
                <span class="material-symbols-outlined text-primary/40 text-[20px]">pending</span>
                <span class="text-[9px] font-label text-primary/40 uppercase tracking-widest">QUEUE</span>
            </div>
            <p class="text-[10px] font-label text-primary/50 uppercase tracking-widest mb-1">Pending Jobs</p>
            <p class="text-3xl font-headline text-primary">{{ number_format($pendingJobs) }}</p>
            @if($lastJobAt)
                <p class="text-[11px] font-label text-primary/40 mt-2">Last: {{ $lastJobAt->diffForHumans() }}</p>
            @else
                <p class="text-[11px] font-label text-primary/30 mt-2">No queued jobs</p>
            @endif
        </div>

        <!-- Failed Jobs -->
        <div class="admin-card-pad">
            <div class="flex items-center justify-between mb-3">
                <span class="material-symbols-outlined text-[20px] {{ $failedJobs > 0 ? 'text-red-400' : 'text-primary/40' }}">error</span>
                <span class="text-[9px] font-label text-primary/40 uppercase tracking-widest">FAILED</span>
            </div>
            <p class="text-[10px] font-label text-primary/50 uppercase tracking-widest mb-1">Failed Jobs</p>
            <p class="text-3xl font-headline {{ $failedJobs > 0 ? 'text-red-500' : 'text-primary' }}">
                {{ number_format($failedJobs) }}
            </p>
            <p class="text-[11px] font-label mt-2 {{ $failedJobs > 0 ? 'text-red-400' : 'text-secondary' }}">
                {{ $failedJobs > 0 ? 'Requires attention' : 'All clear' }}
            </p>
        </div>

        <!-- Disk Usage -->
        <div class="admin-card-pad">
            <div class="flex items-center justify-between mb-3">
                <span class="material-symbols-outlined text-primary/40 text-[20px]">storage</span>
                <span class="text-[9px] font-label text-primary/40 uppercase tracking-widest">STORAGE</span>
            </div>
            <p class="text-[10px] font-label text-primary/50 uppercase tracking-widest mb-1">Disk Used</p>
            @if($diskTotal > 0)
                <p class="text-3xl font-headline text-primary">{{ $diskPct }}%</p>
                <div class="mt-2 h-1.5 bg-primary/5 rounded-full overflow-hidden">
                    <div class="h-full rounded-full {{ $diskPct >= 90 ? 'bg-red-400' : ($diskPct >= 70 ? 'bg-amber-400' : 'bg-secondary') }}"
                         style="width: {{ $diskPct }}%"></div>
                </div>
                <p class="text-[11px] font-label text-primary/40 mt-1">
                    {{ number_format($diskUsed / 1073741824, 1) }} GB /
                    {{ number_format($diskTotal / 1073741824, 1) }} GB
                </p>
            @else
                <p class="text-3xl font-headline text-primary/30">N/A</p>
                <p class="text-[11px] font-label text-primary/30 mt-2">Unavailable</p>
            @endif
        </div>

        <!-- Sentry Errors -->
        <div class="admin-card-pad">
            <div class="flex items-center justify-between mb-3">
                <span class="material-symbols-outlined text-[20px] {{ $sentryErrors > 0 ? 'text-red-400' : 'text-primary/40' }}">bug_report</span>
                <span class="text-[9px] font-label text-primary/40 uppercase tracking-widest">SENTRY</span>
            </div>
            <p class="text-[10px] font-label text-primary/50 uppercase tracking-widest mb-1">Unresolved Errors</p>
            @if(is_null($sentryErrors))
                <p class="text-3xl font-headline text-primary/30">N/A</p>
                <p class="text-[11px] font-label text-primary/30 mt-2">Token not configured</p>
            @else
                <p class="text-3xl font-headline {{ $sentryErrors > 0 ? 'text-red-500' : 'text-primary' }}">
                    {{ number_format($sentryErrors) }}
                </p>
                <p class="text-[11px] font-label mt-2 {{ $sentryErrors > 0 ? 'text-red-400' : 'text-secondary' }}">
                    {{ $sentryErrors > 0 ? 'Last 24 hours' : 'No errors today' }}
                </p>
            @endif
        </div>

    </div>

    <!-- Disk Usage Detail -->
    @if($diskTotal > 0)
    <div class="admin-card-pad">
        <h3 class="admin-section-title mb-5">Disk Usage Detail</h3>
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-label text-primary">Storage partition</span>
            <span class="text-sm font-label text-primary font-bold">{{ $diskPct }}% used</span>
        </div>
        <div class="h-3 bg-primary/5 rounded-full overflow-hidden mb-3">
            <div class="h-full rounded-full transition-all
                        {{ $diskPct >= 90 ? 'bg-red-400' : ($diskPct >= 70 ? 'bg-amber-400' : 'bg-secondary') }}"
                 style="width: {{ $diskPct }}%"></div>
        </div>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-lg font-headline font-bold text-primary">{{ number_format($diskUsed / 1073741824, 2) }} GB</p>
                <p class="text-[10px] font-label text-primary/40 uppercase tracking-widest mt-0.5">Used</p>
            </div>
            <div>
                <p class="text-lg font-headline font-bold text-secondary">{{ number_format($diskFree / 1073741824, 2) }} GB</p>
                <p class="text-[10px] font-label text-primary/40 uppercase tracking-widest mt-0.5">Free</p>
            </div>
            <div>
                <p class="text-lg font-headline font-bold text-primary">{{ number_format($diskTotal / 1073741824, 2) }} GB</p>
                <p class="text-[10px] font-label text-primary/40 uppercase tracking-widest mt-0.5">Total</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Queue Health + Recent Failed Jobs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Queue Health -->
        <div class="admin-card-pad">
            <h3 class="admin-section-title mb-5">Queue Health</h3>

            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-primary/5">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-primary/5 flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary/50 text-[16px]">schedule</span>
                        </div>
                        <span class="text-sm font-label text-primary">Pending</span>
                    </div>
                    <span class="text-sm font-headline font-bold text-primary">{{ number_format($pendingJobs) }}</span>
                </div>

                <div class="flex items-center justify-between py-3 border-b border-primary/5">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg {{ $failedJobs > 0 ? 'bg-red-50' : 'bg-primary/5' }} flex items-center justify-center">
                            <span class="material-symbols-outlined text-[16px] {{ $failedJobs > 0 ? 'text-red-400' : 'text-primary/50' }}">close</span>
                        </div>
                        <span class="text-sm font-label text-primary">Failed</span>
                    </div>
                    <span class="text-sm font-headline font-bold {{ $failedJobs > 0 ? 'text-red-500' : 'text-primary' }}">
                        {{ number_format($failedJobs) }}
                    </span>
                </div>

                <div class="flex items-center justify-between py-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-primary/5 flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary/50 text-[16px]">history</span>
                        </div>
                        <span class="text-sm font-label text-primary">Last queued</span>
                    </div>
                    <span class="text-sm font-label text-primary/60">
                        {{ $lastJobAt ? $lastJobAt->diffForHumans() : 'Never' }}
                    </span>
                </div>
            </div>

            <!-- Overall status indicator -->
            <div class="mt-5 pt-5 border-t border-primary/5 flex items-center space-x-3">
                @if($failedJobs > 0)
                    <div class="w-2.5 h-2.5 rounded-full bg-red-400 animate-pulse"></div>
                    <span class="text-sm font-label text-red-500">{{ $failedJobs }} failed job(s) need attention</span>
                @elseif($pendingJobs > 0)
                    <div class="w-2.5 h-2.5 rounded-full bg-amber-400"></div>
                    <span class="text-sm font-label text-amber-600">{{ $pendingJobs }} job(s) processing</span>
                @else
                    <div class="w-2.5 h-2.5 rounded-full bg-secondary"></div>
                    <span class="text-sm font-label text-secondary">Queue is healthy</span>
                @endif
            </div>
        </div>

        <!-- Recent Failed Jobs -->
        <div class="admin-card-pad">
            <h3 class="admin-section-title mb-5">
                Recent Failed Jobs
                @if($recentFailed->isNotEmpty())
                    <span class="ml-2 text-[10px] bg-red-100 text-red-500 px-2 py-0.5 rounded-full">{{ $recentFailed->count() }}</span>
                @endif
            </h3>

            @forelse($recentFailed as $job)
                <div class="py-3 {{ !$loop->last ? 'border-b border-primary/5' : '' }}">
                    <div class="flex items-start justify-between mb-1">
                        <span class="text-xs font-label text-primary/60 font-mono">{{ $job->queue }}</span>
                        <span class="text-[11px] font-label text-primary/40 whitespace-nowrap ml-2">
                            {{ \Carbon\Carbon::parse($job->failed_at)->diffForHumans() }}
                        </span>
                    </div>
                    <p class="text-[11px] font-label text-red-500 line-clamp-2 break-all">
                        {{ Str::limit(explode("\n", $job->exception)[0] ?? '', 100) }}
                    </p>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <span class="material-symbols-outlined text-secondary text-[40px] mb-2">check_circle</span>
                    <p class="text-sm font-label text-primary/40">No failed jobs</p>
                </div>
            @endforelse
        </div>

    </div>

    <!-- Sentry Errors Detail -->
    <div class="admin-card-pad">
        <div class="flex items-center justify-between mb-5">
            <h3 class="admin-section-title">Sentry Error Tracking</h3>
            @if(!is_null($sentryErrors))
                <span class="text-[10px] font-label text-primary/40">Cached · refreshes every 5 min</span>
            @endif
        </div>

        @if(is_null($sentryErrors))
            <div class="flex items-center space-x-4 p-4 bg-primary/5 rounded-lg">
                <span class="material-symbols-outlined text-primary/40 text-[32px]">info</span>
                <div>
                    <p class="text-sm font-label text-primary font-bold mb-1">Sentry not configured</p>
                    <p class="text-xs font-label text-primary/50">
                        Set <code class="bg-primary/10 px-1 py-0.5 rounded text-[11px]">SENTRY_AUTH_TOKEN</code>,
                        <code class="bg-primary/10 px-1 py-0.5 rounded text-[11px]">SENTRY_ORG_SLUG</code>, and
                        <code class="bg-primary/10 px-1 py-0.5 rounded text-[11px]">SENTRY_PROJECT_SLUG</code>
                        in your <code class="bg-primary/10 px-1 py-0.5 rounded text-[11px]">.env</code> to enable the errors feed.
                    </p>
                </div>
            </div>
        @else
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-lg {{ $sentryErrors > 0 ? 'bg-red-50' : 'bg-secondary/10' }} flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[28px] {{ $sentryErrors > 0 ? 'text-red-400' : 'text-secondary' }}">
                        {{ $sentryErrors > 0 ? 'bug_report' : 'check_circle' }}
                    </span>
                </div>
                <div>
                    <p class="text-2xl font-headline font-bold {{ $sentryErrors > 0 ? 'text-red-500' : 'text-primary' }}">
                        {{ number_format($sentryErrors) }} unresolved issue{{ $sentryErrors !== 1 ? 's' : '' }}
                    </p>
                    <p class="text-sm font-label text-primary/50 mt-1">
                        {{ $sentryErrors > 0
                            ? 'Open Sentry dashboard to review and resolve these errors.'
                            : 'No unresolved errors in the last 24 hours.' }}
                    </p>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection

@props([
    'user' => auth()->user(),
    'compact' => false,
])

@php
    $aiUsed = (int) ($user->ai_quota_used ?? 0);
    $aiLimit = $user->getQuotaLimit();
    $aiPercentage = $aiLimit > 0 ? min(100, round(($aiUsed / $aiLimit) * 100)) : 0;

    $resumeUsed = $user->getResumeQuotaUsed();
    $resumeLimit = $user->getResumeLimit();
    $resumePercentage = $resumeLimit ? min(100, round(($resumeUsed / $resumeLimit) * 100)) : 100;
    $isPremium = $user->isPremium();
@endphp

<section {{ $attributes->merge(['class' => $compact ? 'grid gap-3' : 'grid gap-4 rounded-lg border border-primary/10 bg-tertiary p-4 shadow-sm']) }}
         aria-label="Current plan usage"
         aria-live="polite">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <x-user.plan-badge :user="$user" />
        @unless($isPremium)
            <a href="{{ route('user.upgrade-quota') }}"
               class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-[#A16207]/25 bg-[#A16207]/10 px-3 py-2 text-xs font-label font-bold text-[#7C4A03] transition hover:bg-[#A16207]/15 focus:outline-none focus:ring-2 focus:ring-[#A16207]/30">
                <span class="material-symbols-outlined text-[16px] icon-filled" aria-hidden="true">lock_open</span>
                {{ __('messages.quota_status.upgrade') }}
            </a>
        @endunless
    </div>

    <div class="grid gap-3 sm:grid-cols-2">
        <div class="rounded-lg border border-primary/10 bg-surface-container-low p-3" data-ai-quota-widget>
            <div class="mb-2 flex items-center justify-between gap-3">
                <span class="text-[11px] font-label font-bold uppercase tracking-widest text-primary/60">{{ __('messages.quota_status.ai_credits') }}</span>
                <span class="text-sm font-bold text-primary"><span data-ai-quota-used>{{ $aiUsed }}</span>/<span data-ai-quota-limit>{{ $aiLimit }}</span> {{ __('messages.quota_status.used_suffix') }}</span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-primary/10" aria-hidden="true">
                <div data-ai-quota-bar class="h-full rounded-full bg-secondary transition-[width] duration-500 ease-out-quart" style="width: {{ $aiPercentage }}%"></div>
            </div>
        </div>

        <div class="rounded-lg border border-primary/10 bg-surface-container-low p-3">
            <div class="mb-2 flex items-center justify-between gap-3">
                <span class="text-[11px] font-label font-bold uppercase tracking-widest text-primary/60">{{ __('messages.quota_status.resumes') }}</span>
                <span class="text-sm font-bold text-primary">
                    {{ $resumeUsed }}/{{ $resumeLimit ?? __('messages.dashboard.unlimited') }} {{ $resumeLimit ? __('messages.quota_status.created_suffix') : '' }}
                </span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-primary/10" aria-hidden="true">
                <div class="h-full rounded-full {{ $isPremium ? 'bg-[#A16207]' : 'bg-primary' }} transition-[width] duration-500 ease-out-quart" style="width: {{ $resumePercentage }}%"></div>
            </div>
        </div>
    </div>
</section>

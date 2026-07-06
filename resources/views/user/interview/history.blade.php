@extends('layouts.user.app')

@section('title', 'Resumify — ' . __('messages.interview.history.page_title'))

@section('content')
<div class="flex-1 flex flex-col min-w-0 overflow-hidden">

    <x-user.page-header title="{{ __('messages.interview.history.heading') }}" backUrl="{{ route('interview.index') }}">
        <a href="{{ route('interview.index') }}"
           class="flex items-center gap-2 px-4 py-2 rounded-xl bg-secondary text-white text-sm font-semibold
                  hover:bg-secondary/90 active:scale-[.98] transition-all">
            <span class="material-symbols-outlined text-[18px]">add</span>
            {{ __('messages.interview.feedback.start_new_interview') }}
        </a>
    </x-user.page-header>

    <div class="flex-1 overflow-y-auto px-4 py-6 md:px-8 custom-scrollbar">
        <div class="max-w-3xl mx-auto space-y-4">

            {{-- Filter Bar --}}
            <form method="GET" action="{{ route('interview.history') }}"
                  class="flex flex-wrap items-center gap-3">

                {{-- Resume filter --}}
                <select name="cv_id"
                        onchange="this.form.submit()"
                        class="text-sm border border-primary/20 rounded-xl px-3 py-2 bg-surface text-primary
                               focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/50">
                    <option value="">{{ __('messages.interview.history.all_resumes') }}</option>
                    @foreach($cvs as $cv)
                        <option value="{{ $cv->id }}" {{ request('cv_id') === $cv->id ? 'selected' : '' }}>
                            {{ $cv->title }}
                        </option>
                    @endforeach
                </select>

                {{-- Sort by --}}
                <div class="flex items-center gap-1 text-sm">
                    <span class="text-primary/50 text-xs">{{ __('messages.interview.history.sort_label') }}</span>
                    @foreach(['date' => __('messages.interview.history.sort_date'), 'score' => __('messages.interview.history.sort_score')] as $key => $label)
                        @php
                            $isActive = $sort === $key;
                            $newOrder = ($isActive && $order === 'desc') ? 'asc' : 'desc';
                        @endphp
                        <a href="{{ route('interview.history', array_merge(request()->query(), ['sort' => $key, 'order' => $isActive ? $newOrder : 'desc'])) }}"
                           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all
                                  {{ $isActive ? 'bg-secondary text-white' : 'bg-surface border border-primary/15 text-primary/60 hover:text-primary hover:border-primary/30' }}">
                            {{ $label }}
                            @if($isActive)
                                {{ $order === 'desc' ? '↓' : '↑' }}
                            @endif
                        </a>
                    @endforeach
                </div>

                @if(request()->hasAny(['cv_id', 'sort', 'order']))
                    <a href="{{ route('interview.history') }}"
                       class="text-xs text-primary/40 hover:text-primary/70 transition-colors underline underline-offset-2">
                        {{ __('messages.interview.history.reset') }}
                    </a>
                @endif
            </form>

            {{-- Session list --}}
            <div class="bg-surface rounded-2xl border border-primary/10 overflow-hidden">
                @forelse($sessions as $session)
                @php
                    $hasFeedback = $session->feedback !== null;
                    $link        = $hasFeedback
                        ? route('interview.feedback', $session)
                        : route('interview.show', $session);
                    $trend       = $trends[$session->id] ?? null;
                @endphp
                <a href="{{ $link }}"
                   class="flex items-center gap-4 px-5 py-4 border-b border-primary/5
                          hover:bg-surface-container-low transition-colors last:border-b-0">

                    {{-- Date --}}
                    <div class="shrink-0 text-center w-12">
                        <p class="text-xs font-bold text-primary leading-tight">
                            {{ $session->started_at->format('d') }}
                        </p>
                        <p class="text-[10px] text-primary/50 uppercase tracking-wide">
                            {{ $session->started_at->format('M Y') }}
                        </p>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-primary truncate">{{ $session->job_target }}</p>
                        <p class="text-xs text-primary/50 truncate mt-0.5">
                            {{ $session->cv->title ?? '—' }}
                            @if($session->status !== 'completed')
                                &nbsp;·&nbsp;
                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ $session->status === 'active' ? 'bg-yellow-100 text-yellow-600' : 'bg-primary/8 text-primary/40' }}">
                                    {{ $session->status }}
                                </span>
                            @endif
                        </p>
                    </div>

                    {{-- Trend --}}
                    <div class="shrink-0 w-14 text-center">
                        @if($hasFeedback && $trend !== null && $trend !== 0)
                            @if($trend > 0)
                                <span class="text-xs font-bold text-green-600">▲ +{{ $trend }}</span>
                            @else
                                <span class="text-xs font-bold text-red-500">▼ {{ $trend }}</span>
                            @endif
                        @elseif($hasFeedback)
                            <span class="text-xs text-primary/25">—</span>
                        @endif
                    </div>

                    {{-- Score --}}
                    <div class="shrink-0">
                        @if($hasFeedback)
                            <x-user.score-circle :score="$session->feedback->overall_score" size="sm" />
                        @else
                            <span class="text-xs text-primary/25 w-10 text-center block">—</span>
                        @endif
                    </div>

                    {{-- Chevron --}}
                    <span class="material-symbols-outlined text-primary/25 text-[20px] shrink-0">chevron_right</span>

                </a>
                @empty
                <div class="py-16 text-center">
                    <span class="material-symbols-outlined text-primary/20 text-[56px] block mb-3">history</span>
                    <p class="text-sm font-semibold text-primary/40 mb-1">{{ __('messages.interview.history.no_sessions_yet') }}</p>
                    <p class="text-xs text-primary/30 mb-5">{{ __('messages.interview.history.complete_first_interview') }}</p>
                    <a href="{{ route('interview.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-secondary text-white
                              font-semibold text-sm hover:bg-secondary/90 active:scale-[.98] transition-all">
                        <span class="material-symbols-outlined text-[16px]">play_arrow</span>
                        {{ __('messages.interview.history.start_first_interview') }}
                    </a>
                </div>
                @endforelse
            </div>

            @if($sessions->hasPages())
                <div class="mt-4 px-1">{{ $sessions->links() }}</div>
            @endif

        </div>
    </div>

</div>
@endsection

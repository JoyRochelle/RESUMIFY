@extends('layouts.user.app')

@section('title', 'Resumify — ' . __('messages.interview.feedback.page_title'))

@section('body_class', 'h-screen flex overflow-hidden')

@section('content')
    @php
        $feedback = $session->feedback;
        $badge = $feedback->readiness_badge;

        $badgeConfig = match ($badge) {
            'ready' => [
                'label' => __('messages.interview.feedback.badge_ready'),
                'bg' => 'bg-green-50',
                'text' => 'text-green-700',
                'border' => 'border-green-200',
                'dot' => 'bg-green-500',
            ],
            'almost_ready' => [
                'label' => __('messages.interview.feedback.badge_almost_ready'),
                'bg' => 'bg-yellow-50',
                'text' => 'text-yellow-700',
                'border' => 'border-yellow-200',
                'dot' => 'bg-yellow-500',
            ],
            default => [
                'label' => __('messages.interview.feedback.badge_needs_practice'),
                'bg' => 'bg-red-50',
                'text' => 'text-red-700',
                'border' => 'border-red-200',
                'dot' => 'bg-red-500',
            ],
        };
    @endphp

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <x-user.page-header title="{{ __('messages.interview.feedback.heading') }}" backUrl="{{ route('interview.index') }}" />

        <div class="flex-1 overflow-y-auto px-4 py-6 md:px-8 custom-scrollbar">
            <div class="max-w-2xl mx-auto space-y-6">

                {{-- Readiness Badge Banner --}}
                <div
                    class="rounded-2xl border px-5 py-4 flex items-center gap-4
                        {{ $badgeConfig['bg'] }} {{ $badgeConfig['border'] }}">
                    <span class="w-3 h-3 rounded-full shrink-0 {{ $badgeConfig['dot'] }}"></span>
                    <div class="min-w-0">
                        <p class="font-semibold text-sm {{ $badgeConfig['text'] }}">{{ $badgeConfig['label'] }}</p>
                        <p class="text-xs {{ $badgeConfig['text'] }} opacity-75 mt-0.5 truncate">
                            {{ __('messages.interview.feedback.position_label', ['job' => $session->job_target]) }}
                        </p>
                    </div>
                </div>

                {{-- Score Circle + Summary --}}
                <div
                    class="bg-surface rounded-2xl border border-primary/10 p-6 flex flex-col sm:flex-row items-center gap-6">
                    <x-user.score-circle :score="$feedback->overall_score" size="lg" />
                    <div class="text-center sm:text-left">
                        <p class="font-semibold text-primary text-base">{{ __('messages.interview.feedback.overall_score') }}</p>
                        <p class="text-primary/60 text-sm mt-1 leading-relaxed">
                            @if ($feedback->overall_score >= 75)
                                {{ __('messages.interview.feedback.score_excellent') }}
                            @elseif($feedback->overall_score >= 50)
                                {{ __('messages.interview.feedback.score_good') }}
                            @else
                                {{ __('messages.interview.feedback.score_needs_practice') }}
                            @endif
                        </p>
                        <p class="text-primary/40 text-xs mt-2">
                            {{ $session->ended_at?->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>

                {{-- Missing Keywords --}}
                @if (!empty($feedback->missing_keywords))
                    <div class="bg-surface rounded-2xl border border-primary/10 p-5">
                        <p class="font-semibold text-primary text-sm mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-red-400">label_off</span>
                            {{ __('messages.ats.results.missing_keywords') }}
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($feedback->missing_keywords as $keyword)
                                <x-user.keyword-tag variant="danger">{{ $keyword }}</x-user.keyword-tag>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- STAR Score Cards --}}
                @if (!empty($feedback->question_scores))
                    <div class="space-y-4">
                        <p class="font-semibold text-primary text-sm px-1">{{ __('messages.interview.feedback.per_question_breakdown') }}</p>

                        @foreach ($feedback->question_scores as $i => $qs)
                            @php
                                $starScores = $qs['star_scores'] ?? [
                                    'situation' => 0,
                                    'task' => 0,
                                    'action' => 0,
                                    'result' => 0,
                                ];
                                $starLabels = [
                                    'situation' => __('messages.interview.feedback.star_situation'),
                                    'task' => __('messages.interview.feedback.star_task'),
                                    'action' => __('messages.interview.feedback.star_action'),
                                    'result' => __('messages.interview.feedback.star_result'),
                                ];
                            @endphp
                            <div class="bg-surface rounded-2xl border border-primary/10 p-5 space-y-4">

                                {{-- Question --}}
                                <div class="flex items-start gap-3">
                                    <span
                                        class="shrink-0 w-6 h-6 rounded-full bg-secondary/10 text-secondary
                                     text-xs font-bold flex items-center justify-center mt-0.5">
                                        {{ $i + 1 }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="font-medium text-primary text-sm leading-relaxed">
                                            {{ $qs['question'] ?? '' }}</p>
                                        @if (!empty($qs['answer_summary']))
                                            <p class="text-primary/50 text-xs mt-1 leading-relaxed italic">
                                                {{ $qs['answer_summary'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                {{-- STAR Bars --}}
                                <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                                    @foreach ($starLabels as $key => $label)
                                        @php $val = (int)($starScores[$key] ?? 0); @endphp
                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <span
                                                    class="text-xs font-medium text-primary/70">{{ $label }}</span>
                                                <span class="text-xs font-bold text-primary">{{ $val }}</span>
                                            </div>
                                            <div class="w-full h-1.5 bg-primary/8 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full transition-all
                                            {{ $val >= 75 ? 'bg-secondary' : ($val >= 50 ? 'bg-yellow-400' : 'bg-red-400') }}"
                                                    style="width: {{ $val }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Feedback text --}}
                                @if (!empty($qs['feedback']))
                                    <div class="rounded-xl bg-surface-container-low px-4 py-3 border border-primary/5">
                                        <p class="text-primary/70 text-xs leading-relaxed">{{ $qs['feedback'] }}</p>
                                    </div>
                                @endif

                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <a href="{{ route('interview.index') }}"
                        class="flex-1 flex items-center justify-center gap-2 px-5 py-3 rounded-xl
                          bg-secondary text-white font-semibold text-sm
                          hover:bg-secondary/90 active:scale-[.98] transition-all">
                        <span class="material-symbols-outlined text-[18px]">play_arrow</span>
                        {{ __('messages.interview.feedback.start_new_interview') }}
                    </a>
                    <a href="{{ route('interview.show', $session) }}"
                        class="flex-1 flex items-center justify-center gap-2 px-5 py-3 rounded-xl
                          border border-primary/20 text-primary/70 font-medium text-sm
                          hover:border-primary/40 hover:text-primary transition-all">
                        <span class="material-symbols-outlined text-[18px]">chat</span>
                        {{ __('messages.interview.feedback.view_conversation') }}
                    </a>
                </div>

                {{-- Upgrade prompt for basic users --}}
                @if (!auth()->user()->isPremium() && !auth()->user()->isAdmin())
                    <div class="bg-secondary/5 border border-secondary/20 rounded-2xl p-5 text-center space-y-3 pb-8">
                        <p class="font-semibold text-primary text-sm">{{ __('messages.interview.feedback.want_more_practice') }}</p>
                        <p class="text-primary/60 text-xs leading-relaxed">
                            {{ __('messages.interview.feedback.upgrade_unlimited_desc') }}
                        </p>
                        <a href="{{ route('user.upgrade-quota') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                          bg-secondary text-white font-semibold text-sm
                          hover:bg-secondary/90 active:scale-[.98] transition">
                            <span class="material-symbols-outlined text-[16px]">workspace_premium</span>
                            {{ __('messages.interview.index.upgrade_to_premium') }}
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

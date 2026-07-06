@extends('layouts.user.app')

@section('title', 'Resumify — ' . __('messages.interview.index.page_title'))

@section('content')
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <x-user.page-header title="{{ __('messages.interview.index.heading') }}" backUrl="{{ route('dashboard') }}" />

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="mx-4 mt-4 px-4 py-3 bg-secondary/10 border border-secondary/30 text-secondary rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex-1 overflow-y-auto px-4 py-6 md:px-8">
            <div class="max-w-xl mx-auto">

                {{-- Intro card --}}
                <div class="bg-surface-container-low rounded-2xl p-5 mb-6 border border-primary/10">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-secondary/15 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-secondary text-[20px]">smart_toy</span>
                        </div>
                        <div>
                            <p class="font-semibold text-primary text-sm">{{ __('messages.interview.index.greeting') }}</p>
                            <p class="text-primary/60 text-sm mt-1">
                                {{ __('messages.interview.index.intro') }}
                            </p>
                        </div>
                    </div>
                </div>

                @if ($trialUsed)
                    {{-- Upgrade wall --}}
                    <div class="bg-surface rounded-2xl border border-primary/10 p-8 text-center space-y-4">
                        <span class="material-symbols-outlined text-primary/30 text-[48px] block">lock</span>
                        <p class="font-semibold text-primary">{{ __('messages.interview.index.trial_used_title') }}</p>
                        <p class="text-primary/60 text-sm leading-relaxed">
                            {!! __('messages.interview.index.trial_used_body') !!}
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center pt-2">
                            <a href="{{ route('user.upgrade-quota') }}"
                                class="px-5 py-2.5 rounded-xl bg-secondary text-white font-semibold text-sm
                              hover:bg-secondary/90 active:scale-[.98] transition">
                                {{ __('messages.interview.index.upgrade_to_premium') }}
                            </a>
                            @if ($lastSession = auth()->user()->interviewSessions()->latest()->first())
                                <a href="{{ route('interview.show', $lastSession) }}"
                                    class="px-5 py-2.5 rounded-xl border border-primary/20 text-primary/70
                              font-medium text-sm hover:border-primary/40 transition">
                                    {{ __('messages.interview.index.view_last_session') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Form --}}
                    <div class="bg-surface rounded-2xl border border-primary/10 p-6 space-y-5">

                        {{-- Quota bar --}}
                        @php $user = auth()->user(); @endphp
                        <div class="flex items-center justify-between text-xs text-primary/50 pb-1">
                            <span>{{ __('messages.interview.index.ai_credits_remaining') }}</span>
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-1.5 bg-primary/10 rounded-full overflow-hidden">
                                    <div class="h-full bg-secondary rounded-full transition-all"
                                        style="width: {{ $user->getQuotaPercentage() }}%"></div>
                                </div>
                                <span class="font-bold text-primary">
                                    {{ $user->getQuotaRemaining() }}/{{ $user->getQuotaLimit() }}
                                </span>
                            </div>
                        </div>

                        {{-- Resume selector --}}
                        <div class="space-y-1.5">
                            <label for="cv-select" class="text-sm font-medium text-primary">
                                {{ __('messages.interview.index.select_cv') }}
                            </label>
                            @if ($cvs->isEmpty())
                                <p class="text-sm text-primary/50 py-2">
                                    {{ __('messages.interview.index.no_cv_yet') }}
                                    <a href="{{ route('dashboard') }}" class="text-secondary underline">{{ __('messages.interview.index.create_cv_first') }}</a>.
                                </p>
                            @else
                                <select id="cv-select"
                                    class="w-full px-4 py-2.5 rounded-xl border border-primary/20 bg-surface text-primary text-sm
                                       focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/50 transition">
                                    @foreach ($cvs as $cv)
                                        @php
                                            $targetJob = $cv->sections->where('type', 'target_job')->first();
                                            $prefill = $targetJob->content['job_title'] ?? '';
                                        @endphp
                                        <option value="{{ $cv->id }}" data-job-target="{{ $prefill }}">
                                            {{ $cv->title }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        {{-- Job target --}}
                        <div class="space-y-1.5">
                            <label for="job-target" class="text-sm font-medium text-primary">
                                {{ __('messages.interview.index.position_applied_for') }}
                            </label>
                            <input id="job-target" type="text" maxlength="200"
                                placeholder="{{ __('messages.interview.index.position_placeholder') }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-primary/20 bg-surface text-primary text-sm
                                  placeholder:text-primary/30 focus:outline-none focus:ring-2 focus:ring-secondary/30
                                  focus:border-secondary/50 transition" />
                        </div>

                        {{-- Trial info banner for basic users --}}
                        @if (!$user->isPremium() && !$user->isAdmin())
                            <div
                                class="flex items-center gap-2 px-4 py-2.5 rounded-xl
                            bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs">
                                <span class="material-symbols-outlined text-[16px] shrink-0">info</span>
                                <span>{{ __('messages.interview.index.trial_banner_before') }} <strong>{{ __('messages.interview.index.trial_banner_bold') }}</strong> {{ __('messages.interview.index.trial_banner_after') }}</span>
                            </div>
                        @endif

                        {{-- Error banner --}}
                        <div id="error-banner"
                            class="hidden px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm"></div>

                        {{-- Submit --}}
                        <button id="start-btn" onclick="startInterview()"
                            class="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-xl
                               bg-secondary text-white font-semibold text-sm
                               hover:bg-secondary/90 active:scale-[.98] transition-all duration-150
                               disabled:opacity-50 disabled:cursor-not-allowed">
                            <span id="start-icon" class="material-symbols-outlined text-[18px]">play_arrow</span>
                            <span id="start-spinner" style="display:none" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                            <span id="start-label">{{ __('messages.interview.index.start_interview') }}</span>
                        </button>

                    </div>
                @endif

                {{-- Recent History Section --}}
                @if(isset($recentSessions) && $recentSessions->isNotEmpty())
                    <div class="mt-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-primary">{{ __('messages.interview.index.recent_interviews') }}</h3>
                            <a href="{{ route('interview.history') }}" class="text-sm font-medium text-secondary hover:underline">
                                {{ __('messages.interview.index.view_full_history') }}
                            </a>
                        </div>
                        <div class="space-y-3">
                            @foreach($recentSessions as $session)
                                <a href="{{ route('interview.show', $session) }}"
                                   class="block bg-surface-container-low rounded-xl p-4 border border-primary/10 hover:border-secondary/30 hover:shadow-sm transition group">
                                    <div class="flex justify-between items-start">
                                        <div class="min-w-0 flex-1 pr-4">
                                            <p class="font-semibold text-primary text-sm truncate group-hover:text-secondary transition-colors">
                                                {{ $session->job_target }}
                                            </p>
                                            <p class="text-xs text-primary/60 mt-1 truncate">
                                                {{ __('messages.interview.index.cv_prefix') }} {{ $session->cv->title ?? __('messages.interview.index.deleted_cv') }}
                                            </p>
                                        </div>
                                        <div class="text-right shrink-0">
                                            @if($session->status === 'completed' && $session->feedback)
                                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                                    {{ __('messages.interview.index.score_label', ['score' => $session->feedback->overall_score ?? 0]) }}
                                                </span>
                                            @elseif($session->status === 'active')
                                                <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                                    {{ __('messages.interview.index.in_progress') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 bg-primary/10 text-primary/60 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                                    {{ __('messages.interview.index.ended') }}
                                                </span>
                                            @endif
                                            <p class="text-[10px] text-primary/40 mt-2 font-medium">
                                                {{ $session->started_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif


            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const cvSelect = document.getElementById('cv-select');
            const jobTargetInput = document.getElementById('job-target');
            const startBtn = document.getElementById('start-btn');
            const startIcon = document.getElementById('start-icon');
            const startSpinner = document.getElementById('start-spinner');
            const startLabel = document.getElementById('start-label');
            const errorBanner = document.getElementById('error-banner');

            // Pre-fill job target from selected CV's stored job title
            if (cvSelect) {
                const prefill = () => {
                    const opt = cvSelect.selectedOptions[0];
                    if (opt && opt.dataset.jobTarget) {
                        jobTargetInput.value = opt.dataset.jobTarget;
                    }
                };
                cvSelect.addEventListener('change', prefill);
                prefill();
            }

            function setLoading(on) {
                startBtn.disabled = on;
                startIcon.style.display = on ? 'none' : 'inline-block';
                startSpinner.style.display = on ? 'inline-block' : 'none';
                startLabel.textContent = on ? @json(__('messages.interview.index.starting')) : @json(__('messages.interview.index.start_interview'));
            }

            function showError(msg) {
                errorBanner.textContent = msg;
                errorBanner.classList.remove('hidden');
            }

            async function startInterview() {
                errorBanner.classList.add('hidden');

                const cvId = cvSelect ? cvSelect.value : null;
                const jobTarget = jobTargetInput.value.trim();

                if (!cvId) {
                    showError(@json(__('messages.interview.index.select_cv_first_error')));
                    return;
                }
                if (!jobTarget) {
                    showError(@json(__('messages.interview.index.enter_position_error')));
                    return;
                }

                setLoading(true);

                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 60000);

                try {
                    const res = await fetch('{{ route('interview.start') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            cv_id: cvId,
                            job_target: jobTarget
                        }),
                        signal: controller.signal,
                    });

                    clearTimeout(timeoutId);
                    const data = await res.json();

                    if (data.success) {
                        window.location.href = `/interview/sessions/${data.session_id}`;
                    } else {
                        showError(data.message || @json(__('messages.interview.index.start_failed_error')));
                        setLoading(false);
                    }
                } catch (err) {
                    clearTimeout(timeoutId);
                    showError(err.name === 'AbortError' ?
                        @json(__('messages.interview.index.timeout_error')) :
                        @json(__('messages.interview.index.network_error')));
                    setLoading(false);
                }
            }
        </script>
    @endpush
@endsection

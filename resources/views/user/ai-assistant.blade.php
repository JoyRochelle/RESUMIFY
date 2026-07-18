@extends('layouts.user.app')

@section('title', __('messages.ats.page_title') . ' - Resumify')
@section('content')
    @php
        $user = auth()->user();
    @endphp

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Page Header --}}
        <x-user.page-header title="{{ __('messages.ats.page_title') }}" backUrl="{{ route('dashboard') }}">
            <button id="ats-instructions-btn" type="button"
                class="flex items-center gap-2 text-sm px-4 py-2 rounded-lg border border-primary/15 text-primary/70 hover:text-primary hover:border-primary/30 transition-all duration-200">
                <span class="material-symbols-outlined text-[16px]">info</span>
                {{ __('messages.ats.how_it_works') }}
            </button>
        </x-user.page-header>

        <div class="px-4 lg:px-6 py-3 bg-surface-container-low border-b border-primary/10">
            <x-user.quota-status :user="$user" compact class="grid gap-3" />
        </div>

        {{-- Mobile tab bar (hidden on lg+) --}}
        <div class="flex lg:hidden border-b border-primary/10 bg-surface-container-low shrink-0" role="tablist" aria-label="{{ __('messages.ats.sections_aria_label') }}">
            <button id="ats-tab-setup" type="button" onclick="switchAtsTab('setup')"
                role="tab"
                aria-selected="true"
                aria-controls="ats-panel-setup"
                class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-bold text-primary border-b-2 border-primary transition-colors">
                <span class="material-symbols-outlined text-[18px]">tune</span> {{ __('messages.ats.tabs.setup') }}
            </button>
            <button id="ats-tab-results" type="button" onclick="switchAtsTab('results')"
                role="tab"
                aria-selected="false"
                aria-controls="ats-panel-results"
                class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-bold text-primary/70 border-b-2 border-transparent transition-colors">
                <span class="material-symbols-outlined text-[18px]">analytics</span> {{ __('messages.ats.tabs.results') }}
            </button>
        </div>

        <div class="flex-1 flex flex-col lg:flex-row overflow-y-auto lg:overflow-hidden pb-20 lg:pb-0">

            {{-- ════════════════ LEFT PANEL — INPUTS ════════════════ --}}
            @include('user.ai.ats.setup-panel', ['cvs' => $cvs ?? collect(), 'history' => $history ?? collect(), 'user' => $user])

            {{-- ════════════════ RIGHT PANEL — RESULTS ════════════════ --}}
            @include('user.ai.ats.results-panel')
        </div>
    </div>

    {{-- ── How It Works modal ─────────────────────────────────────── --}}
    <div id="instructions-modal"
        class="fixed inset-0 bg-surface/80 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="instructions-modal-title"
        aria-describedby="instructions-modal-description">
        <div class="bg-tertiary w-full max-w-lg rounded-2xl shadow-2xl border border-primary/10 transform scale-95 transition-transform duration-300 overflow-hidden"
            id="instructions-content">
            <div class="p-6 border-b border-primary/10 flex justify-between items-center bg-surface-container-low">
                <h3 id="instructions-modal-title" class="font-headline text-xl font-bold text-primary">
                    {{ __('messages.ats.instructions_modal.title') }}
                </h3>
                <button type="button" onclick="closeInstructions()"
                    aria-label="{{ __('messages.ats.instructions_modal.close_aria') }}"
                    class="text-primary/50 hover:text-primary material-symbols-outlined rounded-full p-2 hover:bg-primary/5 transition-colors focus:outline-none focus:ring-2 focus:ring-secondary/40">close</button>
            </div>
            <div id="instructions-modal-description" class="p-6 space-y-4 text-sm text-primary/80 leading-relaxed">
                <p>{{ __('messages.ats.instructions_modal.intro') }}
                </p>
                <ul class="space-y-3">
                    <li class="leading-relaxed">
                        <span><strong class="text-primary">{{ __('messages.ats.instructions_modal.keyword_match_label') }}</strong> {{ __('messages.ats.instructions_modal.keyword_match_desc') }}</span>
                    </li>
                    <li class="leading-relaxed">
                        <span><strong class="text-primary">{{ __('messages.ats.instructions_modal.action_verbs_label') }}</strong> {{ __('messages.ats.instructions_modal.action_verbs_desc') }}</span>
                    </li>
                    <li class="leading-relaxed">
                        <span><strong class="text-primary">{{ __('messages.ats.instructions_modal.quantification_label') }}</strong> {{ __('messages.ats.instructions_modal.quantification_desc') }}</span>
                    </li>
                    <li class="leading-relaxed">
                        <span><strong class="text-primary">{!! __('messages.ats.instructions_modal.length_format_label') !!}</strong> {!! __('messages.ats.instructions_modal.length_format_desc') !!}</span>
                    </li>
                </ul>
                <p class="text-primary/50 text-xs pt-2">{{ __('messages.ats.instructions_modal.tip') }}</p>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.45s ease both;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 0.8s linear infinite;
        }

        /* Score circle animation */
        #score-circle-container circle.progress-ring {
            transition: stroke-dashoffset 1s ease;
        }
    </style>

        <script>
        window.atsConfig = {
            analyzeUrl: '{{ route("ats.analyze") }}',
            historyUrl: '{{ url("ats/history") }}',
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
    @vite('resources/js/features/ats-analyzer.js')
@endsection
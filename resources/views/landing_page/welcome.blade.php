@extends('layouts.landing_page.app')

@section('title', 'Resumify | Write Your Success Story')

@section('content')
    {{-- ======================== HERO SECTION ======================== --}}
    <section class="min-h-[85vh] flex flex-col md:flex-row bg-surface">

        {{-- Hero Left: Content --}}
        <div class="w-full md:w-[40%] flex items-start justify-center md:justify-start px-4 sm:px-8 md:px-16 py-12 md:py-20">
            <div class="max-w-md w-full text-center md:text-left">
                <h1
                    class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-headline text-primary leading-tight tracking-tight mb-6 md:mb-8 animate-fade-up-blur">
                    {{ __('messages.landing.welcome.hero.title') }}
                </h1>
                <p class="text-base sm:text-lg md:text-xl text-outline mb-8 md:mb-10 leading-relaxed font-body animate-fade-up" style="animation-delay: 120ms">
                    {{ __('messages.landing.welcome.hero.subtitle') }}
                </p>
                <div class="flex flex-col sm:flex-row items-center md:items-start justify-center md:justify-start gap-3 sm:gap-4 animate-fade-up" style="animation-delay: 240ms">
                    <x-landing_page.button variant="primary" icon="arrow_forward" href="{{ route('register') }}" class="btn-shimmer">
                        {{ __('messages.landing.welcome.hero.cta') }}
                    </x-landing_page.button>
                    <x-landing_page.button variant="outline" href="{{ route('templates') }}">
                        {{ __('messages.landing.welcome.hero.cta_secondary') }}
                    </x-landing_page.button>
                </div>
                <p class="mt-4 text-sm text-outline font-body animate-fade-up" style="animation-delay: 320ms">
                    {{ __('messages.landing.welcome.hero.reassurance') }}
                </p>
            </div>
        </div>

        {{-- Hero Right: Visual Preview --}}
        <div
            class="w-full md:w-[60%] bg-surface-container-low relative flex items-start justify-center px-4 sm:px-8 md:px-16 py-10 md:py-20">
            {{-- Decorative drifting dot grid behind the preview stack --}}
            <div class="absolute inset-0 dot-pattern" style="--dot-color: rgb(79 59 47 / 0.14)" aria-hidden="true"></div>
            <div class="relative w-full max-w-sm md:max-w-md">

                {{-- Resume Card Preview: the centerpiece "raised page" --}}
                <div class="bg-tertiary p-6 md:p-8 rounded-lg shadow-2xl relative z-10 border border-primary/10 animate-fade-up" style="animation-delay: 100ms">
                    <div class="flex justify-between items-start mb-6 md:mb-8">
                        <div>
                            <h2 class="text-xl md:text-2xl font-headline text-primary tracking-tighter leading-none">Theofrolic
                            </h2>
                            <p class="text-secondary font-bold font-body tracking-widest uppercase text-[10px] mt-2">Senior
                                Product Designer</p>
                        </div>
                        <div class="text-right text-[10px] text-outline font-body leading-relaxed">
                            <p>Jakarta, Indonesia</p>
                            <p>theofrolic@resumify.ai</p>
                        </div>
                    </div>

                    <div class="space-y-4 md:space-y-6">
                        <div class="h-px bg-primary/10 w-full mb-2"></div>
                        <div>
                            <h3 class="text-[10px] font-bold text-primary font-body mb-3 md:mb-4 uppercase tracking-widest">
                                {{ __('messages.landing.welcome.preview.work_experience') }}</h3>
                            <div class="space-y-4 md:space-y-5">
                                <div>
                                    <div class="flex justify-between items-start mb-1">
                                        <div>
                                            <p class="text-xs font-bold text-primary font-body">Lead Designer</p>
                                            <p class="text-[10px] text-outline font-body">TechNova Solutions</p>
                                        </div>
                                        <span class="text-[10px] text-outline font-body whitespace-nowrap">2021 –
                                            Present</span>
                                    </div>
                                </div>

                                <div class="bg-secondary/10 border border-secondary/20 p-4 md:p-5 rounded-lg">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="material-symbols-outlined text-[16px] text-secondary"
                                            style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                                        <span class="text-[10px] font-bold text-secondary uppercase tracking-wider">{{ __('messages.landing.welcome.preview.ai_optimized') }}</span>
                                    </div>
                                    <p class="text-[11px] text-primary/80 leading-loose font-body">
                                        {{ __('messages.landing.welcome.preview.ai_optimized_desc') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Desktop satellites: in flow, side by side, tucked ~40px under the document's bottom edge --}}
                <div class="hidden md:flex relative z-20 -mt-10 items-start justify-between gap-6">
                    {{-- INPUT EDITOR --}}
                    <div class="-ml-10 w-64 shrink-0 bg-primary p-5 rounded-lg shadow-2xl -rotate-1 animate-fade-up" style="animation-delay: 250ms">
                        <p class="text-[9px] text-white/50 mb-4 tracking-widest uppercase font-bold">{{ __('messages.landing.welcome.preview.input_editor') }}</p>
                        <div class="space-y-4">
                            <div>
                                <label class="text-[8px] text-white/50 uppercase tracking-widest block mb-1">{{ __('messages.landing.welcome.preview.name_label') }}</label>
                                <div class="border-b border-white/15 pb-1">
                                    <span class="text-white text-[12px]">Theofrolic</span>
                                </div>
                            </div>
                            <div>
                                <label class="text-[8px] text-white/70 uppercase tracking-widest block mb-1">{{ __('messages.landing.welcome.preview.description_label') }}</label>
                                {{-- The "focused" field: emerald underline + live caret, mirroring the real editor's focus state --}}
                                <div class="border-b-2 border-secondary pb-1">
                                    <span class="text-white/90 text-[11px] leading-relaxed">{{ __('messages.landing.welcome.preview.description_value') }}</span><span class="ml-0.5 inline-block h-3 w-0.5 translate-y-0.5 bg-white/80 animate-pulse" aria-hidden="true"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ATS Match Score --}}
                    <div class="-mr-10 mt-4 flex min-w-0 flex-1 items-center gap-4 bg-tertiary p-5 rounded-lg shadow-xl border border-primary/10 rotate-1 animate-fade-up" style="animation-delay: 400ms">
                        <x-user.score-circle :score="92" size="sm" />
                        <div class="min-w-0">
                            <p class="text-[9px] text-primary/60 tracking-widest uppercase font-bold">{{ __('messages.landing.welcome.preview.ats_match_score') }}</p>
                            <p class="mt-1 font-headline text-lg font-bold leading-tight text-primary">{{ __('messages.landing.welcome.preview.high_match') }}</p>
                            <p class="text-[10px] text-primary/60">{{ __('messages.landing.welcome.preview.resume_quality') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Mobile-only: ATS Score badge (inline, not absolute) --}}
                <div class="md:hidden mt-4 bg-tertiary p-4 rounded-lg shadow-md border border-primary/10 flex items-center gap-4 animate-fade-up" style="animation-delay: 250ms">
                    <x-user.score-circle :score="92" size="sm" />
                    <div class="flex-1 text-right">
                        <p class="text-[10px] text-primary/60">{{ __('messages.landing.welcome.preview.resume_quality') }}</p>
                        <p class="text-lg font-headline font-bold text-primary">{{ __('messages.landing.welcome.preview.high_match') }}</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ======================== FEATURES SECTION ======================== --}}
    <section class="py-16 sm:py-24 md:py-32 px-4 sm:px-8">
        <div class="max-w-2xl mx-auto text-center mb-12 md:mb-16 animate-scroll-reveal">
            <h2 class="text-3xl sm:text-4xl font-headline font-bold text-primary tracking-tight mb-4 leading-tight">
                {{ __('messages.landing.welcome.features.title') }}
            </h2>
            <p class="text-base sm:text-lg text-outline font-body leading-relaxed">
                {{ __('messages.landing.welcome.features.subtitle') }}
            </p>
        </div>
        <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-8">
            <x-landing_page.feature-card title="{{ __('messages.landing.welcome.features.ai_bullet.title') }}" icon="auto_awesome" :filled="true">
                {{ __('messages.landing.welcome.features.ai_bullet.desc') }}
            </x-landing_page.feature-card>

            <x-landing_page.feature-card title="{{ __('messages.landing.welcome.features.ats_scanner.title') }}" icon="analytics" class="animate-scroll-reveal-2">
                {{ __('messages.landing.welcome.features.ats_scanner.desc') }}
            </x-landing_page.feature-card>

            <x-landing_page.feature-card title="{{ __('messages.landing.welcome.features.premium_templates.title') }}" icon="article" class="animate-scroll-reveal-3">
                {{ __('messages.landing.welcome.features.premium_templates.desc') }}
            </x-landing_page.feature-card>
        </div>
    </section>

    {{-- ======================== HOW IT WORKS ======================== --}}
    <section class="py-16 sm:py-24 px-4 sm:px-8 bg-surface-container-low/60">
        <div class="max-w-2xl mx-auto text-center mb-12 md:mb-16 animate-scroll-reveal">
            <h2 class="text-3xl sm:text-4xl font-headline font-bold text-primary tracking-tight mb-4 leading-tight">
                {{ __('messages.landing.welcome.how.title') }}
            </h2>
            <p class="text-base sm:text-lg text-outline font-body leading-relaxed">
                {{ __('messages.landing.welcome.how.subtitle') }}
            </p>
        </div>

        {{-- Anime.js timeline choreographs this: marker 1 pops, the line draws
             rightward, markers 2 and 3 pop as it reaches them. Without JS the
             section is simply static and fully visible. --}}
        <div class="relative max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-8" data-anime-steps>
            {{-- Connector line between step markers (desktop) --}}
            <div class="hidden md:block absolute top-[22px] left-[17%] right-[17%] h-px bg-primary/10 origin-left" data-anime-steps-line aria-hidden="true"></div>

            @foreach (['build', 'scan', 'rehearse'] as $i => $step)
                <div class="relative text-center md:px-4" data-anime-step>
                    {{-- The last step is the payoff — it carries the section's one emerald accent --}}
                    <div data-anime-step-marker class="relative z-10 mx-auto mb-5 flex h-11 w-11 items-center justify-center rounded-full border font-headline text-lg font-bold shadow-sm {{ $loop->last ? 'border-secondary/30 bg-secondary/10 text-secondary' : 'border-primary/15 bg-tertiary text-primary' }}">
                        {{ $i + 1 }}
                    </div>
                    <h3 data-anime-step-text class="text-xl font-headline font-bold text-primary mb-2 tracking-tight">
                        {{ __('messages.landing.welcome.how.steps.' . $step . '.title') }}
                    </h3>
                    <p data-anime-step-text class="mx-auto max-w-[36ch] text-sm md:text-base text-outline font-body leading-relaxed">
                        {{ __('messages.landing.welcome.how.steps.' . $step . '.desc') }}
                    </p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ======================== CALL TO ACTION ======================== --}}
    <section class="py-12 sm:py-16 md:py-24 px-4 sm:px-8">
        <div
            class="max-w-5xl mx-auto bg-primary rounded-2xl relative min-h-[320px] md:min-h-[400px] flex items-center p-8 sm:p-12 md:p-20 overflow-hidden">
            {{-- Decorative drifting dot grid on the dark panel --}}
            <div class="absolute inset-0 dot-pattern" aria-hidden="true"></div>
            <div class="relative z-10 max-w-2xl text-left">
                <h2
                    class="text-3xl sm:text-4xl md:text-5xl text-white mb-4 md:mb-6 leading-tight font-headline tracking-tight">
                    {{ __('messages.landing.welcome.cta.title') }}</h2>
                <p class="text-base md:text-lg text-white/80 mb-8 md:mb-10 leading-relaxed font-body">
                    {{ __('messages.landing.welcome.cta.subtitle') }}
                </p>
                <x-landing_page.button variant="light" href="{{ route('register') }}" class="btn-shimmer btn-shimmer-dark">
                    {{ __('messages.landing.welcome.cta.button') }}
                </x-landing_page.button>
            </div>
        </div>
    </section>
@endsection

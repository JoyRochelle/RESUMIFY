@extends('layouts.landing_page.app')

@section('title', 'Resumify | Write Your Success Story')

@section('content')
    {{-- ======================== HERO SECTION ======================== --}}
    <section class="min-h-[85vh] flex flex-col md:flex-row bg-surface">

        {{-- Hero Left: Content --}}
        <div class="w-full md:w-[40%] flex items-start justify-center md:justify-start px-4 sm:px-8 md:px-16 py-12 md:py-20">
            <div class="max-w-md w-full text-center md:text-left">
                <h1
                    class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-headline text-primary leading-tight tracking-tighter mb-6 md:mb-8">
                    {{ __('messages.landing.welcome.hero.title') }} <span class="inline-block text-3xl sm:text-4xl md:text-5xl">✨</span>
                </h1>
                <p class="text-base sm:text-lg md:text-xl text-outline mb-8 md:mb-10 leading-relaxed font-body">
                    {{ __('messages.landing.welcome.hero.subtitle') }}
                </p>
                <div class="flex justify-center md:justify-start">
                    <x-landing_page.button variant="primary" icon="arrow_forward" href="{{ route('register') }}">
                        {{ __('messages.landing.welcome.hero.cta') }}
                    </x-landing_page.button>
                </div>
            </div>
        </div>

        {{-- Hero Right: Visual Preview --}}
        <div
            class="w-full md:w-[60%] bg-surface-container-low relative flex items-start justify-center px-4 sm:px-8 md:px-16 py-10 md:py-20">
            <div class="relative w-full max-w-sm md:max-w-md">

                {{-- Resume Card Preview --}}
                <div class="bg-white p-6 md:p-8 rounded-lg shadow-2xl relative z-10 border border-primary/5">
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

                {{-- ATS Match Score — Desktop only (absolute positioned) --}}
                <div
                    class="hidden md:block absolute -right-10 top-[360px] bg-white/95 backdrop-blur-xl p-5 rounded-md shadow-xl w-56 z-20 border border-primary/10 transform rotate-1">
                    <p class="text-[9px] text-outline mb-3 tracking-widest uppercase font-bold">{{ __('messages.landing.welcome.preview.ats_match_score') }}</p>
                    <div class="flex items-end gap-1.5 h-10 mb-3">
                        <div class="w-full bg-red-200 h-1/4 rounded-sm"></div>
                        <div class="w-full bg-red-300 h-2/5 rounded-sm"></div>
                        <div class="w-full bg-secondary/30 h-3/5 rounded-sm"></div>
                        <div class="w-full bg-secondary/70 h-4/5 rounded-sm"></div>
                        <div class="w-full bg-secondary h-full rounded-sm"></div>
                    </div>
                    <div class="flex justify-between items-center text-[10px] font-bold">
                        <span class="text-outline">{{ __('messages.landing.welcome.preview.low') }}</span>
                        <span class="text-secondary">{{ __('messages.landing.welcome.preview.high') }}</span>
                    </div>
                </div>

                {{-- INPUT EDITOR — Desktop only (absolute positioned) --}}
                <div class="hidden md:block absolute top-[380px] -left-16 bg-primary p-7 rounded-md shadow-2xl w-72 z-30">
                    <p class="text-[9px] text-white/40 mb-5 tracking-widest uppercase font-bold">{{ __('messages.landing.welcome.preview.input_editor') }}</p>
                    <div class="space-y-5">
                        <div>
                            <label class="text-[8px] text-white/40 uppercase tracking-widest block mb-1">{{ __('messages.landing.welcome.preview.name_label') }}</label>
                            <div class="border-b border-white/10 pb-1">
                                <span class="text-white text-[12px]">Theofrolic</span>
                            </div>
                        </div>
                        <div>
                            <label class="text-[8px] text-white/40 uppercase tracking-widest block mb-1">{{ __('messages.landing.welcome.preview.company_label') }}</label>
                            <div class="border-b border-white/10 pb-1">
                                <span class="text-white text-[12px]">Resumify</span>
                            </div>
                        </div>
                        <div>
                            <label class="text-[8px] text-white/40 uppercase tracking-widest block mb-1">{{ __('messages.landing.welcome.preview.description_label') }}</label>
                            <div class="border-b border-white/10 pb-1">
                                <span class="text-white/80 text-[11px] leading-relaxed">{{ __('messages.landing.welcome.preview.description_value') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mobile-only: ATS Score badge (inline, not absolute) --}}
                <div
                    class="md:hidden mt-4 bg-white/95 p-4 rounded-lg shadow-md border border-primary/10 flex items-center gap-4">
                    <div>
                        <p class="text-[9px] text-outline mb-2 tracking-widest uppercase font-bold">{{ __('messages.landing.welcome.preview.ats_match_score') }}</p>
                        <div class="flex items-end gap-1 h-8">
                            <div class="w-4 bg-red-200 h-1/4 rounded-sm"></div>
                            <div class="w-4 bg-red-300 h-2/5 rounded-sm"></div>
                            <div class="w-4 bg-secondary/30 h-3/5 rounded-sm"></div>
                            <div class="w-4 bg-secondary/70 h-4/5 rounded-sm"></div>
                            <div class="w-4 bg-secondary h-full rounded-sm"></div>
                        </div>
                    </div>
                    <div class="flex-1 text-right">
                        <p class="text-[10px] text-outline">{{ __('messages.landing.welcome.preview.resume_quality') }}</p>
                        <p class="text-lg font-headline font-bold text-secondary">{{ __('messages.landing.welcome.preview.high_match') }}</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ======================== FEATURES SECTION ======================== --}}
    <section class="py-16 sm:py-24 md:py-32 px-4 sm:px-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-8">
            <x-landing_page.feature-card title="{{ __('messages.landing.welcome.features.ai_bullet.title') }}" icon="auto_awesome" :filled="true">
                {{ __('messages.landing.welcome.features.ai_bullet.desc') }}
            </x-landing_page.feature-card>

            <x-landing_page.feature-card title="{{ __('messages.landing.welcome.features.ats_scanner.title') }}" icon="analytics">
                {{ __('messages.landing.welcome.features.ats_scanner.desc') }}
            </x-landing_page.feature-card>

            <x-landing_page.feature-card title="{{ __('messages.landing.welcome.features.premium_templates.title') }}" icon="article">
                {{ __('messages.landing.welcome.features.premium_templates.desc') }}
            </x-landing_page.feature-card>
        </div>
    </section>

    {{-- ======================== CALL TO ACTION ======================== --}}
    <section class="py-12 sm:py-16 md:py-24 px-4 sm:px-8">
        <div
            class="max-w-5xl mx-auto bg-primary rounded-2xl relative min-h-[320px] md:min-h-[400px] flex items-center p-8 sm:p-12 md:p-20 overflow-hidden">
            <div class="relative z-10 max-w-2xl text-left">
                <h2
                    class="text-3xl sm:text-4xl md:text-5xl text-white mb-4 md:mb-6 leading-tight font-headline tracking-tighter">
                    {{ __('messages.landing.welcome.cta.title') }}</h2>
                <p class="text-base md:text-lg text-white/80 mb-8 md:mb-10 leading-relaxed font-body">
                    {{ __('messages.landing.welcome.cta.subtitle') }}
                </p>
                <x-landing_page.button variant="light" href="{{ route('register') }}">
                    {{ __('messages.landing.welcome.cta.button') }}
                </x-landing_page.button>
            </div>
        </div>
    </section>
@endsection

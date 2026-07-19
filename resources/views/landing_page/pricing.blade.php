@extends('layouts.landing_page.app')

@section('title', 'Pricing | Unlock Your Potential with Resumify')

@php
    $basicResumeLimit = config('plans.resume_limits.basic');
    $premiumResumeLimit = config('plans.resume_limits.premium');

    $basicResumeLabel = $basicResumeLimit === null
        ? 'Unlimited Resumes'
        : $basicResumeLimit . ' Active Resume' . ($basicResumeLimit === 1 ? '' : 's');

    $premiumResumeLabel = $premiumResumeLimit === null
        ? 'Unlimited Resumes'
        : $premiumResumeLimit . ' Resume' . ($premiumResumeLimit === 1 ? '' : 's');

    $basicResumeCompareLabel = $basicResumeLimit === null
        ? 'Unlimited'
        : $basicResumeLimit . ' Resume' . ($basicResumeLimit === 1 ? '' : 's');

    $premiumResumeCompareLabel = $premiumResumeLimit === null
        ? 'Unlimited'
        : $premiumResumeLimit . ' Resume' . ($premiumResumeLimit === 1 ? '' : 's');
@endphp

@section('content')
    {{-- Header Section --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-8 pt-14 sm:pt-20 pb-8 text-center">
        <h1
            class="text-4xl sm:text-5xl md:text-6xl font-headline font-bold text-primary tracking-tight mb-4 sm:mb-6 leading-tight">
            {{ __('messages.landing.pricing.hero.title') }}</h1>
        <p class="text-base sm:text-lg md:text-xl text-outline max-w-2xl mx-auto font-body leading-relaxed">
            {{ __('messages.landing.pricing.hero.subtitle') }}
        </p>
    </section>

    {{-- Pricing Cards Section --}}
    <section class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-10 mb-16 sm:mb-24 max-w-5xl mx-auto px-4 sm:px-8">

        <x-product.pricing-card plan="{{ __('messages.landing.pricing.plans.starter') }}" price="Rp 0" period="{{ __('messages.landing.pricing.plans.forever') }}" :features="[$basicResumeLabel, __('messages.landing.pricing.plans.standard_templates')]" :disabledFeatures="[__('messages.landing.pricing.plans.no_ai_enhancement')]"
            buttonText="{{ __('messages.landing.pricing.plans.get_started') }}" onclick="window.location.href='{{ route('register') }}'" />

        <x-product.pricing-card plan="{{ __('messages.landing.pricing.plans.premium_pro') }}" price="Rp 49.000" period="{{ __('messages.landing.pricing.plans.month') }}" :features="[
            $premiumResumeLabel,
            ['title' => __('messages.landing.pricing.plans.ai_bullet_optimizer'), 'subtitle' => __('messages.landing.pricing.plans.ai_bullet_optimizer_subtitle')],
            __('messages.landing.pricing.plans.realtime_ats_matcher'),
            __('messages.landing.pricing.plans.premium_pdf_export'),
            __('messages.landing.pricing.plans.priority_support'),
        ]" :isPremium="true"
            buttonText="{{ __('messages.landing.pricing.plans.activate_premium') }}" onclick="window.location.href='{{ route('register') }}'" />

    </section>

    {{-- Compare Our Features Section --}}
    <section class="max-w-4xl mx-auto px-4 sm:px-8 py-16 sm:py-24">
        <h2 class="text-3xl md:text-4xl font-headline font-bold text-center mb-10 sm:mb-16 tracking-tight text-primary">
            {{ __('messages.landing.pricing.compare.title') }}</h2>

        {{-- Desktop Table (hidden on mobile) --}}
        <div class="hidden sm:block w-full bg-tertiary rounded-2xl p-8 shadow-sm border border-primary/10">
            {{-- Table Header --}}
            <div class="grid grid-cols-3 pb-6 border-b-2 border-primary/10 mb-4">
                <span class="text-xs text-outline uppercase tracking-[0.2em] font-bold font-body pl-6">{{ __('messages.landing.pricing.compare.key_features') }}</span>
                <span class="text-xs text-outline uppercase tracking-[0.2em] font-bold font-body text-center">{{ __('messages.landing.pricing.plans.starter') }}</span>
                <span
                    class="text-xs text-secondary uppercase tracking-[0.2em] font-bold font-body text-center flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-[16px] icon-filled">auto_awesome</span> {{ __('messages.landing.pricing.plans.premium_pro') }}
                </span>
            </div>
            <div class="divide-y divide-primary/5">
                <div class="grid grid-cols-3 py-6 items-center hover:bg-primary/5 rounded-lg px-6 transition-colors">
                    <span class="text-base font-body text-primary font-medium">{{ __('messages.landing.pricing.compare.number_of_resumes') }}</span>
                    <span class="text-base font-body text-outline text-center">{{ $basicResumeCompareLabel }}</span>
                    <span class="text-base font-body font-bold text-primary text-center">{{ $premiumResumeCompareLabel }}</span>
                </div>
                <div class="grid grid-cols-3 py-6 items-center hover:bg-primary/5 rounded-lg px-6 transition-colors">
                    <span class="text-base font-body text-primary font-medium">{{ __('messages.landing.pricing.plans.ai_bullet_optimizer') }}</span>
                    <span class="text-base font-body text-outline text-center">—</span>
                    <span class="flex justify-center">
                        <span class="material-symbols-outlined text-secondary text-2xl icon-filled">check_circle</span>
                    </span>
                </div>
                <div class="grid grid-cols-3 py-6 items-center hover:bg-primary/5 rounded-lg px-6 transition-colors">
                    <span class="text-base font-body text-primary font-medium">{{ __('messages.landing.pricing.plans.realtime_ats_matcher') }}</span>
                    <span class="text-base font-body text-outline text-center">—</span>
                    <span class="flex justify-center">
                        <span class="material-symbols-outlined text-secondary text-2xl icon-filled">check_circle</span>
                    </span>
                </div>
                <div class="grid grid-cols-3 py-6 items-center hover:bg-primary/5 rounded-lg px-6 transition-colors">
                    <span class="text-base font-body text-primary font-medium">{{ __('messages.landing.pricing.plans.premium_pdf_export') }}</span>
                    <span class="text-base font-body text-outline text-center">{{ __('messages.landing.pricing.compare.standard') }}</span>
                    <span class="text-base font-body font-bold text-primary text-center">{{ __('messages.landing.pricing.compare.premium') }}</span>
                </div>
                <div class="grid grid-cols-3 py-6 items-center hover:bg-primary/5 rounded-lg px-6 transition-colors">
                    <span class="text-base font-body text-primary font-medium">{{ __('messages.landing.pricing.plans.priority_support') }}</span>
                    <span class="text-base font-body text-outline text-center">—</span>
                    <span class="flex justify-center">
                        <span class="material-symbols-outlined text-secondary text-2xl icon-filled">check_circle</span>
                    </span>
                </div>
            </div>
        </div>

        {{-- Mobile Cards (hidden on sm+) --}}
        <div class="sm:hidden space-y-4">
            @php
                $features = [
                    [
                        'label' => __('messages.landing.pricing.compare.number_of_resumes'),
                        'free' => $basicResumeCompareLabel,
                        'premium' => $premiumResumeCompareLabel,
                        'premiumCheck' => false,
                    ],
                    ['label' => __('messages.landing.pricing.plans.ai_bullet_optimizer'), 'free' => null, 'premium' => null, 'premiumCheck' => true],
                    ['label' => __('messages.landing.pricing.plans.realtime_ats_matcher'), 'free' => null, 'premium' => null, 'premiumCheck' => true],
                    [
                        'label' => __('messages.landing.pricing.plans.premium_pdf_export'),
                        'free' => __('messages.landing.pricing.compare.standard'),
                        'premium' => __('messages.landing.pricing.compare.premium'),
                        'premiumCheck' => false,
                    ],
                    ['label' => __('messages.landing.pricing.plans.priority_support'), 'free' => null, 'premium' => null, 'premiumCheck' => true],
                ];
            @endphp
            @foreach ($features as $f)
                <div class="bg-tertiary rounded-2xl border border-primary/10 p-6 shadow-sm">
                    <p class="text-base font-bold text-primary font-body mb-5">{{ $f['label'] }}</p>
                    <div class="flex justify-around items-center">
                        <div class="text-center flex-1">
                            <p class="text-[10px] text-outline uppercase tracking-[0.2em] font-bold mb-2">{{ __('messages.landing.pricing.plans.starter') }}</p>
                            @if ($f['free'])
                                <span class="text-sm font-body text-outline">{{ $f['free'] }}</span>
                            @else
                                <span class="text-outline">—</span>
                            @endif
                        </div>
                        <div class="w-px h-10 bg-primary/10"></div>
                        <div class="text-center flex-1">
                            <p
                                class="text-[10px] text-secondary uppercase tracking-[0.2em] font-bold mb-2 flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-[12px] icon-filled">auto_awesome</span> PRO
                            </p>
                            @if ($f['premiumCheck'])
                                <span
                                    class="material-symbols-outlined text-secondary text-2xl icon-filled">check_circle</span>
                            @else
                                <span class="text-sm font-body font-bold text-primary">{{ $f['premium'] }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- FAQ Section --}}
    <section class="max-w-3xl mx-auto px-4 sm:px-8 py-16 sm:py-24">
        <h2 class="text-3xl md:text-4xl font-headline font-bold text-center mb-12 tracking-tight text-primary">{{ __('messages.landing.pricing.faq.title') }}</h2>
        <div class="space-y-4">
            <x-ui.disclosure id="pricing-faq-1" :title="__('messages.landing.pricing.faq.q1')">
                <p class="text-outline font-body leading-relaxed text-sm">{{ __('messages.landing.pricing.faq.a1') }}</p>
            </x-ui.disclosure>

            <x-ui.disclosure id="pricing-faq-2" :title="__('messages.landing.pricing.faq.q2')">
                <p class="text-outline font-body leading-relaxed text-sm">{{ __('messages.landing.pricing.faq.a2') }}</p>
            </x-ui.disclosure>

            <x-ui.disclosure id="pricing-faq-3" :title="__('messages.landing.pricing.faq.q3')">
                <p class="text-outline font-body leading-relaxed text-sm">{{ __('messages.landing.pricing.faq.a3') }}</p>
            </x-ui.disclosure>
        </div>
    </section>
@endsection

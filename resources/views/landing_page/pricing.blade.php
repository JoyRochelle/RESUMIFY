@extends('layouts.landing_page.app')

@section('title', 'Pricing | Unlock Your Potential with Resumify')

@section('content')
{{-- Header Section --}}
<section class="max-w-7xl mx-auto px-4 sm:px-8 pt-14 sm:pt-20 pb-8 text-center">
    <h1 class="text-4xl sm:text-5xl md:text-6xl font-headline font-bold text-primary tracking-tighter mb-4 sm:mb-6 leading-tight">Invest in Your Career</h1>
    <p class="text-base sm:text-lg md:text-xl text-outline max-w-2xl mx-auto font-body leading-relaxed">
        Start for free, or unlock your full potential with AI Premium features.
    </p>
</section>

{{-- Pricing Cards Section --}}
<section class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-10 mb-16 sm:mb-24 max-w-5xl mx-auto px-4 sm:px-8">

    <x-user.pricing-card plan="Starter" price="Rp 0" period="forever" 
        :features="['1 Active Resume', 'Standard Templates']" 
        :disabledFeatures="['No AI Enhancement']"
        buttonText="Get Started" onclick="window.location.href='{{ route('register') }}'" />

    <x-user.pricing-card plan="Premium PRO" price="Rp 49.000" period="month" :features="[
        'Unlimited Resumes',
        ['title' => 'AI Bullet Point Optimizer', 'subtitle' => 'Optimize with high-impact keywords'],
        'Real-time ATS Matcher',
        'Premium PDF Export',
        'Priority Support',
    ]" :isPremium="true"
        buttonText="Activate Premium Now" onclick="window.location.href='{{ route('register') }}'" />

</section>

{{-- Compare Our Features Section --}}
<section class="max-w-4xl mx-auto px-4 sm:px-8 py-16 sm:py-24">
    <h2 class="text-3xl md:text-4xl font-headline font-bold text-center mb-10 sm:mb-16 tracking-tighter text-primary">Compare Our Features</h2>

    {{-- Desktop Table (hidden on mobile) --}}
    <div class="hidden sm:block w-full bg-tertiary rounded-3xl p-8 shadow-sm border border-primary/10">
        {{-- Table Header --}}
        <div class="grid grid-cols-3 pb-6 border-b-2 border-primary/10 mb-4">
            <span class="text-xs text-outline uppercase tracking-[0.2em] font-bold font-body pl-6">Key Features</span>
            <span class="text-xs text-outline uppercase tracking-[0.2em] font-bold font-body text-center">Starter</span>
            <span class="text-xs text-secondary uppercase tracking-[0.2em] font-bold font-body text-center flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-[16px] icon-filled">auto_awesome</span> Premium PRO
            </span>
        </div>
        <div class="divide-y divide-primary/5">
            <div class="grid grid-cols-3 py-6 items-center hover:bg-primary/5 rounded-xl px-6 transition-colors">
                <span class="text-base font-body text-primary font-medium">Number of Resumes</span>
                <span class="text-base font-body text-outline text-center">1 Resume</span>
                <span class="text-base font-body font-bold text-primary text-center">Unlimited</span>
            </div>
            <div class="grid grid-cols-3 py-6 items-center hover:bg-primary/5 rounded-xl px-6 transition-colors">
                <span class="text-base font-body text-primary font-medium">AI Bullet Point Optimizer</span>
                <span class="text-base font-body text-outline text-center">—</span>
                <span class="flex justify-center">
                    <span class="material-symbols-outlined text-secondary text-2xl icon-filled">check_circle</span>
                </span>
            </div>
            <div class="grid grid-cols-3 py-6 items-center hover:bg-primary/5 rounded-xl px-6 transition-colors">
                <span class="text-base font-body text-primary font-medium">Real-time ATS Matcher</span>
                <span class="text-base font-body text-outline text-center">—</span>
                <span class="flex justify-center">
                    <span class="material-symbols-outlined text-secondary text-2xl icon-filled">check_circle</span>
                </span>
            </div>
            <div class="grid grid-cols-3 py-6 items-center hover:bg-primary/5 rounded-xl px-6 transition-colors">
                <span class="text-base font-body text-primary font-medium">Premium PDF Export</span>
                <span class="text-base font-body text-outline text-center">Standard</span>
                <span class="text-base font-body font-bold text-primary text-center">Premium</span>
            </div>
            <div class="grid grid-cols-3 py-6 items-center hover:bg-primary/5 rounded-xl px-6 transition-colors">
                <span class="text-base font-body text-primary font-medium">Priority Support</span>
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
            ['label' => 'Number of Resumes',       'free' => '1 Resume',  'premium' => 'Unlimited', 'premiumCheck' => false],
            ['label' => 'AI Bullet Point Optimizer','free' => null,        'premium' => null,         'premiumCheck' => true],
            ['label' => 'Real-time ATS Matcher',   'free' => null,        'premium' => null,         'premiumCheck' => true],
            ['label' => 'Premium PDF Export',      'free' => 'Standard',  'premium' => 'Premium',    'premiumCheck' => false],
            ['label' => 'Priority Support',        'free' => null,        'premium' => null,         'premiumCheck' => true],
        ];
        @endphp
        @foreach($features as $f)
        <div class="bg-tertiary rounded-2xl border border-primary/10 p-6 shadow-sm">
            <p class="text-base font-bold text-primary font-body mb-5">{{ $f['label'] }}</p>
            <div class="flex justify-around items-center">
                <div class="text-center flex-1">
                    <p class="text-[10px] text-outline uppercase tracking-[0.2em] font-bold mb-2">Starter</p>
                    @if($f['free'])
                        <span class="text-sm font-body text-outline">{{ $f['free'] }}</span>
                    @else
                        <span class="text-outline">—</span>
                    @endif
                </div>
                <div class="w-px h-10 bg-primary/10"></div>
                <div class="text-center flex-1">
                    <p class="text-[10px] text-secondary uppercase tracking-[0.2em] font-bold mb-2 flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-[12px] icon-filled">auto_awesome</span> PRO
                    </p>
                    @if($f['premiumCheck'])
                        <span class="material-symbols-outlined text-secondary text-2xl icon-filled">check_circle</span>
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
<section class="max-w-3xl mx-auto px-4 sm:px-8 py-16 sm:py-24" x-data="{ active: null }">
    <h2 class="text-3xl md:text-4xl font-headline font-bold text-center mb-12 tracking-tighter text-primary">Frequently Asked Questions</h2>
    <div class="space-y-4">
        {{-- FAQ 1 --}}
        <div class="bg-surface-container-lowest rounded-sm overflow-hidden border border-primary/5 shadow-sm">
            <button @click="active = (active === 1 ? null : 1)" class="w-full flex items-center justify-between p-6 text-left group">
                <span class="font-bold text-primary font-body">Can I cancel my subscription?</span>
                <span class="material-symbols-outlined text-outline transition-transform duration-300" :class="active === 1 ? 'rotate-180 text-primary' : ''">expand_more</span>
            </button>
            <div x-show="active === 1" x-collapse class="px-6 pb-6 text-outline font-body leading-relaxed text-sm">
                Yes, you can cancel your subscription at any time through your account settings. Your premium access will remain active until the end of the current billing period.
            </div>
        </div>

        {{-- FAQ 2 --}}
        <div class="bg-surface-container-lowest rounded-sm overflow-hidden border border-primary/5 shadow-sm">
            <button @click="active = (active === 2 ? null : 2)" class="w-full flex items-center justify-between p-6 text-left group">
                <span class="font-bold text-primary font-body">What payment methods are available?</span>
                <span class="material-symbols-outlined text-outline transition-transform duration-300" :class="active === 2 ? 'rotate-180 text-primary' : ''">expand_more</span>
            </button>
            <div x-show="active === 2" x-collapse class="px-6 pb-6 text-outline font-body leading-relaxed text-sm">
                We accept credit cards (Visa, Mastercard), PayPal, and various local digital wallets to facilitate your transactions securely.
            </div>
        </div>

        {{-- FAQ 3 --}}
        <div class="bg-surface-container-lowest rounded-sm overflow-hidden border border-primary/5 shadow-sm">
            <button @click="active = (active === 3 ? null : 3)" class="w-full flex items-center justify-between p-6 text-left group">
                <span class="font-bold text-primary font-body">How does the AI help my resume?</span>
                <span class="material-symbols-outlined text-outline transition-transform duration-300" :class="active === 3 ? 'rotate-180 text-primary' : ''">expand_more</span>
            </button>
            <div x-show="active === 3" x-collapse class="px-6 pb-6 text-outline font-body leading-relaxed text-sm">
                Our AI analyzes job descriptions and provides relevant keyword suggestions, optimizes bullet point grammar, and ensures your resume format is well-read by ATS systems.
            </div>
        </div>
    </div>
</section>
@endsection
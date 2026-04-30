@extends('landing_page.app')

@section('title', 'Pricing | Unlock Your Potential with Resumify')

@section('content')
{{-- Header Section --}}
<section class="max-w-7xl mx-auto px-8 pt-20 pb-8 text-center">
    <h1 class="text-5xl md:text-6xl font-headline font-bold text-primary tracking-tighter mb-6 leading-tight">Invest in Your Career</h1>
    <p class="text-lg md:text-xl text-outline max-w-2xl mx-auto font-body leading-relaxed">
        Start for free, or unlock your full potential with AI Premium features.
    </p>
</section>

{{-- Pricing Cards Section --}}
<section class="max-w-7xl mx-auto px-8 pb-24">
    <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">

        {{-- Free Plan --}}
        <div class="bg-surface-container-lowest rounded-sm p-10 flex flex-col items-center text-center transition-transform hover:scale-[1.01] border border-primary/5 shadow-sm">
            <span class="text-outline uppercase tracking-[0.2em] text-[10px] font-bold mb-4 font-body">Basic</span>
            <h3 class="text-3xl font-headline font-bold mb-2 text-primary">Free</h3>
            <div class="flex items-baseline mb-8">
                <span class="text-4xl font-headline font-bold text-primary">$0</span>
                <span class="text-outline ml-1 font-body">/forever</span>
            </div>
            <ul class="space-y-4 mb-10 w-full text-left font-body text-sm">
                <li class="flex items-center space-x-3 text-primary">
                    <span class="material-symbols-outlined text-outline" style="font-size: 20px;">check_circle</span>
                    <span>1 Active Resume</span>
                </li>
                <li class="flex items-center space-x-3 text-outline/50">
                    <span class="material-symbols-outlined" style="font-size: 20px;">cancel</span>
                    <span>AI Bullet Point Optimizer</span>
                </li>
                <li class="flex items-center space-x-3 text-outline/50">
                    <span class="material-symbols-outlined" style="font-size: 20px;">cancel</span>
                    <span>Real-time ATS Matcher</span>
                </li>
            </ul>
            <x-landing_page.button variant="outline" class="w-full">Get Started</x-landing_page.button>
        </div>

        {{-- Premium Plan --}}
        <div class="bg-surface-container-lowest rounded-sm p-10 flex flex-col items-center text-center relative transition-transform hover:scale-[1.01] border-2 border-secondary/20 shadow-xl overflow-hidden">
            <div class="absolute top-0 right-0 bg-secondary px-6 py-1.5 rounded-bl-sm text-white text-[10px] font-bold uppercase tracking-wider">
                Most Popular
            </div>
            <span class="text-secondary uppercase tracking-[0.2em] text-[10px] font-bold mb-4 font-body">Recommended</span>
            <h3 class="text-3xl font-headline font-bold mb-2 text-primary">Premium</h3>
            <div class="flex items-baseline mb-8">
                <span class="text-4xl font-headline font-bold text-primary">$15</span>
                <span class="text-outline ml-1 font-body">/month</span>
            </div>
            <ul class="space-y-4 mb-10 w-full text-left font-body text-sm">
                <li class="flex items-center space-x-3 text-primary">
                    <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1; font-size: 20px;">check_circle</span>
                    <span class="font-bold">Unlimited Resumes</span>
                </li>
                <li class="flex items-center space-x-3 text-primary">
                    <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1; font-size: 20px;">check_circle</span>
                    <span class="font-bold">AI Bullet Point Optimizer</span>
                </li>
                <li class="flex items-center space-x-3 text-primary">
                    <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1; font-size: 20px;">check_circle</span>
                    <span class="font-bold">Real-time ATS Matcher</span>
                </li>
            </ul>
            <x-landing_page.button variant="primary" class="w-full">Select Premium</x-landing_page.button>
        </div>
    </div>
</section>

{{-- Compare Our Features Section --}}
<section class="max-w-4xl mx-auto px-8 py-24">
    <h2 class="text-3xl md:text-4xl font-headline font-bold text-center mb-16 tracking-tighter text-primary">Compare Our Features</h2>

    <div class="w-full">
        {{-- Table Header --}}
        <div class="grid grid-cols-3 pb-4 border-b border-primary/10 mb-2">
            <span class="text-[10px] text-outline uppercase tracking-[0.15em] font-bold font-body">Key Features</span>
            <span class="text-[10px] text-outline uppercase tracking-[0.15em] font-bold font-body text-center">Free</span>
            <span class="text-[10px] text-secondary uppercase tracking-[0.15em] font-bold font-body text-center">Premium</span>
        </div>

        {{-- Feature Rows --}}
        <div class="divide-y divide-primary/5">
            {{-- Number of Resumes --}}
            <div class="grid grid-cols-3 py-5 items-center">
                <span class="text-sm font-body text-primary">Number of Resumes</span>
                <span class="text-sm font-body text-outline text-center">1 Resume</span>
                <span class="text-sm font-body font-bold text-primary text-center">Unlimited</span>
            </div>

            {{-- AI Bullet Point Optimizer --}}
            <div class="grid grid-cols-3 py-5 items-center">
                <span class="text-sm font-body text-primary">AI Bullet Point Optimizer</span>
                <span class="text-sm font-body text-outline text-center">—</span>
                <span class="flex justify-center">
                    <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1; font-size: 20px;">check_circle</span>
                </span>
            </div>

            {{-- Real-time ATS Matcher --}}
            <div class="grid grid-cols-3 py-5 items-center">
                <span class="text-sm font-body text-primary">Real-time ATS Matcher</span>
                <span class="text-sm font-body text-outline text-center">—</span>
                <span class="flex justify-center">
                    <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1; font-size: 20px;">check_circle</span>
                </span>
            </div>

            {{-- Premium PDF Export --}}
            <div class="grid grid-cols-3 py-5 items-center">
                <span class="text-sm font-body text-primary">Premium PDF Export</span>
                <span class="text-sm font-body text-outline text-center">Standard</span>
                <span class="text-sm font-body font-bold text-primary text-center">Premium</span>
            </div>

            {{-- Priority Support --}}
            <div class="grid grid-cols-3 py-5 items-center">
                <span class="text-sm font-body text-primary">Priority Support</span>
                <span class="text-sm font-body text-outline text-center">—</span>
                <span class="flex justify-center">
                    <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1; font-size: 20px;">check_circle</span>
                </span>
            </div>
        </div>
    </div>
</section>

{{-- FAQ Section --}}
<section class="max-w-3xl mx-auto px-8 py-24" x-data="{ active: null }">
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
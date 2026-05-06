@extends('layouts.landing_page.app')

@section('title', 'Templates | Choose a Template that Fits Your Career')

@section('content')
<section class="max-w-7xl mx-auto px-8 pt-20 pb-8 text-center">
    {{-- Header: font-headline dan tracking-tighter sesuai DESIGN.md --}}
    <h1 class="text-5xl md:text-6xl font-headline font-bold tracking-tighter mb-6 text-primary leading-tight">
        Choose a Template that Fits<br>Your Career
    </h1>
    <p class="text-lg text-outline leading-relaxed font-body max-w-2xl mx-auto">
        From minimalist to creative, all our templates are optimized to pass ATS filters with a high-end editorial touch.
    </p>
</section>

{{-- Category Filter Tabs --}}
<section class="max-w-7xl mx-auto px-8 pb-16" x-data="{ activeCategory: 'all' }">
    <div class="flex flex-wrap justify-center gap-3 mb-16">
        <button @click="activeCategory = 'all'"
                :class="activeCategory === 'all' ? 'bg-secondary text-white border-secondary' : 'bg-transparent text-primary border-primary/20 hover:border-primary/40'"
                class="px-6 py-2 rounded-full text-sm font-bold font-body border transition-all duration-300">
            All
        </button>
        <button @click="activeCategory = 'professional'"
                :class="activeCategory === 'professional' ? 'bg-secondary text-white border-secondary' : 'bg-transparent text-primary border-primary/20 hover:border-primary/40'"
                class="px-6 py-2 rounded-full text-sm font-bold font-body border transition-all duration-300">
            Professional
        </button>
        <button @click="activeCategory = 'creative'"
                :class="activeCategory === 'creative' ? 'bg-secondary text-white border-secondary' : 'bg-transparent text-primary border-primary/20 hover:border-primary/40'"
                class="px-6 py-2 rounded-full text-sm font-bold font-body border transition-all duration-300">
            Creative
        </button>
        <button @click="activeCategory = 'technology'"
                :class="activeCategory === 'technology' ? 'bg-secondary text-white border-secondary' : 'bg-transparent text-primary border-primary/20 hover:border-primary/40'"
                class="px-6 py-2 rounded-full text-sm font-bold font-body border transition-all duration-300">
            Technology
        </button>
        <button @click="activeCategory = 'managerial'"
                :class="activeCategory === 'managerial' ? 'bg-secondary text-white border-secondary' : 'bg-transparent text-primary border-primary/20 hover:border-primary/40'"
                class="px-6 py-2 rounded-full text-sm font-bold font-body border transition-all duration-300">
            Managerial
        </button>
    </div>

    {{-- Template Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">

        @foreach($templates as $template)
        <div x-show="activeCategory === 'all' || activeCategory === '{{ $template->category }}'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <x-landing_page.template-card 
                title="{{ $template->name }}" 
                category="{{ strtoupper(str_replace('_', ' ', $template->category)) }}" 
                badge="{{ $template->badge }}" 
                badgeColor="{{ $template->badge_color }}">
                
                <div class="w-full h-80 bg-[#f4f4f5] flex items-center justify-center rounded-sm overflow-hidden relative group">
                    <img src="{{ $template->thumbnail }}" alt="{{ $template->name }}" class="w-full h-full object-cover object-top transition-transform duration-500 group-hover:scale-105">
                </div>

            </x-landing_page.template-card>
        </div>
        @endforeach

    </div>
</section>

{{-- CTA Section: Haven't found the right fit? --}}
<section class="max-w-5xl mx-auto px-8 py-16">
    <div class="relative rounded-2xl overflow-hidden min-h-[320px] flex flex-col items-center justify-center text-center p-12 md:p-16">
        {{-- Background gradient overlay --}}
        <div class="absolute inset-0 bg-gradient-to-br from-primary/90 via-primary/80 to-primary/70"></div>
        {{-- Nature texture overlay for depth --}}
        <div class="absolute inset-0 opacity-30" style="background: linear-gradient(135deg, rgba(34,60,30,0.6) 0%, rgba(79,59,47,0.4) 40%, rgba(120,90,60,0.3) 70%, rgba(79,59,47,0.5) 100%);"></div>
        
        <div class="relative z-10 max-w-xl">
            <h2 class="text-3xl md:text-4xl font-headline font-bold mb-4 tracking-tighter text-white leading-tight">
                Haven't found the right fit?
            </h2>
            <p class="text-white/70 mb-8 font-body leading-relaxed text-sm md:text-base">
                Don't worry, all templates can be fully customized to meet your personal brand needs. Start your career journey today.
            </p>
            <a href="{{ route('home') }}" class="inline-block bg-white text-primary px-8 py-3 rounded-sm font-bold font-body text-sm hover:bg-white/90 transition-all active:scale-95 shadow-sm">
                Register for Free Now
            </a>
        </div>
    </div>
</section>
@endsection
@extends('landing_page.app')

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

        {{-- 1. Eleanor Vance - Professional, Executive --}}
        <div x-show="activeCategory === 'all' || activeCategory === 'professional'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <x-landing_page.template-card title="Eleanor Vance" category="PROFESSIONAL, EXECUTIVE" badge="ATS OK" badgeColor="blue">
                {{-- Resume skeleton: header bar + lines layout --}}
                <div class="space-y-4">
                    <div class="h-3 w-2/3 bg-primary/15 rounded-sm"></div>
                    <div class="h-px w-full bg-primary/10"></div>
                    <div class="space-y-2">
                        <div class="h-2 w-full bg-primary/10 rounded-sm"></div>
                        <div class="h-2 w-5/6 bg-primary/10 rounded-sm"></div>
                        <div class="h-2 w-4/6 bg-primary/10 rounded-sm"></div>
                    </div>
                    <div class="h-px w-full bg-primary/10"></div>
                    <div class="space-y-2">
                        <div class="h-2 w-full bg-primary/10 rounded-sm"></div>
                        <div class="h-2 w-3/4 bg-primary/10 rounded-sm"></div>
                    </div>
                </div>
            </x-landing_page.template-card>
        </div>

        {{-- 2. The Creative - Creative, Design & Arts --}}
        <div x-show="activeCategory === 'all' || activeCategory === 'creative'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <x-landing_page.template-card title="The Creative" category="DESIGN & ARTS" badge="BEST SELLER" badgeColor="secondary">
                {{-- Resume skeleton: circle + right aligned lines --}}
                <div class="flex gap-4 mb-4">
                    <div class="w-10 h-10 rounded-full bg-primary/10 shrink-0"></div>
                    <div class="flex-1 space-y-2 pt-1">
                        <div class="h-2.5 w-full bg-primary/10 rounded-sm"></div>
                        <div class="h-2 w-3/4 bg-primary/8 rounded-sm"></div>
                    </div>
                </div>
                <div class="space-y-2 mt-4">
                    <div class="h-2 w-full bg-primary/8 rounded-sm"></div>
                    <div class="h-2 w-5/6 bg-primary/8 rounded-sm"></div>
                    <div class="h-2 w-full bg-primary/8 rounded-sm"></div>
                    <div class="h-2 w-2/3 bg-primary/8 rounded-sm"></div>
                </div>
                <div class="mt-4 space-y-2">
                    <div class="h-2 w-full bg-primary/8 rounded-sm"></div>
                    <div class="h-2 w-4/5 bg-primary/8 rounded-sm"></div>
                </div>
            </x-landing_page.template-card>
        </div>

        {{-- 3. The Architect - Technology, Tech & Engineering --}}
        <div x-show="activeCategory === 'all' || activeCategory === 'technology'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <x-landing_page.template-card title="The Architect" category="TECH & ENGINEERING">
                {{-- Resume skeleton: photo placeholder + sidebar layout --}}
                <div class="flex gap-3 mb-4">
                    <div class="w-14 h-10 bg-primary/8 rounded-sm flex items-center justify-center">
                        <span class="text-[8px] text-primary/30 font-body italic">Photo</span>
                    </div>
                    <div class="flex-1 space-y-2 pt-1">
                        <div class="h-2.5 w-full bg-primary/12 rounded-sm"></div>
                        <div class="h-2 w-1/2 bg-primary/8 rounded-sm"></div>
                    </div>
                </div>
                <div class="space-y-2 mt-4">
                    <div class="h-2 w-full bg-primary/8 rounded-sm"></div>
                    <div class="h-2 w-5/6 bg-primary/8 rounded-sm"></div>
                    <div class="h-2 w-full bg-primary/8 rounded-sm"></div>
                </div>
            </x-landing_page.template-card>
        </div>

        {{-- 4. Standard Ivory - Professional, Academic & Legal --}}
        <div x-show="activeCategory === 'all' || activeCategory === 'professional'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <x-landing_page.template-card title="Standard Ivory" category="ACADEMIC & LEGAL">
                {{-- Resume skeleton: clean lines layout --}}
                <div class="space-y-3">
                    <div class="h-2.5 w-3/4 bg-primary/12 rounded-sm"></div>
                    <div class="h-px w-full bg-primary/10"></div>
                    <div class="space-y-2">
                        <div class="h-2 w-full bg-primary/8 rounded-sm"></div>
                        <div class="h-2 w-full bg-primary/8 rounded-sm"></div>
                        <div class="h-2 w-4/5 bg-primary/8 rounded-sm"></div>
                    </div>
                    <div class="h-px w-full bg-primary/10 mt-2"></div>
                    <div class="space-y-2">
                        <div class="h-2 w-full bg-primary/8 rounded-sm"></div>
                        <div class="h-2 w-3/4 bg-primary/8 rounded-sm"></div>
                        <div class="h-2 w-5/6 bg-primary/8 rounded-sm"></div>
                    </div>
                </div>
            </x-landing_page.template-card>
        </div>

        {{-- 5. Modern Visual - Creative, Marketing & Sales --}}
        <div x-show="activeCategory === 'all' || activeCategory === 'creative'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <x-landing_page.template-card title="Modern Visual" category="MARKETING & SALES" badge="AI ENHANCED" badgeColor="purple">
                {{-- Resume skeleton: two column layout with photo --}}
                <div class="flex gap-3 mb-4">
                    <div class="w-12 h-14 bg-primary/8 rounded-sm flex items-center justify-center">
                        <span class="text-[7px] text-primary/30 font-body italic">Photo</span>
                    </div>
                    <div class="flex-1 space-y-2 pt-2">
                        <div class="h-2.5 w-full bg-primary/12 rounded-sm"></div>
                        <div class="h-2 w-2/3 bg-primary/8 rounded-sm"></div>
                    </div>
                </div>
                <div class="space-y-2 mt-3">
                    <div class="h-2 w-full bg-primary/8 rounded-sm"></div>
                    <div class="h-2 w-5/6 bg-primary/8 rounded-sm"></div>
                    <div class="h-2 w-full bg-primary/8 rounded-sm"></div>
                    <div class="h-2 w-2/3 bg-primary/8 rounded-sm"></div>
                </div>
            </x-landing_page.template-card>
        </div>

        {{-- 6. Sidebar Minimal - Managerial --}}
        <div x-show="activeCategory === 'all' || activeCategory === 'managerial'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <x-landing_page.template-card title="Sidebar Minimal" category="MANAGERIAL">
                {{-- Resume skeleton: sidebar + content layout --}}
                <div class="flex gap-3">
                    <div class="w-1/3 space-y-3">
                        <div class="h-2 w-full bg-primary/10 rounded-sm"></div>
                        <div class="h-2 w-3/4 bg-primary/8 rounded-sm"></div>
                        <div class="h-2 w-full bg-primary/8 rounded-sm"></div>
                        <div class="h-6 bg-primary/5 rounded-sm mt-2"></div>
                    </div>
                    <div class="flex-1 space-y-3">
                        <div class="h-3 w-2/3 bg-primary/12 rounded-sm"></div>
                        <div class="space-y-2">
                            <div class="h-2 w-full bg-primary/8 rounded-sm"></div>
                            <div class="h-2 w-full bg-primary/8 rounded-sm"></div>
                            <div class="h-2 w-4/5 bg-primary/8 rounded-sm"></div>
                        </div>
                        <div class="space-y-2 mt-2">
                            <div class="h-2 w-full bg-primary/8 rounded-sm"></div>
                            <div class="h-2 w-3/4 bg-primary/8 rounded-sm"></div>
                        </div>
                    </div>
                </div>
            </x-landing_page.template-card>
        </div>

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
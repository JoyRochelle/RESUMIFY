@extends('landing_page.app')

@section('title', 'Resumify | Write Your Success Story')

@section('content')
<section class="min-h-[85vh] flex flex-col md:flex-row bg-surface">
    {{-- Hero Left: Content --}}
    {{-- Perbaikan: Mengubah items-center menjadi items-start agar sejajar dengan kanan --}}
    {{-- Menyesuaikan py agar sinkron dengan padding kanan --}}
    <div class="w-full md:w-[40%] flex items-start px-8 md:px-16 py-12 md:py-20">
        <div class="max-w-md w-full">
            <h1 class="text-6xl md:text-7xl font-headline text-primary leading-tight tracking-tighter mb-8">
                Write Your Success Story <span class="inline-block text-5xl">✨</span>
            </h1>
            <p class="text-xl text-outline mb-10 leading-relaxed font-body">
                Adapt your resume to job openings with artificial intelligence.
            </p>
            <x-landing_page.button variant="primary" icon="arrow_forward">
                Upgrade Your Resume
            </x-landing_page.button>
        </div>
    </div>

    {{-- Hero Right: Visual Preview --}}
    {{-- Menjaga items-start untuk layout preview yang mengalir ke bawah --}}
    <div class="w-full md:w-[60%] bg-[#f0f4f8] relative flex items-start justify-center px-8 md:px-16 py-12 md:py-20">
        <div class="relative w-full max-w-md">
            
            {{-- Resume Card Preview --}}
            <div class="bg-white p-8 pb-96 rounded-sm shadow-2xl relative z-10 border border-primary/5 min-h-[700px]">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h2 class="text-2xl font-serif text-primary tracking-tighter leading-none">Theofrolic</h2>
                        <p class="text-[#00c9a7] font-bold font-body tracking-widest uppercase text-[10px] mt-2">Senior Product Designer</p>
                    </div>
                    <div class="text-right text-[10px] text-gray-400 font-body leading-relaxed">
                        <p>Jakarta, Indonesia</p>
                        <p>theofrolic@resumify.ai</p>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="h-px bg-gray-100 w-full mb-2"></div>
                    <div>
                        <h3 class="text-[10px] font-bold text-primary font-body mb-4 uppercase tracking-widest">WORK EXPERIENCE</h3>
                        <div class="space-y-5">
                            <div>
                                <div class="flex justify-between items-start mb-1">
                                    <div>
                                        <p class="text-xs font-bold text-primary font-body">Lead Designer</p>
                                        <p class="text-[10px] text-gray-400 font-body">TechNova Solutions</p>
                                    </div>
                                    <span class="text-[10px] text-gray-400 font-body whitespace-nowrap">2021 – Present</span>
                                </div>
                            </div>

                            <div class="bg-[#f0faf7] p-5 rounded-sm border-l-4 border-[#00c9a7]">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-symbols-outlined text-[16px] text-[#00c9a7]" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                                    <span class="text-[10px] font-bold text-[#00c9a7] uppercase tracking-wider">AI Optimized</span>
                                </div>
                                <p class="text-[11px] text-primary/80 leading-loose font-body">
                                    Leading a design team of 12 people and increased user conversion rates by 34% through systematic A/B testing.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ATS Match Score - Posisi tetap konsisten --}}
            <div class="absolute -right-10 top-[350px] bg-white/95 backdrop-blur-xl p-5 rounded-md shadow-xl w-56 z-20 border border-gray-100 transform rotate-1">
                <p class="text-[9px] text-gray-400 mb-3 tracking-widest uppercase font-bold">ATS MATCH SCORE</p>
                <div class="flex items-end gap-1.5 h-10 mb-3">
                    <div class="w-full bg-[#f4c4c4] h-1/4 rounded-sm"></div>
                    <div class="w-full bg-[#e89d9d] h-2/5 rounded-sm"></div>
                    <div class="w-full bg-[#a7e2cc] h-3/5 rounded-sm"></div>
                    <div class="w-full bg-[#00c9a7] h-4/5 rounded-sm opacity-80"></div>
                    <div class="w-full bg-[#00c9a7] h-full rounded-sm"></div>
                </div>
                <div class="flex justify-between items-center text-[10px] font-bold">
                    <span class="text-gray-400">Low</span>
                    <span class="text-[#00c9a7]">High</span>
                </div>
            </div>

            {{-- INPUT EDITOR --}}
            <div class="absolute top-[480px] -left-16 bg-[#44362d] p-7 rounded-md shadow-2xl w-72 z-30">
                <p class="text-[9px] text-white/40 mb-5 tracking-widest uppercase font-bold">INPUT EDITOR</p>
                <div class="space-y-5">
                    <div>
                        <label class="text-[8px] text-white/40 uppercase tracking-widest block mb-1">NAME</label>
                        <div class="border-b border-white/10 pb-1">
                            <span class="text-white text-[12px]">Theofrolic</span>
                        </div>
                    </div>
                    <div>
                        <label class="text-[8px] text-white/40 uppercase tracking-widest block mb-1">COMPANY</label>
                        <div class="border-b border-white/10 pb-1">
                            <span class="text-white text-[12px]">Resumify</span>
                        </div>
                    </div>
                    <div>
                        <label class="text-[8px] text-white/40 uppercase tracking-widest block mb-1">DESCRIPTION</label>
                        <div class="border-b border-white/10 pb-1">
                            <span class="text-white/80 text-[11px] leading-relaxed">Leading design team and increasing conversions by 34%...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Features Section --}}
<section class="py-32 px-8">
    <div class="max-w-7xl mx-auto grid md:grid-cols-3 gap-8">
        {{-- Perbaikan Path: x-landing_page.feature-card --}}
        <x-landing_page.feature-card title="AI Bullet Point Generator" icon="auto_awesome" :filled="true">
            Write your achievements instantly with data-driven suggestions that stand out to recruiters.
        </x-landing_page.feature-card>

        <x-landing_page.feature-card title="ATS Match Score Scanner" icon="analytics">
            Evaluate your resume against job descriptions in real-time to ensure it passes filtration systems.
        </x-landing_page.feature-card>

        <x-landing_page.feature-card title="Premium Templates" icon="article">
            A collection of professionally curated templates for various industries and career levels.
        </x-landing_page.feature-card>
    </div>
</section>

{{-- Call to Action --}}
<section class="py-24 px-8">
    <div class="max-w-5xl mx-auto bg-primary rounded-2xl relative min-h-[400px] flex items-center p-12 md:p-20 overflow-hidden">
        <div class="relative z-10 max-w-2xl text-left">
            <h2 class="text-4xl md:text-5xl text-white mb-6 leading-tight font-headline tracking-tighter">Ready to build your story?</h2>
            <p class="text-lg text-white/80 mb-10 leading-relaxed font-body">
                Join thousands of professionals who have accelerated their career with Resumify.
            </p>
            {{-- Perbaikan: Button putih sesuai mockup --}}
            <a href="{{ route('home') }}" class="inline-block bg-white text-primary px-8 py-3 rounded-sm font-bold font-body text-sm hover:bg-white/90 transition-all active:scale-95 shadow-sm">
                Start for Free Now
            </a>
        </div>
    </div>
</section>
@endsection
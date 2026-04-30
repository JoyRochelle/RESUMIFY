@extends('layouts.user.app')

@section('title', 'Resumify AI Assistant | Optimization')
@section('body_class', 'overflow-hidden h-screen')

@section('content')
    <div class="flex-1 flex flex-col min-w-0">
        
        {{-- Page Header --}}
        <x-user.page-header title="AI Assistant: ATS Optimizer">
            <x-user.button variant="ghost" class="text-sm px-3">Instructions</x-user.button>
            <x-user.button variant="primary" icon="history" iconClass="text-[18px]" class="text-sm px-3">History</x-user.button>
        </x-user.page-header>

        <div class="flex-1 flex flex-col lg:flex-row overflow-y-auto lg:overflow-hidden pb-20 lg:pb-0">
            
            <aside class="w-full lg:w-[40%] bg-surface-container-low flex flex-col border-b lg:border-b-0 lg:border-r border-primary/10 z-20 shrink-0 lg:h-full">
                <div class="p-4 lg:p-6 lg:overflow-y-auto custom-scrollbar space-y-6 lg:h-full">
                    
                    <div class="bg-tertiary rounded-xl p-6 border border-primary/10 shadow-sm flex flex-col gap-4">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary/60">description</span>
                                Your Resume
                            </h3>
                            <x-user.button variant="text" icon="upload_file" iconClass="text-sm" class="!px-0 hover:underline">Upload PDF</x-user.button>
                        </div>
                        <textarea class="w-full bg-surface-container-low rounded-lg border-none focus:ring-1 focus:ring-primary p-4 text-sm leading-relaxed custom-scrollbar resize-none" placeholder="Paste resume here..." rows="8">Senior Product Designer with 6+ years experience in fintech. Expert in UI/UX, user research, and cross-functional team leadership. Proficient in Figma and React.</textarea>
                    </div>

                    <div class="bg-tertiary rounded-xl p-6 border border-primary/10 shadow-sm flex flex-col gap-4">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary/60">target</span>
                                Job Description
                            </h3>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-primary/60">Target Role</span>
                        </div>
                        <textarea class="w-full bg-surface-container-low rounded-lg border-none focus:ring-1 focus:ring-primary p-4 text-sm leading-relaxed custom-scrollbar resize-none" placeholder="Paste job description here..." rows="8">We are looking for a Senior Product Designer to lead our design systems team. Requirements: 5+ years experience, mastery of Figma, familiarity with Docker/AWS environments, and experience with Laravel 12 for dashboard prototyping.</textarea>
                    </div>

                    <x-user.button variant="primary" class="w-full py-4 text-md shadow-lg group flex items-center justify-center gap-3">
                        Analyze Match 
                        <span class="material-symbols-outlined text-tertiary group-hover:rotate-12 transition-transform icon-filled">auto_awesome</span>
                    </x-user.button>
                </div>
            </aside>

            <main class="w-full lg:w-[60%] lg:h-full bg-primary/5 p-4 lg:p-10 lg:overflow-y-auto custom-scrollbar">
                <div class="max-w-4xl mx-auto space-y-8">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <div class="bg-tertiary rounded-2xl p-8 border border-primary/10 shadow-sm flex flex-col items-center justify-center text-center">
                            <div class="mb-4">
                                <x-user.score-circle :score="78" size="lg" />
                            </div>
                            <h4 class="text-secondary font-bold text-lg">Very Good</h4>
                            <p class="text-primary/60 text-xs mt-1">Needs Minor Polish</p>
                        </div>

                        <div class="bg-tertiary rounded-2xl p-6 border border-primary/10 shadow-sm md:col-span-2">
                            <div class="flex items-center gap-2 mb-4 text-red-500">
                                <span class="material-symbols-outlined text-[20px]" data-icon="error_outline">error_outline</span>
                                <h4 class="font-bold tracking-tight text-sm uppercase">Missing Keywords</h4>
                            </div>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <x-user.keyword-tag variant="danger">Laravel 12</x-user.keyword-tag>
                                <x-user.keyword-tag variant="neutral">Docker</x-user.keyword-tag>
                                <x-user.keyword-tag variant="neutral">AWS</x-user.keyword-tag>
                            </div>
                            <p class="text-xs text-primary/80 leading-relaxed italic">Adding these will increase your ATS visibility by approx. 14%.</p>
                        </div>
                    </div>

                    <div class="bg-tertiary rounded-2xl p-6 border border-primary/10 shadow-sm">
                        <div class="flex items-center gap-2 mb-4 text-secondary">
                            <span class="material-symbols-outlined text-[20px]" data-icon="check_circle">check_circle</span>
                            <h4 class="font-bold tracking-tight text-sm uppercase">Optimized Action Verbs</h4>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <x-user.keyword-tag variant="success" class="py-2 rounded-xl">Lead</x-user.keyword-tag>
                            <x-user.keyword-tag variant="success" class="py-2 rounded-xl">Spearheaded</x-user.keyword-tag>
                            <x-user.keyword-tag variant="success" class="py-2 rounded-xl">Optimized</x-user.keyword-tag>
                            <x-user.keyword-tag variant="success" class="py-2 rounded-xl">Engineered</x-user.keyword-tag>
                        </div>
                    </div>

                    <section class="glass-effect rounded-2xl p-8 border border-secondary/20 relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-secondary/10 blur-3xl rounded-full"></div>
                        <div class="flex items-center gap-3 mb-8">
                            <span class="material-symbols-outlined text-secondary icon-filled">auto_awesome</span>
                            <h3 class="font-headline text-2xl font-bold text-primary">Strategic AI Insights</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                            <x-user.insight-item title="Quantify Impact">
                                Your experience in "fintech" is strong, but adding specific metrics (e.g., "Increased conversion by 12%") will trigger higher relevancy.
                            </x-user.insight-item>
                            <x-user.insight-item title="Technical Stack">
                                Mention "infrastructure familiarity" to connect your design work to the Docker/AWS requirements.
                            </x-user.insight-item>
                            <x-user.insight-item title="Tone Check">
                                Your summary is professional, but consider making it more "leadership-oriented" to match the Senior role.
                            </x-user.insight-item>
                            <x-user.insight-item title="Layout Tip">
                                Ensure your "Design Systems" section is listed first under Skills to match the JD's primary focus area.
                            </x-user.insight-item>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>
@endsection
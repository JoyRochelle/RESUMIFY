<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Resumify Workspace | Editor</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&family=Manrope:wght@200..800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-surface font-body text-primary overflow-hidden h-screen flex">

    @include('layouts.user.sidenavbar')

    <div class="flex-1 flex flex-col min-w-0">
        
        {{-- Page Header --}}
        <x-user.page-header title="Editor: Senior Product Designer">
            <x-user.button variant="ghost" class="text-sm px-3 hidden sm:flex">Preview</x-user.button>
            <x-user.button variant="primary" icon="download" iconClass="text-[18px]" class="text-sm px-3 w-full sm:w-auto justify-center">Download PDF</x-user.button>
        </x-user.page-header>

        <div class="flex-1 flex flex-col lg:flex-row overflow-y-auto lg:overflow-hidden pb-20 lg:pb-0">
            
            <aside class="w-full lg:w-[40%] bg-surface-container-low flex flex-col border-b lg:border-b-0 lg:border-r border-primary/10 z-20 shrink-0 lg:h-full">
                <div class="p-4 lg:p-6 lg:overflow-y-auto custom-scrollbar space-y-6 lg:h-full">
                    
                    <x-user.editor-accordion title="Personal Info" icon="person" />
                    
                    <x-user.editor-accordion title="Work Experience" icon="work" :isOpen="true">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <x-user.form-input label="Title" name="title" value="Senior Product Designer" />
                            <x-user.form-input label="Company" name="company" value="Linear" />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <x-user.form-input label="Start Date" name="start_date" value="Jan 2021" />
                            <x-user.form-input label="End Date" name="end_date" value="Present" />
                        </div>
                        
                        <div class="relative group">
                            <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Description</label>
                            <div class="relative">
                                <textarea name="description" class="w-full bg-surface-container-low rounded-lg border-none focus:ring-1 focus:ring-primary p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none" rows="5">Leading design systems for the world's most productive software teams. Crafting high-fidelity components and maintaining visual consistency across mobile and desktop platforms.</textarea>
                                <x-user.button variant="pill" icon="auto_awesome" iconClass="text-sm icon-filled" class="absolute bottom-3 right-3">
                                    ✨ Enhance with AI
                                </x-user.button>
                            </div>
                        </div>
                        <x-user.button variant="dashed" icon="add_circle">Add New Experience</x-user.button>

                    </x-user.editor-accordion>
                    
                    <x-user.editor-accordion title="Education" icon="school" />
                    <x-user.editor-accordion title="Skills" icon="bolt" />
                    
                </div>
            </aside>

            <main class="w-full lg:w-[60%] lg:h-full bg-primary/5 flex flex-col items-center p-4 lg:p-12 lg:overflow-y-auto relative custom-scrollbar">
                
                <div class="bg-tertiary w-full max-w-[595px] lg:min-h-[842px] shadow-xl rounded-sm p-6 lg:p-16 relative flex flex-col lg:my-auto shrink-0 mb-10 lg:mb-0">
                    
                    <div class="absolute -top-4 -right-4 bg-tertiary/90 backdrop-blur-xl p-4 rounded-2xl shadow-xl border border-primary/10 z-10 flex flex-col items-center">
                        <div class="text-[10px] font-bold text-primary/60 uppercase tracking-widest mb-2">ATS Score</div>
                        <x-user.score-circle :score="78" size="sm" :showPercent="false"/>
                    </div>
                    
                    <header class="text-center mb-12">
                        <h2 class="font-headline text-3xl md:text-4xl font-bold text-primary tracking-tight mb-2">Eleanor Vance</h2>
                        <p class="text-sm font-body text-primary/60 tracking-widest uppercase">Senior Product Designer • San Francisco, CA</p>
                        <div class="mt-4 flex justify-center gap-6 text-xs font-medium text-primary/80">
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">mail</span> eleanor@vance.design</span>
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">public</span> vance.design</span>
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">call</span> +1 (555) 000-1111</span>
                        </div>
                    </header>
                    
                    <div class="space-y-10">
                        <section>
                            <h3 class="font-headline text-lg font-bold text-primary border-b border-primary/20 pb-1 mb-3">Professional Summary</h3>
                            <p class="text-[13px] leading-relaxed text-primary/80">
                                Accomplished Product Designer with 8+ years of experience crafting intuitive digital experiences for high-growth tech companies. Expertise in systems thinking, accessibility-first design, and bridge-building between engineering and design teams.
                            </p>
                        </section>
                        
                        <section class="space-y-6">
                            <h3 class="font-headline text-lg font-bold text-primary border-b border-primary/20 pb-1 mb-4">Experience</h3>
                            
                            <div class="space-y-1">
                                <div class="flex justify-between items-baseline">
                                    <h4 class="font-headline text-md font-bold text-primary">Senior Product Designer</h4>
                                    <span class="font-headline italic text-sm text-primary/60">Jan 2021 — Present</span>
                                </div>
                                <div class="text-sm font-bold text-secondary">Linear</div>
                                <p class="text-[13px] leading-relaxed text-primary/80 mt-2">
                                    Leading design systems for the world's most productive software teams. Crafting high-fidelity components and maintaining visual consistency across mobile and desktop platforms.
                                </p>
                            </div>
                            
                            <div class="space-y-1">
                                <div class="flex justify-between items-baseline">
                                    <h4 class="font-headline text-md font-bold text-primary">Product Designer</h4>
                                    <span class="font-headline italic text-sm text-primary/60">Mar 2018 — Dec 2020</span>
                                </div>
                                <div class="text-sm font-bold text-secondary">Airbnb</div>
                                <p class="text-[13px] leading-relaxed text-primary/80 mt-2">
                                    Focused on the guest booking experience and internationalization of the design system. Reduced checkout friction by 12% through iterative testing and accessible UI patterns.
                                </p>
                            </div>
                        </section>
                        
                        <section>
                            <h3 class="font-headline text-lg font-bold text-primary border-b border-primary/20 pb-1 mb-4">Education</h3>
                            <div class="flex justify-between items-baseline">
                                <h4 class="font-headline text-md font-bold text-primary">BFA in Interaction Design</h4>
                                <span class="font-headline italic text-sm text-primary/60">2014 — 2018</span>
                            </div>
                            <div class="text-sm font-medium text-primary/80">Rhode Island School of Design</div>
                        </section>
                        
                        <section>
                            <h3 class="font-headline text-lg font-bold text-primary border-b border-primary/20 pb-1 mb-4">Expertise</h3>
                            <div class="flex flex-wrap gap-2">
                                <x-user.keyword-tag variant="neutral" class="bg-surface-container-low text-primary border-primary/5 text-[11px] uppercase px-3 py-1">Design Systems</x-user.keyword-tag>
                                <x-user.keyword-tag variant="neutral" class="bg-surface-container-low text-primary border-primary/5 text-[11px] uppercase px-3 py-1">Figma</x-user.keyword-tag>
                                <x-user.keyword-tag variant="neutral" class="bg-surface-container-low text-primary border-primary/5 text-[11px] uppercase px-3 py-1">React & Tailwind</x-user.keyword-tag>
                                <x-user.keyword-tag variant="neutral" class="bg-surface-container-low text-primary border-primary/5 text-[11px] uppercase px-3 py-1">Accessibility</x-user.keyword-tag>
                                <x-user.keyword-tag variant="neutral" class="bg-surface-container-low text-primary border-primary/5 text-[11px] uppercase px-3 py-1">Prototyping</x-user.keyword-tag>
                            </div>
                        </section>
                    </div>
                    
                    <footer class="mt-auto pt-12 border-t border-primary/10 flex justify-center">
                        <span class="font-headline italic text-xs text-primary/60">1 of 1</span>
                    </footer>
                </div>

                <div class="sticky bottom-24 lg:bottom-10 z-40 bg-tertiary/80 backdrop-blur-md border border-primary/10 px-6 py-3 rounded-full shadow-lg flex items-center gap-6 shrink-0 mx-auto">
                    <x-user.button variant="text" icon="zoom_in" class="hidden sm:flex">
                        <span class="text-xs uppercase tracking-widest">Zoom</span>
                    </x-user.button>
                    <div class="w-px h-4 bg-primary/20 hidden sm:block"></div>
                    <x-user.button variant="text" icon="layers">
                        <span class="text-xs uppercase tracking-widest">Layout</span>
                    </x-user.button>
                    <div class="w-px h-4 bg-primary/20"></div>
                    <x-user.button variant="text" icon="history">
                        <span class="text-xs uppercase tracking-widest">History</span>
                    </x-user.button>
                </div>
                
            </main>
        </div>
    </div>
    @include('layouts.user.mobilenavbar')
</body>
</html>

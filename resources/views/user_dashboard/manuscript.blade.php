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

<body class="bg-surface font-body text-primary overflow-hidden h-screen flex flex-row">

    @include('layouts.sidenavbar')

    <div class="flex-1 flex flex-col min-w-0">
        
        <header class="h-16 flex justify-between items-center px-8 w-full border-b border-primary/10 bg-surface shrink-0 z-30">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-primary/60 hover:text-primary transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-primary">arrow_back</span>
                    <h1 class="font-headline text-xl font-bold text-primary tracking-tight">Editor: Senior Product Designer</h1>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <button class="px-5 py-2 text-sm font-bold text-primary hover:bg-primary/5 transition-colors rounded-lg">Preview Full Screen</button>
                <button class="px-5 py-2 text-sm font-bold bg-primary text-tertiary rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    Download PDF
                </button>
            </div>
        </header>

        <div class="flex-1 flex overflow-hidden">
            
            <aside class="w-[40%] bg-surface-container-low flex flex-col border-r border-primary/10 z-20">
                <div class="p-6 overflow-y-auto custom-scrollbar space-y-6">
                    
                    <div class="bg-tertiary rounded-lg p-5 border border-primary/10 shadow-sm">
                        <div class="flex justify-between items-center cursor-pointer group">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary/60 group-hover:text-primary transition-colors">person</span>
                                <h3 class="font-bold text-primary">Personal Info</h3>
                            </div>
                            <span class="material-symbols-outlined text-primary/60 group-hover:text-primary transition-colors">expand_more</span>
                        </div>
                    </div>
                    
                    <div class="bg-tertiary rounded-lg border border-primary/10 shadow-sm">
                        <div class="p-5 flex justify-between items-center bg-surface-container-low rounded-t-lg border-b border-primary/10 cursor-pointer">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary">work</span>
                                <h3 class="font-bold text-primary">Work Experience</h3>
                            </div>
                            <span class="material-symbols-outlined text-primary">expand_less</span>
                        </div>
                        <div class="p-6 space-y-8">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="relative">
                                    <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-1 block">Title</label>
                                    <input class="w-full border-b border-primary/20 focus:border-primary bg-transparent py-2 px-0 outline-none transition-all focus:ring-0 text-primary" type="text" value="Senior Product Designer" />
                                </div>
                                <div class="relative">
                                    <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-1 block">Company</label>
                                    <input class="w-full border-b border-primary/20 focus:border-primary bg-transparent py-2 px-0 outline-none transition-all focus:ring-0 text-primary" type="text" value="Linear" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="relative">
                                    <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-1 block">Start Date</label>
                                    <input class="w-full border-b border-primary/20 focus:border-primary bg-transparent py-2 px-0 outline-none transition-all focus:ring-0 text-primary" type="text" value="Jan 2021" />
                                </div>
                                <div class="relative">
                                    <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-1 block">End Date</label>
                                    <input class="w-full border-b border-primary/20 focus:border-primary bg-transparent py-2 px-0 outline-none transition-all focus:ring-0 text-primary" type="text" value="Present" />
                                </div>
                            </div>
                            <div class="relative group">
                                <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Description</label>
                                <div class="relative">
                                    <textarea class="w-full bg-surface-container-low rounded-lg border-none focus:ring-1 focus:ring-primary p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none" rows="5">Leading design systems for the world's most productive software teams. Crafting high-fidelity components and maintaining visual consistency across mobile and desktop platforms.</textarea>
                                    <button class="absolute bottom-3 right-3 flex items-center gap-2 bg-secondary text-tertiary px-3 py-1.5 rounded-full text-xs font-bold hover:brightness-110 transition-all shadow-md">
                                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                                        ✨ Enhance with AI
                                    </button>
                                </div>
                            </div>
                            <button class="w-full py-4 border-2 border-dashed border-primary/20 rounded-lg text-primary/60 font-bold hover:text-primary hover:border-primary/50 hover:bg-primary/5 transition-all flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">add_circle</span>
                                Add New Experience
                            </button>
                        </div>
                    </div>
                    
                    <div class="bg-tertiary rounded-lg p-5 border border-primary/10 shadow-sm">
                        <div class="flex justify-between items-center cursor-pointer group">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary/60 group-hover:text-primary transition-colors">school</span>
                                <h3 class="font-bold text-primary">Education</h3>
                            </div>
                            <span class="material-symbols-outlined text-primary/60 group-hover:text-primary transition-colors">expand_more</span>
                        </div>
                    </div>
                    
                    <div class="bg-tertiary rounded-lg p-5 border border-primary/10 shadow-sm">
                        <div class="flex justify-between items-center cursor-pointer group">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary/60 group-hover:text-primary transition-colors">bolt</span>
                                <h3 class="font-bold text-primary">Skills</h3>
                            </div>
                            <span class="material-symbols-outlined text-primary/60 group-hover:text-primary transition-colors">expand_more</span>
                        </div>
                    </div>
                    
                </div>
            </aside>

            <main class="w-[60%] bg-primary/5 flex items-center justify-center p-12 overflow-y-auto relative custom-scrollbar">
                
                <div class="bg-tertiary w-[595px] min-h-[842px] shadow-xl rounded-sm p-16 relative flex flex-col">
                    
                    <div class="absolute -top-4 -right-4 bg-tertiary/90 backdrop-blur-xl p-4 rounded-2xl shadow-xl border border-primary/10 z-10 flex flex-col items-center">
                        <div class="text-[10px] font-bold text-primary/60 uppercase tracking-widest mb-2">ATS Score</div>
                        <div class="relative w-16 h-16 flex items-center justify-center">
                            <svg class="w-full h-full transform -rotate-90">
                                <circle class="text-primary/10" cx="32" cy="32" fill="transparent" r="28" stroke="currentColor" stroke-width="4"></circle>
                                <circle class="text-secondary" cx="32" cy="32" fill="transparent" r="28" stroke="currentColor" stroke-dasharray="176" stroke-dashoffset="35.2" stroke-width="4"></circle>
                            </svg>
                            <span class="absolute font-headline font-bold text-primary">82</span>
                        </div>
                    </div>
                    
                    <header class="text-center mb-12">
                        <h2 class="font-headline text-5xl font-bold text-primary tracking-tight mb-2">Eleanor Vance</h2>
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
                                <span class="px-3 py-1 bg-surface-container-low rounded-full text-[11px] font-bold text-primary uppercase border border-primary/5">Design Systems</span>
                                <span class="px-3 py-1 bg-surface-container-low rounded-full text-[11px] font-bold text-primary uppercase border border-primary/5">Figma</span>
                                <span class="px-3 py-1 bg-surface-container-low rounded-full text-[11px] font-bold text-primary uppercase border border-primary/5">React & Tailwind</span>
                                <span class="px-3 py-1 bg-surface-container-low rounded-full text-[11px] font-bold text-primary uppercase border border-primary/5">Accessibility</span>
                                <span class="px-3 py-1 bg-surface-container-low rounded-full text-[11px] font-bold text-primary uppercase border border-primary/5">Prototyping</span>
                            </div>
                        </section>
                    </div>
                    
                    <footer class="mt-auto pt-12 border-t border-primary/10 flex justify-center">
                        <span class="font-headline italic text-xs text-primary/60">1 of 1</span>
                    </footer>
                </div>

                <div class="absolute bottom-10 left-1/2 -translate-x-1/2 bg-tertiary/80 backdrop-blur-md border border-primary/10 px-6 py-3 rounded-full shadow-lg flex items-center gap-6">
                    <button class="flex items-center gap-2 text-primary/60 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">zoom_in</span>
                        <span class="text-xs font-bold uppercase tracking-widest">Zoom</span>
                    </button>
                    <div class="w-px h-4 bg-primary/20"></div>
                    <button class="flex items-center gap-2 text-primary/60 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">layers</span>
                        <span class="text-xs font-bold uppercase tracking-widest">Layout</span>
                    </button>
                    <div class="w-px h-4 bg-primary/20"></div>
                    <button class="flex items-center gap-2 text-primary/60 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">history</span>
                        <span class="text-xs font-bold uppercase tracking-widest">History</span>
                    </button>
                </div>
                
            </main>
        </div>
    </div>
</body>
</html>
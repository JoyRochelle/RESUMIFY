<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Resumify - Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&family=Manrope:wght@200..800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface text-primary font-body antialiased min-h-screen flex">
    
    @include('layouts.sidenavbar')

    <main class="flex-1 p-8 md:p-12 max-w-7xl mx-auto w-full">
        <header class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
            <div>
                <h1 class="text-5xl md:text-6xl font-headline text-primary tracking-tight leading-tight mb-4">Welcome, <br/>Eleanor Vance</h1>
                <div class="flex flex-wrap items-center gap-4">
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-secondary text-tertiary text-sm font-label font-semibold">
                        Premium Member
                    </span>
                    <span class="text-primary/60 font-label text-sm">AI Quota Remaining: <span class="serif-number font-bold text-primary">45</span>/50</span>
                </div>
            </div>
            
            <x-btn-create />

        </header>

        <section>
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl font-body font-medium text-primary tracking-wide">Your Resumes</h2>
                <div class="h-px flex-1 mx-6 bg-primary/10 hidden md:block"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="group bg-tertiary rounded-lg border border-primary/10 hover:shadow-[0_16px_32px_rgba(79,59,47,0.08)] transition-all duration-500 overflow-hidden flex flex-col cursor-pointer">
                    <div class="aspect-[3/4] bg-surface-container-low p-6 overflow-hidden">
                        <div class="bg-tertiary h-full w-full shadow-sm rounded-sm p-4 space-y-3 transform group-hover:scale-105 group-hover:rotate-1 transition-transform duration-500 origin-top border border-primary/5">
                            <div class="h-1.5 w-1/3 bg-primary/20 rounded-full"></div>
                            <div class="h-1.5 w-2/3 bg-primary/10 rounded-full"></div>
                            <div class="grid grid-cols-3 gap-2 py-4">
                                <div class="h-24 bg-surface border border-primary/10 rounded"></div>
                                <div class="col-span-2 space-y-2">
                                    <div class="h-1 w-full bg-primary/10 rounded-full"></div>
                                    <div class="h-1 w-full bg-primary/10 rounded-full"></div>
                                    <div class="h-1 w-5/6 bg-primary/10 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 bg-tertiary">
                        <h3 class="text-lg font-headline font-bold text-primary mb-1">Senior Product Designer</h3>
                        <p class="text-sm text-primary/60 font-label mb-6">Last edited: 2 days ago</p>
                        <div class="flex items-center justify-between">
                            <button class="text-secondary font-label font-bold text-sm hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-base" data-icon="edit">edit</span>
                                Edit
                            </button>
                            <button class="p-1 hover:bg-surface-container-low rounded-full transition-colors text-primary/60">
                                <span class="material-symbols-outlined" data-icon="more_vert">more_vert</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="group bg-tertiary rounded-lg border border-primary/10 hover:shadow-[0_16px_32px_rgba(79,59,47,0.08)] transition-all duration-500 overflow-hidden flex flex-col cursor-pointer">
                    <div class="aspect-[3/4] bg-surface-container-low p-6 overflow-hidden">
                        <div class="bg-tertiary h-full w-full shadow-sm rounded-sm p-4 space-y-3 transform group-hover:scale-105 group-hover:-rotate-1 transition-transform duration-500 origin-top border border-primary/5">
                            <div class="flex justify-between">
                                <div class="h-1.5 w-1/4 bg-primary/20 rounded-full"></div>
                                <div class="h-4 w-4 rounded-full bg-secondary/20"></div>
                            </div>
                            <div class="h-1.5 w-1/2 bg-primary/10 rounded-full"></div>
                            <div class="space-y-4 pt-4">
                                <div class="h-1.5 w-full bg-primary/10 rounded-full"></div>
                                <div class="h-1.5 w-full bg-primary/10 rounded-full"></div>
                                <div class="h-1.5 w-3/4 bg-primary/10 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 bg-tertiary">
                        <h3 class="text-lg font-headline font-bold text-primary mb-1">UX Research Lead</h3>
                        <p class="text-sm text-primary/60 font-label mb-6">Last edited: 1 week ago</p>
                        <div class="flex items-center justify-between">
                            <button class="text-secondary font-label font-bold text-sm hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-base" data-icon="edit">edit</span>
                                Edit
                            </button>
                            <button class="p-1 hover:bg-surface-container-low rounded-full transition-colors text-primary/60">
                                <span class="material-symbols-outlined" data-icon="more_vert">more_vert</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="group relative bg-surface-container-low/50 rounded-lg border-2 border-dashed border-primary/20 hover:border-primary/50 hover:bg-surface-container-low transition-all duration-500 overflow-hidden flex flex-col items-center justify-center min-h-[400px] cursor-pointer">
                    <div class="flex flex-col items-center text-center p-8">
                        <div class="w-16 h-16 rounded-full bg-tertiary flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                            <span class="material-symbols-outlined text-primary text-3xl" data-icon="add">add</span>
                        </div>
                        <p class="font-headline text-xl text-primary mb-2">Start New Manuscript</p>
                        <p class="text-sm text-primary/60 font-label max-w-[200px]">Create your professional career narrative in minutes.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-24 grid grid-cols-1 md:grid-cols-2 gap-12 border-t border-primary/10 pt-12">
            <div class="flex gap-6">
                <div class="text-5xl font-headline italic text-secondary/40 serif-number">01</div>
                <div>
                    <h4 class="font-label font-bold text-primary/70 uppercase tracking-widest text-xs mb-3">DAILY TIP</h4>
                    <p class="font-headline text-2xl text-primary leading-snug">"Use strong action verbs to give weight to your professional narrative."</p>
                </div>
            </div>
            <div class="flex gap-6">
                <div class="text-5xl font-headline italic text-secondary/40 serif-number">02</div>
                <div>
                    <h4 class="font-label font-bold text-primary/70 uppercase tracking-widest text-xs mb-3">AI Insight</h4>
                    <p class="font-headline text-2xl text-primary leading-snug">Your 'Senior Product Designer' resume has a <span class="text-secondary font-bold">92%</span> match for 2024 tech industry standards.</p>
                </div>
            </div>
        </section>
    </main>

    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-tertiary border-t border-primary/10 flex justify-around items-center py-3 px-6 z-50">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-primary/50 hover:text-primary' }} transition-colors">
            <span class="material-symbols-outlined" {!! request()->routeIs('dashboard') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>dashboard</span>
            <span class="text-[10px] font-label font-medium mt-1">Dashboard</span>
        </a>
        <a href="{{ route('manuscript') }}" class="flex flex-col items-center {{ request()->routeIs('manuscript') ? 'text-primary' : 'text-primary/50 hover:text-primary' }} transition-colors">
            <span class="material-symbols-outlined" {!! request()->routeIs('manuscript') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>description</span>
            <span class="text-[10px] font-label font-medium mt-1">Manuscripts</span>
        </a>
        <button class="flex flex-col items-center justify-center -translate-y-6 bg-primary text-tertiary w-14 h-14 rounded-full shadow-lg">
            <span class="material-symbols-outlined">add</span>
        </button>
        <button class="flex flex-col items-center text-primary/50">
            <span class="material-symbols-outlined">auto_fix_high</span>
            <span class="text-[10px] font-label font-medium mt-1">AI</span>
        </button>
        <button class="flex flex-col items-center text-primary/50">
            <span class="material-symbols-outlined">settings</span>
            <span class="text-[10px] font-label font-medium mt-1">Account</span>
        </button>
    </nav>
</body>
</html>
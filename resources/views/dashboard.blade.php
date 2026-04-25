<!DOCTYPE html>
<html class="light" lang="en">
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
                
                <x-resume-card title="Senior Product Designer" date="2 days ago" url="#" />
                <x-resume-card title="UX Research Lead" date="1 week ago" url="#" />

                <a href="#" class="group relative bg-surface-container-low/50 rounded-lg border-2 border-dashed border-primary/20 hover:border-primary/50 hover:bg-surface-container-low transition-all duration-500 overflow-hidden flex flex-col items-center justify-center min-h-[400px] cursor-pointer block">
                    <div class="flex flex-col items-center text-center p-8">
                        <div class="w-16 h-16 rounded-full bg-tertiary flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                            <span class="material-symbols-outlined text-primary text-3xl" data-icon="add">add</span>
                        </div>
                        <p class="font-headline text-xl text-primary mb-2">Start New Manuscript</p>
                        <p class="text-sm text-primary/60 font-label max-w-[200px]">Create your professional career narrative in minutes.</p>
                    </div>
                </a>
            </div>
        </section>

        <section class="mt-24 grid grid-cols-1 md:grid-cols-2 gap-12 border-t border-primary/10 pt-12">
            
            <x-insight-block number="01" label="DAILY TIP">
                "Use strong action verbs to give weight to your professional narrative."
            </x-insight-block>

            <x-insight-block number="02" label="AI Insight">
                Your 'Senior Product Designer' resume has a <span class="text-secondary font-bold">92%</span> match for 2024 tech industry standards.
            </x-insight-block>

        </section>
    </main>

    @include('layouts.mobilenavbar')

</body>
</html>
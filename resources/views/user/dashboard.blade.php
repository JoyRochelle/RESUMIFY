@extends('layouts.user.app')

@section('title', 'Resumify - Dashboard')

@section('content')
    <main class="flex-1 p-8 md:p-12 max-w-7xl mx-auto w-full">
        <header class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
            <div>
                <h1 class="text-5xl md:text-6xl font-headline text-primary tracking-tight leading-tight mb-4">Welcome, <br/>{{ auth()->user()->name }}</h1>
                <div class="flex flex-wrap items-center gap-4">
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-secondary text-tertiary text-sm font-label font-semibold">
                        {{ auth()->user()->isPremium() ? 'Premium Member' : 'Basic Member' }}
                    </span>
                    <span class="text-primary/60 font-label text-sm">AI Quota Remaining: <span class="serif-number font-bold text-primary">{{ auth()->user()->getQuotaRemaining() }}</span>/{{ auth()->user()->getQuotaLimit() }}</span>
                </div>
            </div>
            
            <x-user.btn-create />
        </header>

        <section>
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl font-body font-medium text-primary tracking-wide">Your Resumes</h2>
                <div class="h-px flex-1 mx-6 bg-primary/10 hidden md:block"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <x-user.resume-card title="Senior Product Designer" date="2 days ago" url="{{ route('user.manuscript') }}" />
                <x-user.resume-card title="UX Research Lead" date="1 week ago" url="{{ route('user.manuscript') }}" />

                <a href="{{ route('resumes.create') }}" class="group relative bg-surface-container-low/50 rounded-lg border-2 border-dashed border-primary/20 hover:border-primary/50 hover:bg-surface-container-low transition-all duration-500 overflow-hidden flex flex-col items-center justify-center min-h-[400px] cursor-pointer block">
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
            
            <x-user.insight-block number="01" label="DAILY TIP">
                "Use strong action verbs to give weight to your professional narrative."
            </x-user.insight-block>

            <x-user.insight-block number="02" label="AI Insight">
                Your 'Senior Product Designer' resume has a <span class="text-secondary font-bold">92%</span> match for 2024 tech industry standards.
            </x-user.insight-block>

        </section>
    </main>
@endsection

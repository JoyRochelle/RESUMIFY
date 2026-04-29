<section
    class="hidden md:flex w-full md:w-3/5 bg-surface-container-low flex-col justify-center items-center px-12 lg:px-24 relative overflow-hidden transition-auth-left">
    {{-- Background Decorative Element --}}
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-fixed rounded-full blur-3xl opacity-20"></div>
    <div>
        <header>
            <h1 class="text-4xl lg:text-5xl font-headline text-on-surface tracking-tight leading-tight">
                One Account, One Adapted Career ✨
            </h1>
            <p class="mt-4 text-lg text-on-surface-variant font-body">
                Build professional resumes and boost your ATS score in minutes.</p>
            </p>
        </header>

        {{-- Resume Visualization --}}
        <div
            class="relative bg-surface-container-lowest rounded-xl shadow-2xl p-8 border border-outline-variant/20 trasform -rotate-1 hover:rotate-0 transition-transform duration-500 mt-8">
            <div class=" border-surface-variant pb-6 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-3xl font-headline font-bold text-primary">Eleanor Vance</h2>
                        <p class="text-secondary font-medium tracking-wide uppercase text-xs mt-1">Senior Product
                            Architect</p>
                    </div>
                    <div class="text-right text-xs text-on-surface-variant space-y-1">
                        <p>vance.eleanor@email.com</p>
                        <p>San Francisco, cA</p>
                    </div>
                </div>
                {{-- Resume Skeleton Content --}}
                <div class="space-y-6">
                    <div class="space-y-2">
                        <div class="h-3 w-24 bg-surface-container rounded-full"></div>
                        <div class="h-2 w-full bg-surface-container-low rounded-full"></div>
                        <div class="h-2 w-5/6 bg-surface-container-low rounded-full"></div>
                    </div>
                    <div class="space-y-4">
                        <div class="h-3 w-32 bg-surface-container rounded-full"></div>
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 rounded-lg bg-surface-container"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-2 w-full bg-surface-container-low rounded-full"></div>
                                <div class="h-2 w-2/3 bg-surface-container-low rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ATS Match Score Graphic --}}
                <div
                    class="absolute -right-8 -bottom-4 glass-panel p-6 rounded-xl shadow-xl w-48 animate-bounce-subtle">
                    <div class="flex justify-between items-end mb-3">
                        <span class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">ATS
                            MATCH</span>
                        <span class="text-2xl font-headline font-bold text-secondary">94%</span>
                    </div>
                    <div class="h-2 w-full bg-surface-container rounded-full overflow-hidden">
                        <div class="h-full bg-secondary w-[94%] shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="material-symbols-outlined w-6 h-6 text-emerald-500" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                        <span class="text-[10px] text-on-surface-variant font-medium">AI Optimization Active</span>
                    </div>
                </div>
            </div>
        </div>
        {{-- Trust Badges --}}
        <div
            class="justify-center mt-16 lg:mt-20 flex items-center gap-8 opacity-40 grayscale hover:grayscale-0 transition-all duration-300">
            <div class="h-8 w-24 bg-on-surface-variant/20 rounded"></div>
            <div class="h-8 w-20 bg-on-surface-variant/20 rounded"></div>
            <div class="h-8 w-28 bg-on-surface-variant/20 rounded"></div>
        </div>
    </div>
    </div>
</section>

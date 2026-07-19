<section
    class="hidden md:flex w-full md:w-3/5 bg-surface-container-low flex-col justify-center items-center px-12 lg:px-16 relative overflow-hidden transition-auth-left">
    {{-- Background Decorative Element --}}
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-fixed rounded-full blur-3xl opacity-20" aria-hidden="true"></div>

    <div class="relative w-full max-w-2xl">
        <header class="animate-fade-up">
            <h1 class="text-4xl lg:text-5xl font-headline font-bold text-on-surface tracking-tight leading-tight">
                {{ __('messages.auth.marketing.headline') }}
            </h1>
            <p class="mt-4 text-lg text-on-surface-variant font-body">
                {{ __('messages.auth.marketing.subtitle') }}
            </p>
        </header>

        {{-- Resume Visualization: same persona as the landing hero --}}
        <div class="relative mt-10 bg-surface-container-lowest rounded-lg shadow-2xl p-8 border border-outline-variant/20 -rotate-1 hover:rotate-0 transition-transform duration-300 ease-out animate-fade-up"
             style="animation-delay: 150ms">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-3xl font-headline font-bold text-primary">Theofrolic</h2>
                    <p class="text-secondary font-bold tracking-widest uppercase text-xs mt-1">Senior Product Designer</p>
                </div>
                <div class="text-right text-xs text-on-surface-variant space-y-1">
                    <p>theofrolic@resumify.ai</p>
                    <p>Jakarta, Indonesia</p>
                </div>
            </div>

            {{-- Resume Skeleton Content --}}
            <div class="space-y-6 pb-4">
                <div class="space-y-2">
                    <div class="h-3 w-24 bg-primary/10 rounded-full"></div>
                    <div class="h-2 w-full bg-primary/5 rounded-full"></div>
                    <div class="h-2 w-5/6 bg-primary/5 rounded-full"></div>
                </div>
                <div class="space-y-4">
                    <div class="h-3 w-32 bg-primary/10 rounded-full"></div>
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 rounded-lg bg-primary/10"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-2 w-full bg-primary/5 rounded-full"></div>
                            <div class="h-2 w-2/3 bg-primary/5 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ATS Match Score Graphic --}}
            <div class="absolute -right-8 -bottom-6 glass-panel p-5 rounded-lg shadow-xl w-48 rotate-1 animate-fade-up"
                 style="animation-delay: 300ms">
                <div class="flex justify-between items-end mb-3">
                    <span class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">{{ __('messages.auth.marketing.ats_match') }}</span>
                    <span class="text-2xl font-headline font-bold text-secondary"><span data-count-up="92">92</span>%</span>
                </div>
                <div class="h-2 w-full bg-primary/10 rounded-full overflow-hidden">
                    <div data-anime-bar class="h-full w-[92%] origin-left rounded-full bg-secondary"></div>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-secondary icon-filled" aria-hidden="true">auto_awesome</span>
                    <span class="text-[10px] text-on-surface-variant font-medium">{{ __('messages.auth.marketing.ai_optimization') }}</span>
                </div>
            </div>
        </div>

        {{-- Reassurance points --}}
        <ul class="mt-16 flex flex-wrap items-center justify-center gap-x-8 gap-y-3 animate-fade-up" style="animation-delay: 400ms">
            @foreach (['ats_friendly', 'bilingual', 'free'] as $point)
                <li class="flex items-center gap-2 text-sm text-on-surface-variant font-body">
                    <span class="material-symbols-outlined text-[16px] text-secondary" aria-hidden="true">check</span>
                    {{ __('messages.auth.marketing.points.' . $point) }}
                </li>
            @endforeach
        </ul>
    </div>
</section>

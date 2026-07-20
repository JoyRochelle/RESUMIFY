<main id="ats-panel-results"
                role="tabpanel"
                aria-labelledby="ats-tab-results"
                class="w-full lg:w-[58%] lg:h-full bg-primary/[0.03] p-4 lg:p-8 lg:overflow-y-auto custom-scrollbar hidden lg:block">

                {{-- Empty state --}}
                <div id="ats-empty-state"
                    class="h-full flex flex-col items-center justify-center text-center py-20 lg:py-0">
                    <div class="w-24 h-24 rounded-full bg-secondary/10 flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-secondary text-4xl icon-filled">analytics</span>
                    </div>
                    <h3 class="font-headline text-2xl text-primary mb-2">{{ __('messages.ats.results.select_analyze_heading') }}</h3>
                    <p class="text-primary/50 text-sm max-w-xs leading-relaxed">
                        {{ __('messages.ats.results.select_analyze_before') }} <strong class="text-primary/70">{{ __('messages.ats.analyze_match') }}</strong>
                        {{ __('messages.ats.results.select_analyze_after') }}
                    </p>
                </div>

                {{-- Resume preview (shown when a resume is selected, before analysis) --}}
                <div id="ats-resume-preview" style="display:none" class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-primary text-sm">
                            <span id="ats-preview-title">{{ __('messages.ats.results.resume_preview') }}</span>
                        </h3>
                        <span class="text-[10px] font-label text-primary/40 uppercase tracking-widest">{{ __('messages.ats.results.click_to_score', ['action' => __('messages.ats.analyze_match')]) }}</span>
                    </div>
                    <div id="ats-preview-container"
                        class="relative w-full bg-white rounded-xl border border-primary/10 shadow-sm overflow-hidden"
                        style="aspect-ratio: 210/297;">
                        <iframe id="ats-preview-iframe"
                            scrolling="no"
                            style="width: 794px; height: 1123px; transform-origin: top left; border: none; position: absolute; top: 0; left: 0; pointer-events: none;"
                            loading="lazy"></iframe>
                    </div>
                </div>

                {{-- Results (hidden until analysis) --}}
                <div id="ats-results" class="hidden space-y-6 max-w-3xl mx-auto">

                    {{-- Score row --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                        {{-- Score circle card --}}
                        <div
                            class="bg-tertiary rounded-2xl p-7 border border-primary/10 shadow-sm flex flex-col items-center justify-center text-center">
                            <div id="score-circle-container" class="mb-4"></div>
                            <h4 id="score-rating-label" class="font-bold text-lg text-secondary"></h4>
                            <p id="score-rating-sub" class="text-primary/50 text-xs mt-1"></p>
                        </div>

                        {{-- Missing keywords card --}}
                        <div
                            class="bg-tertiary rounded-2xl p-5 border border-primary/10 shadow-sm md:col-span-2 flex flex-col max-h-[320px]">
                            <div class="flex items-center gap-2 mb-4 text-red-500 shrink-0">
                                <h4 class="font-bold tracking-tight text-xs uppercase">{{ __('messages.ats.results.missing_keywords') }}</h4>
                                <span id="missing-count-badge"
                                    class="ml-auto text-[10px] font-bold bg-red-500/10 text-red-500 px-2 py-0.5 rounded-full"></span>
                            </div>
                            <div class="overflow-y-auto custom-scrollbar flex-1 pr-2">
                                <div id="missing-keywords-container" class="flex flex-col gap-2 min-h-[2rem]"></div>
                            </div>
                            <p id="missing-keywords-tip"
                                class="text-xs text-primary/60 leading-relaxed italic mt-3 shrink-0"></p>
                        </div>
                    </div>

                    {{-- Matched Keywords --}}
                    <div class="bg-tertiary rounded-2xl p-5 border border-primary/10 shadow-sm">
                        <div class="flex items-center gap-2 mb-4 text-secondary">
                            <h4 class="font-bold tracking-tight text-xs uppercase">{{ __('messages.ats.results.matched_keywords') }}</h4>
                            <span id="matched-count-badge"
                                class="ml-auto text-[10px] font-bold bg-secondary/10 text-secondary px-2 py-0.5 rounded-full"></span>
                        </div>
                        <div id="matched-keywords-container" class="flex flex-wrap gap-2 min-h-[2rem]"></div>
                    </div>

                    {{-- Action Verbs --}}
                    <div class="bg-tertiary rounded-2xl p-5 border border-primary/10 shadow-sm">
                        <div class="flex items-center gap-2 mb-4 text-primary/70">
                            <h4 class="font-bold tracking-tight text-xs uppercase">{{ __('messages.ats.results.action_verbs_detected') }}</h4>
                        </div>
                        <div id="action-verbs-container" class="flex flex-wrap gap-2 mb-3 min-h-[2rem]"></div>
                        <div id="missing-verbs-row" class="hidden mt-3 pt-3 border-t border-primary/5">
                            <p class="text-[11px] text-primary/50 mb-2 uppercase tracking-wider font-bold">{{ __('messages.ats.results.consider_adding') }}
                            </p>
                            <div id="missing-verbs-container" class="flex flex-wrap gap-2"></div>
                        </div>
                    </div>

                    {{-- Length & Format tip --}}
                    <div id="length-tip-card" class="flex items-start gap-4 rounded-2xl p-5 border">
                        <span id="length-tip-icon" class="material-symbols-outlined text-[22px] shrink-0 mt-0.5"></span>
                        <div>
                            <p id="length-tip-title" class="text-xs font-bold uppercase tracking-wider mb-1"></p>
                            <p id="length-tip-body" class="text-sm text-primary/70 leading-relaxed"></p>
                        </div>
                    </div>

                    {{-- Section Breakdown --}}
                    <section class="rounded-2xl p-7 border border-primary/10 bg-tertiary">
                        <div class="flex items-center gap-2 mb-5 text-primary">
                            <h4 class="font-bold tracking-tight text-sm uppercase">{{ __('messages.ats.results.section_breakdown') }}</h4>
                        </div>
                        <div id="section-breakdown-container" class="grid grid-cols-1 gap-3"></div>
                    </section>

                    {{-- Strategic Insights --}}
                    <section
                        class="rounded-2xl p-7 border border-secondary/20 bg-secondary/[0.04] relative overflow-hidden">
                        <div
                            class="absolute -top-12 -right-12 w-48 h-48 bg-secondary/10 blur-3xl rounded-full pointer-events-none">
                        </div>
                        <div class="mb-6">
                            <h3 class="font-headline text-xl font-bold text-primary">{{ __('messages.ats.results.strategic_insights') }}</h3>
                        </div>
                        <div id="insights-container" class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5"></div>
                    </section>

                    {{-- Re-analyze nudge --}}
                    <p class="text-center text-xs text-primary/30 pb-4">
                        {{ __('messages.ats.results.reanalyze_before') }} <span class="font-bold text-primary/50">{{ __('messages.ats.analyze_match') }}</span>
                        {{ __('messages.ats.results.reanalyze_after') }}
                    </p>
                </div>
            </main>
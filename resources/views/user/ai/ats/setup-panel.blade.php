<aside id="ats-panel-setup"
                role="tabpanel"
                aria-labelledby="ats-tab-setup"
                class="w-full lg:w-[42%] bg-surface-container-low flex flex-col border-b lg:border-b-0 lg:border-r border-primary/10 z-20 shrink-0 lg:h-full">
                <div class="p-4 lg:p-6 lg:overflow-y-auto custom-scrollbar space-y-5 lg:h-full">

                    {{-- Select CV --}}
                    @if (isset($cvs) && $cvs->isNotEmpty())
                        <div class="bg-tertiary rounded-xl p-5 border border-primary/10 shadow-sm flex flex-col gap-3">
                            <label for="cv-selector" class="font-bold text-primary flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-primary/60 text-[18px]">folder_open</span>
                                Select from your Resumes
                            </label>
                            <select id="cv-selector"
                                class="w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none p-3 text-sm transition-all duration-200">
                                <option value="">-- Choose a Resume --</option>
                                @foreach ($cvs as $cv)
                                    <option value="{{ $cv->id }}" data-sections="{{ json_encode($cv->sections) }}">
                                        {{ $cv->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Resume input (Hidden, populated by CV selector) --}}
                    <input type="hidden" id="resume-input" value="">

                    {{-- Target Job section (accordion style, matches manuscript editor) --}}
                    <x-user.editor-accordion title="Target Job" icon="target" :isOpen="true">
                        <div class="grid grid-cols-1 gap-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <x-ui.form-input
                                    id="ats-job-title"
                                    label="Target Job Title"
                                    name="job_title"
                                    value=""
                                    placeholder="e.g. Senior Software Engineer" />
                                <x-ui.form-input
                                    id="ats-job-company"
                                    label="Target Company"
                                    name="job_company"
                                    value=""
                                    placeholder="e.g. Acme Corp" />
                            </div>
                <div class="relative group mt-2">
                                <label for="jd-input" class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Job Description</label>
                                <textarea id="jd-input" rows="6"
                                    placeholder="Paste the job description here to see how well your resume matches..."
                                    class="w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-0 p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30"></textarea>
                            </div>
                            <p class="text-xs text-primary/40 -mt-2 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">info</span>
                                Auto-filled from your resume's target job. You can edit before analyzing.
                            </p>
                        </div>
                    </x-user.editor-accordion>

                    {{-- Analyze Button --}}
                    @if($user->canUsePremiumFeature('ats_analyze'))
                        <button id="analyze-btn" type="button"
                            class="w-full py-4 rounded-xl font-bold text-sm tracking-wide shadow-lg bg-primary text-tertiary hover:bg-primary/90 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-3 group focus:outline-none focus:ring-2 focus:ring-secondary/40">
                            <span id="analyze-btn-label">Analyze Match</span>
                            <span id="analyze-spinner" style="display:none"
                                class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                            <span id="analyze-icon"
                                class="material-symbols-outlined text-tertiary/80 group-hover:rotate-12 transition-transform icon-filled text-[20px]">auto_awesome</span>
                        </button>
                    @else
                        <x-user.premium-lock
                            title="ATS Analyzer"
                            description="Premium unlocks full resume-to-job matching, missing keywords, and prioritized improvement guidance."
                            align="left"
                            class="w-full">
                            <span class="flex w-full min-h-11 items-center justify-center gap-3 rounded-xl border border-[#A16207]/25 bg-[#A16207]/10 px-4 py-4 text-sm font-bold tracking-wide text-[#7C4A03] shadow-sm">
                                <span class="material-symbols-outlined icon-filled text-[20px]" aria-hidden="true">lock</span>
                                Analyze Match
                                <span class="text-xs uppercase tracking-widest">Premium</span>
                            </span>
                        </x-user.premium-lock>
                    @endif

                    {{-- Error banner --}}
                    <div id="ats-error"
                        class="hidden bg-red-500/10 text-red-600 border border-red-500/20 rounded-xl p-4 text-sm flex items-start gap-3">
                        <span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">error_outline</span>
                        <span id="ats-error-msg"></span>
                    </div>

                    {{-- ─── Scan History ─────────────────────────────────── --}}
                    @if (isset($history) && $history->isNotEmpty())
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-primary/70 text-xs uppercase tracking-widest flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[15px]">history</span>
                                    Scan History
                                </h3>
                                <span class="text-[10px] text-primary/40">{{ $history->count() }} recent scan{{ $history->count() > 1 ? 's' : '' }}</span>
                            </div>
                            <div id="ats-history-list" class="flex flex-col gap-2">
                                @foreach ($history as $scan)
                                    @php
                                        $scoreColor = $scan->score >= 70 ? 'text-secondary bg-secondary/10 border-secondary/20'
                                            : ($scan->score >= 50 ? 'text-yellow-600 bg-yellow-500/10 border-yellow-500/20'
                                            : 'text-red-500 bg-red-500/10 border-red-500/20');
                                    @endphp
                                    <div id="history-card-{{ $scan->id }}"
                                        class="history-card group flex items-center gap-2 bg-tertiary border border-primary/10 hover:border-primary/25 rounded-xl p-2 transition-all duration-200">
                                        <button type="button"
                                            class="flex min-w-0 flex-1 items-center gap-3 rounded-lg p-1 text-left focus:outline-none focus:ring-2 focus:ring-secondary/40"
                                            onclick="loadHistoryResult('{{ $scan->id }}', this.closest('.history-card'))"
                                            aria-label="Load scan {{ $scan->job_title ?: 'Untitled Scan' }}">

                                        {{-- Score badge --}}
                                        <div class="shrink-0 w-11 h-11 rounded-lg border flex flex-col items-center justify-center {{ $scoreColor }}">
                                            <span class="font-headline font-bold text-sm leading-none">{{ $scan->score ?? '?' }}</span>
                                            <span class="text-[9px] font-bold uppercase tracking-wider opacity-70">pts</span>
                                        </div>

                                        {{-- Info --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-primary truncate leading-tight">
                                                {{ $scan->job_title ?: 'Untitled Scan' }}
                                                @if($scan->job_company)
                                                    <span class="font-normal text-primary/50">@ {{ $scan->job_company }}</span>
                                                @endif
                                            </p>
                                            <p class="text-[11px] text-primary/40 mt-0.5 truncate">
                                                {{ $scan->cv?->title ?? 'No resume linked' }}
                                                · {{ $scan->created_at->diffForHumans() }}
                                            </p>
                                        </div>

                                        </button>

                                        {{-- Delete button --}}
                                        <button type="button" title="Delete"
                                            aria-label="Delete scan {{ $scan->job_title ?: 'Untitled Scan' }}"
                                            onclick="deleteHistoryScan('{{ $scan->id }}')"
                                            class="shrink-0 opacity-0 group-hover:opacity-100 text-primary/30 hover:text-red-500 transition-all duration-200 rounded-lg p-2 hover:bg-red-500/10 focus:opacity-100 focus:outline-none focus:ring-2 focus:ring-red-300">
                                            <span class="material-symbols-outlined text-[17px]" aria-hidden="true">delete</span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        {{-- Empty history state (hidden once items are prepended by JS) --}}
                        <div id="ats-history-list" class="flex flex-col gap-2">
                            <div id="ats-history-empty" class="flex items-center gap-2 text-primary/30 text-xs italic py-2">
                                <span class="material-symbols-outlined text-[15px]">history</span>
                                No scan history yet — run your first analysis above.
                            </div>
                        </div>
                    @endif
                </div>
            </aside>
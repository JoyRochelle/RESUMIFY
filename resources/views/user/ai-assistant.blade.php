@extends('layouts.user.app')

@section('title', 'Resumify — ATS Analyzer')
@section('content')
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Page Header --}}
        <x-user.page-header title="ATS Analyzer">
            <button id="ats-instructions-btn" type="button"
                    class="flex items-center gap-2 text-sm px-4 py-2 rounded-lg border border-primary/15 text-primary/70 hover:text-primary hover:border-primary/30 transition-all duration-200">
                <span class="material-symbols-outlined text-[16px]">info</span>
                How It Works
            </button>
        </x-user.page-header>

        <div class="flex-1 flex flex-col lg:flex-row overflow-y-auto lg:overflow-hidden pb-20 lg:pb-0">

            {{-- ════════════════ LEFT PANEL — INPUTS ════════════════ --}}
            <aside class="w-full lg:w-[42%] bg-surface-container-low flex flex-col border-b lg:border-b-0 lg:border-r border-primary/10 z-20 shrink-0 lg:h-full">
                <div class="p-4 lg:p-6 lg:overflow-y-auto custom-scrollbar space-y-5 lg:h-full">

                    {{-- Resume input --}}
                    <div class="bg-tertiary rounded-xl p-5 border border-primary/10 shadow-sm flex flex-col gap-3">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-primary/60 text-[18px]">description</span>
                                Your Resume
                            </h3>
                            <span id="resume-word-count" class="text-[10px] font-label text-primary/40 uppercase tracking-wider">0 words</span>
                        </div>
                        <textarea id="resume-input"
                                  class="w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none p-4 text-sm leading-relaxed custom-scrollbar resize-none transition-all duration-200"
                                  placeholder="Paste your full resume text here…"
                                  rows="10"></textarea>
                    </div>

                    {{-- Job Description input --}}
                    <div class="bg-tertiary rounded-xl p-5 border border-primary/10 shadow-sm flex flex-col gap-3">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-primary/60 text-[18px]">target</span>
                                Job Description
                            </h3>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-primary/40">Target Role</span>
                        </div>
                        <textarea id="jd-input"
                                  class="w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none p-4 text-sm leading-relaxed custom-scrollbar resize-none transition-all duration-200"
                                  placeholder="Paste the job description here…"
                                  rows="10"></textarea>
                    </div>

                    {{-- Analyze Button --}}
                    <button id="analyze-btn" type="button"
                            class="w-full py-4 rounded-xl font-bold text-sm tracking-wide shadow-lg bg-primary text-tertiary hover:bg-primary/90 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-3 group">
                        <span id="analyze-btn-label">Analyze Match</span>
                        <span id="analyze-spinner" class="hidden material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                        <span id="analyze-icon" class="material-symbols-outlined text-tertiary/80 group-hover:rotate-12 transition-transform icon-filled text-[20px]">auto_awesome</span>
                    </button>

                    {{-- Error banner --}}
                    <div id="ats-error" class="hidden bg-red-500/10 text-red-600 border border-red-500/20 rounded-xl p-4 text-sm flex items-start gap-3">
                        <span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">error_outline</span>
                        <span id="ats-error-msg"></span>
                    </div>
                </div>
            </aside>

            {{-- ════════════════ RIGHT PANEL — RESULTS ════════════════ --}}
            <main class="w-full lg:w-[58%] lg:h-full bg-primary/[0.03] p-4 lg:p-8 lg:overflow-y-auto custom-scrollbar">

                {{-- Empty state --}}
                <div id="ats-empty-state" class="h-full flex flex-col items-center justify-center text-center py-20 lg:py-0">
                    <div class="w-24 h-24 rounded-full bg-secondary/10 flex items-center justify-center mb-6 animate-pulse-slow">
                        <span class="material-symbols-outlined text-secondary text-4xl icon-filled">analytics</span>
                    </div>
                    <h3 class="font-headline text-2xl text-primary mb-2">Paste & Analyze</h3>
                    <p class="text-primary/50 text-sm max-w-xs leading-relaxed">
                        Add your resume and a job description on the left, then click <strong class="text-primary/70">Analyze Match</strong> to see your ATS score and actionable recommendations.
                    </p>
                </div>

                {{-- Results (hidden until analysis) --}}
                <div id="ats-results" class="hidden space-y-6 max-w-3xl mx-auto">

                    {{-- Score row --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                        {{-- Score circle card --}}
                        <div class="bg-tertiary rounded-2xl p-7 border border-primary/10 shadow-sm flex flex-col items-center justify-center text-center">
                            <div id="score-circle-container" class="mb-4"></div>
                            <h4 id="score-rating-label" class="font-bold text-lg text-secondary"></h4>
                            <p  id="score-rating-sub"   class="text-primary/50 text-xs mt-1"></p>
                        </div>

                        {{-- Missing keywords card --}}
                        <div class="bg-tertiary rounded-2xl p-5 border border-primary/10 shadow-sm md:col-span-2">
                            <div class="flex items-center gap-2 mb-4 text-red-500">
                                <span class="material-symbols-outlined text-[18px]">error_outline</span>
                                <h4 class="font-bold tracking-tight text-xs uppercase">Missing Keywords</h4>
                                <span id="missing-count-badge"
                                      class="ml-auto text-[10px] font-bold bg-red-500/10 text-red-500 px-2 py-0.5 rounded-full"></span>
                            </div>
                            <div id="missing-keywords-container" class="flex flex-wrap gap-2 mb-3 min-h-[2rem]"></div>
                            <p id="missing-keywords-tip" class="text-xs text-primary/60 leading-relaxed italic"></p>
                        </div>
                    </div>

                    {{-- Matched Keywords --}}
                    <div class="bg-tertiary rounded-2xl p-5 border border-primary/10 shadow-sm">
                        <div class="flex items-center gap-2 mb-4 text-secondary">
                            <span class="material-symbols-outlined text-[18px] icon-filled">check_circle</span>
                            <h4 class="font-bold tracking-tight text-xs uppercase">Matched Keywords</h4>
                            <span id="matched-count-badge"
                                  class="ml-auto text-[10px] font-bold bg-secondary/10 text-secondary px-2 py-0.5 rounded-full"></span>
                        </div>
                        <div id="matched-keywords-container" class="flex flex-wrap gap-2 min-h-[2rem]"></div>
                    </div>

                    {{-- Action Verbs --}}
                    <div class="bg-tertiary rounded-2xl p-5 border border-primary/10 shadow-sm">
                        <div class="flex items-center gap-2 mb-4 text-primary/70">
                            <span class="material-symbols-outlined text-[18px]">bolt</span>
                            <h4 class="font-bold tracking-tight text-xs uppercase">Action Verbs Detected</h4>
                        </div>
                        <div id="action-verbs-container" class="flex flex-wrap gap-2 mb-3 min-h-[2rem]"></div>
                        <div id="missing-verbs-row" class="hidden mt-3 pt-3 border-t border-primary/5">
                            <p class="text-[11px] text-primary/50 mb-2 uppercase tracking-wider font-bold">Consider Adding</p>
                            <div id="missing-verbs-container" class="flex flex-wrap gap-2"></div>
                        </div>
                    </div>

                    {{-- Length & Format tip --}}
                    <div id="length-tip-card" class="flex items-start gap-4 rounded-2xl p-5 border">
                        <span id="length-tip-icon" class="material-symbols-outlined text-[22px] shrink-0 mt-0.5"></span>
                        <div>
                            <p id="length-tip-title" class="text-xs font-bold uppercase tracking-wider mb-1"></p>
                            <p id="length-tip-body"  class="text-sm text-primary/70 leading-relaxed"></p>
                        </div>
                    </div>

                    {{-- Strategic Insights --}}
                    <section class="rounded-2xl p-7 border border-secondary/20 bg-secondary/[0.04] relative overflow-hidden">
                        <div class="absolute -top-12 -right-12 w-48 h-48 bg-secondary/10 blur-3xl rounded-full pointer-events-none"></div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="material-symbols-outlined text-secondary icon-filled">auto_awesome</span>
                            <h3 class="font-headline text-xl font-bold text-primary">Strategic Insights</h3>
                        </div>
                        <div id="insights-container" class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5"></div>
                    </section>

                    {{-- Re-analyze nudge --}}
                    <p class="text-center text-xs text-primary/30 pb-4">
                        Update your texts and click <span class="font-bold text-primary/50">Analyze Match</span> again to see your new score.
                    </p>
                </div>
            </main>
        </div>
    </div>

    {{-- ── How It Works modal ─────────────────────────────────────── --}}
    <div id="instructions-modal"
         class="fixed inset-0 bg-surface/80 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4">
        <div class="bg-tertiary w-full max-w-lg rounded-2xl shadow-2xl border border-primary/10 transform scale-95 transition-transform duration-300 overflow-hidden" id="instructions-content">
            <div class="p-6 border-b border-primary/10 flex justify-between items-center bg-surface-container-low">
                <h3 class="font-headline text-xl font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">info</span>
                    How ATS Scoring Works
                </h3>
                <button onclick="closeInstructions()"
                        class="text-primary/50 hover:text-primary material-symbols-outlined rounded-full p-1 hover:bg-primary/5 transition-colors">close</button>
            </div>
            <div class="p-6 space-y-4 text-sm text-primary/80 leading-relaxed">
                <p>Our ATS analyzer mimics how Applicant Tracking Systems evaluate your resume against a job description.</p>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-secondary text-[18px] shrink-0 mt-0.5 icon-filled">check_circle</span>
                        <span><strong class="text-primary">Keyword Match (65%)</strong> — We extract critical single-word and multi-word terms from the JD and check how many appear in your resume.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-secondary text-[18px] shrink-0 mt-0.5 icon-filled">check_circle</span>
                        <span><strong class="text-primary">Action Verbs (15%)</strong> — Strong, impactful verbs signal an achievement-oriented candidate to ATS parsers.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-secondary text-[18px] shrink-0 mt-0.5 icon-filled">check_circle</span>
                        <span><strong class="text-primary">Quantification (12%)</strong> — Numbers and percentages dramatically improve relevancy scores in most ATS systems.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-secondary text-[18px] shrink-0 mt-0.5 icon-filled">check_circle</span>
                        <span><strong class="text-primary">Length &amp; Format (8%)</strong> — Resumes between 200–800 words are parsed most reliably by automated systems.</span>
                    </li>
                </ul>
                <p class="text-primary/50 text-xs pt-2">Tip: The closer your resume's language mirrors the job description, the higher your match score will be.</p>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.45s ease both; }
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.5; }
        }
        .animate-pulse-slow { animation: pulse-slow 2.5s ease-in-out infinite; }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .animate-spin { animation: spin 0.8s linear infinite; }

        /* Score circle animation */
        #score-circle-container circle.progress-ring {
            transition: stroke-dashoffset 1s ease;
        }
    </style>

    <script>
    // ── Utility ──────────────────────────────────────────────────────────────

    function wordCount(text) {
        return text.trim() ? text.trim().split(/\s+/).length : 0;
    }

    function makeDangerTag(text) {
        return `<span class="px-3 py-1.5 rounded-full text-xs font-bold border bg-red-500/10 text-red-600 border-red-500/20">${escHtml(text)}</span>`;
    }
    function makeSuccessTag(text) {
        return `<span class="px-3 py-1.5 rounded-full text-xs font-bold border bg-secondary/10 text-secondary border-secondary/20">${escHtml(text)}</span>`;
    }
    function makeNeutralTag(text) {
        return `<span class="px-3 py-1.5 rounded-full text-xs font-bold border bg-primary/5 text-primary/60 border-primary/10">${escHtml(text)}</span>`;
    }
    function escHtml(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    // ── Word count live update ────────────────────────────────────────────────

    document.getElementById('resume-input').addEventListener('input', function () {
        document.getElementById('resume-word-count').textContent = wordCount(this.value) + ' words';
    });

    // ── Build score circle SVG ────────────────────────────────────────────────

    function buildScoreCircle(score) {
        const r           = 56;
        const center      = 64;
        const strokeW     = 8;
        const circ        = +(2 * Math.PI * r).toFixed(1);
        const offset      = +((1 - score / 100) * circ).toFixed(1);
        const colorClass  = score >= 70 ? 'text-secondary' : score >= 50 ? 'text-yellow-500' : 'text-red-500';

        return `
        <div class="relative w-32 h-32 flex items-center justify-center">
            <svg class="w-full h-full -rotate-90">
                <circle class="text-primary/10" cx="${center}" cy="${center}" r="${r}"
                        fill="transparent" stroke="currentColor" stroke-width="${strokeW}"></circle>
                <circle class="progress-ring ${colorClass}" cx="${center}" cy="${center}" r="${r}"
                        fill="transparent" stroke="currentColor"
                        stroke-dasharray="${circ}" stroke-dashoffset="${circ}"
                        stroke-linecap="round" stroke-width="${strokeW}"
                        data-target="${offset}"></circle>
            </svg>
            <span class="absolute font-headline text-3xl font-bold text-primary italic">${score}%</span>
        </div>`;
    }

    function animateCircle(container) {
        const circle = container.querySelector('.progress-ring');
        if (!circle) return;
        const target = parseFloat(circle.dataset.target);
        // trigger reflow then set final value
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                circle.style.transition = 'stroke-dashoffset 1.1s cubic-bezier(.4,0,.2,1)';
                circle.style.strokeDashoffset = target;
            });
        });
    }

    // ── Render results ────────────────────────────────────────────────────────

    function renderResults(data) {
        // Show results panel
        document.getElementById('ats-empty-state').classList.add('hidden');
        const resultsEl = document.getElementById('ats-results');
        resultsEl.classList.remove('hidden');

        // ─ Score circle
        const scoreContainer = document.getElementById('score-circle-container');
        scoreContainer.innerHTML = buildScoreCircle(data.score);
        animateCircle(scoreContainer);

        // ─ Rating
        const ratingColorMap = { success: 'text-secondary', warning: 'text-yellow-500', danger: 'text-red-500' };
        document.getElementById('score-rating-label').textContent   = data.rating.label;
        document.getElementById('score-rating-label').className     = `font-bold text-lg ${ratingColorMap[data.rating.color] ?? 'text-secondary'}`;
        document.getElementById('score-rating-sub').textContent     = data.rating.sublabel;

        // ─ Missing keywords
        const missingContainer = document.getElementById('missing-keywords-container');
        document.getElementById('missing-count-badge').textContent = data.missing.length + ' missing';
        if (data.missing.length === 0) {
            missingContainer.innerHTML = `<span class="text-xs text-secondary font-semibold">🎉 No critical keywords missing!</span>`;
        } else {
            missingContainer.innerHTML = data.missing.map(k => makeDangerTag(k)).join('');
        }
        const pct = data.missing.length > 0
            ? Math.round((data.missing.length / (data.missing.length + data.matched.length)) * 100)
            : 0;
        document.getElementById('missing-keywords-tip').textContent =
            data.missing.length > 0
                ? `Adding these terms could increase your ATS visibility by approx. ${pct}%.`
                : 'Your resume covers all critical keywords from the job description.';

        // ─ Matched keywords
        const matchedContainer = document.getElementById('matched-keywords-container');
        document.getElementById('matched-count-badge').textContent = data.matched.length + ' matched';
        matchedContainer.innerHTML = data.matched.length
            ? data.matched.map(k => makeSuccessTag(k)).join('')
            : `<span class="text-xs text-primary/40">No keywords matched yet.</span>`;

        // ─ Action verbs
        const verbsContainer = document.getElementById('action-verbs-container');
        verbsContainer.innerHTML = data.action_verbs.length
            ? data.action_verbs.map(v => makeSuccessTag(v)).join('')
            : `<span class="text-xs text-primary/40">No strong action verbs detected.</span>`;

        const missingVerbsRow = document.getElementById('missing-verbs-row');
        if (data.missing_verbs && data.missing_verbs.length > 0) {
            missingVerbsRow.classList.remove('hidden');
            document.getElementById('missing-verbs-container').innerHTML =
                data.missing_verbs.map(v => makeNeutralTag(v)).join('');
        } else {
            missingVerbsRow.classList.add('hidden');
        }

        // ─ Length tip
        const lengthCard  = document.getElementById('length-tip-card');
        const lengthIcon  = document.getElementById('length-tip-icon');
        const lengthTitle = document.getElementById('length-tip-title');
        const lengthBody  = document.getElementById('length-tip-body');

        if (data.word_count >= 200 && data.word_count <= 800) {
            lengthCard.className = 'flex items-start gap-4 rounded-2xl p-5 border border-secondary/20 bg-secondary/5';
            lengthIcon.textContent = 'check_circle';
            lengthIcon.className   = 'material-symbols-outlined text-[22px] shrink-0 mt-0.5 text-secondary icon-filled';
            lengthTitle.textContent = 'Resume Length';
            lengthTitle.className   = 'text-xs font-bold uppercase tracking-wider mb-1 text-secondary';
        } else {
            lengthCard.className = 'flex items-start gap-4 rounded-2xl p-5 border border-yellow-500/20 bg-yellow-500/5';
            lengthIcon.textContent = 'warning';
            lengthIcon.className   = 'material-symbols-outlined text-[22px] shrink-0 mt-0.5 text-yellow-500';
            lengthTitle.textContent = 'Resume Length Warning';
            lengthTitle.className   = 'text-xs font-bold uppercase tracking-wider mb-1 text-yellow-600';
        }
        lengthBody.textContent = data.length_tip;

        // ─ Insights
        const insightsContainer = document.getElementById('insights-container');
        insightsContainer.innerHTML = data.insights.map((ins, i) => `
            <div class="animate-fade-in-up" style="animation-delay:${i * 80}ms">
                <h5 class="font-bold text-sm text-primary mb-1.5">${escHtml(ins.title)}</h5>
                <p  class="text-sm text-primary/65 leading-relaxed">${escHtml(ins.body)}</p>
            </div>
        `).join('');

        // Fade-in animation for whole results
        resultsEl.querySelectorAll(':scope > div, :scope > section, :scope > p').forEach((el, i) => {
            el.style.animation = `fadeInUp 0.4s ease ${i * 60}ms both`;
        });
    }

    // ── Analyze button ────────────────────────────────────────────────────────

    document.getElementById('analyze-btn').addEventListener('click', async function () {
        const resume = document.getElementById('resume-input').value.trim();
        const jd     = document.getElementById('jd-input').value.trim();

        // Client-side quick check
        if (resume.length < 50) {
            showError('Please paste a more detailed resume (at least 50 characters).');
            return;
        }
        if (jd.length < 50) {
            showError('Please paste a more detailed job description (at least 50 characters).');
            return;
        }

        clearError();
        setLoading(true);

        try {
            const response = await fetch('{{ route("ats.analyze") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ resume, job_description: jd }),
            });

            let data;
            try {
                data = await response.json();
            } catch (_) {
                showError('Unexpected server response. Please try again.');
                return;
            }

            if (!response.ok) {
                const msg = data.message ?? (data.errors ? Object.values(data.errors).flat().join(' ') : 'Something went wrong.');
                showError(msg);
                return;
            }

            renderResults(data);
        } catch (e) {
            showError('Network error — please check your connection and try again.');
        } finally {
            setLoading(false);
        }
    });

    function setLoading(loading) {
        const btn     = document.getElementById('analyze-btn');
        const label   = document.getElementById('analyze-btn-label');
        const spinner = document.getElementById('analyze-spinner');
        const icon    = document.getElementById('analyze-icon');

        btn.disabled       = loading;
        label.textContent  = loading ? 'Analyzing…' : 'Analyze Match';
        spinner.classList.toggle('hidden', !loading);
        icon.classList.toggle('hidden', loading);
    }

    function showError(msg) {
        const el = document.getElementById('ats-error');
        document.getElementById('ats-error-msg').textContent = msg;
        el.classList.remove('hidden');
    }
    function clearError() {
        document.getElementById('ats-error').classList.add('hidden');
    }

    // ── Instructions modal ────────────────────────────────────────────────────

    document.getElementById('ats-instructions-btn').addEventListener('click', () => {
        const modal   = document.getElementById('instructions-modal');
        const content = document.getElementById('instructions-content');
        modal.classList.remove('hidden');
        void modal.offsetWidth;
        modal.style.opacity = '1';
        content.classList.replace('scale-95', 'scale-100');
    });

    function closeInstructions() {
        const modal   = document.getElementById('instructions-modal');
        const content = document.getElementById('instructions-content');
        modal.style.opacity = '0';
        content.classList.replace('scale-100', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    // Close modal on outside click
    document.getElementById('instructions-modal').addEventListener('click', function (e) {
        if (e.target === this) closeInstructions();
    });
    </script>
@endsection
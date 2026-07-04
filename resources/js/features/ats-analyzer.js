
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

        function parseCvSections(option) {
            const sectionsRaw = option.getAttribute('data-sections');
            if (!sectionsRaw) return {
                text: '',
                jd: ''
            };
            try {
                const sections = JSON.parse(sectionsRaw);
                let resumeText = '';
                let jobDesc = '';

                function extractTextFromContent(content) {
                    if (!content) return '';
                    if (typeof content === 'string') return content;
                    if (Array.isArray(content)) {
                        return content.map(extractTextFromContent).filter(Boolean).join('\n');
                    }
                    if (typeof content === 'object') {
                        return Object.values(content).map(extractTextFromContent).filter(Boolean).join(' | ');
                    }
                    return String(content);
                }

                sections.forEach(sec => {
                    if (sec.type === 'target_job') {
                        const title = sec.content?.job_title || '';
                        const company = sec.content?.job_company || '';
                        const desc = sec.content?.job_description || '';
                        jobDesc = (title + '\n\n' + desc).trim();
                        return { title, company, desc };
                    } else {
                        if (sec.content) {
                            resumeText += extractTextFromContent(sec.content) + '\n\n';
                        }
                    }
                });

                // Also extract individual target job fields
                const targetJobSec = sections.find(s => s.type === 'target_job');
                const jobTitle = targetJobSec?.content?.job_title || '';
                const jobCompany = targetJobSec?.content?.job_company || '';
                const jobDesc2 = targetJobSec?.content?.job_description || '';

                return {
                    text: resumeText.trim(),
                    jd: jobDesc,
                    jobTitle,
                    jobCompany,
                    jobDesc: jobDesc2,
                };
            } catch (e) {
                console.error("Failed to parse sections", e);
                return {
                    text: '',
                    jd: ''
                };
            }
        }

        const cvSelector = document.getElementById('cv-selector');
        if (cvSelector) {
            cvSelector.addEventListener('change', function() {
                if (!this.value) {
                    document.getElementById('resume-input').value = '';
                    // Clear target job fields
                    const titleInput = document.querySelector('[name="job_title"]');
                    const companyInput = document.querySelector('[name="job_company"]');
                    const jdTextarea = document.getElementById('jd-input');
                    if (titleInput) titleInput.value = '';
                    if (companyInput) companyInput.value = '';
                    if (jdTextarea) jdTextarea.value = '';
                    return;
                }
                const selectedOption = this.options[this.selectedIndex];
                const data = parseCvSections(selectedOption);
                document.getElementById('resume-input').value = data.text;
                // Auto-fill target job fields from resume data
                const titleInput = document.querySelector('[name="job_title"]');
                const companyInput = document.querySelector('[name="job_company"]');
                const jdTextarea = document.getElementById('jd-input');
                if (titleInput) titleInput.value = data.jobTitle || '';
                if (companyInput) companyInput.value = data.jobCompany || '';
                if (jdTextarea) jdTextarea.value = data.jobDesc || '';
            });
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                const sp = document.getElementById('ats-panel-setup');
                const rp = document.getElementById('ats-panel-results');
                if (sp) {
                    sp.classList.remove('hidden');
                }
                if (rp) {
                    rp.classList.remove('hidden');
                }
            }
        });

        function switchAtsTab(tab) {
            if (window.innerWidth >= 1024) return;
            const setupPanel = document.getElementById('ats-panel-setup');
            const resultsPanel = document.getElementById('ats-panel-results');
            const setupBtn = document.getElementById('ats-tab-setup');
            const resultsBtn = document.getElementById('ats-tab-results');

            setupPanel.classList.toggle('hidden', tab !== 'setup');
            resultsPanel.classList.toggle('hidden', tab !== 'results');

            [setupBtn, resultsBtn].forEach(btn => {
                const active = btn.id === `ats-tab-${tab}`;
                btn.classList.toggle('text-primary', active);
                btn.classList.toggle('border-primary', active);
                btn.classList.toggle('text-primary/40', !active);
                btn.classList.toggle('border-transparent', !active);
                btn.setAttribute('aria-selected', active ? 'true' : 'false');
            });

        }


        function buildScoreCircle(score) {
            const r = 56;
            const center = 64;
            const strokeW = 8;
            const circ = +(2 * Math.PI * r).toFixed(1);
            const offset = +((1 - score / 100) * circ).toFixed(1);
            const colorClass = score >= 70 ? 'text-secondary' : score >= 50 ? 'text-yellow-500' : 'text-red-500';

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

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    circle.style.transition = 'stroke-dashoffset 1.1s cubic-bezier(.4,0,.2,1)';
                    circle.style.strokeDashoffset = target;
                });
            });
        }

        function renderResults(data) {
            // Show results panel, hide empty state and resume preview
            document.getElementById('ats-empty-state').classList.add('hidden');
            const previewPanel = document.getElementById('ats-resume-preview');
            if (previewPanel) previewPanel.style.display = 'none';
            const resultsEl = document.getElementById('ats-results');
            resultsEl.classList.remove('hidden');
            // On mobile, auto-switch to results tab
            switchAtsTab('results');

            // Score circle
            const scoreContainer = document.getElementById('score-circle-container');
            scoreContainer.innerHTML = buildScoreCircle(data.score);
            animateCircle(scoreContainer);

            // Rating
            const ratingColorMap = {
                success: 'text-secondary',
                warning: 'text-yellow-500',
                danger: 'text-red-500'
            };
            document.getElementById('score-rating-label').textContent = data.rating.label;
            document.getElementById('score-rating-label').className =
                `font-bold text-lg ${ratingColorMap[data.rating.color] ?? 'text-secondary'}`;
            document.getElementById('score-rating-sub').textContent = data.rating.sublabel;

            // ─ Missing keywords
            const missingContainer = document.getElementById('missing-keywords-container');
            document.getElementById('missing-count-badge').textContent = data.missing.length + ' missing';
            if (data.missing.length === 0) {
                missingContainer.innerHTML =
                    `<span class="text-xs text-secondary font-semibold">🎉 No critical keywords missing!</span>`;
            } else {
                missingContainer.innerHTML = data.missing.map(m => `
                <div class="flex flex-col p-3 bg-red-500/5 rounded-xl border border-red-500/10 hover:bg-red-500/10 transition-colors">
                    <span class="text-xs font-bold text-red-600 mb-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">cancel</span> ${escHtml(m.keyword)}</span>
                    <span class="text-[11px] text-primary/70 leading-relaxed">${escHtml(m.context)}</span>
                </div>
            `).join('');
            }
            const pct = data.missing.length > 0 ?
                Math.round((data.missing.length / (data.missing.length + data.matched.length)) * 100) :
                0;
            document.getElementById('missing-keywords-tip').textContent =
                data.missing.length > 0 ?
                `Adding these terms could increase your ATS visibility by approx. ${pct}%.` :
                'Your resume covers all critical keywords from the job description.';

            const matchedContainer = document.getElementById('matched-keywords-container');
            document.getElementById('matched-count-badge').textContent = data.matched.length + ' matched';
            matchedContainer.innerHTML = data.matched.length ?
                data.matched.map(k => makeSuccessTag(k)).join('') :
                `<span class="text-xs text-primary/40">No keywords matched yet.</span>`;

            const verbsContainer = document.getElementById('action-verbs-container');
            verbsContainer.innerHTML = data.action_verbs.length ?
                data.action_verbs.map(v => makeSuccessTag(v)).join('') :
                `<span class="text-xs text-primary/40">No strong action verbs detected.</span>`;

            const missingVerbsRow = document.getElementById('missing-verbs-row');
            if (data.missing_verbs && data.missing_verbs.length > 0) {
                missingVerbsRow.classList.remove('hidden');
                document.getElementById('missing-verbs-container').innerHTML =
                    data.missing_verbs.map(v => makeNeutralTag(v)).join('');
            } else {
                missingVerbsRow.classList.add('hidden');
            }

            // Length tip
            const lengthCard = document.getElementById('length-tip-card');
            const lengthIcon = document.getElementById('length-tip-icon');
            const lengthTitle = document.getElementById('length-tip-title');
            const lengthBody = document.getElementById('length-tip-body');

            if (data.word_count >= 200 && data.word_count <= 800) {
                lengthCard.className = 'flex items-start gap-4 rounded-2xl p-5 border border-secondary/20 bg-secondary/5';
                lengthIcon.textContent = 'check_circle';
                lengthIcon.className = 'material-symbols-outlined text-[22px] shrink-0 mt-0.5 text-secondary icon-filled';
                lengthTitle.textContent = 'Resume Length';
                lengthTitle.className = 'text-xs font-bold uppercase tracking-wider mb-1 text-secondary';
            } else {
                lengthCard.className = 'flex items-start gap-4 rounded-2xl p-5 border border-yellow-500/20 bg-yellow-500/5';
                lengthIcon.textContent = 'warning';
                lengthIcon.className = 'material-symbols-outlined text-[22px] shrink-0 mt-0.5 text-yellow-500';
                lengthTitle.textContent = 'Resume Length Warning';
                lengthTitle.className = 'text-xs font-bold uppercase tracking-wider mb-1 text-yellow-600';
            }
            lengthBody.textContent = data.length_tip;

            const breakdownContainer = document.getElementById('section-breakdown-container');
            if (data.section_breakdown && data.section_breakdown.length > 0) {
                breakdownContainer.innerHTML = data.section_breakdown.map((sec, i) => {
                    let colorClass = 'text-secondary bg-secondary/10 border-secondary/20';
                    let icon = 'check_circle';
                    if (sec.strength === 'Weak') {
                        colorClass = 'text-red-500 bg-red-500/10 border-red-500/20';
                        icon = 'error';
                    } else if (sec.strength === 'Adequate') {
                        colorClass = 'text-yellow-600 bg-yellow-500/10 border-yellow-500/20';
                        icon = 'warning';
                    }
                    return `
                <div class="flex items-start gap-4 p-4 rounded-xl border border-primary/5 bg-primary/[0.02] hover:bg-primary/5 transition-colors animate-fade-in-up" style="animation-delay:${i * 80}ms">
                    <div class="flex flex-col items-center justify-center shrink-0 w-[72px] py-2 rounded-lg border ${colorClass}">
                        <span class="material-symbols-outlined text-[20px] mb-1 icon-filled">${icon}</span>
                        <span class="text-[9px] font-bold uppercase tracking-wider">${escHtml(sec.strength)}</span>
                    </div>
                    <div>
                        <h5 class="text-sm font-bold text-primary mb-1">${escHtml(sec.section)}</h5>
                        <p class="text-xs text-primary/70 leading-relaxed">${escHtml(sec.feedback)}</p>
                    </div>
                </div>`;
                }).join('');
            } else {
                breakdownContainer.innerHTML = `<p class="text-sm text-primary/50">No section breakdown available.</p>`;
            }

            const insightsContainer = document.getElementById('insights-container');
            insightsContainer.innerHTML = data.insights.map((ins, i) => `
            <div class="animate-fade-in-up" style="animation-delay:${i * 80}ms">
                <h5 class="font-bold text-sm text-primary mb-1.5">${escHtml(ins.title)}</h5>
                <p  class="text-sm text-primary/65 leading-relaxed">${escHtml(ins.body)}</p>
            </div>
        `).join('');


            resultsEl.querySelectorAll(':scope > div, :scope > section, :scope > p').forEach((el, i) => {
                el.style.animation = `fadeInUp 0.4s ease ${i * 60}ms both`;
            });
        }

        const analyzeBtn = document.getElementById('analyze-btn');
        if (analyzeBtn) analyzeBtn.addEventListener('click', async function() {
            const resume = document.getElementById('resume-input').value.trim();
            const jobTitleEl = document.querySelector('[name="job_title"]');
            const jobCompanyEl = document.querySelector('[name="job_company"]');
            const jobTitle = jobTitleEl ? jobTitleEl.value.trim() : '';
            const jobCompany = jobCompanyEl ? jobCompanyEl.value.trim() : '';
            const jobDescRaw = document.getElementById('jd-input').value.trim();
            const cvId = document.getElementById('cv-selector')?.value || null;
            const cvTitle = document.getElementById('cv-selector')?.options[document.getElementById('cv-selector')?.selectedIndex]?.text?.trim() || null;

            // Compose the full jd: title on top, then description
            const jd = [jobTitle, jobDescRaw].filter(Boolean).join('\n\n');
            if (resume.length < 50) {
                showError('The selected resume must have more content (at least 50 characters).');
                return;
            }
            if (jd.length < 50) {
                showError(
                    'Please enter a Target Job description with at least 50 characters before analyzing.'
                );
                return;
            }
            clearError();
            setLoading(true);
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 90_000);

            try {
                let response;
                try {
                    response = await fetch(`${window.atsConfig.analyzeUrl}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.atsConfig.csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            resume,
                            job_description: jd,
                            cv_id:      cvId || null,
                            job_title:  jobTitle || null,
                            job_company: jobCompany || null,
                        }),
                        signal: controller.signal,
                    });
                } catch (fetchErr) {
                    if (fetchErr.name === 'AbortError') {
                        showError('Request timed out. The AI service took too long — please try again.');
                    } else {
                        showError('Network error — please check your connection and try again.');
                    }
                    setLoading(false);
                    return;
                }

                let data;
                try {
                    data = await response.json();
                } catch (_) {
                    showError('Unexpected server response. Please try again.');
                    setLoading(false);
                    return;
                }

                if (!response.ok) {
                    if (response.status === 402 && data.upgrade_url) {
                        showError(data.message ?? 'Upgrade to Premium to unlock ATS Analyzer.');
                        window.location.href = data.upgrade_url;
                        return;
                    }

                    const msg = data.message ?? (data.errors ? Object.values(data.errors).flat().join(' ') :
                        'Something went wrong.');
                    showError(msg);
                    setLoading(false);
                    return;
                }

                renderResults(data);

                // Prepend new card to history list
                if (data._scan_id) {
                    prependHistory({
                        id:         data._scan_id,
                        score:      data.score,
                        job_title:  jobTitle,
                        job_company: jobCompany,
                        cv_title:   cvTitle,
                        created_at: data._scan_created,
                    }, data);
                }
            } finally {
                clearTimeout(timeoutId);
                setLoading(false);
            }
        });

        // ── History helpers ──────────────────────────────────────────────

        /**
         * Cache for result_json keyed by scan id (populated when card is first clicked or newly created).
         */
        const _historyCache = {};

        /**
         * Prepend a newly created scan card to the top of #ats-history-list.
         */
        function prependHistory(meta, resultData) {
            const list = document.getElementById('ats-history-list');
            if (!list) return;

            // Hide the "no history yet" placeholder if present
            const empty = document.getElementById('ats-history-empty');
            if (empty) empty.style.display = 'none';

            // Cache the full result so loadHistoryResult can use it immediately
            _historyCache[meta.id] = resultData;

            const card = buildHistoryCard(meta);
            list.insertAdjacentHTML('afterbegin', card);

            // Animate in
            const el = document.getElementById('history-card-' + meta.id);
            if (el) {
                el.style.opacity = '0';
                el.style.transform = 'translateY(-8px)';
                requestAnimationFrame(() => {
                    el.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                });
            }
        }

        /**
         * Build the HTML string for a history card.
         */
        function buildHistoryCard(meta) {
            const score = meta.score ?? null;
            let scoreColor = 'text-red-500 bg-red-500/10 border-red-500/20';
            if (score !== null && score >= 70) scoreColor = 'text-secondary bg-secondary/10 border-secondary/20';
            else if (score !== null && score >= 50) scoreColor = 'text-yellow-600 bg-yellow-500/10 border-yellow-500/20';

            const label = score !== null ? score : '?';
            const title = escHtml(meta.job_title || 'Untitled Scan');
            const company = meta.job_company ? ` <span class="font-normal text-primary/50">@ ${escHtml(meta.job_company)}</span>` : '';
            const cvLine = escHtml(meta.cv_title || 'No resume linked');
            const timeAgo = meta.created_at ? new Date(meta.created_at).toLocaleString('id-ID', {day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'}) : 'just now';

            return `<div id="history-card-${meta.id}"
                class="history-card group flex items-center gap-2 bg-tertiary border border-primary/10 hover:border-primary/25 rounded-xl p-2 transition-all duration-200">
                <button type="button"
                    class="flex min-w-0 flex-1 items-center gap-3 rounded-lg p-1 text-left focus:outline-none focus:ring-2 focus:ring-secondary/40"
                    onclick="loadHistoryResult('${meta.id}', this.closest('.history-card'))"
                    aria-label="Load scan ${title}">
                    <div class="shrink-0 w-11 h-11 rounded-lg border flex flex-col items-center justify-center ${scoreColor}">
                        <span class="font-headline font-bold text-sm leading-none">${label}</span>
                        <span class="text-[9px] font-bold uppercase tracking-wider opacity-70">pts</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-primary truncate leading-tight">${title}${company}</p>
                        <p class="text-[11px] text-primary/40 mt-0.5 truncate">${cvLine} · ${timeAgo}</p>
                    </div>
                </button>
                <button type="button" title="Delete"
                    aria-label="Delete scan ${title}"
                    onclick="deleteHistoryScan('${meta.id}')"
                    class="shrink-0 opacity-0 group-hover:opacity-100 text-primary/30 hover:text-red-500 transition-all duration-200 rounded-lg p-2 hover:bg-red-500/10 focus:opacity-100 focus:outline-none focus:ring-2 focus:ring-red-300">
                    <span class="material-symbols-outlined text-[17px]" aria-hidden="true">delete</span>
                </button>
            </div>`;
        }

        /**
         * Load a history scan result into the right panel.
         * Data is read from the inline data-result attribute or from cache.
         */
        function loadHistoryResult(scanId, cardEl) {
            // Check in-memory cache first (covers newly created scans in this session)
            if (_historyCache[scanId]) {
                renderResults(_historyCache[scanId]);
                highlightActiveCard(cardEl);
                switchAtsTab('results');
                return;
            }

            // Otherwise read from the data-result attribute on the card element
            const raw = cardEl?.dataset?.result;
            if (raw) {
                try {
                    const data = JSON.parse(raw);
                    _historyCache[scanId] = data;
                    renderResults(data);
                    highlightActiveCard(cardEl);
                    switchAtsTab('results');
                    return;
                } catch (e) {
                    console.error('Failed to parse history result JSON', e);
                }
            }

            // Fallback: fetch from server (for history items from previous sessions)
            if (cardEl) {
                cardEl.style.opacity = '0.5';
                cardEl.style.pointerEvents = 'none';
            }
            fetch(`${window.atsConfig.historyUrl}/${scanId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.atsConfig.csrfToken }
            })
            .then(r => r.json())
            .then(data => {
                _historyCache[scanId] = data;
                renderResults(data);
                highlightActiveCard(cardEl);
                switchAtsTab('results');
            })
            .catch(() => console.error('Failed to load history scan', scanId))
            .finally(() => {
                if (cardEl) {
                    cardEl.style.opacity = '';
                    cardEl.style.pointerEvents = '';
                }
            });
        }

        /**
         * Highlight the active history card and remove highlight from others.
         */
        function highlightActiveCard(cardEl) {
            document.querySelectorAll('.history-card').forEach(c => {
                c.classList.remove('border-secondary', 'bg-secondary/5');
                c.classList.add('border-primary/10');
            });
            if (cardEl) {
                cardEl.classList.remove('border-primary/10');
                cardEl.classList.add('border-secondary', 'bg-secondary/5');
            }
        }

        /**
         * DELETE a scan from the database and remove its card from the DOM.
         */
        async function deleteHistoryScan(scanId) {
            const card = document.getElementById('history-card-' + scanId);
            if (!card) return;

            // Optimistic UI: fade out immediately
            card.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
            card.style.opacity = '0';
            card.style.transform = 'translateX(12px)';

            try {
                const res = await fetch(`${window.atsConfig.historyUrl}/${scanId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': window.atsConfig.csrfToken,
                        'Accept': 'application/json',
                    },
                });

                if (res.ok) {
                    setTimeout(() => {
                        card.remove();
                        delete _historyCache[scanId];

                        // Show empty placeholder if no cards left
                        const list = document.getElementById('ats-history-list');
                        if (list && list.querySelectorAll('.history-card').length === 0) {
                            list.innerHTML = `<div id="ats-history-empty" class="flex items-center gap-2 text-primary/30 text-xs italic py-2">
                                <span class="material-symbols-outlined text-[15px]">history</span>
                                No scan history yet — run your first analysis above.
                            </div>`;
                        }
                    }, 280);
                } else {
                    // Revert on failure
                    card.style.opacity = '1';
                    card.style.transform = 'none';
                }
            } catch {
                card.style.opacity = '1';
                card.style.transform = 'none';
            }
        }

        function setLoading(loading) {
            const btn = document.getElementById('analyze-btn');
            const label = document.getElementById('analyze-btn-label');
            const spinner = document.getElementById('analyze-spinner');
            const icon = document.getElementById('analyze-icon');

            if (!btn || !label || !spinner || !icon) return;

            btn.disabled = loading;
            label.textContent = loading ? 'Analyzing…' : 'Analyze Match';
            spinner.style.display = loading ? 'inline-block' : 'none';
            icon.style.display = loading ? 'none' : 'inline-block';
        }

        function showError(msg) {
            const el = document.getElementById('ats-error');
            document.getElementById('ats-error-msg').textContent = msg;
            el.classList.remove('hidden');
        }

        function clearError() {
            document.getElementById('ats-error').classList.add('hidden');
        }

        document.getElementById('ats-instructions-btn').addEventListener('click', () => {
            const modal = document.getElementById('instructions-modal');
            const content = document.getElementById('instructions-content');
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.style.opacity = '1';
            content.classList.replace('scale-95', 'scale-100');
        });

        function closeInstructions() {
            const modal = document.getElementById('instructions-modal');
            const content = document.getElementById('instructions-content');
            modal.style.opacity = '0';
            content.classList.replace('scale-100', 'scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        document.getElementById('instructions-modal').addEventListener('click', function(e) {
            if (e.target === this) closeInstructions();
        });

        Object.assign(window, {
            switchAtsTab,
            loadHistoryResult,
            deleteHistoryScan,
            closeInstructions,
        });
    

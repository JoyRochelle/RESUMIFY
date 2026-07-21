
        const t = window.editorConfig.i18n;

        function angleLabel(angle) {
            return `${t.angle_labels[angle] || angle} ${t.angle_suffix}`.trim();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function previewPdf(resumeId) {
            if (!resumeId) return;
            window.open(`/resumes/${resumeId}/preview`, '_blank');
        }

        // ── Photo Upload ───────────────────────────────────────────
        function handlePhotoUpload(input) {
            const file = input.files[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) {
                alert(t.photo_too_large);
                input.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                const dataUrl = e.target.result;
                // Update preview
                const img = document.getElementById('photo-preview-img');
                const placeholder = document.getElementById('photo-placeholder-icon');
                if (img) { img.src = dataUrl; img.classList.remove('hidden'); }
                if (placeholder) placeholder.classList.add('hidden');
                // Store in hidden input and trigger save
                const hiddenInput = document.getElementById('photo-hidden-input');
                if (hiddenInput) {
                    hiddenInput.value = dataUrl;
                    hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                // Auto-save the personal info section
                const form = document.querySelector('.section-form[data-section-id]');
                if (form) saveSection(form);
            };
            reader.readAsDataURL(file);
        }

        function removePhoto() {
            const img = document.getElementById('photo-preview-img');
            const placeholder = document.getElementById('photo-placeholder-icon');
            const hiddenInput = document.getElementById('photo-hidden-input');
            if (img) { img.src = ''; img.classList.add('hidden'); }
            if (placeholder) placeholder.classList.remove('hidden');
            if (hiddenInput) {
                hiddenInput.value = '';
                const form = document.querySelector('.section-form[data-section-id]');
                if (form) saveSection(form);
            }
        }
        // ── End Photo Upload ───────────────────────────────────────

        async function downloadPdf(resumeId) {
            if (!resumeId) return;
            const btn = document.getElementById('download-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = `<span class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span> <span class="ml-1">${t.generating}</span>`;
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                const response = await fetch(`/resumes/${resumeId}/pdf`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.status === 402) {
                    const data = await response.json();
                    showToast(data.message || t.premium_required_pdf, 'error');
                    if (data.upgrade_url) window.location.href = data.upgrade_url;
                    return;
                }

                if (!response.ok) throw new Error('Network response was not ok');
                
                const blob = await response.blob();
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;

                const contentDisposition = response.headers.get('Content-Disposition');
                let filename = 'my-resume.pdf';
                if (contentDisposition && contentDisposition.includes('filename=')) {
                    filename = contentDisposition.split('filename=')[1].replace(/["']/g, '');
                }
                
                a.download = filename;
                a.click();
                URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Failed to download PDF:', error);
                alert(t.download_pdf_failed);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        }

        let previewTimeout;
        let originalPreviewSrc = window.editorConfig.hasCv ? `/resumes/${window.editorConfig.cvId}/preview` : "";

        function previewTemplate(templateId) {
            if (window.editorConfig.hasCv) {
            clearTimeout(previewTimeout);
            const iframe = document.getElementById('resume-preview-iframe');
            if (iframe) {
                iframe.src = `/resumes/${window.editorConfig.cvId}/preview?template_id=${templateId}`;
            }
            }
        }

        function resetPreview() {
            if (window.editorConfig.hasCv) {
            // Wait a small delay before resetting to avoid flicker when moving between cards
            previewTimeout = setTimeout(() => {
                const iframe = document.getElementById('resume-preview-iframe');
                if (iframe && iframe.src !== originalPreviewSrc) {
                    // Only reset if we didn't just save a new template
                    iframe.src = originalPreviewSrc;
                }
            }, 300);
            }
        }

        function openTemplateModal() {
            const modal = document.getElementById('template-modal');
            const modalContent = document.getElementById('template-modal-content');
            modal.classList.remove('hidden');
            // Trigger reflow
            void modal.offsetWidth;
            modal.style.opacity = '1';
            modal.style.pointerEvents = 'auto';
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
            scaleThumbnails();
        }

        function closeTemplateModal() {
            const modal = document.getElementById('template-modal');
            const modalContent = document.getElementById('template-modal-content');
            modal.style.opacity = '0';
            modal.style.pointerEvents = 'none';
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        async function selectTemplate(templateId) {
            if (window.editorConfig.hasCv) {
            try {
                const response = await fetch(`/resumes/${window.editorConfig.cvId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': window.editorConfig.csrfToken
                    },
                    body: JSON.stringify({ template_id: templateId })
                });
                
                if (response.status === 402) {
                    const data = await response.json();
                    showToast(data.message || t.premium_required_template, 'error');
                    if (data.upgrade_url) window.location.href = data.upgrade_url;
                    return;
                }

                if (!response.ok) throw new Error('Network response was not ok');
                
                const data = await response.json();
                if(data.success) {
                    const iframe = document.getElementById('resume-preview-iframe');
                    if (iframe) {
                        // Update original source so we don't revert on mouseleave
                        originalPreviewSrc = `/resumes/${window.editorConfig.cvId}/preview?template_id=${templateId}`;
                        // Add a subtle loading state
                        iframe.style.opacity = '0.5';
                        iframe.src = originalPreviewSrc;
                        iframe.onload = () => { iframe.style.opacity = '1'; };
                    } else {
                        window.location.reload();
                    }
                    
                    // Update UI selection
                    document.querySelectorAll('.template-card').forEach(card => {
                        card.classList.remove('border-secondary', 'bg-secondary/5');
                        card.classList.add('border-primary/10');
                        const checkmark = card.querySelector('.checkmark');
                        if (checkmark) checkmark.remove();
                    });
                    
                    const selectedCard = document.getElementById('template-card-' + templateId);
                    if (selectedCard) {
                        selectedCard.classList.remove('border-primary/10');
                        selectedCard.classList.add('border-secondary', 'bg-secondary/5');
                        selectedCard.innerHTML += `
                        <div class="absolute top-3 right-3 bg-secondary text-white rounded-full w-6 h-6 shadow-md flex items-center justify-center checkmark">
                            <span class="material-symbols-outlined text-[14px]">check</span>
                        </div>`;
                    }
                    
                    closeTemplateModal();
                }
            } catch (error) {
                console.error('Failed to change template:', error);
                alert(t.change_template_failed);
            }
            } else {
                alert(t.no_resume_for_template);
            }
        }

        function scaleIframe() {
            const container = document.getElementById('preview-container');
            const iframe = document.getElementById('resume-preview-iframe');
            if (container && iframe) {
                const scale = container.offsetWidth / 794;
                iframe.style.transform = `scale(${scale})`;

                // Size the iframe (and its scaled container) to the ACTUAL rendered
                // content height, not a fixed one-page height — otherwise CVs longer
                // than one A4 page get clipped with no way to scroll to the rest.
                let contentHeight = 1123;
                try {
                    const doc = iframe.contentDocument;
                    if (doc && doc.documentElement) {
                        contentHeight = Math.max(doc.documentElement.scrollHeight, 1123);
                    }
                } catch (e) {
                    // Cross-origin or not-yet-loaded — keep the single-page fallback height.
                }

                iframe.style.height = `${contentHeight}px`;
                container.style.height = `${contentHeight * scale}px`;
            }
            scaleThumbnails();
        }
        
        function scaleThumbnails() {
            document.querySelectorAll('.template-thumbnail-iframe').forEach(iframe => {
                const parent = iframe.parentElement;
                if (parent.offsetWidth > 0) {
                    const scale = parent.offsetWidth / 794;
                    // Check if it's currently hovered (which scales it up further in CSS)
                    // The CSS handles hover scale, but we'll set base scale here via JS variable or just override inline
                    iframe.style.transform = `scale(${scale})`;
                    // To keep hover working, we'd need to use CSS variables, but for now inline style overrides the hover class.
                    // Let's use CSS variable for base scale so hover still works!
                    parent.style.setProperty('--base-scale', scale);
                    iframe.style.transform = `scale(var(--base-scale))`;
                }
            });
        }
        window.addEventListener('resize', function() {
            scaleIframe();
            // On resize to desktop, clear any mobile tab inline styles
            if (window.innerWidth >= 1024) {
                const ep = document.getElementById('ms-panel-edit');
                const pp = document.getElementById('ms-panel-preview');
                if (ep) { ep.style.display = ''; ep.classList.remove('hidden'); }
                if (pp) { pp.style.display = ''; pp.classList.remove('hidden'); }
            }
        });
        document.addEventListener('DOMContentLoaded', () => {
            scaleIframe();
            const iframe = document.getElementById('resume-preview-iframe');
            if (iframe) iframe.addEventListener('load', scaleIframe);
        });

        function switchMsTab(tab) {
            if (window.innerWidth >= 1024) return;
            const editPanel    = document.getElementById('ms-panel-edit');
            const previewPanel = document.getElementById('ms-panel-preview');
            const editBtn      = document.getElementById('ms-tab-edit');
            const previewBtn   = document.getElementById('ms-tab-preview');

            editPanel.classList.toggle('hidden', tab !== 'edit');
            previewPanel.classList.toggle('hidden', tab !== 'preview');

            [editBtn, previewBtn].forEach(btn => {
                const active = btn.id === `ms-tab-${tab}`;
                btn.classList.toggle('text-primary',     active);
                btn.classList.toggle('border-primary',   active);
                btn.classList.toggle('text-primary/70',  !active);
                btn.classList.toggle('border-transparent', !active);
            });

            if (tab === 'preview') scaleIframe();
        }

        // ── Toast helper ────────────────────────────────────────────────
        function showToast(message, type = 'success') {
            const existing = document.getElementById('save-toast');
            if (existing) existing.remove();
            const icons = { success: 'check_circle', error: 'error', saving: 'progress_activity' };
            const colors = { success: 'text-emerald-500', error: 'text-red-400', saving: 'text-secondary' };
            const toast = document.createElement('div');
            toast.id = 'save-toast';
            toast.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-2 px-5 py-3 rounded-full shadow-xl border border-primary/10 bg-tertiary/95 backdrop-blur-md text-sm font-medium text-primary transition-all duration-300 opacity-0 translate-y-2';
            toast.innerHTML = `<span class="material-symbols-outlined text-[18px] ${colors[type]} ${type==='saving'?'animate-spin':''}">${icons[type]}</span><span>${message}</span>`;
            document.body.appendChild(toast);
            requestAnimationFrame(() => { toast.style.opacity = '1'; toast.style.transform = 'translateX(-50%) translateY(0)'; });
            if (type !== 'saving') setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
        }

        // Auto-save logic
        if (window.editorConfig.hasCv) {
        let saveTimeout;
        const cvId = window.editorConfig.cvId;

        function triggerAutoSave(form) {
            clearTimeout(saveTimeout);
            const iframe = document.getElementById('resume-preview-iframe');
            if (iframe) iframe.style.opacity = '0.7';
            showToast(t.saving, 'saving');
            saveTimeout = setTimeout(() => saveSection(form), 800);
        }
        
        document.querySelectorAll('.section-form').forEach(form => {
            form.addEventListener('input', (e) => {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    triggerAutoSave(form);
                }
            });
            form.addEventListener('change', (e) => {
                if (e.target.tagName === 'SELECT') {
                    triggerAutoSave(form);
                }
            });
        });



        async function saveSection(form) {
            const sectionId = form.getAttribute('data-section-id');
            if (!sectionId) return;

            const data = {};
            const lists = form.querySelectorAll('.list-item');
            
            if (lists.length > 0) {
                data.content = [];
                lists.forEach(item => {
                    const itemData = {};
                    item.querySelectorAll('input, textarea, select').forEach(el => {
                        if (el.name) itemData[el.name] = el.value;
                    });
                    data.content.push(itemData);
                });
            } else {
                data.content = {};
                form.querySelectorAll('input, textarea, select').forEach(el => {
                    if (el.name) data.content[el.name] = el.value;
                });
            }

            try {
                const response = await fetch(`/resumes/${cvId}/section/${sectionId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': window.editorConfig.csrfToken
                    },
                    body: JSON.stringify(data)
                });
                
                if (response.ok) {
                    const result = await response.json();
                    const iframe = document.getElementById('resume-preview-iframe');
                    if (result.success && result.html && iframe) {
                        try {
                            iframe.contentDocument.open();
                            iframe.contentDocument.write(result.html);
                            iframe.contentDocument.close();
                            scaleIframe();
                        } catch (e) {
                            // fallback: full src reload if contentDocument is inaccessible
                            iframe.src = `/resumes/${cvId}/preview?t=${Date.now()}`;
                        }
                    }
                    if (iframe) iframe.style.opacity = '1';
                    showToast(result.saved_at ? `✓ ${t.saved_at_prefix} · ${result.saved_at}` : t.changes_saved, 'success');
                    if (result.ats_score !== undefined) {
                        updateAtsUi(result.ats_score, t.keyword_match);
                    }
                } else {
                    console.error('Failed to save section');
                    const iframe = document.getElementById('resume-preview-iframe');
                    if (iframe) iframe.style.opacity = '1';
                    showToast(t.save_failed, 'error');
                }
            } catch (error) {
                console.error('Network error', error);
                const iframe = document.getElementById('resume-preview-iframe');
                if (iframe) iframe.style.opacity = '1';
                showToast(t.network_error_not_saved, 'error');
            }
        }

        // Add/Remove Item Logic
        function addListItem(listId, btn) {
            const list = document.getElementById(listId);
            const items = list.querySelectorAll('.list-item');
            if (items.length === 0) return;

            const lastItem = items[items.length - 1];

            // Anti-spam: the previous entry must have real typed content before a
            // new empty row may be added. Only text fields count — a lone dropdown
            // (e.g. a skill level with no name) is not meaningful content and must
            // not unlock another row.
            const lastItemHasContent = [...lastItem.querySelectorAll('input, textarea')]
                .some(el => el.value && el.value.trim() !== '');
            if (!lastItemHasContent) {
                showToast(t.fill_previous_entry, 'error');
                return;
            }

            const clone = lastItem.cloneNode(true);

            // Clear text fields
            clone.querySelectorAll('input, textarea').forEach(input => {
                input.value = '';
            });

            // Reset dropdowns to their placeholder (first option, e.g. "Select
            // Level") so a new row never inherits the previous row's selection.
            clone.querySelectorAll('select').forEach(select => {
                select.selectedIndex = 0;
            });

            list.appendChild(clone);
            
            // Trigger save
            const form = btn.closest('form');
            if (form) {
                clearTimeout(saveTimeout);
                const iframe = document.getElementById('resume-preview-iframe');
                if (iframe) iframe.style.opacity = '0.7';
                saveTimeout = setTimeout(() => saveSection(form), 800);
            }
        }

        function removeListItem(btn) {
            const item = btn.closest('.list-item');
            const list = item.parentElement;
            const form = btn.closest('form');
            
            // Prevent removing the very last item completely, just clear it instead
            if (list.querySelectorAll('.list-item').length <= 1) {
                item.querySelectorAll('input, textarea, select').forEach(el => el.value = el.tagName === 'SELECT' ? '' : '');
            } else {
                item.remove();
            }
            
            // Trigger save
            if (form) {
                clearTimeout(saveTimeout);
                const iframe = document.getElementById('resume-preview-iframe');
                if (iframe) iframe.style.opacity = '0.7';
                saveTimeout = setTimeout(() => saveSection(form), 800);
            }
        }

        async function deleteSection(sectionId) {
            if (!confirm(t.confirm_remove_section)) return;
            
            try {
                const response = await fetch(`/resumes/${window.editorConfig.cvId || 0}/section/${sectionId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    window.location.reload();
                } else {
                    showToast(t.delete_section_failed, 'error');
                }
            } catch (e) {
                showToast(t.network_error, 'error');
            }
        }

        Object.assign(window, {
            addListItem,
            removeListItem,
            deleteSection,
        });
        }

        if (!window.editorConfig.hasCv) {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(openTemplateModal, 100);
            const closeBtn = document.getElementById('close-modal-btn');
            if (closeBtn) closeBtn.style.display = 'none';
        });
        }

        // ── ATS Score (Gemini) ─────────────────────────────────────────
        if (window.editorConfig.hasCv) {
        let atsTipOpen = false;
        let atsDebounce;

        function toggleAtsDetails() {
            const card = document.getElementById('ats-tip-card');
            if(card) {
                card.classList.toggle('hidden');
            }
        }

        function updateAtsUi(score, label) {
            const arc    = document.getElementById('ats-arc');
            const num    = document.getElementById('ats-score-num');
            const lbl    = document.getElementById('ats-label');
            const loader = document.getElementById('ats-loading');

            if (loader) loader.classList.add('hidden');

            const circumference = 138.2;
            const offset = circumference - (score / 100) * circumference;

            if (arc) {
                arc.style.strokeDashoffset = offset;
                // Color based on score
                arc.classList.remove('text-secondary', 'text-emerald-500', 'text-amber-400', 'text-red-400');
                if (score >= 75) arc.classList.add('text-emerald-500');
                else if (score >= 50) arc.classList.add('text-amber-400');
                else arc.classList.add('text-red-400');
            }
            if (num) num.textContent = score;
            const minScore = document.getElementById('ats-min-score');
            if (minScore) minScore.textContent = score;
            if (lbl) {
                lbl.textContent = score === 0 ? t.no_target_job : label;
                lbl.className = 'text-[10px] font-semibold mt-2 ' +
                    (score >= 75 ? 'text-emerald-500' : score >= 50 ? 'text-amber-400' : 'text-red-400');
            }
        }

        // Initial score on page load
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const initialScore = window.editorConfig.atsScore;
                updateAtsUi(initialScore, t.keyword_match);
            }, 800);
        });
        function toggleAtsMinimize(e) {
            e.stopPropagation();
            const max = document.getElementById('ats-maximized');
            const min = document.getElementById('ats-minimized');
            const widget = document.getElementById('ats-widget');
            
            if (max.classList.contains('hidden')) {
                // Restore to max
                max.classList.remove('hidden');
                min.classList.replace('flex', 'hidden');
                widget.classList.remove('p-2', 'rounded-full');
                widget.classList.add('p-4', 'rounded-2xl');
            } else {
                // Minimize
                max.classList.add('hidden');
                min.classList.replace('hidden', 'flex');
                widget.classList.add('p-2', 'rounded-full');
                widget.classList.remove('p-4', 'rounded-2xl');
            }
        }

        Object.assign(window, {
            toggleAtsDetails,
            updateAtsUi,
            toggleAtsMinimize,
        });
        }

        // ── Client-side validation before save ────────────────────────
        function validateFormBeforeSave(form) {
            let valid = true;
            form.querySelectorAll('input, select, textarea').forEach(input => {
                const val = input.value.trim();
                
                // Check required
                if (input.required && !val) {
                    valid = false;
                    input.classList.add('border-red-400');
                    input.addEventListener('input', () => input.classList.remove('border-red-400'), { once: true });
                }
                
                // Check types if there is a value
                if (val) {
                    let typeValid = true;
                    if (input.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                        typeValid = false;
                    } else if (input.type === 'tel' && !/^[+\d\s\-().]{7,20}$/.test(val)) {
                        typeValid = false;
                    } else if (input.type === 'url' && !/^https?:\/\/.+/.test(val)) {
                        typeValid = false;
                    }
                    
                    if (!typeValid) {
                        valid = false;
                        input.classList.add('border-red-400');
                        input.addEventListener('input', () => input.classList.remove('border-red-400'), { once: true });
                    }
                }
            });
            return valid;
        }

        // ── AI Refine Bullet ─────────────────────────────────────────
        let currentRefineTextarea = null;

        function openRefineModal(btn) {
            currentRefineTextarea = btn.parentElement.querySelector('textarea');
            const text = currentRefineTextarea.value.trim();
            if (!text || text.length < 10) {
                alert(t.refine_min_length);
                return;
            }

            const modal = document.getElementById('refine-modal');
            const content = document.getElementById('refine-modal-content');
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.style.opacity = '1';
            modal.style.pointerEvents = 'auto';
            content.classList.replace('scale-95', 'scale-100');

            document.getElementById('refine-loading').classList.remove('hidden');
            document.getElementById('refine-results').classList.add('hidden');
            document.getElementById('refine-results').innerHTML = '';

            const jobContext = document.querySelector('[name="job_description"]')?.value || '';

            console.log(`[AI Refine] Sending request for refinement...`);
            
            fetch(`/resumes/${window.editorConfig.cvId}/ai/refine-bullet`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.editorConfig.csrfToken
                },
                body: JSON.stringify({ text, job_context: jobContext })
            })
            .then(res => {
                console.log(`[AI Refine] Received response with status: ${res.status} ${res.statusText}`);
                return res.json();
            })
            .then(data => {
                console.log('[AI Refine] Response payload:', data);
                document.getElementById('refine-loading').classList.add('hidden');
                if (data.quota) {
                    window.dispatchEvent(new CustomEvent('ai-quota:updated', { detail: data.quota }));
                }
                if (data.success && data.options) {
                    const resultsContainer = document.getElementById('refine-results');
                    resultsContainer.classList.remove('hidden');
                    data.options.forEach(opt => {
                        const div = document.createElement('div');
                        div.className = 'p-4 rounded-xl border border-primary/10 hover:border-secondary cursor-pointer transition-colors bg-surface-container-low text-sm text-primary/80 leading-relaxed';
                        div.textContent = opt;
                        div.onclick = () => {
                            currentRefineTextarea.value = opt;
                            // Trigger input event to save if auto-save is bound
                            currentRefineTextarea.dispatchEvent(new Event('input', { bubbles: true }));
                            closeRefineModal();
                        };
                        resultsContainer.appendChild(div);
                    });
                } else {
                    document.getElementById('refine-loading').classList.add('hidden');
                    const resultsContainer = document.getElementById('refine-results');
                    resultsContainer.classList.remove('hidden');
                    resultsContainer.innerHTML = `<div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">${data.message || t.refine_failed}</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('refine-loading').classList.add('hidden');
                const resultsContainer = document.getElementById('refine-results');
                resultsContainer.classList.remove('hidden');
                resultsContainer.innerHTML = `<div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">${t.connection_error}</div>`;
            });
        }

        function closeRefineModal() {
            const modal = document.getElementById('refine-modal');
            const content = document.getElementById('refine-modal-content');
            modal.style.opacity = '0';
            modal.style.pointerEvents = 'none';
            content.classList.replace('scale-100', 'scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
            currentRefineTextarea = null;
        }

        // ── Parallel CV Versions ───────────────────────────────────────
        function openCvVersionsModal() {
            const modal = document.getElementById('cv-versions-modal');
            const content = document.getElementById('cv-versions-modal-content');
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.style.opacity = '1';
            modal.style.pointerEvents = 'auto';
            content.classList.replace('scale-95', 'scale-100');

            document.getElementById('cv-versions-setup').classList.remove('hidden');
            document.getElementById('cv-versions-loading').classList.add('hidden');
            document.getElementById('cv-versions-results').classList.add('hidden');
            document.getElementById('cv-versions-results').innerHTML = '';
        }

        function closeCvVersionsModal() {
            const modal = document.getElementById('cv-versions-modal');
            const content = document.getElementById('cv-versions-modal-content');
            modal.style.opacity = '0';
            modal.style.pointerEvents = 'none';
            content.classList.replace('scale-100', 'scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        function generateCvVersions() {
            const jobDescription = document.querySelector('[name="job_description"]')?.value || '';
            if (!jobDescription || jobDescription.length < 50) {
                alert(t.job_description_min_length);
                closeCvVersionsModal();
                return;
            }

            document.getElementById('cv-versions-setup').classList.add('hidden');
            document.getElementById('cv-versions-loading').style.display = 'flex';

            console.log(`[AI Versions] Sending parallel requests to generate 3 CV versions...`);
            
            fetch(`/resumes/${window.editorConfig.cvId}/ai/generate-versions`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.editorConfig.csrfToken
                },
                body: JSON.stringify({ job_description: jobDescription })
            })
            .then(res => {
                console.log(`[AI Versions] Received response with status: ${res.status} ${res.statusText}`);
                return res.json();
            })
            .then(data => {
                console.log('[AI Versions] Response payload:', data);
                document.getElementById('cv-versions-loading').style.display = 'none';
                if (data.quota) {
                    window.dispatchEvent(new CustomEvent('ai-quota:updated', { detail: data.quota }));
                }
                if (data.success && data.versions) {
                    window.lastGeneratedVersions = data.versions;
                    const resultsContainer = document.getElementById('cv-versions-results');
                    resultsContainer.classList.remove('hidden');

                    const angleIcons = {
                        leadership: 'groups',
                        technical: 'code',
                        ownership: 'verified_user'
                    };

                    data.versions.forEach(v => {
                        const div = document.createElement('div');
                        div.className = 'p-6 rounded-2xl border border-primary/10 bg-surface-container-low flex flex-col gap-4 h-full';

                        const warningHtml = v.warning ? `
                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex gap-2">
                                <span class="material-symbols-outlined text-[16px] shrink-0">warning</span>
                                <span>${t.version_warning_prefix}${escapeHtml([...(v.warning.entities || []), ...(v.warning.numbers || [])].join(', '))}.</span>
                            </div>
                        ` : '';

                        div.innerHTML = `
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-full bg-secondary/10 flex items-center justify-center text-secondary">
                                    <span class="material-symbols-outlined">${angleIcons[v.angle] || 'description'}</span>
                                </div>
                                <h4 class="font-bold text-primary text-lg">${angleLabel(v.angle)}</h4>
                            </div>
                            <p class="text-sm text-primary/70 leading-relaxed flex-1">${t.version_emphasizes.replace(':angle', (t.angle_labels[v.angle] || v.angle).toLowerCase())}</p>
                            ${warningHtml}
                            <div class="flex flex-col gap-2 w-full mt-auto">
                                <button onclick="openApplyVersionModal('${v.id}')" class="w-full py-2.5 bg-primary text-white hover:bg-primary/90 font-bold rounded-xl transition-colors text-sm flex items-center justify-center gap-2"><span class="material-symbols-outlined text-[16px]">check_circle</span> ${t.apply_this_version}</button>
                                <button onclick="previewCvVersion('${v.id}')" class="w-full py-2.5 bg-secondary/10 hover:bg-secondary text-secondary hover:text-white font-bold rounded-xl transition-colors text-sm flex items-center justify-center gap-2"><span class="material-symbols-outlined text-[16px]">visibility</span> ${t.preview_button}</button>
                                <button onclick="downloadCvVersion('${v.id}')" class="w-full py-2.5 border border-primary/20 hover:bg-primary/5 text-primary font-bold rounded-xl transition-colors text-sm flex items-center justify-center gap-2"><span class="material-symbols-outlined text-[16px]">download</span> ${t.download_pdf_button}</button>
                            </div>
                        `;
                        resultsContainer.appendChild(div);
                    });
                } else {
                    document.getElementById('cv-versions-loading').style.display = 'none';
                    const resultsContainer = document.getElementById('cv-versions-results');
                    resultsContainer.classList.remove('hidden');
                    resultsContainer.innerHTML = `<div class="col-span-full p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-center">${data.message || t.generate_versions_failed}</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('cv-versions-loading').style.display = 'none';
                const resultsContainer = document.getElementById('cv-versions-results');
                resultsContainer.classList.remove('hidden');
                resultsContainer.innerHTML = `<div class="col-span-full p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-center">${t.connection_error}</div>`;
            });
        }

        function previewCvVersion(id) {
            window.open(`/resumes/${window.editorConfig.cvId}/preview?adaptation_id=${id}`, '_blank');
        }

        function downloadCvVersion(id) {
            window.open(`/resumes/${window.editorConfig.cvId}/pdf?adaptation_id=${id}`, '_blank');
        }

        // ── Apply Tailored Version ──────────────────────────────────────
        let pendingApplyVersionId = null;

        function openApplyVersionModal(id) {
            pendingApplyVersionId = id;
            const version = (window.lastGeneratedVersions || []).find(v => v.id === id);

            const warningBox = document.getElementById('apply-version-warning');
            if (version && version.warning) {
                const flagged = [...(version.warning.entities || []), ...(version.warning.numbers || [])];
                warningBox.innerHTML = `<span class="material-symbols-outlined text-[16px] align-middle mr-1">warning</span>${t.apply_version_warning_prefix}${escapeHtml(flagged.join(', '))}. ${t.apply_version_warning_suffix}`;
                warningBox.classList.remove('hidden');
            } else {
                warningBox.classList.add('hidden');
                warningBox.innerHTML = '';
            }

            const modal = document.getElementById('apply-version-modal');
            const content = document.getElementById('apply-version-modal-content');
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.style.opacity = '1';
            modal.style.pointerEvents = 'auto';
            content.classList.replace('scale-95', 'scale-100');
        }

        function closeApplyVersionModal() {
            pendingApplyVersionId = null;
            const modal = document.getElementById('apply-version-modal');
            const content = document.getElementById('apply-version-modal-content');
            modal.style.opacity = '0';
            modal.style.pointerEvents = 'none';
            content.classList.replace('scale-100', 'scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        function confirmApplyVersion() {
            if (!pendingApplyVersionId) return;
            const id = pendingApplyVersionId;

            const confirmBtn = document.getElementById('apply-version-confirm-btn');
            confirmBtn.disabled = true;

            fetch(`/resumes/${window.editorConfig.cvId}/ai/versions/${id}/apply`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.editorConfig.csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(t.version_applied, 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    confirmBtn.disabled = false;
                    showToast(data.message || t.apply_version_failed, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                confirmBtn.disabled = false;
                showToast(t.version_not_applied, 'error');
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const confirmBtn = document.getElementById('apply-version-confirm-btn');
            if (confirmBtn) confirmBtn.addEventListener('click', confirmApplyVersion);
        });

        // ── CV History (snapshots) ──────────────────────────────────────
        const historyReasonLabels = {
            chameleon_apply: t.history_reason_chameleon_apply,
            pre_restore: t.history_reason_pre_restore,
        };

        function openHistoryModal() {
            const modal = document.getElementById('history-modal');
            const content = document.getElementById('history-modal-content');
            const list = document.getElementById('history-list');
            list.innerHTML = `<p class="text-sm text-primary/60 text-center py-8">${t.loading}</p>`;

            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.style.opacity = '1';
            modal.style.pointerEvents = 'auto';
            content.classList.replace('scale-95', 'scale-100');

            fetch(`/resumes/${window.editorConfig.cvId}/history`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                const snapshots = data.snapshots || [];
                if (snapshots.length === 0) {
                    list.innerHTML = `<p class="text-sm text-primary/60 text-center py-8">${t.no_history}</p>`;
                    return;
                }
                list.innerHTML = snapshots.map(s => {
                    const label = historyReasonLabels[s.reason] || t.history_reason_snapshot;
                    const date = new Date(s.created_at).toLocaleString();
                    return `
                        <div class="p-4 rounded-xl border border-primary/10 bg-surface-container-low flex items-center justify-between gap-4">
                            <div>
                                <p class="font-bold text-primary text-sm">${escapeHtml(label)}</p>
                                <p class="text-xs text-primary/60">${escapeHtml(date)}</p>
                            </div>
                            <button onclick="restoreSnapshot('${s.id}')" class="py-2 px-4 rounded-xl bg-secondary/10 hover:bg-secondary text-secondary hover:text-white font-bold text-xs transition-colors shrink-0">${t.restore}</button>
                        </div>
                    `;
                }).join('');
            })
            .catch(err => {
                console.error(err);
                list.innerHTML = `<p class="text-sm text-red-600 text-center py-8">${t.history_load_failed}</p>`;
            });
        }

        function closeHistoryModal() {
            const modal = document.getElementById('history-modal');
            const content = document.getElementById('history-modal-content');
            modal.style.opacity = '0';
            modal.style.pointerEvents = 'none';
            content.classList.replace('scale-100', 'scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        function restoreSnapshot(id) {
            if (!confirm(t.confirm_restore)) return;

            fetch(`/resumes/${window.editorConfig.cvId}/history/${id}/restore`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.editorConfig.csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(t.restored, 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || t.restore_failed, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast(t.restore_network_error, 'error');
            });
        }

        Object.assign(window, {
            previewPdf,
            handlePhotoUpload,
            removePhoto,
            downloadPdf,
            previewTemplate,
            resetPreview,
            openTemplateModal,
            closeTemplateModal,
            selectTemplate,
            switchMsTab,
            validateFormBeforeSave,
            openRefineModal,
            closeRefineModal,
            openCvVersionsModal,
            closeCvVersionsModal,
            generateCvVersions,
            previewCvVersion,
            downloadCvVersion,
            openApplyVersionModal,
            closeApplyVersionModal,
            confirmApplyVersion,
            openHistoryModal,
            closeHistoryModal,
            restoreSnapshot,
        });

        // ── Modal accessibility: Escape-to-close, focus trap, focus restore ──
        // (No body scroll-lock: the editor body is already `overflow-hidden`.)
        (function () {
            const closers = {
                'template-modal':      closeTemplateModal,
                'refine-modal':        closeRefineModal,
                'cv-versions-modal':   closeCvVersionsModal,
                'history-modal':       closeHistoryModal,
                'apply-version-modal': closeApplyVersionModal,
            };
            const FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
            let restoreTarget = null;

            const modalEls = () => Object.keys(closers)
                .map(id => document.getElementById(id))
                .filter(Boolean);
            const visibleFocusables = (el) =>
                [...el.querySelectorAll(FOCUSABLE)].filter(n => n.offsetParent !== null);

            // Track show/hide to manage focus on open and restore it on close.
            modalEls().forEach(el => {
                new MutationObserver(() => {
                    const open = !el.classList.contains('hidden');
                    if (open && !el.dataset.a11yOpen) {
                        el.dataset.a11yOpen = '1';
                        restoreTarget = document.activeElement;
                        setTimeout(() => { visibleFocusables(el)[0]?.focus(); }, 60);
                    } else if (!open && el.dataset.a11yOpen) {
                        delete el.dataset.a11yOpen;
                        if (typeof restoreTarget?.focus === 'function') restoreTarget.focus();
                        restoreTarget = null;
                    }
                }).observe(el, { attributes: true, attributeFilter: ['class'] });
            });

            document.addEventListener('keydown', (e) => {
                const open = modalEls().filter(el => !el.classList.contains('hidden'));
                if (!open.length) return;
                const modal = open[open.length - 1];

                if (e.key === 'Escape') {
                    // The template modal is mandatory when there is no CV yet — don't let Escape strand the user.
                    if (modal.id === 'template-modal' && !window.editorConfig.hasCv) return;
                    e.preventDefault();
                    closers[modal.id]?.();
                    return;
                }

                if (e.key === 'Tab') {
                    const f = visibleFocusables(modal);
                    if (!f.length) return;
                    const first = f[0], last = f[f.length - 1];
                    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
                    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
                    else if (!modal.contains(document.activeElement)) { e.preventDefault(); first.focus(); }
                }
            });
        })();


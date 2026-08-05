@props(['templates'])

@php
    $categories = $templates->pluck('category')->filter()->unique()->sort()->values();
    $user = auth()->user();
@endphp

<div id="create-modal"
     class="fixed inset-0 bg-surface/80 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-200 flex items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="create-modal-title"
     aria-describedby="create-modal-description">
    {{-- One form for the whole modal. Each template used to be its own form
         with an invisible full-card submit button, so merely clicking a card
         created the resume — no confirmation step and no way to guard against
         a double click producing two resumes. --}}
    <form id="create-modal-content"
          action="{{ route('resumes.store') }}"
          method="POST"
          onsubmit="return prepareCreateResumeSubmit(event)"
          class="bg-tertiary w-full max-w-5xl max-h-[90vh] rounded-lg shadow-2xl border border-primary/10 flex flex-col overflow-hidden transform scale-95 transition-transform duration-200 ease-out">
        @csrf

        <div class="p-6 border-b border-primary/10 flex justify-between items-start gap-4 bg-surface-container-low shrink-0">
            <div>
                <h3 id="create-modal-title" class="font-headline text-2xl font-bold text-primary">
                    Create New Resume
                </h3>
                <p id="create-modal-description" class="mt-1 text-sm text-primary/60">Name your resume, choose a template, then press Create.</p>
            </div>
            <button type="button"
                    onclick="closeCreateModal()"
                    aria-label="Close template selection"
                    class="inline-flex min-h-11 min-w-11 items-center justify-center text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">close</button>
        </div>

        <div class="p-6 overflow-y-auto custom-scrollbar bg-surface flex-1">
            <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-[1.4fr_1fr_0.8fr]">
                <div>
                    <label for="create-resume-title" class="block text-[11px] font-label text-primary/60 uppercase tracking-widest mb-1.5">
                        Resume Name <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <input id="create-resume-title"
                           name="title"
                           type="text"
                           maxlength="100"
                           required
                           autocomplete="off"
                           value="{{ old('title') }}"
                           placeholder="e.g. Senior Product Designer Resume"
                           aria-describedby="create-resume-title-error"
                           aria-invalid="false"
                           oninput="clearCreateResumeTitleError(); updateCreateResumeSubmitState()"
                           class="w-full bg-tertiary border border-primary/15 rounded-lg px-4 py-3 text-sm font-label text-primary placeholder:text-primary/35 focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20">
                    <p id="create-resume-title-error" class="hidden mt-1 text-xs text-red-600" role="alert">Please enter a resume name before creating.</p>
                </div>

                <div>
                    <label for="create-template-search" class="block text-[11px] font-label text-primary/60 uppercase tracking-widest mb-1.5">Search Templates</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-primary/35 text-[18px]" aria-hidden="true">search</span>
                        <input id="create-template-search"
                               type="search"
                               placeholder="Search by name..."
                               oninput="filterCreateResumeTemplates()"
                               class="w-full bg-tertiary border border-primary/15 rounded-lg pl-10 pr-4 py-3 text-sm font-label text-primary placeholder:text-primary/35 focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 lg:grid-cols-1">
                    <div>
                        <label for="create-template-category" class="block text-[11px] font-label text-primary/60 uppercase tracking-widest mb-1.5">Category</label>
                        <select id="create-template-category"
                                onchange="filterCreateResumeTemplates()"
                                class="w-full bg-tertiary border border-primary/15 rounded-lg px-3 py-3 text-sm font-label text-primary focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20">
                            <option value="">All</option>
                            @foreach($categories as $category)
                                <option value="{{ strtolower($category) }}">{{ ucfirst($category) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="create-template-access" class="block text-[11px] font-label text-primary/60 uppercase tracking-widest mb-1.5">Access</label>
                        <select id="create-template-access"
                                onchange="filterCreateResumeTemplates()"
                                class="w-full bg-tertiary border border-primary/15 rounded-lg px-3 py-3 text-sm font-label text-primary focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20">
                            <option value="">All</option>
                            <option value="free">Free</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="create-template-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach($templates as $template)
                    @php
                        $access = $template->is_premium ? 'premium' : 'free';
                        $category = strtolower($template->category ?? '');
                        $isLocked = $template->is_premium && !$user->canUsePremiumFeature('premium_templates');
                    @endphp

                    {{-- A label rather than a button: the card embeds an iframe
                         preview, which a button may not contain. Locked cards
                         carry no radio, so their label is inert and the upgrade
                         link inside keeps working. --}}
                    <label data-template-card
                          data-template-name="{{ strtolower($template->name) }}"
                          data-template-category="{{ $category }}"
                          data-template-access="{{ $access }}"
                          class="group relative border {{ $isLocked ? 'border-[#A16207]/30 bg-[#A16207]/[0.03]' : 'cursor-pointer border-primary/10 bg-tertiary hover:border-secondary hover:shadow-lg hover:-translate-y-1 has-[:checked]:border-secondary has-[:checked]:ring-2 has-[:checked]:ring-secondary has-[:checked]:shadow-lg focus-within:ring-2 focus-within:ring-secondary/40' }} rounded-lg overflow-hidden transition-all duration-200 ease-out">
                        @unless($isLocked)
                            <input type="radio"
                                   name="template_id"
                                   value="{{ $template->id }}"
                                   data-template-label="{{ $template->name }}"
                                   onchange="updateCreateResumeSubmitState()"
                                   @checked(old('template_id') == $template->id)
                                   class="peer sr-only">
                        @endunless

                        <div class="relative w-full aspect-[210/297] bg-surface-container-low overflow-hidden border-b border-primary/5">
                            <x-template-preview-frame
                                :template="$template"
                                src="{{ route('templates.demo', $template) }}"
                                title="{{ $template->name }} template preview"
                                iframe-class="template-thumbnail-iframe pointer-events-none transition-transform duration-500 origin-top-left" />
                            <div class="absolute inset-0 bg-transparent z-10"></div>

                            @if($template->is_premium)
                                <div class="absolute left-3 top-3 z-30 inline-flex items-center gap-1 rounded-full border border-[#A16207]/25 bg-tertiary/95 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-[#7C4A03] shadow-sm backdrop-blur">
                                    <span class="material-symbols-outlined text-[13px] icon-filled" aria-hidden="true">workspace_premium</span>
                                    Premium
                                </div>
                            @endif

                            @if($isLocked)
                                <div class="absolute inset-0 z-20 flex items-center justify-center bg-white/70 p-4 text-center opacity-100 backdrop-blur-[2px]">
                                    <div class="rounded-lg border border-[#A16207]/20 bg-tertiary/95 p-4 shadow-lg">
                                        <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-[#A16207]/10 text-[#7C4A03]">
                                            <span class="material-symbols-outlined icon-filled" aria-hidden="true">lock</span>
                                        </span>
                                        <p class="text-sm font-bold text-primary">Locked Premium Template</p>
                                        <p class="mt-1 text-xs leading-relaxed text-primary/60">Unlock polished layouts for senior roles and creative applications.</p>
                                        <a href="{{ route('user.upgrade-quota') }}"
                                           class="mt-3 inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-tertiary transition hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-[#A16207]/40">
                                            <span class="material-symbols-outlined text-[15px] icon-filled" aria-hidden="true">workspace_premium</span>
                                            Upgrade to Unlock
                                        </a>
                                    </div>
                                </div>
                            @else
                                <span class="absolute right-3 top-3 z-30 hidden h-7 w-7 items-center justify-center rounded-full bg-secondary text-white shadow peer-checked:flex" aria-hidden="true">
                                    <span class="material-symbols-outlined text-[16px] icon-filled">check</span>
                                </span>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 peer-checked:opacity-0 transition-opacity flex items-end justify-center pb-4 z-20">
                                    <span class="bg-secondary text-white text-xs px-3 py-1.5 rounded-full font-bold shadow-sm">{{ __('messages.resume.create.select_label') }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h4 class="font-bold text-sm text-primary {{ $isLocked ? '' : 'group-hover:text-secondary' }} transition-colors truncate">{{ $template->name }}</h4>
                                    <p class="text-[11px] text-primary/60 mt-1 line-clamp-1">
                                        {{ $template->category ? ucfirst($template->category) . ' · ' : '' }}{{ $template->is_premium ? 'Premium' : 'Free' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            <div id="create-template-empty" class="hidden rounded-lg border border-dashed border-primary/20 bg-tertiary px-6 py-10 text-center">
                <p class="font-headline text-xl font-bold text-primary">No templates match this filter</p>
                <p class="mt-1 text-sm text-primary/60">Try clearing the search or choosing another category.</p>
            </div>
        </div>

        <div class="shrink-0 border-t border-primary/10 bg-surface-container-low px-6 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p id="create-template-summary"
               class="text-sm text-primary/60"
               aria-live="polite"
               data-empty-label="{{ __('messages.resume.create.no_template_selected') }}"
               data-selected-prefix="{{ __('messages.resume.create.template_selected_prefix') }}">{{ __('messages.resume.create.no_template_selected') }}</p>

            <div class="flex items-center gap-3">
                <button type="button"
                        onclick="closeCreateModal()"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg border border-primary/15 px-5 py-2.5 text-sm font-bold text-primary transition hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">{{ __('messages.resume.create.cancel') }}</button>

                <button type="submit"
                        id="create-resume-submit"
                        disabled
                        data-loading-label="{{ __('messages.resume.create.creating') }}"
                        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-bold text-tertiary transition hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-secondary/40 disabled:cursor-not-allowed disabled:opacity-60">
                    <span id="create-resume-submit-spinner" class="material-symbols-outlined animate-spin text-[18px]" style="display:none" aria-hidden="true">progress_activity</span>
                    <span id="create-resume-submit-icon" class="material-symbols-outlined text-[18px]" aria-hidden="true">add</span>
                    <span id="create-resume-submit-label">{{ __('messages.resume.create.submit') }}</span>
                </button>
            </div>
        </div>
    </form>
</div>

@once
    @push('scripts')
        <script>
            function clearCreateResumeTitleError() {
                const titleInput = document.getElementById('create-resume-title');
                const error = document.getElementById('create-resume-title-error');
                if (!titleInput || !error) return;

                titleInput.setAttribute('aria-invalid', 'false');
                error.classList.add('hidden');
            }

            function selectedCreateResumeTemplate() {
                return document.querySelector('#create-template-grid input[name="template_id"]:checked');
            }

            function updateCreateResumeSubmitState() {
                const titleInput = document.getElementById('create-resume-title');
                const submit = document.getElementById('create-resume-submit');
                const summary = document.getElementById('create-template-summary');
                if (!submit) return;

                // A submission already in flight must stay locked, whatever the
                // fields say — this is the anti-spam guard.
                if (submit.dataset.submitting === 'true') return;

                const title = titleInput ? titleInput.value.trim() : '';
                const selected = selectedCreateResumeTemplate();

                submit.disabled = !title || !selected;

                if (summary) {
                    summary.textContent = selected
                        ? `${summary.dataset.selectedPrefix} ${selected.dataset.templateLabel}`
                        : summary.dataset.emptyLabel;
                }
            }

            function prepareCreateResumeSubmit(event) {
                const submit = document.getElementById('create-resume-submit');

                if (submit && submit.dataset.submitting === 'true') {
                    event.preventDefault();
                    return false;
                }

                const titleInput = document.getElementById('create-resume-title');
                const error = document.getElementById('create-resume-title-error');
                const title = titleInput ? titleInput.value.trim() : '';

                if (!title) {
                    event.preventDefault();
                    if (titleInput) {
                        titleInput.setAttribute('aria-invalid', 'true');
                        titleInput.focus();
                    }
                    if (error) {
                        error.classList.remove('hidden');
                    }
                    return false;
                }

                if (!selectedCreateResumeTemplate()) {
                    event.preventDefault();
                    return false;
                }

                if (submit) {
                    submit.dataset.submitting = 'true';
                    submit.disabled = true;
                    submit.setAttribute('aria-busy', 'true');
                    document.getElementById('create-resume-submit-spinner').style.display = '';
                    document.getElementById('create-resume-submit-icon').style.display = 'none';
                    document.getElementById('create-resume-submit-label').textContent = submit.dataset.loadingLabel;
                }

                return true;
            }

            function filterCreateResumeTemplates() {
                const query = (document.getElementById('create-template-search')?.value || '').trim().toLowerCase();
                const category = document.getElementById('create-template-category')?.value || '';
                const access = document.getElementById('create-template-access')?.value || '';
                const cards = document.querySelectorAll('[data-template-card]');
                const empty = document.getElementById('create-template-empty');
                let visibleCount = 0;

                cards.forEach((card) => {
                    const nameMatches = !query || card.dataset.templateName.includes(query);
                    const categoryMatches = !category || card.dataset.templateCategory === category;
                    const accessMatches = !access || card.dataset.templateAccess === access;
                    const visible = nameMatches && categoryMatches && accessMatches;

                    card.classList.toggle('hidden', !visible);
                    if (visible) visibleCount++;
                });

                // Never submit a template the user can no longer see.
                const selected = selectedCreateResumeTemplate();
                if (selected && selected.closest('[data-template-card]')?.classList.contains('hidden')) {
                    selected.checked = false;
                }
                updateCreateResumeSubmitState();

                if (empty) {
                    empty.classList.toggle('hidden', visibleCount !== 0);
                }

                if (typeof scaleThumbnails === 'function') {
                    requestAnimationFrame(() => {
                        scaleThumbnails();
                        window.queueTemplatePreviewFrames?.(document.getElementById('create-template-grid'));
                    });
                }
            }

            document.addEventListener('DOMContentLoaded', updateCreateResumeSubmitState);
        </script>
    @endpush
@endonce

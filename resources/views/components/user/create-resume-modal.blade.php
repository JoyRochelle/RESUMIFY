@props(['templates'])

@php
    $categories = $templates->pluck('category')->filter()->unique()->sort()->values();
    $user = auth()->user();
@endphp

<div id="create-modal"
     class="fixed inset-0 bg-surface/80 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="create-modal-title"
     aria-describedby="create-modal-description">
    <div id="create-modal-content"
         class="bg-tertiary w-full max-w-5xl max-h-[90vh] rounded-2xl shadow-2xl border border-primary/10 flex flex-col overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-primary/10 flex justify-between items-start gap-4 bg-surface-container-low">
            <div>
                <h3 id="create-modal-title" class="font-headline text-2xl font-bold text-primary">
                    Create New Resume
                </h3>
                <p id="create-modal-description" class="mt-1 text-sm text-primary/60">Name your resume first, then choose the template that fits your target role.</p>
            </div>
            <button type="button"
                    onclick="closeCreateModal()"
                    aria-label="Close template selection"
                    class="text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full p-2 hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">close</button>
        </div>

        <div class="p-6 overflow-y-auto custom-scrollbar bg-surface flex-1">
            <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-[1.4fr_1fr_0.8fr]">
                <div>
                    <label for="create-resume-title" class="block text-[11px] font-label text-primary/60 uppercase tracking-widest mb-1.5">
                        Resume Name <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <input id="create-resume-title"
                           type="text"
                           maxlength="100"
                           required
                           autocomplete="off"
                           value="{{ old('title') }}"
                           placeholder="e.g. Senior Product Designer Resume"
                           aria-describedby="create-resume-title-error"
                           aria-invalid="false"
                           oninput="clearCreateResumeTitleError()"
                           class="w-full bg-tertiary border border-primary/15 rounded-lg px-4 py-3 text-sm font-label text-primary placeholder:text-primary/35 focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20">
                    <p id="create-resume-title-error" class="hidden mt-1 text-xs text-red-600" role="alert">Please enter a resume name before choosing a template.</p>
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

            <div id="create-template-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6" aria-live="polite">
                @foreach($templates as $template)
                    @php
                        $access = $template->is_premium ? 'premium' : 'free';
                        $category = strtolower($template->category ?? '');
                        $isLocked = $template->is_premium && !$user->canUsePremiumFeature('premium_templates');
                    @endphp
                    <form action="{{ route('resumes.store') }}"
                          method="POST"
                          onsubmit="{{ $isLocked ? 'return false' : 'return prepareCreateResumeSubmit(event)' }}"
                          data-template-card
                          data-template-name="{{ strtolower($template->name) }}"
                          data-template-category="{{ $category }}"
                          data-template-access="{{ $access }}"
                          class="group relative border {{ $isLocked ? 'border-[#A16207]/30 bg-[#A16207]/[0.03]' : 'border-primary/10 bg-tertiary hover:border-secondary hover:shadow-lg hover:-translate-y-1' }} rounded-xl overflow-hidden transition-all duration-200">
                        @csrf
                        <input type="hidden" name="title" class="js-create-resume-title-value" value="{{ old('title') }}">
                        <input type="hidden" name="template_id" value="{{ $template->id }}">

                        <div class="relative w-full aspect-[210/297] bg-surface-container-low overflow-hidden border-b border-primary/5">
                            <iframe src="{{ route('templates.demo', $template) }}"
                                    style="width: 794px; height: 1123px; transform-origin: top left; border: none; position: absolute; top: 0; left: 0;"
                                    class="template-thumbnail-iframe pointer-events-none transition-transform duration-500 origin-top-left"
                                    loading="lazy"
                                    tabindex="-1">
                            </iframe>
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
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4 z-20">
                                    <span class="bg-secondary text-white text-xs px-3 py-1.5 rounded-full font-bold shadow-sm">Use Template</span>
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

                        @unless($isLocked)
                            <button type="submit"
                                    class="absolute inset-0 w-full h-full opacity-0 z-30 cursor-pointer"
                                    aria-label="Use {{ $template->name }} template"></button>
                        @endunless
                    </form>
                @endforeach
            </div>

            <div id="create-template-empty" class="hidden rounded-lg border border-dashed border-primary/20 bg-tertiary px-6 py-10 text-center">
                <p class="font-headline text-xl font-bold text-primary">No templates match this filter</p>
                <p class="mt-1 text-sm text-primary/60">Try clearing the search or choosing another category.</p>
            </div>
        </div>
    </div>
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

            function prepareCreateResumeSubmit(event) {
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

                event.currentTarget.querySelector('.js-create-resume-title-value').value = title;
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

                if (empty) {
                    empty.classList.toggle('hidden', visibleCount !== 0);
                }

                if (typeof scaleThumbnails === 'function') {
                    requestAnimationFrame(scaleThumbnails);
                }
            }
        </script>
    @endpush
@endonce

<!-- Work Experience -->
                    <x-user.editor-accordion title="{{ __('messages.editor.sections.work_experience.title') }}" icon="work">
                        <form class="section-form" data-section-id="{{ $experience->id ?? '' }}">
                            <div class="space-y-6" id="experience-list">
                                @forelse($expContent as $index => $job)
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-ui.form-input label="{{ __('messages.editor.sections.work_experience.job_title') }}" name="title" value="{{ $job['title'] ?? '' }}" class="auto-save" :required="true" placeholder="{{ __('messages.editor.sections.work_experience.job_title_placeholder') }}" />
                                        <x-ui.form-input label="{{ __('messages.editor.sections.work_experience.company') }}" name="company" value="{{ $job['company'] ?? '' }}" class="auto-save" :required="true" placeholder="{{ __('messages.editor.sections.work_experience.company_placeholder') }}" />
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-ui.form-input label="{{ __('messages.editor.sections.work_experience.start_date') }}" name="start_date" type="month" value="{{ $job['start_date'] ?? '' }}" class="auto-save" />
                                        <x-ui.form-input label="{{ __('messages.editor.sections.work_experience.end_date') }}" name="end_date" type="month" value="{{ $job['end_date'] ?? '' }}" class="auto-save" hint="{{ __('messages.editor.sections.work_experience.end_date_hint') }}" />
                                    </div>
                                    <div class="relative mt-2">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">{{ __('messages.editor.sections.work_experience.description') }}</label>
                                        <textarea name="description" placeholder="{{ __('messages.editor.sections.work_experience.description_placeholder') }}" class="auto-save w-full bg-surface-container-low rounded-lg scroll-pad-b ring-1 ring-inset ring-primary/10 focus:ring-secondary p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="4">{{ $job['description'] ?? '' }}</textarea>
                                        <button type="button" onclick="openRefineModal(this)" class="absolute bottom-3 right-3 text-[10px] font-bold bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-2 py-1 rounded transition-colors flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-[12px]">auto_awesome</span>{{ __('messages.editor.sections.work_experience.refine') }}</button>
                                    </div>
                                </div>
                                @empty
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-ui.form-input label="{{ __('messages.editor.sections.work_experience.job_title') }}" name="title" value="" class="auto-save" />
                                        <x-ui.form-input label="{{ __('messages.editor.sections.work_experience.company') }}" name="company" value="" class="auto-save" />
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-ui.form-input label="{{ __('messages.editor.sections.work_experience.start_date') }}" name="start_date" type="month" value="" class="auto-save" />
                                        <x-ui.form-input label="{{ __('messages.editor.sections.work_experience.end_date') }}" name="end_date" type="month" value="" class="auto-save" hint="{{ __('messages.editor.sections.work_experience.end_date_hint') }}" />
                                    </div>
                                    <div class="relative mt-2">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">{{ __('messages.editor.sections.work_experience.description') }}</label>
                                        <textarea name="description" placeholder="{{ __('messages.editor.sections.work_experience.description_placeholder') }}" class="auto-save w-full bg-surface-container-low rounded-lg scroll-pad-b ring-1 ring-inset ring-primary/10 focus:ring-secondary p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="4"></textarea>
                                        <button type="button" onclick="openRefineModal(this)" class="absolute bottom-3 right-3 text-[10px] font-bold bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-2 py-1 rounded transition-colors flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-[12px]">auto_awesome</span>{{ __('messages.editor.sections.work_experience.refine') }}</button>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            <div class="mt-4">
                                <button type="button" onclick="addListItem('experience-list', this)" class="w-full py-3 rounded-xl border border-dashed border-primary/30 text-primary/70 hover:bg-primary/5 hover:text-primary transition-colors flex items-center justify-center gap-2 font-bold text-sm">
                                    <span class="material-symbols-outlined text-[20px]">add_circle</span> {{ __('messages.editor.sections.work_experience.add') }}
                                </button>
                            </div>
                        </form>
                    </x-user.editor-accordion>
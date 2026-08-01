<!-- Education -->
                    <x-user.editor-accordion data-tour="section-education" title="{{ __('messages.editor.sections.education.title') }}" icon="school">
                        <form class="section-form" data-section-id="{{ $education->id ?? '' }}">
                            <div class="space-y-6" id="education-list">
                                @forelse($eduContent as $index => $edu)
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-ui.form-input label="{{ __('messages.editor.sections.education.degree') }}" name="degree" value="{{ $edu['degree'] ?? '' }}" class="auto-save" :required="true" placeholder="{{ __('messages.editor.sections.education.degree_placeholder') }}" />
                                        <x-ui.form-input label="{{ __('messages.editor.sections.education.school') }}" name="school" value="{{ $edu['school'] ?? '' }}" class="auto-save" :required="true" placeholder="{{ __('messages.editor.sections.education.school_placeholder') }}" />
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-ui.form-input label="{{ __('messages.editor.sections.education.start_date') }}" name="start_date" type="month" value="{{ $edu['start_date'] ?? '' }}" class="auto-save" />
                                        <x-ui.form-input label="{{ __('messages.editor.sections.education.end_date') }}" name="end_date" type="month" value="{{ $edu['end_date'] ?? '' }}" class="auto-save" hint="{{ __('messages.editor.sections.education.end_date_hint') }}" />
                                    </div>
                                    <div class="relative mt-2">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">{{ __('messages.editor.sections.education.additional_info') }}</label>
                                        <textarea name="description" class="auto-save w-full bg-surface-container-low rounded-lg scroll-pad-b ring-1 ring-primary/10 focus:ring-secondary p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="2">{{ $edu['description'] ?? '' }}</textarea>
                                    </div>
                                </div>
                                @empty
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-ui.form-input label="{{ __('messages.editor.sections.education.degree') }}" name="degree" value="" class="auto-save" />
                                        <x-ui.form-input label="{{ __('messages.editor.sections.education.school') }}" name="school" value="" class="auto-save" />
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-ui.form-input label="{{ __('messages.editor.sections.education.start_date') }}" name="start_date" type="month" value="" class="auto-save" />
                                        <x-ui.form-input label="{{ __('messages.editor.sections.education.end_date') }}" name="end_date" type="month" value="" class="auto-save" hint="{{ __('messages.editor.sections.education.end_date_hint') }}" />
                                    </div>
                                    <div class="relative mt-2">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">{{ __('messages.editor.sections.education.additional_info') }}</label>
                                        <textarea name="description" class="auto-save w-full bg-surface-container-low rounded-lg scroll-pad-b ring-1 ring-primary/10 focus:ring-secondary p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="2"></textarea>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            <div class="mt-4">
                                <button type="button" onclick="addListItem('education-list', this)" class="w-full py-3 rounded-xl border border-dashed border-primary/30 text-primary/70 hover:bg-primary/5 hover:text-primary transition-colors flex items-center justify-center gap-2 font-bold text-sm">
                                    <span class="material-symbols-outlined text-[20px]">add_circle</span> {{ __('messages.editor.sections.education.add') }}
                                </button>
                            </div>
                        </form>
                    </x-user.editor-accordion>
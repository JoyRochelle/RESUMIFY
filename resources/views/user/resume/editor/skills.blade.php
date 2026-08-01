<!-- Skills -->
                    <x-user.editor-accordion data-tour="section-skills" title="{{ __('messages.editor.sections.skills.title') }}" icon="bolt">
                        <form class="section-form" data-section-id="{{ $skills->id ?? '' }}">
                            <div class="grid grid-cols-1 gap-4" id="skills-list">
                                @forelse($skillsContent as $index => $skill)
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="flex gap-4 items-center w-full">
                                        <div class="flex-1">
                                        <x-ui.form-input label="{{ __('messages.editor.sections.skills.skill_name') }}" name="name" value="{{ $skill['name'] ?? '' }}" class="auto-save" />
                                    </div>
                                    <div class="flex-1">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-1 block">{{ __('messages.editor.sections.skills.proficiency_level') }}</label>
                                        <div class="relative">
                                            <select name="level" class="auto-save w-full border-b-2 border-primary/15 focus:border-secondary bg-transparent py-2 pl-0 pr-6 outline-none transition-all duration-200 text-primary text-sm appearance-none cursor-pointer">
                                                <option value="">{{ __('messages.editor.sections.skills.select_level') }}</option>
                                                @foreach(['Beginner','Elementary','Intermediate','Advanced','Expert'] as $lvl)
                                                    <option value="{{ $lvl }}" {{ ($skill['level'] ?? '') === $lvl ? 'selected' : '' }}>{{ __('messages.editor.sections.skills.levels.' . $lvl) }}</option>
                                                @endforeach
                                            </select>
                                            <span class="material-symbols-outlined pointer-events-none absolute right-0 top-1/2 -translate-y-1/2 text-primary/40 text-[20px]">expand_more</span>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                @empty
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="flex gap-4 items-center w-full">
                                        <div class="flex-1">
                                        <x-ui.form-input label="{{ __('messages.editor.sections.skills.skill_name') }}" name="name" value="" class="auto-save" />
                                    </div>
                                    <div class="flex-1">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-1 block">{{ __('messages.editor.sections.skills.proficiency_level') }}</label>
                                        <div class="relative">
                                            <select name="level" class="auto-save w-full border-b-2 border-primary/15 focus:border-secondary bg-transparent py-2 pl-0 pr-6 outline-none transition-all duration-200 text-primary text-sm appearance-none cursor-pointer">
                                                <option value="">{{ __('messages.editor.sections.skills.select_level') }}</option>
                                                @foreach(['Beginner','Elementary','Intermediate','Advanced','Expert'] as $lvl)
                                                    <option value="{{ $lvl }}">{{ __('messages.editor.sections.skills.levels.' . $lvl) }}</option>
                                                @endforeach
                                            </select>
                                            <span class="material-symbols-outlined pointer-events-none absolute right-0 top-1/2 -translate-y-1/2 text-primary/40 text-[20px]">expand_more</span>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            <div class="mt-4">
                                <button type="button" onclick="addListItem('skills-list', this)" class="w-full py-3 rounded-xl border border-dashed border-primary/30 text-primary/70 hover:bg-primary/5 hover:text-primary transition-colors flex items-center justify-center gap-2 font-bold text-sm">
                                    <span class="material-symbols-outlined text-[20px]">add_circle</span> {{ __('messages.editor.sections.skills.add') }}
                                </button>
                            </div>
                        </form>
                    </x-user.editor-accordion>
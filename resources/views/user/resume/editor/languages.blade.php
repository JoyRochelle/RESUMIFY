<!-- Languages -->
                    @if($languages)
                    <x-user.editor-accordion title="{{ __('messages.editor.sections.languages.title') }}" icon="translate" id="section-languages">
                        <form class="section-form" data-section-id="{{ $languages->id }}">
                            <div class="grid grid-cols-1 gap-4" id="languages-list">
                                @forelse($langsContent as $index => $lang)
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="flex gap-4 items-center w-full">
                                        <div class="flex-1">
                                        <x-ui.form-input label="{{ __('messages.editor.sections.languages.language') }}" name="name" value="{{ $lang['name'] ?? '' }}" class="auto-save" />
                                    </div>
                                    <div class="flex-1">
                                        <div class="relative">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-1 block">{{ __('messages.editor.sections.languages.proficiency') }}</label>
                                        <select name="level" class="auto-save w-full border-b-2 border-primary/15 focus:border-secondary bg-transparent py-2 px-0 outline-none transition-all duration-200 text-primary text-sm appearance-none cursor-pointer">
                                            <option value="">{{ __('messages.editor.sections.languages.select_level') }}</option>
                                            @foreach(['Beginner','Conversational','Fluent','Native'] as $lvl)
                                                <option value="{{ $lvl }}" {{ ($lang['level'] ?? '') === $lvl ? 'selected' : '' }}>{{ __('messages.editor.sections.languages.levels.' . $lvl) }}</option>
                                            @endforeach
                                        </select>
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
                                        <x-ui.form-input label="{{ __('messages.editor.sections.languages.language') }}" name="name" value="" class="auto-save" />
                                    </div>
                                    <div class="flex-1">
                                        <div class="relative">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-1 block">{{ __('messages.editor.sections.languages.proficiency') }}</label>
                                        <select name="level" class="auto-save w-full border-b-2 border-primary/15 focus:border-secondary bg-transparent py-2 px-0 outline-none transition-all duration-200 text-primary text-sm appearance-none cursor-pointer">
                                            <option value="">{{ __('messages.editor.sections.languages.select_level') }}</option>
                                            @foreach(['Beginner','Conversational','Fluent','Native'] as $lvl)
                                                <option value="{{ $lvl }}">{{ __('messages.editor.sections.languages.levels.' . $lvl) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    </div>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            <div class="mt-4 flex gap-2">
                                <button type="button" onclick="addListItem('languages-list', this)" class="flex-1 py-3 rounded-xl border border-dashed border-primary/30 text-primary/70 hover:bg-primary/5 hover:text-primary transition-colors flex items-center justify-center gap-2 font-bold text-sm">
                                    <span class="material-symbols-outlined text-[20px]">add_circle</span> {{ __('messages.editor.sections.languages.add') }}
                                </button>
                                <button type="button" onclick="deleteSection('{{ $languages->id }}')" class="py-3 px-4 rounded-xl border border-red-200 text-red-500 hover:bg-red-50 transition-colors flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </div>
                        </form>
                    </x-user.editor-accordion>
                    @endif
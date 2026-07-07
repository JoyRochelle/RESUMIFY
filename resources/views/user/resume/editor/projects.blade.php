<!-- Projects -->
                    @if($projects)
                    <x-user.editor-accordion title="{{ __('messages.editor.sections.projects.title') }}" icon="rocket_launch" id="section-projects">
                        <form class="section-form" data-section-id="{{ $projects->id }}">
                            <div class="space-y-6" id="projects-list">
                                @forelse($projectsContent as $index => $project)
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="grid grid-cols-1 gap-4">
                                        <x-ui.form-input label="{{ __('messages.editor.sections.projects.name') }}" name="name" value="{{ $project['name'] ?? '' }}" class="auto-save" />
                                        <x-ui.form-input label="{{ __('messages.editor.sections.projects.url') }}" name="url" value="{{ $project['url'] ?? '' }}" class="auto-save" />
                                        <div class="relative group mt-2">
                                            <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">{{ __('messages.editor.sections.projects.description') }}</label>
                                            <textarea name="description" class="auto-save w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-0 p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="3">{{ $project['description'] ?? '' }}</textarea>
                                            <button type="button" onclick="openRefineModal(this)" class="absolute bottom-3 right-3 text-[10px] font-bold bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-2 py-1 rounded transition-colors flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-[12px]">auto_awesome</span>{{ __('messages.editor.sections.projects.refine') }}</button>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="grid grid-cols-1 gap-4">
                                        <x-ui.form-input label="{{ __('messages.editor.sections.projects.name') }}" name="name" value="" class="auto-save" />
                                        <x-ui.form-input label="{{ __('messages.editor.sections.projects.url') }}" name="url" value="" class="auto-save" />
                                        <div class="relative group mt-2">
                                            <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">{{ __('messages.editor.sections.projects.description') }}</label>
                                            <textarea name="description" class="auto-save w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-0 p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="3"></textarea>
                                            <button type="button" onclick="openRefineModal(this)" class="absolute bottom-3 right-3 text-[10px] font-bold bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-2 py-1 rounded transition-colors flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-[12px]">auto_awesome</span>{{ __('messages.editor.sections.projects.refine') }}</button>
                                        </div>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            <div class="mt-4 flex gap-2">
                                <button type="button" onclick="addListItem('projects-list', this)" class="flex-1 py-3 rounded-xl border border-dashed border-primary/30 text-primary/70 hover:bg-primary/5 hover:text-primary transition-colors flex items-center justify-center gap-2 font-bold text-sm">
                                    <span class="material-symbols-outlined text-[20px]">add_circle</span> {{ __('messages.editor.sections.projects.add') }}
                                </button>
                                <button type="button" onclick="deleteSection('{{ $projects->id }}')" class="py-3 px-4 rounded-xl border border-red-200 text-red-500 hover:bg-red-50 transition-colors flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </div>
                        </form>
                    </x-user.editor-accordion>
                    @endif
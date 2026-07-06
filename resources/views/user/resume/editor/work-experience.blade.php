<!-- Work Experience -->
                    <x-user.editor-accordion title="Work Experience" icon="work">
                        <form class="section-form" data-section-id="{{ $experience->id ?? '' }}">
                            <div class="space-y-6" id="experience-list">
                                @forelse($expContent as $index => $job)
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-ui.form-input label="Job Title" name="title" value="{{ $job['title'] ?? '' }}" class="auto-save" :required="true" placeholder="e.g. Software Engineer" />
                                        <x-ui.form-input label="Company" name="company" value="{{ $job['company'] ?? '' }}" class="auto-save" :required="true" placeholder="e.g. Acme Corp" />
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-ui.form-input label="Start Date" name="start_date" type="month" value="{{ $job['start_date'] ?? '' }}" class="auto-save" />
                                        <x-ui.form-input label="End Date" name="end_date" type="month" value="{{ $job['end_date'] ?? '' }}" class="auto-save" hint="Leave blank if current" />
                                    </div>
                                    <div class="relative mt-2">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Description</label>
                                        <textarea name="description" placeholder="Describe your key responsibilities and achievements..." class="auto-save w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-0 p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="4">{{ $job['description'] ?? '' }}</textarea>
                                        <button type="button" onclick="openRefineModal(this)" class="absolute bottom-3 right-3 text-[10px] font-bold bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-2 py-1 rounded transition-colors flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-[12px]">auto_awesome</span>Refine</button>
                                    </div>
                                </div>
                                @empty
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-ui.form-input label="Job Title" name="title" value="" class="auto-save" />
                                        <x-ui.form-input label="Company" name="company" value="" class="auto-save" />
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-ui.form-input label="Start Date" name="start_date" type="month" value="" class="auto-save" />
                                        <x-ui.form-input label="End Date" name="end_date" type="month" value="" class="auto-save" hint="Leave blank if current" />
                                    </div>
                                    <div class="relative mt-2">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Description</label>
                                        <textarea name="description" placeholder="Describe your key responsibilities and achievements..." class="auto-save w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-0 p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="4"></textarea>
                                        <button type="button" onclick="openRefineModal(this)" class="absolute bottom-3 right-3 text-[10px] font-bold bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-2 py-1 rounded transition-colors flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-[12px]">auto_awesome</span>Refine</button>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            <div class="mt-4">
                                <button type="button" onclick="addListItem('experience-list', this)" class="w-full py-3 rounded-xl border border-dashed border-primary/30 text-primary/70 hover:bg-primary/5 hover:text-primary transition-colors flex items-center justify-center gap-2 font-bold text-sm">
                                    <span class="material-symbols-outlined text-[20px]">add_circle</span> Add Experience
                                </button>
                            </div>
                        </form>
                    </x-user.editor-accordion>
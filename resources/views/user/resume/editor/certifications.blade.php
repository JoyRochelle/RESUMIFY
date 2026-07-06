<!-- Certifications -->
                    @if($certifications)
                    <x-user.editor-accordion title="{{ __('messages.editor.sections.certifications.title') }}" icon="workspace_premium" id="section-certifications">
                        <form class="section-form" data-section-id="{{ $certifications->id }}">
                            <div class="space-y-6" id="certifications-list">
                                @forelse($certsContent as $index => $cert)
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <x-ui.form-input label="{{ __('messages.editor.sections.certifications.name') }}" name="name" value="{{ $cert['name'] ?? '' }}" class="auto-save" />
                                        <x-ui.form-input label="{{ __('messages.editor.sections.certifications.issuer') }}" name="issuer" value="{{ $cert['issuer'] ?? '' }}" class="auto-save" />
                                        <x-ui.form-input label="{{ __('messages.editor.sections.certifications.date') }}" name="date" value="{{ $cert['date'] ?? '' }}" class="auto-save" type="month" />
                                    </div>
                                </div>
                                @empty
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <x-ui.form-input label="{{ __('messages.editor.sections.certifications.name') }}" name="name" value="" class="auto-save" />
                                        <x-ui.form-input label="{{ __('messages.editor.sections.certifications.issuer') }}" name="issuer" value="" class="auto-save" />
                                        <x-ui.form-input label="{{ __('messages.editor.sections.certifications.date') }}" name="date" value="" class="auto-save" type="month" />
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            <div class="mt-4 flex gap-2">
                                <button type="button" onclick="addListItem('certifications-list', this)" class="flex-1 py-3 rounded-xl border border-dashed border-primary/30 text-primary/70 hover:bg-primary/5 hover:text-primary transition-colors flex items-center justify-center gap-2 font-bold text-sm">
                                    <span class="material-symbols-outlined text-[20px]">add_circle</span> {{ __('messages.editor.sections.certifications.add') }}
                                </button>
                                <button type="button" onclick="deleteSection('{{ $certifications->id }}')" class="py-3 px-4 rounded-xl border border-red-200 text-red-500 hover:bg-red-50 transition-colors flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </div>
                        </form>
                    </x-user.editor-accordion>
                    @endif
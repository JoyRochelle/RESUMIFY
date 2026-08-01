<!-- Target Job (for ATS scoring) -->
                    <x-user.editor-accordion title="{{ __('messages.editor.sections.target_job.title') }}" icon="target" :isOpen="true" data-tour="section-target-job">
                        <form class="section-form" data-section-id="{{ $targetJob->id ?? '' }}">
                            <div class="grid grid-cols-1 gap-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <x-ui.form-input label="{{ __('messages.editor.sections.target_job.job_title') }}" name="job_title" value="{{ $targetJobContent['job_title'] ?? '' }}" class="auto-save" placeholder="{{ __('messages.editor.sections.target_job.job_title_placeholder') }}" />
                                    <x-ui.form-input label="{{ __('messages.editor.sections.target_job.job_company') }}" name="job_company" value="{{ $targetJobContent['job_company'] ?? '' }}" class="auto-save" placeholder="{{ __('messages.editor.sections.target_job.job_company_placeholder') }}" />
                                </div>
                                <div class="relative group mt-2">
                                    <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">{{ __('messages.editor.sections.target_job.job_description') }}</label>
                                    <textarea name="job_description" class="auto-save w-full bg-surface-container-low rounded-lg scroll-pad-b ring-1 ring-primary/10 focus:ring-secondary p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="6" placeholder="{{ __('messages.editor.sections.target_job.job_description_placeholder') }}">{{ $targetJobContent['job_description'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </form>
                    </x-user.editor-accordion>
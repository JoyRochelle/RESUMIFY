<!-- Target Job (for ATS scoring) -->
                    <x-user.editor-accordion title="Target Job" icon="target" :isOpen="true">
                        <form class="section-form" data-section-id="{{ $targetJob->id ?? '' }}">
                            <div class="grid grid-cols-1 gap-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <x-ui.form-input label="Target Job Title" name="job_title" value="{{ $targetJobContent['job_title'] ?? '' }}" class="auto-save" placeholder="e.g. Senior Software Engineer" />
                                    <x-ui.form-input label="Target Company" name="job_company" value="{{ $targetJobContent['job_company'] ?? '' }}" class="auto-save" placeholder="e.g. Acme Corp" />
                                </div>
                                <div class="relative group mt-2">
                                    <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Job Description</label>
                                    <textarea name="job_description" class="auto-save w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-0 p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="6" placeholder="Paste the job description here to see how well your resume matches...">{{ $targetJobContent['job_description'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </form>
                    </x-user.editor-accordion>
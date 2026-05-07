@extends('layouts.user.app')

@section('title', 'Resumify Workspace | Editor')

@section('body_class', 'h-screen flex overflow-hidden')

@section('content')
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        {{-- Page Header --}}
        <x-user.page-header title="Editor: Senior Product Designer">
            <x-user.button onclick="previewPdf('{{ auth()->user()->cvs()->latest()->first()->id ?? 1 }}')" variant="ghost" class="text-sm px-3 hidden sm:flex">Preview</x-user.button>
            <x-user.button id="download-btn" onclick="downloadPdf('{{ auth()->user()->cvs()->latest()->first()->id ?? 1 }}')" variant="primary" icon="download" iconClass="text-[18px]" class="text-sm px-3 w-full sm:w-auto justify-center">Download PDF</x-user.button>
        </x-user.page-header>

        <div class="flex-1 flex flex-col lg:flex-row overflow-y-auto lg:overflow-hidden pb-20 lg:pb-0">
            
            <aside class="w-full lg:w-[40%] bg-surface-container-low flex flex-col border-b lg:border-b-0 lg:border-r border-primary/10 z-20 shrink-0 lg:h-full">
                <div class="p-4 lg:p-6 lg:overflow-y-auto custom-scrollbar space-y-6 lg:h-full">
                    
                    @php
                        $personal   = $cv ? $cv->sections->where('type', 'personal_info')->first() : null;
                        $experience = $cv ? $cv->sections->where('type', 'work_experience')->first() : null;
                        $education  = $cv ? $cv->sections->where('type', 'education')->first() : null;
                        $skills     = $cv ? $cv->sections->where('type', 'skills')->first() : null;
                        $personalContent = $personal ? ($personal->content ?? []) : [];
                        $expContent = $experience ? ($experience->content ?? []) : [];
                        $eduContent = $education ? ($education->content ?? []) : [];
                        $skillsContent = $skills ? ($skills->content ?? []) : [];
                    @endphp

                    <!-- Personal Info -->
                    <x-user.editor-accordion title="Personal Info" icon="person" :isOpen="true">
                        <form class="section-form" data-section-id="{{ $personal->id ?? '' }}">
                            <div class="grid grid-cols-1 gap-4">
                                <x-user.form-input label="Full Name" name="name" value="{{ $personalContent['name'] ?? '' }}" class="auto-save" />
                                <x-user.form-input label="Professional Title" name="title" value="{{ $personalContent['title'] ?? '' }}" class="auto-save" />
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <x-user.form-input label="Email" name="email" type="email" value="{{ $personalContent['email'] ?? '' }}" class="auto-save" />
                                    <x-user.form-input label="Phone" name="phone" value="{{ $personalContent['phone'] ?? '' }}" class="auto-save" />
                                </div>
                                <x-user.form-input label="Location" name="location" value="{{ $personalContent['location'] ?? '' }}" class="auto-save" />
                                <div class="relative group mt-2">
                                    <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Professional Summary</label>
                                    <textarea name="summary" class="auto-save w-full bg-surface-container-low rounded-lg border-none focus:ring-1 focus:ring-primary p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none" rows="5">{{ $personalContent['summary'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </form>
                    </x-user.editor-accordion>
                    
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
                                        <x-user.form-input label="Job Title" name="title" value="{{ $job['title'] ?? '' }}" class="auto-save" />
                                        <x-user.form-input label="Company" name="company" value="{{ $job['company'] ?? '' }}" class="auto-save" />
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-user.form-input label="Start Date" name="start_date" value="{{ $job['start_date'] ?? '' }}" class="auto-save" />
                                        <x-user.form-input label="End Date" name="end_date" value="{{ $job['end_date'] ?? '' }}" class="auto-save" />
                                    </div>
                                    <div class="relative mt-2">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Description</label>
                                        <textarea name="description" class="auto-save w-full bg-surface-container-low rounded-lg border-none focus:ring-1 focus:ring-primary p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none" rows="4">{{ $job['description'] ?? '' }}</textarea>
                                    </div>
                                </div>
                                @empty
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-user.form-input label="Job Title" name="title" value="" class="auto-save" />
                                        <x-user.form-input label="Company" name="company" value="" class="auto-save" />
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-user.form-input label="Start Date" name="start_date" value="" class="auto-save" />
                                        <x-user.form-input label="End Date" name="end_date" value="" class="auto-save" />
                                    </div>
                                    <div class="relative mt-2">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Description</label>
                                        <textarea name="description" class="auto-save w-full bg-surface-container-low rounded-lg border-none focus:ring-1 focus:ring-primary p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none" rows="4"></textarea>
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
                    
                    <!-- Education -->
                    <x-user.editor-accordion title="Education" icon="school">
                        <form class="section-form" data-section-id="{{ $education->id ?? '' }}">
                            <div class="space-y-6" id="education-list">
                                @forelse($eduContent as $index => $edu)
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-user.form-input label="Degree/Course" name="degree" value="{{ $edu['degree'] ?? '' }}" class="auto-save" />
                                        <x-user.form-input label="School/University" name="school" value="{{ $edu['school'] ?? '' }}" class="auto-save" />
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-user.form-input label="Start Date" name="start_date" value="{{ $edu['start_date'] ?? '' }}" class="auto-save" />
                                        <x-user.form-input label="End Date" name="end_date" value="{{ $edu['end_date'] ?? '' }}" class="auto-save" />
                                    </div>
                                    <div class="relative mt-2">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Additional Info</label>
                                        <textarea name="description" class="auto-save w-full bg-surface-container-low rounded-lg border-none focus:ring-1 focus:ring-primary p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none" rows="2">{{ $edu['description'] ?? '' }}</textarea>
                                    </div>
                                </div>
                                @empty
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-user.form-input label="Degree/Course" name="degree" value="" class="auto-save" />
                                        <x-user.form-input label="School/University" name="school" value="" class="auto-save" />
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-user.form-input label="Start Date" name="start_date" value="" class="auto-save" />
                                        <x-user.form-input label="End Date" name="end_date" value="" class="auto-save" />
                                    </div>
                                    <div class="relative mt-2">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Additional Info</label>
                                        <textarea name="description" class="auto-save w-full bg-surface-container-low rounded-lg border-none focus:ring-1 focus:ring-primary p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none" rows="2"></textarea>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            <div class="mt-4">
                                <button type="button" onclick="addListItem('education-list', this)" class="w-full py-3 rounded-xl border border-dashed border-primary/30 text-primary/70 hover:bg-primary/5 hover:text-primary transition-colors flex items-center justify-center gap-2 font-bold text-sm">
                                    <span class="material-symbols-outlined text-[20px]">add_circle</span> Add Education
                                </button>
                            </div>
                        </form>
                    </x-user.editor-accordion>

                    <!-- Skills -->
                    <x-user.editor-accordion title="Skills" icon="bolt">
                        <form class="section-form" data-section-id="{{ $skills->id ?? '' }}">
                            <div class="grid grid-cols-1 gap-4" id="skills-list">
                                @forelse($skillsContent as $index => $skill)
                                <div class="list-item flex gap-4 items-center group">
                                    <div class="flex-1">
                                        <x-user.form-input label="Skill Name" name="name" value="{{ $skill['name'] ?? '' }}" class="auto-save" />
                                    </div>
                                    <div class="flex-1">
                                        <x-user.form-input label="Proficiency Level" name="level" value="{{ $skill['level'] ?? '' }}" class="auto-save" />
                                    </div>
                                    <button type="button" onclick="removeListItem(this)" class="text-primary/30 hover:text-red-500 transition-colors mt-6 opacity-0 group-hover:opacity-100">
                                        <span class="material-symbols-outlined text-[24px]">delete</span>
                                    </button>
                                </div>
                                @empty
                                <div class="list-item flex gap-4 items-center group">
                                    <div class="flex-1">
                                        <x-user.form-input label="Skill Name" name="name" value="" class="auto-save" />
                                    </div>
                                    <div class="flex-1">
                                        <x-user.form-input label="Proficiency Level" name="level" value="" class="auto-save" />
                                    </div>
                                    <button type="button" onclick="removeListItem(this)" class="text-primary/30 hover:text-red-500 transition-colors mt-6 opacity-0 group-hover:opacity-100">
                                        <span class="material-symbols-outlined text-[24px]">delete</span>
                                    </button>
                                </div>
                                @endforelse
                            </div>
                            <div class="mt-4">
                                <button type="button" onclick="addListItem('skills-list', this)" class="w-full py-3 rounded-xl border border-dashed border-primary/30 text-primary/70 hover:bg-primary/5 hover:text-primary transition-colors flex items-center justify-center gap-2 font-bold text-sm">
                                    <span class="material-symbols-outlined text-[20px]">add_circle</span> Add Skill
                                </button>
                            </div>
                        </form>
                    </x-user.editor-accordion>
                    
                </div>
            </aside>

            <main class="w-full lg:w-[60%] lg:h-full bg-primary/5 flex flex-col items-center p-4 lg:p-8 lg:overflow-y-auto relative custom-scrollbar">
                
                <div class="w-full max-w-[794px] relative flex flex-col lg:my-auto shrink-0 mb-10 lg:mb-0">
                    <div class="absolute -top-4 -right-4 bg-tertiary/90 backdrop-blur-xl p-4 rounded-2xl shadow-xl border border-primary/10 z-20 flex flex-col items-center">
                        <div class="text-[10px] font-bold text-primary/60 uppercase tracking-widest mb-2">ATS Score</div>
                        <x-user.score-circle :score="78" size="sm" :showPercent="false"/>
                    </div>
                    @if($cv)
                        <div class="w-full relative bg-tertiary shadow-xl rounded-sm border border-primary/10 z-10 overflow-hidden" id="preview-container" style="aspect-ratio: 210/297;">
                            <iframe id="resume-preview-iframe" src="{{ route('resumes.preview', $cv) }}" style="width: 794px; height: 1123px; transform-origin: 0 0; border: none; overflow: hidden;" class="pointer-events-none absolute top-0 left-0"></iframe>
                        </div>
                    @else
                        <div class="bg-tertiary w-full h-full min-h-[842px] shadow-xl rounded-sm p-6 lg:p-16 z-10 flex flex-col">
                            <header class="text-center mb-12">
                                <h2 class="font-headline text-3xl md:text-4xl font-bold text-primary tracking-tight mb-2">{{ auth()->user()->name }}</h2>
                                <p class="text-sm font-body text-primary/60 tracking-widest uppercase">Senior Product Designer • San Francisco, CA</p>
                                <div class="mt-4 flex justify-center gap-6 text-xs font-medium text-primary/80">
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">mail</span> {{ auth()->user()->email }}</span>
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">public</span> vance.design</span>
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">call</span> +1 (555) 000-1111</span>
                                </div>
                            </header>
                            
                            <div class="space-y-10">
                                <section>
                                    <h3 class="font-headline text-lg font-bold text-primary border-b border-primary/20 pb-1 mb-3">Professional Summary</h3>
                                    <p class="text-[13px] leading-relaxed text-primary/80">
                                        Accomplished Product Designer with 8+ years of experience crafting intuitive digital experiences for high-growth tech companies. Expertise in systems thinking, accessibility-first design, and bridge-building between engineering and design teams.
                                    </p>
                                </section>
                                
                                <section class="space-y-6">
                                    <h3 class="font-headline text-lg font-bold text-primary border-b border-primary/20 pb-1 mb-4">Experience</h3>
                                    
                                    <div class="space-y-1">
                                        <div class="flex justify-between items-baseline">
                                            <h4 class="font-headline text-md font-bold text-primary">Senior Product Designer</h4>
                                            <span class="font-headline italic text-sm text-primary/60">Jan 2021 — Present</span>
                                        </div>
                                        <div class="text-sm font-bold text-secondary">Linear</div>
                                        <p class="text-[13px] leading-relaxed text-primary/80 mt-2">
                                            Leading design systems for the world's most productive software teams. Crafting high-fidelity components and maintaining visual consistency across mobile and desktop platforms.
                                        </p>
                                    </div>
                                    
                                    <div class="space-y-1">
                                        <div class="flex justify-between items-baseline">
                                            <h4 class="font-headline text-md font-bold text-primary">Product Designer</h4>
                                            <span class="font-headline italic text-sm text-primary/60">Mar 2018 — Dec 2020</span>
                                        </div>
                                        <div class="text-sm font-bold text-secondary">Airbnb</div>
                                        <p class="text-[13px] leading-relaxed text-primary/80 mt-2">
                                            Focused on the guest booking experience and internationalization of the design system. Reduced checkout friction by 12% through iterative testing and accessible UI patterns.
                                        </p>
                                    </div>
                                </section>
                                
                                <section>
                                    <h3 class="font-headline text-lg font-bold text-primary border-b border-primary/20 pb-1 mb-4">Education</h3>
                                    <div class="flex justify-between items-baseline">
                                        <h4 class="font-headline text-md font-bold text-primary">BFA in Interaction Design</h4>
                                        <span class="font-headline italic text-sm text-primary/60">2014 — 2018</span>
                                    </div>
                                    <div class="text-sm font-medium text-primary/80">Rhode Island School of Design</div>
                                </section>
                                
                                <section>
                                    <h3 class="font-headline text-lg font-bold text-primary border-b border-primary/20 pb-1 mb-4">Expertise</h3>
                                    <div class="flex flex-wrap gap-2">
                                        <x-user.keyword-tag variant="neutral" class="bg-surface-container-low text-primary border-primary/5 text-[11px] uppercase px-3 py-1">Design Systems</x-user.keyword-tag>
                                        <x-user.keyword-tag variant="neutral" class="bg-surface-container-low text-primary border-primary/5 text-[11px] uppercase px-3 py-1">Figma</x-user.keyword-tag>
                                        <x-user.keyword-tag variant="neutral" class="bg-surface-container-low text-primary border-primary/5 text-[11px] uppercase px-3 py-1">React & Tailwind</x-user.keyword-tag>
                                        <x-user.keyword-tag variant="neutral" class="bg-surface-container-low text-primary border-primary/5 text-[11px] uppercase px-3 py-1">Accessibility</x-user.keyword-tag>
                                        <x-user.keyword-tag variant="neutral" class="bg-surface-container-low text-primary border-primary/5 text-[11px] uppercase px-3 py-1">Prototyping</x-user.keyword-tag>
                                    </div>
                                </section>
                            </div>
                            
                            <footer class="mt-auto pt-12 border-t border-primary/10 flex justify-center">
                                <span class="font-headline italic text-xs text-primary/60">1 of 1</span>
                            </footer>
                        </div>
                    @endif
                </div>

                <div class="sticky bottom-24 lg:bottom-10 z-40 bg-tertiary/80 backdrop-blur-md border border-primary/10 px-6 py-3 rounded-full shadow-lg flex items-center gap-6 shrink-0 mx-auto">
                    <x-user.button variant="text" icon="zoom_in" class="hidden sm:flex">
                        <span class="text-xs uppercase tracking-widest">Zoom</span>
                    </x-user.button>
                    <div class="w-px h-4 bg-primary/20 hidden sm:block"></div>
                    <x-user.button variant="text" icon="layers" onclick="openTemplateModal()">
                        <span class="text-xs uppercase tracking-widest">Layout</span>
                    </x-user.button>
                    <div class="w-px h-4 bg-primary/20"></div>
                    <x-user.button variant="text" icon="history">
                        <span class="text-xs uppercase tracking-widest">History</span>
                    </x-user.button>
                </div>
                
            </main>
        </div>
    </div>

    {{-- Template Selection Modal --}}
    <div id="template-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center transition-opacity opacity-0 duration-300" style="opacity: 0; pointer-events: none;">
        <div class="bg-surface w-full max-w-4xl max-h-[80vh] rounded-2xl shadow-2xl flex flex-col overflow-hidden mx-4 transform scale-95 transition-transform duration-300" id="template-modal-content">
            <div class="p-6 border-b border-primary/10 flex justify-between items-center bg-surface-container-low">
                <h3 class="text-xl font-headline font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">layers</span> Select Template
                </h3>
                <button onclick="closeTemplateModal()" class="text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full p-1 hover:bg-primary/5">close</button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar bg-surface flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($templates as $template)
                    <div id="template-card-{{ $template->id }}" onclick="selectTemplate('{{ $template->id }}')" onmouseenter="previewTemplate('{{ $template->id }}')" onmouseleave="resetPreview()" class="template-card cursor-pointer group relative border @if($cv && $cv->template_id === $template->id) border-secondary bg-secondary/5 @else border-primary/10 @endif rounded-xl overflow-hidden hover:border-secondary transition-all hover:shadow-lg hover:-translate-y-1">
                        <div class="relative w-full aspect-[210/297] bg-surface-container-low overflow-hidden border-b border-primary/5">
                            @if($cv)
                            <iframe src="{{ route('resumes.preview', $cv) }}?template_id={{ $template->id }}" 
                                    style="width: 794px; height: 1123px; transform-origin: top left; border: none; position: absolute; top: 0; left: 0;"
                                    class="template-thumbnail-iframe pointer-events-none transition-transform duration-500 origin-top-left"
                                    loading="lazy" tabindex="-1">
                            </iframe>
                            <div class="absolute inset-0 bg-transparent z-10"></div>
                            @else
                            <img src="{{ $template->thumbnail }}" alt="{{ $template->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4 z-20">
                                <span class="bg-secondary text-white text-xs px-3 py-1.5 rounded-full font-bold shadow-sm">Use Template</span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h4 class="font-bold text-sm text-primary group-hover:text-secondary transition-colors">{{ $template->name }}</h4>
                            <p class="text-[11px] text-primary/60 mt-1 line-clamp-2 leading-relaxed">{{ $template->description }}</p>
                        </div>
                        @if($cv && $cv->template_id === $template->id)
                        <div class="absolute top-3 right-3 bg-secondary text-white rounded-full w-6 h-6 shadow-md flex items-center justify-center checkmark">
                            <span class="material-symbols-outlined text-[14px]">check</span>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewPdf(resumeId) {
            window.open(`/resumes/${resumeId}/preview`, '_blank');
        }

        async function downloadPdf(resumeId) {
            const btn = document.getElementById('download-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span> <span class="ml-1">Generating...</span>';
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                const response = await fetch(`/resumes/${resumeId}/pdf`);
                if (!response.ok) throw new Error('Network response was not ok');
                
                const blob = await response.blob();
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;

                const contentDisposition = response.headers.get('Content-Disposition');
                let filename = 'my-resume.pdf';
                if (contentDisposition && contentDisposition.includes('filename=')) {
                    filename = contentDisposition.split('filename=')[1].replace(/["']/g, '');
                }
                
                a.download = filename;
                a.click();
                URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Failed to download PDF:', error);
                alert('Failed to download PDF. Please try again.');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        }

        let previewTimeout;
        let originalPreviewSrc = @if($cv) "{{ route('resumes.preview', $cv) }}" @else "" @endif;

        function previewTemplate(templateId) {
            @if($cv)
            clearTimeout(previewTimeout);
            const iframe = document.getElementById('resume-preview-iframe');
            if (iframe) {
                iframe.src = `/resumes/{{ $cv->id }}/preview?template_id=${templateId}`;
            }
            @endif
        }

        function resetPreview() {
            @if($cv)
            // Wait a small delay before resetting to avoid flicker when moving between cards
            previewTimeout = setTimeout(() => {
                const iframe = document.getElementById('resume-preview-iframe');
                if (iframe && iframe.src !== originalPreviewSrc) {
                    // Only reset if we didn't just save a new template
                    iframe.src = originalPreviewSrc;
                }
            }, 300);
            @endif
        }

        function openTemplateModal() {
            const modal = document.getElementById('template-modal');
            const modalContent = document.getElementById('template-modal-content');
            modal.classList.remove('hidden');
            // Trigger reflow
            void modal.offsetWidth;
            modal.style.opacity = '1';
            modal.style.pointerEvents = 'auto';
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
            scaleThumbnails();
        }

        function closeTemplateModal() {
            const modal = document.getElementById('template-modal');
            const modalContent = document.getElementById('template-modal-content');
            modal.style.opacity = '0';
            modal.style.pointerEvents = 'none';
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        async function selectTemplate(templateId) {
            @if($cv)
            try {
                const response = await fetch(`/resumes/{{ $cv->id }}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ template_id: templateId })
                });
                
                if (!response.ok) throw new Error('Network response was not ok');
                
                const data = await response.json();
                if(data.success) {
                    const iframe = document.getElementById('resume-preview-iframe');
                    if (iframe) {
                        // Update original source so we don't revert on mouseleave
                        originalPreviewSrc = `/resumes/{{ $cv->id }}/preview?template_id=${templateId}`;
                        // Add a subtle loading state
                        iframe.style.opacity = '0.5';
                        iframe.src = originalPreviewSrc;
                        iframe.onload = () => { iframe.style.opacity = '1'; };
                    } else {
                        window.location.reload();
                    }
                    
                    // Update UI selection
                    document.querySelectorAll('.template-card').forEach(card => {
                        card.classList.remove('border-secondary', 'bg-secondary/5');
                        card.classList.add('border-primary/10');
                        const checkmark = card.querySelector('.checkmark');
                        if (checkmark) checkmark.remove();
                    });
                    
                    const selectedCard = document.getElementById('template-card-' + templateId);
                    if (selectedCard) {
                        selectedCard.classList.remove('border-primary/10');
                        selectedCard.classList.add('border-secondary', 'bg-secondary/5');
                        selectedCard.innerHTML += `
                        <div class="absolute top-3 right-3 bg-secondary text-white rounded-full w-6 h-6 shadow-md flex items-center justify-center checkmark">
                            <span class="material-symbols-outlined text-[14px]">check</span>
                        </div>`;
                    }
                    
                    closeTemplateModal();
                }
            } catch (error) {
                console.error('Failed to change template:', error);
                alert('Failed to change template.');
            }
            @else
            alert('No resume available to update template.');
            @endif
        }

        function scaleIframe() {
            const container = document.getElementById('preview-container');
            const iframe = document.getElementById('resume-preview-iframe');
            if (container && iframe) {
                const scale = container.offsetWidth / 794;
                iframe.style.transform = `scale(${scale})`;
            }
            scaleThumbnails();
        }
        
        function scaleThumbnails() {
            document.querySelectorAll('.template-thumbnail-iframe').forEach(iframe => {
                const parent = iframe.parentElement;
                if (parent.offsetWidth > 0) {
                    const scale = parent.offsetWidth / 794;
                    // Check if it's currently hovered (which scales it up further in CSS)
                    // The CSS handles hover scale, but we'll set base scale here via JS variable or just override inline
                    iframe.style.transform = `scale(${scale})`;
                    // To keep hover working, we'd need to use CSS variables, but for now inline style overrides the hover class.
                    // Let's use CSS variable for base scale so hover still works!
                    parent.style.setProperty('--base-scale', scale);
                    iframe.style.transform = `scale(var(--base-scale))`;
                }
            });
        }
        window.addEventListener('resize', scaleIframe);
        document.addEventListener('DOMContentLoaded', scaleIframe);

        // Auto-save logic
        @if($cv)
        let saveTimeout;
        const cvId = "{{ $cv->id }}";
        
        document.querySelectorAll('.section-form').forEach(form => {
            form.addEventListener('input', (e) => {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    clearTimeout(saveTimeout);
                    
                    // Add a tiny saving indicator
                    const iframe = document.getElementById('resume-preview-iframe');
                    if (iframe) iframe.style.opacity = '0.7';

                    saveTimeout = setTimeout(() => saveSection(form), 800);
                }
            });
        });

        async function saveSection(form) {
            const sectionId = form.getAttribute('data-section-id');
            if (!sectionId) return;

            const data = {};
            const lists = form.querySelectorAll('.list-item');
            
            if (lists.length > 0) {
                data.content = [];
                lists.forEach(item => {
                    const itemData = {};
                    item.querySelectorAll('input, textarea').forEach(input => {
                        if (input.name) itemData[input.name] = input.value;
                    });
                    data.content.push(itemData);
                });
            } else {
                data.content = {};
                form.querySelectorAll('input, textarea').forEach(input => {
                    if (input.name) data.content[input.name] = input.value;
                });
            }

            try {
                const response = await fetch(`/resumes/${cvId}/section/${sectionId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.success && result.html) {
                        const iframe = document.getElementById('resume-preview-iframe');
                        if (iframe && iframe.contentDocument) {
                            // Parse the returned HTML string
                            const parser = new DOMParser();
                            const newDoc = parser.parseFromString(result.html, 'text/html');
                            
                            // Smoothly replace the inner body to prevent iframe reloading flash!
                            iframe.contentDocument.body.innerHTML = newDoc.body.innerHTML;
                            iframe.style.opacity = '1';
                        }
                    }
                } else {
                    console.error('Failed to save section');
                    const iframe = document.getElementById('resume-preview-iframe');
                    if (iframe) iframe.style.opacity = '1';
                }
            } catch (error) {
                console.error('Network error', error);
                const iframe = document.getElementById('resume-preview-iframe');
                if (iframe) iframe.style.opacity = '1';
            }
        }

        // Add/Remove Item Logic
        function addListItem(listId, btn) {
            const list = document.getElementById(listId);
            const items = list.querySelectorAll('.list-item');
            if (items.length === 0) return;
            
            const lastItem = items[items.length - 1];
            const clone = lastItem.cloneNode(true);
            
            // Clear inputs
            clone.querySelectorAll('input, textarea').forEach(input => {
                input.value = '';
            });
            
            list.appendChild(clone);
            
            // Trigger save
            const form = btn.closest('form');
            if (form) {
                clearTimeout(saveTimeout);
                const iframe = document.getElementById('resume-preview-iframe');
                if (iframe) iframe.style.opacity = '0.7';
                saveTimeout = setTimeout(() => saveSection(form), 800);
            }
        }

        function removeListItem(btn) {
            const item = btn.closest('.list-item');
            const list = item.parentElement;
            const form = btn.closest('form');
            
            // Prevent removing the very last item completely, just clear it instead
            if (list.querySelectorAll('.list-item').length <= 1) {
                item.querySelectorAll('input, textarea').forEach(input => input.value = '');
            } else {
                item.remove();
            }
            
            // Trigger save
            if (form) {
                clearTimeout(saveTimeout);
                const iframe = document.getElementById('resume-preview-iframe');
                if (iframe) iframe.style.opacity = '0.7';
                saveTimeout = setTimeout(() => saveSection(form), 800);
            }
        }
        @endif
    </script>
@endsection



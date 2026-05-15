@extends('layouts.user.app')

@section('title', 'Resumify Workspace | Editor')

@section('body_class', 'h-screen flex overflow-hidden')

@section('content')
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        {{-- Page Header --}}
        <x-user.page-header title="Editor: Senior Product Designer">
            <x-user.button type="button" onclick="openCvVersionsModal()" variant="outline" icon="auto_awesome" class="text-sm px-3 hidden sm:flex text-secondary border-secondary hover:bg-secondary/10">Tailor CV</x-user.button>
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
                        $targetJob  = $cv ? $cv->sections->where('type', 'target_job')->first() : null;
                        $certifications = $cv ? $cv->sections->where('type', 'certifications')->first() : null;
                        $projects     = $cv ? $cv->sections->where('type', 'projects')->first() : null;
                        $languages    = $cv ? $cv->sections->where('type', 'languages')->first() : null;
                        $personalContent = $personal ? ($personal->content ?? []) : [];
                        $expContent = $experience ? ($experience->content ?? []) : [];
                        $eduContent = $education ? ($education->content ?? []) : [];
                        $skillsContent = $skills ? ($skills->content ?? []) : [];
                        $targetJobContent = $targetJob ? ($targetJob->content ?? []) : [];
                        $certsContent = $certifications ? ($certifications->content ?? []) : [];
                        $projectsContent = $projects ? ($projects->content ?? []) : [];
                        $langsContent = $languages ? ($languages->content ?? []) : [];
                    @endphp

                    <!-- Personal Info -->
                    <x-user.editor-accordion title="Personal Info" icon="person" :isOpen="true">
                        <form class="section-form" data-section-id="{{ $personal->id ?? '' }}">
                            <div class="grid grid-cols-1 gap-4">
                                <x-user.form-input label="Full Name" name="name" value="{{ $personalContent['name'] ?? '' }}" class="auto-save" :required="true" placeholder="e.g. John Doe" />
                                <x-user.form-input label="Professional Title" name="title" value="{{ $personalContent['title'] ?? '' }}" class="auto-save" :required="true" placeholder="e.g. Senior Product Designer" />
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <x-user.form-input label="Email" name="email" type="email" value="{{ $personalContent['email'] ?? '' }}" class="auto-save" :required="true" placeholder="you@example.com" />
                                    <div class="relative group/input" x-data="{ focused: false, error: '' }">
                                        <label class="text-[11px] font-bold uppercase tracking-wider transition-colors duration-200 mb-1 flex items-center gap-1"
                                               :class="error ? 'text-red-500' : (focused ? 'text-secondary' : 'text-primary/60')">
                                            Phone Number
                                        </label>
                                        <div class="relative flex items-center border-b-2 border-primary/15 focus-within:border-secondary transition-all duration-200">
                                            <select name="country_code" class="auto-save bg-transparent py-2 pl-0 pr-6 outline-none text-primary text-sm appearance-none cursor-pointer border-none focus:ring-0 font-medium">
                                                @php
                                                    $codes = ['+1' => 'US (+1)', '+44' => 'UK (+44)', '+61' => 'AU (+61)', '+62' => 'ID (+62)', '+91' => 'IN (+91)'];
                                                    $selectedCode = $personalContent['country_code'] ?? '+62';
                                                @endphp
                                                @foreach($codes as $val => $label)
                                                    <option value="{{ $val }}" {{ $selectedCode === $val ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <span class="material-symbols-outlined text-[16px] text-primary/40 pointer-events-none absolute left-[4.5rem]">arrow_drop_down</span>
                                            <div class="w-px h-4 bg-primary/20 mx-2"></div>
                                            <input
                                                name="phone"
                                                type="tel"
                                                value="{{ $personalContent['phone'] ?? '' }}"
                                                placeholder="812 xxxx xxxx"
                                                class="auto-save w-full bg-transparent py-2 px-0 outline-none focus:ring-0 text-primary text-sm placeholder:text-primary/30 border-none"
                                                @focus="focused = true; error = ''"
                                                @blur="focused = false; validate($event, 'tel', false)"
                                                x-on:input="if(error) validate($event, 'tel', false)"
                                            />
                                            <span class="absolute right-0 top-2 text-red-400 text-[16px] material-symbols-outlined transition-all duration-200"
                                                  x-show="error" style="display:none">error</span>
                                        </div>
                                        <p class="text-[11px] text-red-400 mt-1 leading-tight" x-show="error" x-text="error" style="display:none"></p>
                                    </div>
                                </div>
                                <x-user.form-input label="Location" name="location" value="{{ $personalContent['location'] ?? '' }}" class="auto-save" />
                                <div class="relative group mt-2">
                                    <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Professional Summary</label>
                                    <textarea name="summary" placeholder="Write 2–4 sentences about your background, key skills, and career goals..." class="auto-save w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-0 p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="5">{{ $personalContent['summary'] ?? '' }}</textarea>
                                    <button type="button" onclick="openRefineModal(this)" class="absolute bottom-3 right-3 text-[10px] font-bold bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-2 py-1 rounded transition-colors flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-[12px]">auto_awesome</span>Refine</button>
                                </div>
                            </div>
                        </form>
                    </x-user.editor-accordion>
                    
                    <!-- Target Job (for ATS scoring) -->
                    <x-user.editor-accordion title="Target Job" icon="target">
                        <form class="section-form" data-section-id="{{ $targetJob->id ?? '' }}">
                            <div class="grid grid-cols-1 gap-4">
                                <x-user.form-input label="Target Job Title" name="job_title" value="{{ $targetJobContent['job_title'] ?? '' }}" class="auto-save" placeholder="e.g. Senior Software Engineer" />
                                <div class="relative group mt-2">
                                    <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Job Description</label>
                                    <textarea name="job_description" class="auto-save w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-0 p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="6" placeholder="Paste the job description here to see how well your resume matches...">{{ $targetJobContent['job_description'] ?? '' }}</textarea>
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
                                        <x-user.form-input label="Job Title" name="title" value="{{ $job['title'] ?? '' }}" class="auto-save" :required="true" placeholder="e.g. Software Engineer" />
                                        <x-user.form-input label="Company" name="company" value="{{ $job['company'] ?? '' }}" class="auto-save" :required="true" placeholder="e.g. Acme Corp" />
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-user.form-input label="Start Date" name="start_date" type="month" value="{{ $job['start_date'] ?? '' }}" class="auto-save" />
                                        <x-user.form-input label="End Date" name="end_date" type="month" value="{{ $job['end_date'] ?? '' }}" class="auto-save" hint="Leave blank if current" />
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
                                        <x-user.form-input label="Job Title" name="title" value="" class="auto-save" />
                                        <x-user.form-input label="Company" name="company" value="" class="auto-save" />
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-user.form-input label="Start Date" name="start_date" type="month" value="" class="auto-save" />
                                        <x-user.form-input label="End Date" name="end_date" type="month" value="" class="auto-save" hint="Leave blank if current" />
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
                                        <x-user.form-input label="Degree/Course" name="degree" value="{{ $edu['degree'] ?? '' }}" class="auto-save" :required="true" placeholder="e.g. Bachelor of Science" />
                                        <x-user.form-input label="School/University" name="school" value="{{ $edu['school'] ?? '' }}" class="auto-save" :required="true" placeholder="e.g. University of Indonesia" />
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <x-user.form-input label="Start Date" name="start_date" type="month" value="{{ $edu['start_date'] ?? '' }}" class="auto-save" />
                                        <x-user.form-input label="End Date" name="end_date" type="month" value="{{ $edu['end_date'] ?? '' }}" class="auto-save" hint="Leave blank if current" />
                                    </div>
                                    <div class="relative mt-2">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Additional Info</label>
                                        <textarea name="description" class="auto-save w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-0 p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="2">{{ $edu['description'] ?? '' }}</textarea>
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
                                        <x-user.form-input label="Start Date" name="start_date" type="month" value="" class="auto-save" />
                                        <x-user.form-input label="End Date" name="end_date" type="month" value="" class="auto-save" hint="Leave blank if current" />
                                    </div>
                                    <div class="relative mt-2">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Additional Info</label>
                                        <textarea name="description" class="auto-save w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-0 p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="2"></textarea>
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
                                        <div class="relative">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-1 block">Proficiency Level</label>
                                        <select name="level" class="auto-save w-full border-b-2 border-primary/15 focus:border-secondary bg-transparent py-2 px-0 outline-none transition-all duration-200 text-primary text-sm appearance-none cursor-pointer">
                                            <option value="">Select level</option>
                                            @foreach(['Beginner','Elementary','Intermediate','Advanced','Expert'] as $lvl)
                                                <option value="{{ $lvl }}" {{ ($skill['level'] ?? '') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                                            @endforeach
                                        </select>
                                    </div>
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
                                        <div class="relative">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-1 block">Proficiency Level</label>
                                        <select name="level" class="auto-save w-full border-b-2 border-primary/15 focus:border-secondary bg-transparent py-2 px-0 outline-none transition-all duration-200 text-primary text-sm appearance-none cursor-pointer">
                                            <option value="">Select level</option>
                                            @foreach(['Beginner','Elementary','Intermediate','Advanced','Expert'] as $lvl)
                                                <option value="{{ $lvl }}">{{ $lvl }}</option>
                                            @endforeach
                                        </select>
                                    </div>
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
                    <!-- Certifications -->
                    @if($certifications)
                    <x-user.editor-accordion title="Certifications" icon="workspace_premium" id="section-certifications">
                        <form class="section-form" data-section-id="{{ $certifications->id }}">
                            <div class="space-y-6" id="certifications-list">
                                @forelse($certsContent as $index => $cert)
                                <div class="list-item group relative pl-4 border-l-2 border-primary/10 hover:border-secondary transition-colors">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <x-user.form-input label="Certification Name" name="name" value="{{ $cert['name'] ?? '' }}" class="auto-save" />
                                        <x-user.form-input label="Issuer" name="issuer" value="{{ $cert['issuer'] ?? '' }}" class="auto-save" />
                                        <x-user.form-input label="Date" name="date" value="{{ $cert['date'] ?? '' }}" class="auto-save" type="month" />
                                    </div>
                                    <button type="button" onclick="removeListItem(this)" class="absolute -left-3 top-0 bg-surface rounded-full text-primary/30 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                                        <span class="material-symbols-outlined text-[20px]">remove_circle</span>
                                    </button>
                                </div>
                                @empty
                                <div class="list-item group relative pl-4 border-l-2 border-primary/10 hover:border-secondary transition-colors">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <x-user.form-input label="Certification Name" name="name" value="" class="auto-save" />
                                        <x-user.form-input label="Issuer" name="issuer" value="" class="auto-save" />
                                        <x-user.form-input label="Date" name="date" value="" class="auto-save" type="month" />
                                    </div>
                                    <button type="button" onclick="removeListItem(this)" class="absolute -left-3 top-0 bg-surface rounded-full text-primary/30 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                                        <span class="material-symbols-outlined text-[20px]">remove_circle</span>
                                    </button>
                                </div>
                                @endforelse
                            </div>
                            <div class="mt-4 flex gap-2">
                                <button type="button" onclick="addListItem('certifications-list', this)" class="flex-1 py-3 rounded-xl border border-dashed border-primary/30 text-primary/70 hover:bg-primary/5 hover:text-primary transition-colors flex items-center justify-center gap-2 font-bold text-sm">
                                    <span class="material-symbols-outlined text-[20px]">add_circle</span> Add Certification
                                </button>
                                <button type="button" onclick="deleteSection('{{ $certifications->id }}')" class="py-3 px-4 rounded-xl border border-red-200 text-red-500 hover:bg-red-50 transition-colors flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </div>
                        </form>
                    </x-user.editor-accordion>
                    @endif

                    <!-- Projects -->
                    @if($projects)
                    <x-user.editor-accordion title="Projects" icon="rocket_launch" id="section-projects">
                        <form class="section-form" data-section-id="{{ $projects->id }}">
                            <div class="space-y-6" id="projects-list">
                                @forelse($projectsContent as $index => $project)
                                <div class="list-item group relative pl-4 border-l-2 border-primary/10 hover:border-secondary transition-colors">
                                    <div class="grid grid-cols-1 gap-4">
                                        <x-user.form-input label="Project Name" name="name" value="{{ $project['name'] ?? '' }}" class="auto-save" />
                                        <x-user.form-input label="Project URL (Optional)" name="url" value="{{ $project['url'] ?? '' }}" class="auto-save" />
                                        <div class="relative group mt-2">
                                            <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Description</label>
                                            <textarea name="description" class="auto-save w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-0 p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="3">{{ $project['description'] ?? '' }}</textarea>
                                            <button type="button" onclick="openRefineModal(this)" class="absolute bottom-3 right-3 text-[10px] font-bold bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-2 py-1 rounded transition-colors flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-[12px]">auto_awesome</span>Refine</button>
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeListItem(this)" class="absolute -left-3 top-0 bg-surface rounded-full text-primary/30 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                                        <span class="material-symbols-outlined text-[20px]">remove_circle</span>
                                    </button>
                                </div>
                                @empty
                                <div class="list-item group relative pl-4 border-l-2 border-primary/10 hover:border-secondary transition-colors">
                                    <div class="grid grid-cols-1 gap-4">
                                        <x-user.form-input label="Project Name" name="name" value="" class="auto-save" />
                                        <x-user.form-input label="Project URL (Optional)" name="url" value="" class="auto-save" />
                                        <div class="relative group mt-2">
                                            <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Description</label>
                                            <textarea name="description" class="auto-save w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-0 p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="3"></textarea>
                                            <button type="button" onclick="openRefineModal(this)" class="absolute bottom-3 right-3 text-[10px] font-bold bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-2 py-1 rounded transition-colors flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-[12px]">auto_awesome</span>Refine</button>
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeListItem(this)" class="absolute -left-3 top-0 bg-surface rounded-full text-primary/30 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                                        <span class="material-symbols-outlined text-[20px]">remove_circle</span>
                                    </button>
                                </div>
                                @endforelse
                            </div>
                            <div class="mt-4 flex gap-2">
                                <button type="button" onclick="addListItem('projects-list', this)" class="flex-1 py-3 rounded-xl border border-dashed border-primary/30 text-primary/70 hover:bg-primary/5 hover:text-primary transition-colors flex items-center justify-center gap-2 font-bold text-sm">
                                    <span class="material-symbols-outlined text-[20px]">add_circle</span> Add Project
                                </button>
                                <button type="button" onclick="deleteSection('{{ $projects->id }}')" class="py-3 px-4 rounded-xl border border-red-200 text-red-500 hover:bg-red-50 transition-colors flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </div>
                        </form>
                    </x-user.editor-accordion>
                    @endif

                    <!-- Languages -->
                    @if($languages)
                    <x-user.editor-accordion title="Languages" icon="translate" id="section-languages">
                        <form class="section-form" data-section-id="{{ $languages->id }}">
                            <div class="grid grid-cols-1 gap-4" id="languages-list">
                                @forelse($langsContent as $index => $lang)
                                <div class="list-item flex gap-4 items-center group">
                                    <div class="flex-1">
                                        <x-user.form-input label="Language" name="name" value="{{ $lang['name'] ?? '' }}" class="auto-save" />
                                    </div>
                                    <div class="flex-1">
                                        <div class="relative">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-1 block">Proficiency</label>
                                        <select name="level" class="auto-save w-full border-b-2 border-primary/15 focus:border-secondary bg-transparent py-2 px-0 outline-none transition-all duration-200 text-primary text-sm appearance-none cursor-pointer">
                                            <option value="">Select level</option>
                                            @foreach(['Beginner','Conversational','Fluent','Native'] as $lvl)
                                                <option value="{{ $lvl }}" {{ ($lang['level'] ?? '') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    </div>
                                    <button type="button" onclick="removeListItem(this)" class="text-primary/30 hover:text-red-500 transition-colors mt-6 opacity-0 group-hover:opacity-100">
                                        <span class="material-symbols-outlined text-[24px]">delete</span>
                                    </button>
                                </div>
                                @empty
                                <div class="list-item flex gap-4 items-center group">
                                    <div class="flex-1">
                                        <x-user.form-input label="Language" name="name" value="" class="auto-save" />
                                    </div>
                                    <div class="flex-1">
                                        <div class="relative">
                                        <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-1 block">Proficiency</label>
                                        <select name="level" class="auto-save w-full border-b-2 border-primary/15 focus:border-secondary bg-transparent py-2 px-0 outline-none transition-all duration-200 text-primary text-sm appearance-none cursor-pointer">
                                            <option value="">Select level</option>
                                            @foreach(['Beginner','Conversational','Fluent','Native'] as $lvl)
                                                <option value="{{ $lvl }}">{{ $lvl }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    </div>
                                    <button type="button" onclick="removeListItem(this)" class="text-primary/30 hover:text-red-500 transition-colors mt-6 opacity-0 group-hover:opacity-100">
                                        <span class="material-symbols-outlined text-[24px]">delete</span>
                                    </button>
                                </div>
                                @endforelse
                            </div>
                            <div class="mt-4 flex gap-2">
                                <button type="button" onclick="addListItem('languages-list', this)" class="flex-1 py-3 rounded-xl border border-dashed border-primary/30 text-primary/70 hover:bg-primary/5 hover:text-primary transition-colors flex items-center justify-center gap-2 font-bold text-sm">
                                    <span class="material-symbols-outlined text-[20px]">add_circle</span> Add Language
                                </button>
                                <button type="button" onclick="deleteSection('{{ $languages->id }}')" class="py-3 px-4 rounded-xl border border-red-200 text-red-500 hover:bg-red-50 transition-colors flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </div>
                        </form>
                    </x-user.editor-accordion>
                    @endif

                    <!-- Optional Sections Toggles -->
                    @if($cv && (!$certifications || !$projects || !$languages))
                    <div class="mt-8 border-t border-primary/10 pt-6 px-4">
                        <h4 class="text-sm font-bold text-primary tracking-wide mb-4 flex items-center gap-2"><span class="material-symbols-outlined">add_box</span> Add Optional Section</h4>
                        <div class="flex flex-wrap gap-3">
                            @if(!$certifications)
                            <form action="{{ route('resumes.sections.store', $cv->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="certifications">
                                <input type="hidden" name="title" value="Certifications">
                                <button type="submit" class="px-4 py-2 rounded-full border border-primary/20 text-xs font-bold text-primary/70 hover:bg-secondary hover:text-white hover:border-secondary transition-all">+ Certifications</button>
                            </form>
                            @endif
                            @if(!$projects)
                            <form action="{{ route('resumes.sections.store', $cv->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="projects">
                                <input type="hidden" name="title" value="Projects">
                                <button type="submit" class="px-4 py-2 rounded-full border border-primary/20 text-xs font-bold text-primary/70 hover:bg-secondary hover:text-white hover:border-secondary transition-all">+ Projects</button>
                            </form>
                            @endif
                            @if(!$languages)
                            <form action="{{ route('resumes.sections.store', $cv->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="languages">
                                <input type="hidden" name="title" value="Languages">
                                <button type="submit" class="px-4 py-2 rounded-full border border-primary/20 text-xs font-bold text-primary/70 hover:bg-secondary hover:text-white hover:border-secondary transition-all">+ Languages</button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </aside>

            <main class="w-full lg:w-[60%] lg:h-full bg-primary/5 flex flex-col items-center p-4 lg:p-8 lg:overflow-y-auto relative custom-scrollbar">
                
                <div class="w-full max-w-[794px] relative flex flex-col lg:my-auto shrink-0 mb-10 lg:mb-0">
                    {{-- ATS Score Panel (Gemini-powered) --}}
                    @if($cv)
                    <div id="ats-panel" class="absolute -top-4 -right-4 bg-tertiary/95 backdrop-blur-xl px-4 pt-4 pb-3 rounded-2xl shadow-xl border border-primary/10 z-20 flex flex-col items-center min-w-[110px] transition-all duration-300 cursor-pointer" onclick="toggleAtsTip()">
                        <div class="text-[10px] font-bold text-primary/60 uppercase tracking-widest mb-2 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">analytics</span>ATS Score
                        </div>
                        <div id="ats-score-wrap" class="relative w-14 h-14">
                            <svg class="w-14 h-14 -rotate-90" viewBox="0 0 56 56">
                                <circle cx="28" cy="28" r="22" fill="none" stroke="currentColor" class="text-primary/10" stroke-width="5"/>
                                <circle id="ats-arc" cx="28" cy="28" r="22" fill="none" stroke="currentColor" class="text-secondary transition-all duration-700" stroke-width="5" stroke-linecap="round" stroke-dasharray="138.2" stroke-dashoffset="138.2"/>
                            </svg>
                            <span id="ats-score-num" class="absolute inset-0 flex items-center justify-center text-lg font-bold text-primary">—</span>
                        </div>
                        <div id="ats-label" class="text-[10px] font-semibold mt-2 text-primary/50">—</div>
                        <div id="ats-loading" class="hidden mt-1">
                            <span class="material-symbols-outlined text-[16px] text-secondary animate-spin">progress_activity</span>
                        </div>
                        <div id="ats-tip-card" class="hidden mt-3 pt-2 border-t border-primary/10 w-full max-w-[180px] text-left space-y-2">
                            <p id="ats-tip-text" class="text-[11px] text-primary/70 leading-relaxed italic"></p>
                            <div id="ats-improvements" class="space-y-1 text-[11px] text-primary/60"></div>
                        </div>
                    </div>
                    @else
                    <div class="absolute -top-4 -right-4 bg-tertiary/90 backdrop-blur-xl p-4 rounded-2xl shadow-xl border border-primary/10 z-20 flex flex-col items-center">
                        <div class="text-[10px] font-bold text-primary/60 uppercase tracking-widest mb-2">ATS Score</div>
                        <x-user.score-circle :score="0" size="sm" :showPercent="false"/>
                    </div>
                    @endif
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
                <button id="close-modal-btn" onclick="closeTemplateModal()" class="text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full p-1 hover:bg-primary/5">close</button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar bg-surface flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($templates as $template)
                    @if($cv)
                        <div id="template-card-{{ $template->id }}" onclick="selectTemplate('{{ $template->id }}')" onmouseenter="previewTemplate('{{ $template->id }}')" onmouseleave="resetPreview()" class="template-card cursor-pointer group relative border @if($cv && $cv->template_id === $template->id) border-secondary bg-secondary/5 @else border-primary/10 @endif rounded-xl overflow-hidden hover:border-secondary transition-all hover:shadow-lg hover:-translate-y-1">
                            <div class="relative w-full aspect-[210/297] bg-surface-container-low overflow-hidden border-b border-primary/5">
                                <iframe src="{{ route('resumes.preview', $cv) }}?template_id={{ $template->id }}" 
                                        style="width: 794px; height: 1123px; transform-origin: top left; border: none; position: absolute; top: 0; left: 0;"
                                        class="template-thumbnail-iframe pointer-events-none transition-transform duration-500 origin-top-left"
                                        loading="lazy" tabindex="-1">
                                </iframe>
                                <div class="absolute inset-0 bg-transparent z-10"></div>
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
                    @else
                        <form action="{{ route('resumes.store') }}" method="POST" class="cursor-pointer group relative border border-primary/10 rounded-xl overflow-hidden hover:border-secondary transition-all hover:shadow-lg hover:-translate-y-1 bg-tertiary">
                            @csrf
                            <input type="hidden" name="title" value="My Professional Resume">
                            <input type="hidden" name="template_id" value="{{ $template->id }}">
                            
                            <div class="relative w-full aspect-[210/297] bg-surface-container-low overflow-hidden border-b border-primary/5">
                                <iframe src="{{ route('templates.preview', $template) }}" 
                                        style="width: 794px; height: 1123px; transform-origin: top left; border: none; position: absolute; top: 0; left: 0;"
                                        class="template-thumbnail-iframe pointer-events-none transition-transform duration-500 origin-top-left"
                                        loading="lazy" tabindex="-1">
                                </iframe>
                                <div class="absolute inset-0 bg-transparent z-10"></div>
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4 z-20">
                                    <span class="bg-secondary text-white text-xs px-3 py-1.5 rounded-full font-bold shadow-sm">Use Template</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h4 class="font-bold text-sm text-primary group-hover:text-secondary transition-colors">{{ $template->name }}</h4>
                                <p class="text-[11px] text-primary/60 mt-1 line-clamp-2 leading-relaxed">{{ $template->description }}</p>
                            </div>
                            
                            <button type="submit" class="absolute inset-0 w-full h-full opacity-0 z-30 cursor-pointer"></button>
                        </form>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Refine Modal --}}
    <div id="refine-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center transition-opacity opacity-0 duration-300" style="pointer-events: none;">
        <div class="bg-surface w-full max-w-lg rounded-2xl shadow-2xl flex flex-col mx-4 transform scale-95 transition-transform duration-300" id="refine-modal-content">
            <div class="p-6 border-b border-primary/10 flex justify-between items-center bg-surface-container-low">
                <h3 class="text-xl font-headline font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">auto_awesome</span> Refine with AI
                </h3>
                <button onclick="closeRefineModal()" class="text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full p-1 hover:bg-primary/5">close</button>
            </div>
            <div class="p-6 bg-surface">
                <div id="refine-loading" class="flex flex-col items-center justify-center py-8">
                    <span class="material-symbols-outlined text-[32px] text-secondary animate-spin mb-4">progress_activity</span>
                    <p class="text-sm text-primary/60">Generating optimized bullet points...</p>
                </div>
                <div id="refine-results" class="hidden flex flex-col gap-3"></div>
            </div>
        </div>
    </div>

    {{-- CV Versions Modal --}}
    <div id="cv-versions-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center transition-opacity opacity-0 duration-300" style="pointer-events: none;">
        <div class="bg-surface w-full max-w-5xl max-h-[90vh] rounded-2xl shadow-2xl flex flex-col mx-4 transform scale-95 transition-transform duration-300 overflow-hidden" id="cv-versions-modal-content">
            <div class="p-6 border-b border-primary/10 flex justify-between items-center bg-surface-container-low">
                <h3 class="text-xl font-headline font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">auto_awesome</span> Tailored CV Versions
                </h3>
                <button onclick="closeCvVersionsModal()" class="text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full p-1 hover:bg-primary/5">close</button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar bg-surface flex-1">
                <div id="cv-versions-setup" class="flex flex-col gap-4 max-w-2xl mx-auto py-8 text-center">
                    <span class="material-symbols-outlined text-[48px] text-secondary mb-2">content_copy</span>
                    <h4 class="text-xl font-bold text-primary">Generate Tailored Versions</h4>
                    <p class="text-sm text-primary/70">We will generate 3 distinct CV versions tailored to your target job: Leadership, Technical, and Ownership angles. This uses 3 AI credits.</p>
                    <button onclick="generateCvVersions()" class="mt-4 mx-auto bg-secondary hover:bg-secondary/90 text-white font-bold py-3 px-6 rounded-full shadow-lg hover:shadow-xl transition-all w-fit">Generate Versions</button>
                </div>
                <div id="cv-versions-loading" class="hidden flex-col items-center justify-center py-20">
                    <span class="material-symbols-outlined text-[48px] text-secondary animate-spin mb-6">progress_activity</span>
                    <p class="text-lg font-bold text-primary">Crafting tailored CV versions...</p>
                    <p class="text-sm text-primary/60 mt-2">This usually takes about 10-20 seconds.</p>
                </div>
                <div id="cv-versions-results" class="hidden grid grid-cols-1 md:grid-cols-3 gap-6"></div>
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

        // ── Toast helper ────────────────────────────────────────────────
        function showToast(message, type = 'success') {
            const existing = document.getElementById('save-toast');
            if (existing) existing.remove();
            const icons = { success: 'check_circle', error: 'error', saving: 'progress_activity' };
            const colors = { success: 'text-emerald-500', error: 'text-red-400', saving: 'text-secondary' };
            const toast = document.createElement('div');
            toast.id = 'save-toast';
            toast.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-2 px-5 py-3 rounded-full shadow-xl border border-primary/10 bg-tertiary/95 backdrop-blur-md text-sm font-medium text-primary transition-all duration-300 opacity-0 translate-y-2';
            toast.innerHTML = `<span class="material-symbols-outlined text-[18px] ${colors[type]} ${type==='saving'?'animate-spin':''}">${icons[type]}</span><span>${message}</span>`;
            document.body.appendChild(toast);
            requestAnimationFrame(() => { toast.style.opacity = '1'; toast.style.transform = 'translateX(-50%) translateY(0)'; });
            if (type !== 'saving') setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
        }

        // Auto-save logic
        @if($cv)
        let saveTimeout;
        const cvId = "{{ $cv->id }}";

        function triggerAutoSave(form) {
            clearTimeout(saveTimeout);
            // Run client-side validation
            if (!validateFormBeforeSave(form)) {
                return; // Do not proceed to save if form validation fails
            }
            const iframe = document.getElementById('resume-preview-iframe');
            if (iframe) iframe.style.opacity = '0.7';
            showToast('Saving…', 'saving');
            saveTimeout = setTimeout(() => saveSection(form), 800);
        }
        
        document.querySelectorAll('.section-form').forEach(form => {
            form.addEventListener('input', (e) => {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    triggerAutoSave(form);
                }
            });
            form.addEventListener('change', (e) => {
                if (e.target.tagName === 'SELECT') {
                    triggerAutoSave(form);
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
                    item.querySelectorAll('input, textarea, select').forEach(el => {
                        if (el.name) itemData[el.name] = el.value;
                    });
                    data.content.push(itemData);
                });
            } else {
                data.content = {};
                form.querySelectorAll('input, textarea, select').forEach(el => {
                    if (el.name) data.content[el.name] = el.value;
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
                    const iframe = document.getElementById('resume-preview-iframe');
                    if (result.success && result.html) {
                        if (iframe && iframe.contentDocument) {
                            const parser = new DOMParser();
                            const newDoc = parser.parseFromString(result.html, 'text/html');
                            iframe.contentDocument.body.innerHTML = newDoc.body.innerHTML;
                        }
                    }
                    if (iframe) iframe.style.opacity = '1';
                    showToast(result.saved_at ? `✓ Saved · ${result.saved_at}` : 'Changes saved!', 'success');
                    if (result.ats_score !== undefined) {
                        updateAtsUi(result.ats_score, 'Keyword Match', result.ats_score === 0 ? 'Add a target job to get an ATS keyword match score.' : '', []);
                    }
                } else {
                    console.error('Failed to save section');
                    const iframe = document.getElementById('resume-preview-iframe');
                    if (iframe) iframe.style.opacity = '1';
                    showToast('Failed to save — please retry.', 'error');
                }
            } catch (error) {
                console.error('Network error', error);
                const iframe = document.getElementById('resume-preview-iframe');
                if (iframe) iframe.style.opacity = '1';
                showToast('Network error — changes not saved.', 'error');
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
                item.querySelectorAll('input, textarea, select').forEach(el => el.value = el.tagName === 'SELECT' ? '' : '');
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

        async function deleteSection(sectionId) {
            if (!confirm('Are you sure you want to completely remove this section?')) return;
            
            try {
                const response = await fetch(`/resumes/{{ $cv->id ?? 0 }}/section/${sectionId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    window.location.reload();
                } else {
                    showToast('Failed to delete section.', 'error');
                }
            } catch (e) {
                showToast('Network error.', 'error');
            }
        }
        @endif

        @if(!$cv)
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(openTemplateModal, 100);
            const closeBtn = document.getElementById('close-modal-btn');
            if (closeBtn) closeBtn.style.display = 'none';
        });
        @endif

        // ── ATS Score (Gemini) ─────────────────────────────────────────
        @if($cv)
        let atsTipOpen = false;
        let atsDebounce;

        function toggleAtsTip() {
            atsTipOpen = !atsTipOpen;
            const card = document.getElementById('ats-tip-card');
            if (card) card.classList.toggle('hidden', !atsTipOpen);
        }

        function updateAtsUi(score, label, tip, improvements) {
            const arc    = document.getElementById('ats-arc');
            const num    = document.getElementById('ats-score-num');
            const lbl    = document.getElementById('ats-label');
            const tipEl  = document.getElementById('ats-tip-text');
            const impEl  = document.getElementById('ats-improvements');
            const loader = document.getElementById('ats-loading');

            if (loader) loader.classList.add('hidden');

            const circumference = 138.2;
            const offset = circumference - (score / 100) * circumference;

            if (arc) {
                arc.style.strokeDashoffset = offset;
                // Color based on score
                arc.classList.remove('text-secondary', 'text-emerald-500', 'text-amber-400', 'text-red-400');
                if (score >= 75) arc.classList.add('text-emerald-500');
                else if (score >= 50) arc.classList.add('text-amber-400');
                else arc.classList.add('text-red-400');
            }
            if (num) num.textContent = score;
            if (lbl) {
                lbl.textContent = label || '—';
                lbl.className = 'text-[10px] font-semibold mt-2 ' +
                    (score >= 75 ? 'text-emerald-500' : score >= 50 ? 'text-amber-400' : 'text-red-400');
            }
            if (tipEl) tipEl.textContent = tip || '';
            if (impEl && Array.isArray(improvements)) {
                impEl.innerHTML = improvements.map(i =>
                    `<div class="flex items-start gap-1"><span class="material-symbols-outlined text-[11px] mt-0.5 text-amber-400">arrow_right</span><span>${i}</span></div>`
                ).join('');
            }
        }

        // Initial score on page load
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const initialScore = {{ $cv->ats_score ?? 0 }};
                updateAtsUi(initialScore, 'Keyword Match', initialScore === 0 ? 'Add a target job to get an ATS keyword match score.' : '', []);
            }, 800);
        });
        @endif

        // ── Client-side validation before save ────────────────────────
        function validateFormBeforeSave(form) {
            let valid = true;
            form.querySelectorAll('input, select, textarea').forEach(input => {
                const val = input.value.trim();
                
                // Check required
                if (input.required && !val) {
                    valid = false;
                    input.classList.add('border-red-400');
                    input.addEventListener('input', () => input.classList.remove('border-red-400'), { once: true });
                }
                
                // Check types if there is a value
                if (val) {
                    let typeValid = true;
                    if (input.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                        typeValid = false;
                    } else if (input.type === 'tel' && !/^[+\d\s\-().]{7,20}$/.test(val)) {
                        typeValid = false;
                    } else if (input.type === 'url' && !/^https?:\/\/.+/.test(val)) {
                        typeValid = false;
                    }
                    
                    if (!typeValid) {
                        valid = false;
                        input.classList.add('border-red-400');
                        input.addEventListener('input', () => input.classList.remove('border-red-400'), { once: true });
                    }
                }
            });
            return valid;
        }

        // ── AI Refine Bullet ─────────────────────────────────────────
        let currentRefineTextarea = null;

        function openRefineModal(btn) {
            currentRefineTextarea = btn.parentElement.querySelector('textarea');
            const text = currentRefineTextarea.value.trim();
            if (!text || text.length < 10) {
                alert('Please write at least a few words before refining.');
                return;
            }

            const modal = document.getElementById('refine-modal');
            const content = document.getElementById('refine-modal-content');
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.style.opacity = '1';
            modal.style.pointerEvents = 'auto';
            content.classList.replace('scale-95', 'scale-100');

            document.getElementById('refine-loading').classList.remove('hidden');
            document.getElementById('refine-results').classList.add('hidden');
            document.getElementById('refine-results').innerHTML = '';

            const jobContext = document.querySelector('[name="job_description"]')?.value || '';

            fetch(`/resumes/{{ $cv->id ?? '' }}/ai/refine-bullet`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ text, job_context: jobContext })
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('refine-loading').classList.add('hidden');
                if (data.success && data.options) {
                    const resultsContainer = document.getElementById('refine-results');
                    resultsContainer.classList.remove('hidden');
                    data.options.forEach(opt => {
                        const div = document.createElement('div');
                        div.className = 'p-4 rounded-xl border border-primary/10 hover:border-secondary cursor-pointer transition-colors bg-surface-container-low text-sm text-primary/80 leading-relaxed';
                        div.textContent = opt;
                        div.onclick = () => {
                            currentRefineTextarea.value = opt;
                            // Trigger input event to save if auto-save is bound
                            currentRefineTextarea.dispatchEvent(new Event('input', { bubbles: true }));
                            closeRefineModal();
                        };
                        resultsContainer.appendChild(div);
                    });
                } else {
                    document.getElementById('refine-loading').classList.add('hidden');
                    const resultsContainer = document.getElementById('refine-results');
                    resultsContainer.classList.remove('hidden');
                    resultsContainer.innerHTML = `<div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">${data.message || 'Failed to refine bullet.'}</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('refine-loading').classList.add('hidden');
                const resultsContainer = document.getElementById('refine-results');
                resultsContainer.classList.remove('hidden');
                resultsContainer.innerHTML = `<div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">An error occurred while connecting to the server.</div>`;
            });
        }

        function closeRefineModal() {
            const modal = document.getElementById('refine-modal');
            const content = document.getElementById('refine-modal-content');
            modal.style.opacity = '0';
            modal.style.pointerEvents = 'none';
            content.classList.replace('scale-100', 'scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
            currentRefineTextarea = null;
        }

        // ── Parallel CV Versions ───────────────────────────────────────
        function openCvVersionsModal() {
            const modal = document.getElementById('cv-versions-modal');
            const content = document.getElementById('cv-versions-modal-content');
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.style.opacity = '1';
            modal.style.pointerEvents = 'auto';
            content.classList.replace('scale-95', 'scale-100');

            document.getElementById('cv-versions-setup').classList.remove('hidden');
            document.getElementById('cv-versions-loading').classList.add('hidden');
            document.getElementById('cv-versions-results').classList.add('hidden');
            document.getElementById('cv-versions-results').innerHTML = '';
        }

        function closeCvVersionsModal() {
            const modal = document.getElementById('cv-versions-modal');
            const content = document.getElementById('cv-versions-modal-content');
            modal.style.opacity = '0';
            modal.style.pointerEvents = 'none';
            content.classList.replace('scale-100', 'scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        function generateCvVersions() {
            const jobDescription = document.querySelector('[name="job_description"]')?.value || '';
            if (!jobDescription || jobDescription.length < 50) {
                alert('Please provide a detailed Target Job Description (at least 50 characters) in the Target Job section first.');
                closeCvVersionsModal();
                return;
            }

            document.getElementById('cv-versions-setup').classList.add('hidden');
            document.getElementById('cv-versions-loading').style.display = 'flex';

            fetch(`/resumes/{{ $cv->id ?? '' }}/ai/generate-versions`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ job_description: jobDescription })
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('cv-versions-loading').style.display = 'none';
                if (data.success && data.versions) {
                    const resultsContainer = document.getElementById('cv-versions-results');
                    resultsContainer.classList.remove('hidden');
                    
                    const angleIcons = {
                        leadership: 'groups',
                        technical: 'code',
                        ownership: 'verified_user'
                    };

                    data.versions.forEach(v => {
                        const div = document.createElement('div');
                        div.className = 'p-6 rounded-2xl border border-primary/10 bg-surface-container-low flex flex-col gap-4 h-full';
                        div.innerHTML = `
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-full bg-secondary/10 flex items-center justify-center text-secondary">
                                    <span class="material-symbols-outlined">${angleIcons[v.angle] || 'description'}</span>
                                </div>
                                <h4 class="font-bold text-primary capitalize text-lg">${v.angle} Angle</h4>
                            </div>
                            <p class="text-sm text-primary/70 leading-relaxed flex-1">This version emphasizes ${v.angle} aspects of your experience, perfectly tailored for the provided job description.</p>
                            <div class="flex flex-col gap-2 w-full mt-auto">
                                <button onclick="previewCvVersion('${v.id}')" class="w-full py-2.5 bg-secondary/10 hover:bg-secondary text-secondary hover:text-white font-bold rounded-xl transition-colors text-sm flex items-center justify-center gap-2"><span class="material-symbols-outlined text-[16px]">visibility</span> Preview</button>
                                <button onclick="downloadCvVersion('${v.id}')" class="w-full py-2.5 border border-primary/20 hover:bg-primary/5 text-primary font-bold rounded-xl transition-colors text-sm flex items-center justify-center gap-2"><span class="material-symbols-outlined text-[16px]">download</span> Download PDF</button>
                            </div>
                        `;
                        resultsContainer.appendChild(div);
                    });
                } else {
                    document.getElementById('cv-versions-loading').style.display = 'none';
                    const resultsContainer = document.getElementById('cv-versions-results');
                    resultsContainer.classList.remove('hidden');
                    resultsContainer.innerHTML = `<div class="col-span-full p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-center">${data.message || 'Failed to generate versions.'}</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('cv-versions-loading').style.display = 'none';
                const resultsContainer = document.getElementById('cv-versions-results');
                resultsContainer.classList.remove('hidden');
                resultsContainer.innerHTML = `<div class="col-span-full p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-center">An error occurred while connecting to the server.</div>`;
            });
        }

        function previewCvVersion(id) {
            window.open(`/resumes/{{ $cv->id ?? '' }}/preview?adaptation_id=${id}`, '_blank');
        }

        function downloadCvVersion(id) {
            window.open(`/resumes/{{ $cv->id ?? '' }}/pdf?adaptation_id=${id}`, '_blank');
        }
    </script>
@endsection



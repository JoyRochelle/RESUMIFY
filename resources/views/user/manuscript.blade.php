@extends('layouts.user.app')

@section('title', 'Resumify Workspace | Editor')

@section('body_class', 'h-screen flex overflow-hidden')

@section('content')
    @php
        $user = auth()->user();
    @endphp

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        {{-- Page Header --}}
        <x-user.page-header title="Editor: Senior Product Designer" backUrl="{{ route('dashboard') }}">
            <x-user.button type="button" onclick="openCvVersionsModal()" variant="outline" icon="auto_awesome" class="text-sm px-3 hidden sm:flex text-secondary border-secondary hover:bg-secondary/10">Tailor CV</x-user.button>
            <x-user.button onclick="previewPdf('{{ $cv->id ?? '' }}')" variant="ghost" class="text-sm px-3 hidden sm:flex">Preview</x-user.button>
            @if($user->canUsePremiumFeature('pdf_export'))
                <x-user.button id="download-btn" onclick="downloadPdf('{{ $cv->id ?? '' }}')" variant="primary" icon="download" iconClass="text-[18px]" class="text-sm px-3 w-full sm:w-auto justify-center">Download PDF</x-user.button>
            @else
                <x-user.premium-lock
                    title="Premium PDF export"
                    description="Export polished, high-quality PDFs for applications and recruiter sharing."
                    align="right">
                    <span class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-lg border border-[#A16207]/25 bg-[#A16207]/10 px-4 py-2 text-sm font-bold text-[#7C4A03] sm:w-auto">
                        <span class="material-symbols-outlined text-[18px] icon-filled" aria-hidden="true">lock</span>
                        Download PDF
                    </span>
                </x-user.premium-lock>
            @endif
        </x-user.page-header>

        {{-- Mobile tab bar (hidden on lg+) --}}
        <div class="flex lg:hidden border-b border-primary/10 bg-surface-container-low shrink-0">
            <button id="ms-tab-edit" onclick="switchMsTab('edit')"
                    class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-bold text-primary border-b-2 border-primary transition-colors">
                <span class="material-symbols-outlined text-[18px]">edit_note</span> Edit
            </button>
            <button id="ms-tab-preview" onclick="switchMsTab('preview')"
                    class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-bold text-primary/40 border-b-2 border-transparent transition-colors">
                <span class="material-symbols-outlined text-[18px]">preview</span> Preview
            </button>
        </div>

        <div class="flex-1 flex flex-col lg:flex-row overflow-y-auto lg:overflow-hidden pb-20 lg:pb-0">

            <aside id="ms-panel-edit" class="w-full lg:w-[40%] bg-surface-container-low flex flex-col border-b lg:border-b-0 lg:border-r border-primary/10 z-20 shrink-0 lg:h-full">
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

                    <!-- Target Job (for ATS scoring) -->
                    <x-user.editor-accordion title="Target Job" icon="target" :isOpen="true">
                        <form class="section-form" data-section-id="{{ $targetJob->id ?? '' }}">
                            <div class="grid grid-cols-1 gap-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <x-user.form-input label="Target Job Title" name="job_title" value="{{ $targetJobContent['job_title'] ?? '' }}" class="auto-save" placeholder="e.g. Senior Software Engineer" />
                                    <x-user.form-input label="Target Company" name="job_company" value="{{ $targetJobContent['job_company'] ?? '' }}" class="auto-save" placeholder="e.g. Acme Corp" />
                                </div>
                                <div class="relative group mt-2">
                                    <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Job Description</label>
                                    <textarea name="job_description" class="auto-save w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-0 p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="6" placeholder="Paste the job description here to see how well your resume matches...">{{ $targetJobContent['job_description'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </form>
                    </x-user.editor-accordion>
                    
                    <!-- Personal Info -->
                    <x-user.editor-accordion title="Personal Info" icon="person">
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

                                {{-- Photo Upload: only shown when template supports show_photo --}}
                                @if($cv && !empty($cv->template->style_config['show_photo']))
                                <div id="photo-upload-section" class="mt-2">
                                    <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">photo_camera</span>
                                        Profile Photo
                                    </label>
                                    <div class="flex items-center gap-4">
                                        <div id="photo-preview-wrap" class="w-20 h-20 rounded-full overflow-hidden border-2 border-primary/15 bg-surface-container-low flex items-center justify-center shrink-0">
                                            @if(!empty($personalContent['photo']))
                                                <img id="photo-preview-img" src="{{ $personalContent['photo'] }}" class="w-full h-full object-cover" alt="Profile">
                                            @else
                                                <span id="photo-placeholder-icon" class="material-symbols-outlined text-[32px] text-primary/20">person</span>
                                                <img id="photo-preview-img" src="" class="w-full h-full object-cover hidden" alt="Profile">
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <label for="photo-file-input" class="block w-full cursor-pointer text-center py-2.5 px-3 rounded-xl border border-dashed border-secondary/40 text-secondary hover:bg-secondary/5 text-xs font-bold transition-colors">
                                                <span class="material-symbols-outlined text-[14px] align-middle mr-1">upload</span>
                                                {{ !empty($personalContent['photo']) ? 'Change Photo' : 'Upload Photo' }}
                                            </label>
                                            <input id="photo-file-input" type="file" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="handlePhotoUpload(this)">
                                            <input type="hidden" name="photo" id="photo-hidden-input" value="{{ $personalContent['photo'] ?? '' }}">
                                            @if(!empty($personalContent['photo']))
                                            <button type="button" onclick="removePhoto()" class="mt-2 w-full text-[11px] text-red-400 hover:text-red-500 transition-colors text-center">Remove photo</button>
                                            @else
                                            <p class="text-[11px] text-primary/40 mt-1.5 text-center leading-tight">JPG, PNG, WebP · Max 2MB</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
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
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="flex gap-4 items-center w-full">
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
                                    </div>
                                </div>
                                @empty
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="flex gap-4 items-center w-full">
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
                                    </div>
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
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <x-user.form-input label="Certification Name" name="name" value="{{ $cert['name'] ?? '' }}" class="auto-save" />
                                        <x-user.form-input label="Issuer" name="issuer" value="{{ $cert['issuer'] ?? '' }}" class="auto-save" />
                                        <x-user.form-input label="Date" name="date" value="{{ $cert['date'] ?? '' }}" class="auto-save" type="month" />
                                    </div>
                                </div>
                                @empty
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <x-user.form-input label="Certification Name" name="name" value="" class="auto-save" />
                                        <x-user.form-input label="Issuer" name="issuer" value="" class="auto-save" />
                                        <x-user.form-input label="Date" name="date" value="" class="auto-save" type="month" />
                                    </div>
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
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="grid grid-cols-1 gap-4">
                                        <x-user.form-input label="Project Name" name="name" value="{{ $project['name'] ?? '' }}" class="auto-save" />
                                        <x-user.form-input label="Project URL (Optional)" name="url" value="{{ $project['url'] ?? '' }}" class="auto-save" />
                                        <div class="relative group mt-2">
                                            <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Description</label>
                                            <textarea name="description" class="auto-save w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-0 p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="3">{{ $project['description'] ?? '' }}</textarea>
                                            <button type="button" onclick="openRefineModal(this)" class="absolute bottom-3 right-3 text-[10px] font-bold bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-2 py-1 rounded transition-colors flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-[12px]">auto_awesome</span>Refine</button>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="grid grid-cols-1 gap-4">
                                        <x-user.form-input label="Project Name" name="name" value="" class="auto-save" />
                                        <x-user.form-input label="Project URL (Optional)" name="url" value="" class="auto-save" />
                                        <div class="relative group mt-2">
                                            <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Description</label>
                                            <textarea name="description" class="auto-save w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-0 p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="3"></textarea>
                                            <button type="button" onclick="openRefineModal(this)" class="absolute bottom-3 right-3 text-[10px] font-bold bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-2 py-1 rounded transition-colors flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-[12px]">auto_awesome</span>Refine</button>
                                        </div>
                                    </div>
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
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="flex gap-4 items-center w-full">
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
                                    </div>
                                </div>
                                @empty
                                <div class="list-item border border-primary/10 p-4 rounded-xl bg-surface relative group transition-all hover:border-primary/20">
                                    <button type="button" onclick="removeListItem(this)" class="absolute -top-3 -right-3 bg-tertiary border border-primary/20 text-primary rounded-full w-7 h-7 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-500 hover:border-red-200 z-10">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                    <div class="flex gap-4 items-center w-full">
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
                                    </div>
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

            <main id="ms-panel-preview" class="w-full lg:w-[60%] lg:h-full bg-primary/5 flex-col items-center p-4 lg:p-8 lg:overflow-y-auto relative custom-scrollbar hidden lg:flex">
                {{-- ATS Score Panel (Gemini-powered) --}}
                @if($cv)
                <div id="ats-widget" class="absolute top-2 right-2 lg:top-2 lg:right-4 bg-tertiary/95 backdrop-blur-xl p-4 rounded-2xl shadow-xl border border-primary/10 z-30 flex flex-col items-center w-fit transition-all duration-300">
                    
                    {{-- Maximized Content --}}
                    <div id="ats-maximized" class="flex flex-col items-center cursor-pointer w-full" onclick="toggleAtsMinimize(event)" title="Minimize">
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
                        <div id="ats-label" class="text-[10px] font-semibold mt-2 text-primary/50 text-center max-w-[120px]">—</div>
                        <div id="ats-loading" class="hidden mt-1">
                            <span class="material-symbols-outlined text-[16px] text-secondary animate-spin">progress_activity</span>
                        </div>
                    </div>

                    {{-- Minimized Content --}}
                    <div id="ats-minimized" class="hidden flex items-center gap-2 cursor-pointer px-2 py-1" onclick="toggleAtsMinimize(event)" title="Maximize">
                        <span class="material-symbols-outlined text-secondary text-[20px]">analytics</span>
                        <span id="ats-min-score" class="font-bold text-primary text-sm">—</span>
                    </div>

                </div>
                @else
                <div class="absolute top-2 right-2 lg:top-2 lg:right-4 bg-tertiary/90 backdrop-blur-xl p-4 rounded-2xl shadow-xl border border-primary/10 z-30 flex flex-col items-center w-fit">
                    <div class="text-[10px] font-bold text-primary/60 uppercase tracking-widest mb-2">ATS Score</div>
                    <x-user.score-circle :score="0" size="sm" :showPercent="false"/>
                </div>
                @endif
                
                <div class="w-full max-w-[794px] relative flex flex-col lg:my-auto shrink-0 mb-10 lg:mb-0 mt-20 lg:mt-0">

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
    <div id="template-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center transition-opacity opacity-0 duration-300" style="opacity: 0; pointer-events: none;"
         role="dialog"
         aria-modal="true"
         aria-labelledby="template-modal-title">
        <div class="bg-surface w-full max-w-4xl max-h-[80vh] rounded-2xl shadow-2xl flex flex-col overflow-hidden mx-4 transform scale-95 transition-transform duration-300" id="template-modal-content">
            <div class="p-6 border-b border-primary/10 flex justify-between items-center bg-surface-container-low">
                <h3 id="template-modal-title" class="text-xl font-headline font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">layers</span> Select Template
                </h3>
                <button id="close-modal-btn" type="button" onclick="closeTemplateModal()" aria-label="Close template selection" class="text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full p-2 hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">close</button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar bg-surface flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($templates as $template)
                    @php
                        $isLockedTemplate = $template->is_premium && !$user->canUsePremiumFeature('premium_templates');
                    @endphp
                    @if($cv)
                        <button type="button" id="template-card-{{ $template->id }}"
                            @if($isLockedTemplate)
                                aria-describedby="template-lock-{{ $template->id }}"
                            @else
                                onclick="selectTemplate('{{ $template->id }}')"
                            @endif
                            onmouseenter="previewTemplate('{{ $template->id }}')" onmouseleave="resetPreview()" class="template-card {{ $isLockedTemplate ? 'cursor-not-allowed border-[#A16207]/30 bg-[#A16207]/[0.03]' : 'cursor-pointer hover:border-secondary hover:shadow-lg hover:-translate-y-1' }} group relative border @if(!$isLockedTemplate && $cv && $cv->template_id === $template->id) border-secondary bg-secondary/5 @elseif(!$isLockedTemplate) border-primary/10 @endif rounded-xl overflow-hidden transition-all duration-200 text-left focus:outline-none focus:ring-2 focus:ring-secondary/40" aria-pressed="{{ $cv && $cv->template_id === $template->id ? 'true' : 'false' }}">
                            <div class="relative w-full aspect-[210/297] bg-surface-container-low overflow-hidden border-b border-primary/5">
                                <iframe src="{{ route('resumes.preview', $cv) }}?template_id={{ $template->id }}" 
                                        style="width: 794px; height: 1123px; transform-origin: top left; border: none; position: absolute; top: 0; left: 0;"
                                        class="template-thumbnail-iframe pointer-events-none transition-transform duration-500 origin-top-left"
                                        loading="lazy" tabindex="-1">
                                </iframe>
                                <div class="absolute inset-0 bg-transparent z-10"></div>
                                @if($template->is_premium)
                                    <div class="absolute left-3 top-3 z-30 inline-flex items-center gap-1 rounded-full border border-[#A16207]/25 bg-tertiary/95 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-[#7C4A03] shadow-sm backdrop-blur">
                                        <span class="material-symbols-outlined text-[13px] icon-filled" aria-hidden="true">workspace_premium</span>
                                        Premium
                                    </div>
                                @endif
                                @if($isLockedTemplate)
                                    <div id="template-lock-{{ $template->id }}" class="absolute inset-0 z-20 flex items-center justify-center bg-white/70 p-4 text-center backdrop-blur-[2px]">
                                        <div class="rounded-lg border border-[#A16207]/20 bg-tertiary/95 p-4 shadow-lg">
                                            <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-[#A16207]/10 text-[#7C4A03]">
                                                <span class="material-symbols-outlined icon-filled" aria-hidden="true">lock</span>
                                            </span>
                                            <p class="text-sm font-bold text-primary">Locked Premium Template</p>
                                            <p class="mt-1 text-xs leading-relaxed text-primary/60">Upgrade to apply this layout to your resume.</p>
                                            <a href="{{ route('user.upgrade-quota') }}"
                                               class="mt-3 inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-tertiary transition hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-[#A16207]/40"
                                               onclick="event.stopPropagation()">
                                                <span class="material-symbols-outlined text-[15px] icon-filled" aria-hidden="true">workspace_premium</span>
                                                Upgrade to Unlock
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4 z-20">
                                        <span class="bg-secondary text-white text-xs px-3 py-1.5 rounded-full font-bold shadow-sm">Use Template</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h4 class="font-bold text-sm text-primary {{ $isLockedTemplate ? '' : 'group-hover:text-secondary' }} transition-colors">{{ $template->name }}</h4>
                                <p class="text-[11px] text-primary/60 mt-1 line-clamp-2 leading-relaxed">{{ $template->description }}</p>
                            </div>
                            @if(!$isLockedTemplate && $cv && $cv->template_id === $template->id)
                            <div class="absolute top-3 right-3 bg-secondary text-white rounded-full w-6 h-6 shadow-md flex items-center justify-center checkmark">
                                <span class="material-symbols-outlined text-[14px]">check</span>
                            </div>
                            @endif
                        </button>
                    @else
                        <form action="{{ route('resumes.store') }}" method="POST" onsubmit="{{ $isLockedTemplate ? 'return false' : '' }}" class="{{ $isLockedTemplate ? 'cursor-not-allowed border-[#A16207]/30 bg-[#A16207]/[0.03]' : 'cursor-pointer border-primary/10 bg-tertiary hover:border-secondary hover:shadow-lg hover:-translate-y-1' }} group relative border rounded-xl overflow-hidden transition-all duration-200">
                            @csrf
                            <input type="hidden" name="title" value="My Professional Resume">
                            <input type="hidden" name="template_id" value="{{ $template->id }}">
                            
                            <div class="relative w-full aspect-[210/297] bg-surface-container-low overflow-hidden border-b border-primary/5">
                                <iframe src="{{ route('templates.demo', $template) }}" 
                                        style="width: 794px; height: 1123px; transform-origin: top left; border: none; position: absolute; top: 0; left: 0;"
                                        class="template-thumbnail-iframe pointer-events-none transition-transform duration-500 origin-top-left"
                                        loading="lazy" tabindex="-1">
                                </iframe>
                                <div class="absolute inset-0 bg-transparent z-10"></div>
                                @if($template->is_premium)
                                    <div class="absolute left-3 top-3 z-30 inline-flex items-center gap-1 rounded-full border border-[#A16207]/25 bg-tertiary/95 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-[#7C4A03] shadow-sm backdrop-blur">
                                        <span class="material-symbols-outlined text-[13px] icon-filled" aria-hidden="true">workspace_premium</span>
                                        Premium
                                    </div>
                                @endif
                                @if($isLockedTemplate)
                                    <div class="absolute inset-0 z-20 flex items-center justify-center bg-white/70 p-4 text-center backdrop-blur-[2px]">
                                        <div class="rounded-lg border border-[#A16207]/20 bg-tertiary/95 p-4 shadow-lg">
                                            <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-[#A16207]/10 text-[#7C4A03]">
                                                <span class="material-symbols-outlined icon-filled" aria-hidden="true">lock</span>
                                            </span>
                                            <p class="text-sm font-bold text-primary">Locked Premium Template</p>
                                            <a href="{{ route('user.upgrade-quota') }}" class="mt-3 inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-tertiary transition hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-[#A16207]/40">
                                                <span class="material-symbols-outlined text-[15px] icon-filled" aria-hidden="true">workspace_premium</span>
                                                Upgrade to Unlock
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4 z-20">
                                        <span class="bg-secondary text-white text-xs px-3 py-1.5 rounded-full font-bold shadow-sm">Use Template</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h4 class="font-bold text-sm text-primary {{ $isLockedTemplate ? '' : 'group-hover:text-secondary' }} transition-colors">{{ $template->name }}</h4>
                                <p class="text-[11px] text-primary/60 mt-1 line-clamp-2 leading-relaxed">{{ $template->description }}</p>
                            </div>
                            
                            @unless($isLockedTemplate)
                                <button type="submit" class="absolute inset-0 w-full h-full opacity-0 z-30 cursor-pointer" aria-label="Use {{ $template->name }} template"></button>
                            @endunless
                        </form>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Refine Modal --}}
    <div id="refine-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center transition-opacity opacity-0 duration-300" style="pointer-events: none;"
         role="dialog"
         aria-modal="true"
         aria-labelledby="refine-modal-title"
         aria-describedby="refine-loading">
        <div class="bg-surface w-full max-w-lg rounded-2xl shadow-2xl flex flex-col mx-4 transform scale-95 transition-transform duration-300" id="refine-modal-content">
            <div class="p-6 border-b border-primary/10 flex justify-between items-center bg-surface-container-low">
                <h3 id="refine-modal-title" class="text-xl font-headline font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">auto_awesome</span> Refine with AI
                </h3>
                <button type="button" onclick="closeRefineModal()" aria-label="Close AI refinement" class="text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full p-2 hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">close</button>
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
    <div id="cv-versions-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center transition-opacity opacity-0 duration-300" style="pointer-events: none;"
         role="dialog"
         aria-modal="true"
         aria-labelledby="cv-versions-modal-title">
        <div class="bg-surface w-full max-w-5xl max-h-[90vh] rounded-2xl shadow-2xl flex flex-col mx-4 transform scale-95 transition-transform duration-300 overflow-hidden" id="cv-versions-modal-content">
            <div class="p-6 border-b border-primary/10 flex justify-between items-center bg-surface-container-low">
                <h3 id="cv-versions-modal-title" class="text-xl font-headline font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">auto_awesome</span> Tailored CV Versions
                </h3>
                <button type="button" onclick="closeCvVersionsModal()" aria-label="Close tailored CV versions" class="text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full p-2 hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">close</button>
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
            if (!resumeId) return;
            window.open(`/resumes/${resumeId}/preview`, '_blank');
        }

        // ── Photo Upload ───────────────────────────────────────────
        function handlePhotoUpload(input) {
            const file = input.files[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran foto maksimal 2MB. Silakan pilih file yang lebih kecil.');
                input.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                const dataUrl = e.target.result;
                // Update preview
                const img = document.getElementById('photo-preview-img');
                const placeholder = document.getElementById('photo-placeholder-icon');
                if (img) { img.src = dataUrl; img.classList.remove('hidden'); }
                if (placeholder) placeholder.classList.add('hidden');
                // Store in hidden input and trigger save
                const hiddenInput = document.getElementById('photo-hidden-input');
                if (hiddenInput) {
                    hiddenInput.value = dataUrl;
                    hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                // Auto-save the personal info section
                const form = document.querySelector('.section-form[data-section-id]');
                if (form) saveSection(form);
            };
            reader.readAsDataURL(file);
        }

        function removePhoto() {
            const img = document.getElementById('photo-preview-img');
            const placeholder = document.getElementById('photo-placeholder-icon');
            const hiddenInput = document.getElementById('photo-hidden-input');
            if (img) { img.src = ''; img.classList.add('hidden'); }
            if (placeholder) placeholder.classList.remove('hidden');
            if (hiddenInput) {
                hiddenInput.value = '';
                const form = document.querySelector('.section-form[data-section-id]');
                if (form) saveSection(form);
            }
        }
        // ── End Photo Upload ───────────────────────────────────────

        async function downloadPdf(resumeId) {
            if (!resumeId) return;
            const btn = document.getElementById('download-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span> <span class="ml-1">Generating...</span>';
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                const response = await fetch(`/resumes/${resumeId}/pdf`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.status === 402) {
                    const data = await response.json();
                    showToast(data.message || 'Premium is required for PDF export.', 'error');
                    if (data.upgrade_url) window.location.href = data.upgrade_url;
                    return;
                }

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
                
                if (response.status === 402) {
                    const data = await response.json();
                    showToast(data.message || 'Premium is required for this template.', 'error');
                    if (data.upgrade_url) window.location.href = data.upgrade_url;
                    return;
                }

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
        window.addEventListener('resize', function() {
            scaleIframe();
            // On resize to desktop, clear any mobile tab inline styles
            if (window.innerWidth >= 1024) {
                const ep = document.getElementById('ms-panel-edit');
                const pp = document.getElementById('ms-panel-preview');
                if (ep) { ep.style.display = ''; ep.classList.remove('hidden'); }
                if (pp) { pp.style.display = ''; pp.classList.remove('hidden'); }
            }
        });
        document.addEventListener('DOMContentLoaded', scaleIframe);

        function switchMsTab(tab) {
            if (window.innerWidth >= 1024) return;
            const editPanel    = document.getElementById('ms-panel-edit');
            const previewPanel = document.getElementById('ms-panel-preview');
            const editBtn      = document.getElementById('ms-tab-edit');
            const previewBtn   = document.getElementById('ms-tab-preview');

            editPanel.classList.toggle('hidden', tab !== 'edit');
            previewPanel.classList.toggle('hidden', tab !== 'preview');

            [editBtn, previewBtn].forEach(btn => {
                const active = btn.id === `ms-tab-${tab}`;
                btn.classList.toggle('text-primary',     active);
                btn.classList.toggle('border-primary',   active);
                btn.classList.toggle('text-primary/40',  !active);
                btn.classList.toggle('border-transparent', !active);
            });

            if (tab === 'preview') scaleIframe();
        }

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
                    if (result.success && result.html && iframe) {
                        try {
                            iframe.contentDocument.open();
                            iframe.contentDocument.write(result.html);
                            iframe.contentDocument.close();
                        } catch (e) {
                            // fallback: full src reload if contentDocument is inaccessible
                            iframe.src = `/resumes/${cvId}/preview?t=${Date.now()}`;
                        }
                    }
                    if (iframe) iframe.style.opacity = '1';
                    showToast(result.saved_at ? `✓ Saved · ${result.saved_at}` : 'Changes saved!', 'success');
                    if (result.ats_score !== undefined) {
                        updateAtsUi(result.ats_score, 'Keyword Match');
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

        function toggleAtsDetails() {
            const card = document.getElementById('ats-tip-card');
            if(card) {
                card.classList.toggle('hidden');
            }
        }

        function updateAtsUi(score, label) {
            const arc    = document.getElementById('ats-arc');
            const num    = document.getElementById('ats-score-num');
            const lbl    = document.getElementById('ats-label');
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
            const minScore = document.getElementById('ats-min-score');
            if (minScore) minScore.textContent = score;
            if (lbl) {
                lbl.textContent = score === 0 ? 'No Target Job' : label;
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
                updateAtsUi(initialScore, 'Keyword Match');
            }, 800);
        });
        function toggleAtsMinimize(e) {
            e.stopPropagation();
            const max = document.getElementById('ats-maximized');
            const min = document.getElementById('ats-minimized');
            const widget = document.getElementById('ats-widget');
            
            if (max.classList.contains('hidden')) {
                // Restore to max
                max.classList.remove('hidden');
                min.classList.replace('flex', 'hidden');
                widget.classList.remove('p-2', 'rounded-full');
                widget.classList.add('p-4', 'rounded-2xl');
            } else {
                // Minimize
                max.classList.add('hidden');
                min.classList.replace('hidden', 'flex');
                widget.classList.add('p-2', 'rounded-full');
                widget.classList.remove('p-4', 'rounded-2xl');
            }
        }
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

            console.log(`[AI Refine] Sending request for refinement...`);
            
            fetch(`/resumes/{{ $cv->id ?? '' }}/ai/refine-bullet`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ text, job_context: jobContext })
            })
            .then(res => {
                console.log(`[AI Refine] Received response with status: ${res.status} ${res.statusText}`);
                return res.json();
            })
            .then(data => {
                console.log('[AI Refine] Response payload:', data);
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

            console.log(`[AI Versions] Sending parallel requests to generate 3 CV versions...`);
            
            fetch(`/resumes/{{ $cv->id ?? '' }}/ai/generate-versions`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ job_description: jobDescription })
            })
            .then(res => {
                console.log(`[AI Versions] Received response with status: ${res.status} ${res.statusText}`);
                return res.json();
            })
            .then(data => {
                console.log('[AI Versions] Response payload:', data);
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

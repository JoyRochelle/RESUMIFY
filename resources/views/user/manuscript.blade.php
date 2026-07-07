@extends('layouts.user.app')

@section('title', 'Resumify Workspace | Editor')

@section('body_class', 'h-screen flex overflow-hidden')

@section('content')
    @php
        $user = auth()->user();
    @endphp

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        {{-- Page Header --}}
        <x-user.page-header title="{{ __('messages.editor.title_prefix') }}{{ $cv->title ?? __('messages.dashboard.untitled_resume') }}" backUrl="{{ route('dashboard') }}">
            <x-ui.icon-button type="button" onclick="openCvVersionsModal()" icon="auto_awesome" variant="secondary" label="{{ __('messages.editor.tailor_cv') }}" class="sm:hidden" />
            <x-ui.button type="button" onclick="openCvVersionsModal()" variant="outline" icon="auto_awesome" class="text-sm px-3 hidden sm:flex text-secondary border-secondary hover:bg-secondary/10">{{ __('messages.editor.tailor_cv') }}</x-ui.button>
            <x-ui.button onclick="previewPdf('{{ $cv->id ?? '' }}')" variant="ghost" class="text-sm px-3 hidden sm:flex">{{ __('messages.editor.preview') }}</x-ui.button>
            @if($user->canUsePremiumFeature('pdf_export'))
                <x-ui.button id="download-btn" onclick="downloadPdf('{{ $cv->id ?? '' }}')" variant="primary" icon="download" iconClass="text-[18px]" class="text-sm px-3 w-full sm:w-auto justify-center">{{ __('messages.editor.download_pdf') }}</x-ui.button>
            @else
                <x-user.premium-lock
                    title="{{ __('messages.editor.premium_pdf_title') }}"
                    description="{{ __('messages.editor.premium_pdf_desc') }}"
                    align="right">
                    <span class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-lg border border-[#A16207]/25 bg-[#A16207]/10 px-4 py-2 text-sm font-bold text-[#7C4A03] sm:w-auto">
                        <span class="material-symbols-outlined text-[18px] icon-filled" aria-hidden="true">lock</span>
                        {{ __('messages.editor.download_pdf') }}
                    </span>
                </x-user.premium-lock>
            @endif
        </x-user.page-header>

        {{-- Mobile tab bar (hidden on lg+) --}}
        <div class="flex lg:hidden border-b border-primary/10 bg-surface-container-low shrink-0">
            <button id="ms-tab-edit" onclick="switchMsTab('edit')"
                    class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-bold text-primary border-b-2 border-primary transition-colors">
                <span class="material-symbols-outlined text-[18px]">edit_note</span> {{ __('messages.editor.tab_edit') }}
            </button>
            <button id="ms-tab-preview" onclick="switchMsTab('preview')"
                    class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-bold text-primary/40 border-b-2 border-transparent transition-colors">
                <span class="material-symbols-outlined text-[18px]">preview</span> {{ __('messages.editor.tab_preview') }}
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

                    @include('user.resume.editor.target-job', ['targetJob' => $targetJob, 'targetJobContent' => $targetJobContent])
                    
                    @include('user.resume.editor.personal-info', ['personal' => $personal, 'personalContent' => $personalContent, 'cv' => $cv])
                    
                    @include('user.resume.editor.work-experience', ['experience' => $experience, 'expContent' => $expContent])
                    
                    @include('user.resume.editor.education', ['education' => $education, 'eduContent' => $eduContent])

                    @include('user.resume.editor.skills', ['skills' => $skills, 'skillsContent' => $skillsContent])
                    @include('user.resume.editor.certifications', ['certifications' => $certifications, 'certsContent' => $certsContent])

                    @include('user.resume.editor.projects', ['projects' => $projects, 'projectsContent' => $projectsContent])

                    @include('user.resume.editor.languages', ['languages' => $languages, 'langsContent' => $langsContent])

                    <!-- Optional Sections Toggles -->
                    @if($cv && (!$certifications || !$projects || !$languages))
                    <div class="mt-8 border-t border-primary/10 pt-6 px-4">
                        <h4 class="text-sm font-bold text-primary tracking-wide mb-4 flex items-center gap-2"><span class="material-symbols-outlined">add_box</span> {{ __('messages.editor.add_optional_section') }}</h4>
                        <div class="flex flex-wrap gap-3">
                            @if(!$certifications)
                            <form action="{{ route('resumes.sections.store', $cv->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="certifications">
                                <input type="hidden" name="title" value="Certifications">
                                <button type="submit" class="px-4 py-2 rounded-full border border-primary/20 text-xs font-bold text-primary/70 hover:bg-secondary hover:text-white hover:border-secondary transition-all">{{ __('messages.editor.add_certifications') }}</button>
                            </form>
                            @endif
                            @if(!$projects)
                            <form action="{{ route('resumes.sections.store', $cv->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="projects">
                                <input type="hidden" name="title" value="Projects">
                                <button type="submit" class="px-4 py-2 rounded-full border border-primary/20 text-xs font-bold text-primary/70 hover:bg-secondary hover:text-white hover:border-secondary transition-all">{{ __('messages.editor.add_projects') }}</button>
                            </form>
                            @endif
                            @if(!$languages)
                            <form action="{{ route('resumes.sections.store', $cv->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="languages">
                                <input type="hidden" name="title" value="Languages">
                                <button type="submit" class="px-4 py-2 rounded-full border border-primary/20 text-xs font-bold text-primary/70 hover:bg-secondary hover:text-white hover:border-secondary transition-all">{{ __('messages.editor.add_languages') }}</button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </aside>

            <main id="ms-panel-preview" class="w-full lg:w-[60%] lg:h-full bg-primary/5 flex flex-col items-center p-4 lg:p-8 lg:overflow-y-auto relative custom-scrollbar hidden lg:flex">
                {{-- ATS Score Panel (Gemini-powered) --}}
                @if($cv)
                <div id="ats-widget" class="absolute top-2 right-2 lg:top-2 lg:right-4 bg-tertiary/95 backdrop-blur-xl p-4 rounded-2xl shadow-xl border border-primary/10 z-30 flex flex-col items-center w-fit transition-all duration-300">
                    
                    {{-- Maximized Content --}}
                    <div id="ats-maximized" class="flex flex-col items-center cursor-pointer w-full" onclick="toggleAtsMinimize(event)" title="{{ __('messages.editor.minimize') }}">
                        <div class="text-[10px] font-bold text-primary/60 uppercase tracking-widest mb-2 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">analytics</span>{{ __('messages.editor.ats_score') }}
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
                    <div id="ats-minimized" class="hidden flex items-center gap-2 cursor-pointer px-2 py-1" onclick="toggleAtsMinimize(event)" title="{{ __('messages.editor.maximize') }}">
                        <span class="material-symbols-outlined text-secondary text-[20px]">analytics</span>
                        <span id="ats-min-score" class="font-bold text-primary text-sm">—</span>
                    </div>

                </div>
                @else
                <div class="absolute top-2 right-2 lg:top-2 lg:right-4 bg-tertiary/90 backdrop-blur-xl p-4 rounded-2xl shadow-xl border border-primary/10 z-30 flex flex-col items-center w-fit">
                    <div class="text-[10px] font-bold text-primary/60 uppercase tracking-widest mb-2">{{ __('messages.editor.ats_score') }}</div>
                    <x-user.score-circle :score="0" size="sm" :showPercent="false"/>
                </div>
                @endif
                
                <div class="w-full max-w-[794px] relative flex flex-col lg:my-auto shrink-0 mb-10 lg:mb-0 mt-20 lg:mt-0">

                    @if($cv)
                        <div class="w-full relative bg-tertiary shadow-xl rounded-sm border border-primary/10 z-10" id="preview-container" style="min-height: 1123px;">
                            <iframe id="resume-preview-iframe" src="{{ route('resumes.preview', $cv) }}" style="width: 794px; height: 1123px; transform-origin: 0 0; border: none;" class="pointer-events-none absolute top-0 left-0"></iframe>
                        </div>
                    @else
                        <div class="bg-tertiary w-full h-full min-h-[842px] shadow-xl rounded-sm p-6 lg:p-16 z-10 flex flex-col">
                            <header class="text-center mb-12">
                                <h2 class="font-headline text-3xl md:text-4xl font-bold text-primary tracking-tight mb-2">{{ auth()->user()->name }}</h2>
                                <p class="text-sm font-body text-primary/60 tracking-widest uppercase">{{ __('messages.editor.placeholder.role_location') }}</p>
                                <div class="mt-4 flex justify-center gap-6 text-xs font-medium text-primary/80">
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">mail</span> {{ auth()->user()->email }}</span>
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">public</span> vance.design</span>
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">call</span> +1 (555) 000-1111</span>
                                </div>
                            </header>

                            <div class="space-y-10">
                                <section>
                                    <h3 class="font-headline text-lg font-bold text-primary border-b border-primary/20 pb-1 mb-3">{{ __('messages.editor.placeholder.professional_summary_heading') }}</h3>
                                    <p class="text-[13px] leading-relaxed text-primary/80">
                                        {{ __('messages.editor.placeholder.summary_body') }}
                                    </p>
                                </section>

                                <section class="space-y-6">
                                    <h3 class="font-headline text-lg font-bold text-primary border-b border-primary/20 pb-1 mb-4">{{ __('messages.editor.placeholder.experience_heading') }}</h3>

                                    <div class="space-y-1">
                                        <div class="flex justify-between items-baseline">
                                            <h4 class="font-headline text-md font-bold text-primary">{{ __('messages.editor.placeholder.job_title_1') }}</h4>
                                            <span class="font-headline italic text-sm text-primary/60">Jan 2021 — Present</span>
                                        </div>
                                        <div class="text-sm font-bold text-secondary">Linear</div>
                                        <p class="text-[13px] leading-relaxed text-primary/80 mt-2">
                                            {{ __('messages.editor.placeholder.job_desc_1') }}
                                        </p>
                                    </div>

                                    <div class="space-y-1">
                                        <div class="flex justify-between items-baseline">
                                            <h4 class="font-headline text-md font-bold text-primary">{{ __('messages.editor.placeholder.job_title_2') }}</h4>
                                            <span class="font-headline italic text-sm text-primary/60">Mar 2018 — Dec 2020</span>
                                        </div>
                                        <div class="text-sm font-bold text-secondary">Airbnb</div>
                                        <p class="text-[13px] leading-relaxed text-primary/80 mt-2">
                                            {{ __('messages.editor.placeholder.job_desc_2') }}
                                        </p>
                                    </div>
                                </section>

                                <section>
                                    <h3 class="font-headline text-lg font-bold text-primary border-b border-primary/20 pb-1 mb-4">{{ __('messages.editor.placeholder.education_heading') }}</h3>
                                    <div class="flex justify-between items-baseline">
                                        <h4 class="font-headline text-md font-bold text-primary">{{ __('messages.editor.placeholder.degree') }}</h4>
                                        <span class="font-headline italic text-sm text-primary/60">2014 — 2018</span>
                                    </div>
                                    <div class="text-sm font-medium text-primary/80">Rhode Island School of Design</div>
                                </section>

                                <section>
                                    <h3 class="font-headline text-lg font-bold text-primary border-b border-primary/20 pb-1 mb-4">{{ __('messages.editor.placeholder.expertise_heading') }}</h3>
                                    <div class="flex flex-wrap gap-2">
                                        <x-user.keyword-tag variant="neutral" class="bg-surface-container-low text-primary border-primary/5 text-[11px] uppercase px-3 py-1">{{ __('messages.editor.placeholder.tag_design_systems') }}</x-user.keyword-tag>
                                        <x-user.keyword-tag variant="neutral" class="bg-surface-container-low text-primary border-primary/5 text-[11px] uppercase px-3 py-1">Figma</x-user.keyword-tag>
                                        <x-user.keyword-tag variant="neutral" class="bg-surface-container-low text-primary border-primary/5 text-[11px] uppercase px-3 py-1">React & Tailwind</x-user.keyword-tag>
                                        <x-user.keyword-tag variant="neutral" class="bg-surface-container-low text-primary border-primary/5 text-[11px] uppercase px-3 py-1">{{ __('messages.editor.placeholder.tag_accessibility') }}</x-user.keyword-tag>
                                        <x-user.keyword-tag variant="neutral" class="bg-surface-container-low text-primary border-primary/5 text-[11px] uppercase px-3 py-1">{{ __('messages.editor.placeholder.tag_prototyping') }}</x-user.keyword-tag>
                                    </div>
                                </section>
                            </div>

                            <footer class="mt-auto pt-12 border-t border-primary/10 flex justify-center">
                                <span class="font-headline italic text-xs text-primary/60">{{ __('messages.editor.placeholder.page_of', ['current' => 1, 'total' => 1]) }}</span>
                            </footer>
                        </div>
                    @endif
                </div>

                <div class="sticky bottom-24 lg:bottom-10 z-40 bg-tertiary/80 backdrop-blur-md border border-primary/10 px-6 py-3 rounded-full shadow-lg flex items-center gap-6 shrink-0 mx-auto">
                    <x-ui.button variant="text" icon="layers" onclick="openTemplateModal()">
                        <span class="text-xs uppercase tracking-widest">{{ __('messages.editor.layout') }}</span>
                    </x-ui.button>
                    <div class="w-px h-4 bg-primary/20"></div>
                    <x-ui.button variant="text" icon="history" onclick="openHistoryModal()">
                        <span class="text-xs uppercase tracking-widest">{{ __('messages.editor.history') }}</span>
                    </x-ui.button>
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
                    <span class="material-symbols-outlined text-secondary">layers</span> {{ __('messages.editor.select_template_title') }}
                </h3>
                <button id="close-modal-btn" type="button" onclick="closeTemplateModal()" aria-label="{{ __('messages.editor.close_template_selection') }}" class="text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full p-2 hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">close</button>
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
                                        {{ __('messages.editor.premium_badge') }}
                                    </div>
                                @endif
                                @if($isLockedTemplate)
                                    <div id="template-lock-{{ $template->id }}" class="absolute inset-0 z-20 flex items-center justify-center bg-white/70 p-4 text-center backdrop-blur-[2px]">
                                        <div class="rounded-lg border border-[#A16207]/20 bg-tertiary/95 p-4 shadow-lg">
                                            <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-[#A16207]/10 text-[#7C4A03]">
                                                <span class="material-symbols-outlined icon-filled" aria-hidden="true">lock</span>
                                            </span>
                                            <p class="text-sm font-bold text-primary">{{ __('messages.editor.locked_premium_template') }}</p>
                                            <p class="mt-1 text-xs leading-relaxed text-primary/60">{{ __('messages.editor.upgrade_to_apply_layout') }}</p>
                                            <a href="{{ route('user.upgrade-quota') }}"
                                               class="mt-3 inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-tertiary transition hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-[#A16207]/40"
                                               onclick="event.stopPropagation()">
                                                <span class="material-symbols-outlined text-[15px] icon-filled" aria-hidden="true">workspace_premium</span>
                                                {{ __('messages.editor.upgrade_to_unlock') }}
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4 z-20">
                                        <span class="bg-secondary text-white text-xs px-3 py-1.5 rounded-full font-bold shadow-sm">{{ __('messages.editor.use_template') }}</span>
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
                                        {{ __('messages.editor.premium_badge') }}
                                    </div>
                                @endif
                                @if($isLockedTemplate)
                                    <div class="absolute inset-0 z-20 flex items-center justify-center bg-white/70 p-4 text-center backdrop-blur-[2px]">
                                        <div class="rounded-lg border border-[#A16207]/20 bg-tertiary/95 p-4 shadow-lg">
                                            <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-[#A16207]/10 text-[#7C4A03]">
                                                <span class="material-symbols-outlined icon-filled" aria-hidden="true">lock</span>
                                            </span>
                                            <p class="text-sm font-bold text-primary">{{ __('messages.editor.locked_premium_template') }}</p>
                                            <a href="{{ route('user.upgrade-quota') }}" class="mt-3 inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-tertiary transition hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-[#A16207]/40">
                                                <span class="material-symbols-outlined text-[15px] icon-filled" aria-hidden="true">workspace_premium</span>
                                                {{ __('messages.editor.upgrade_to_unlock') }}
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4 z-20">
                                        <span class="bg-secondary text-white text-xs px-3 py-1.5 rounded-full font-bold shadow-sm">{{ __('messages.editor.use_template') }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h4 class="font-bold text-sm text-primary {{ $isLockedTemplate ? '' : 'group-hover:text-secondary' }} transition-colors">{{ $template->name }}</h4>
                                <p class="text-[11px] text-primary/60 mt-1 line-clamp-2 leading-relaxed">{{ $template->description }}</p>
                            </div>
                            
                            @unless($isLockedTemplate)
                                <button type="submit" class="absolute inset-0 w-full h-full opacity-0 z-30 cursor-pointer" aria-label="{{ __('messages.editor.use_x_template_aria', ['name' => $template->name]) }}"></button>
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
                    <span class="material-symbols-outlined text-secondary">auto_awesome</span> {{ __('messages.editor.refine_with_ai') }}
                </h3>
                <button type="button" onclick="closeRefineModal()" aria-label="{{ __('messages.editor.close_ai_refinement') }}" class="text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full p-2 hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">close</button>
            </div>
            <div class="p-6 bg-surface">
                <div id="refine-loading" class="flex flex-col items-center justify-center py-8">
                    <span class="material-symbols-outlined text-[32px] text-secondary animate-spin mb-4">progress_activity</span>
                    <p class="text-sm text-primary/60">{{ __('messages.editor.generating_bullet_points') }}</p>
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
                    <span class="material-symbols-outlined text-secondary">auto_awesome</span> {{ __('messages.editor.tailored_cv_versions') }}
                </h3>
                <button type="button" onclick="closeCvVersionsModal()" aria-label="{{ __('messages.editor.close_tailored_versions') }}" class="text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full p-2 hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">close</button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar bg-surface flex-1">
                <div id="cv-versions-setup" class="flex flex-col gap-4 max-w-2xl mx-auto py-8 text-center">
                    <span class="material-symbols-outlined text-[48px] text-secondary mb-2">content_copy</span>
                    <h4 class="text-xl font-bold text-primary">{{ __('messages.editor.generate_tailored_versions') }}</h4>
                    <p class="text-sm text-primary/70">{{ __('messages.editor.generate_versions_desc') }}</p>
                    <button onclick="generateCvVersions()" class="mt-4 mx-auto bg-secondary hover:bg-secondary/90 text-white font-bold py-3 px-6 rounded-full shadow-lg hover:shadow-xl transition-all w-fit">{{ __('messages.editor.generate_versions_button') }}</button>
                </div>
                <div id="cv-versions-loading" class="hidden flex-col items-center justify-center py-20">
                    <span class="material-symbols-outlined text-[48px] text-secondary animate-spin mb-6">progress_activity</span>
                    <p class="text-lg font-bold text-primary">{{ __('messages.editor.crafting_versions') }}</p>
                    <p class="text-sm text-primary/60 mt-2">{{ __('messages.editor.crafting_versions_desc') }}</p>
                </div>
                <div id="cv-versions-results" class="hidden grid grid-cols-1 md:grid-cols-3 gap-6"></div>
            </div>
        </div>
    </div>

    {{-- CV History Modal --}}
    <div id="history-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center transition-opacity opacity-0 duration-300" style="pointer-events: none;"
         role="dialog"
         aria-modal="true"
         aria-labelledby="history-modal-title">
        <div class="bg-surface w-full max-w-lg max-h-[80vh] rounded-2xl shadow-2xl flex flex-col mx-4 transform scale-95 transition-transform duration-300 overflow-hidden" id="history-modal-content">
            <div class="p-6 border-b border-primary/10 flex justify-between items-center bg-surface-container-low">
                <h3 id="history-modal-title" class="text-xl font-headline font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">history</span> {{ __('messages.editor.cv_history') }}
                </h3>
                <button type="button" onclick="closeHistoryModal()" aria-label="{{ __('messages.editor.close_cv_history') }}" class="text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full p-2 hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">close</button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar bg-surface flex-1">
                <div id="history-list" class="flex flex-col gap-3">
                    <p class="text-sm text-primary/60 text-center py-8">{{ __('messages.editor.loading_ellipsis') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Apply Version Confirmation Modal --}}
    <div id="apply-version-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center transition-opacity opacity-0 duration-300" style="pointer-events: none;"
         role="dialog"
         aria-modal="true"
         aria-labelledby="apply-version-modal-title">
        <div class="bg-surface w-full max-w-md rounded-2xl shadow-2xl p-6 mx-4 transform scale-95 transition-transform duration-300" id="apply-version-modal-content">
            <h3 id="apply-version-modal-title" class="text-lg font-headline font-bold text-primary flex items-center gap-2 mb-3">
                <span class="material-symbols-outlined text-secondary">auto_awesome</span> {{ __('messages.editor.apply_version_title') }}
            </h3>
            <p class="text-sm text-primary/70 mb-4">{{ __('messages.editor.apply_version_desc') }}</p>
            <div id="apply-version-warning" class="hidden mb-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs"></div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeApplyVersionModal()" class="py-2.5 px-5 rounded-xl border border-primary/20 hover:bg-primary/5 text-primary font-bold text-sm transition-colors">{{ __('messages.editor.cancel') }}</button>
                <button type="button" id="apply-version-confirm-btn" class="py-2.5 px-5 rounded-xl bg-secondary hover:bg-secondary/90 text-white font-bold text-sm transition-colors">{{ __('messages.editor.apply_and_overwrite') }}</button>
            </div>
        </div>
    </div>

        <script>
        window.editorConfig = {
            cvId: '{{ $cv->id ?? "" }}',
            csrfToken: '{{ csrf_token() }}',
            atsScore: {{ $cv->ats_score ?? 0 }},
            hasCv: {{ $cv ? 'true' : 'false' }},
            i18n: @json(__('messages.editor.js')),
        };
    </script>
    @vite('resources/js/features/resume-editor.js')
@endsection
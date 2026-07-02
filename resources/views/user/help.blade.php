@extends('layouts.user.app')

@section('title', 'Help Center - Resumify')

@section('content')
    <main class="flex-1 overflow-y-auto custom-scrollbar bg-primary/5 pb-20 md:pb-0">

        <div class="max-w-4xl mx-auto px-4 sm:px-6 md:px-12 py-8 md:py-16">

            {{-- Hero --}}
            <section class="text-center mb-10 md:mb-16">
                <h2 class="font-headline text-3xl md:text-5xl text-primary mb-4 tracking-tight">Help Center</h2>
                <p class="font-body text-primary/60 text-lg max-w-2xl mx-auto leading-relaxed">
                    Find answers to common questions or reach out to our support team.
                </p>
            </section>

            @if(session('success'))
                <div class="bg-secondary/10 border border-secondary/20 text-secondary text-sm font-label px-5 py-3 rounded-xl flex items-center space-x-2 mb-8">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- My Tickets Link --}}
            <div class="flex justify-end mb-6">
                <a href="{{ route('help.tickets') }}"
                   class="flex items-center space-x-2 text-sm font-label text-primary/60 hover:text-primary bg-white border border-primary/10 px-4 py-2 rounded-xl shadow-sm transition-colors">
                    <span class="material-symbols-outlined text-[18px]">confirmation_number</span>
                    <span>My Tickets</span>
                </a>
            </div>

            {{-- FAQ --}}
            <section class="mb-16" x-data="{ open: null }">
                <h3 class="font-headline text-2xl text-primary mb-8">Frequently Asked Questions</h3>

                {{-- Getting Started --}}
                <div class="mb-6">
                    <h4 class="text-[10px] font-label text-primary/40 uppercase tracking-widest mb-3">Getting Started</h4>
                    <div class="space-y-2">
                        @php
                            $gettingStarted = [
                                ['q' => 'How do I create my first resume?', 'a' => 'Go to your dashboard and click "New Resume". Choose a template, then fill in your personal info, work experience, education, and skills. You can preview and download your resume as a PDF at any time.'],
                                ['q' => 'What templates are available?', 'a' => 'We offer a variety of professionally designed templates suited for different industries and experience levels. Visit the Templates page to preview all available designs.'],
                                ['q' => 'Can I create multiple resumes?', 'a' => 'Yes! You can create as many resumes as you need. Each resume can be customized independently for different job applications.'],
                            ];
                        @endphp
                        @foreach($gettingStarted as $i => $faq)
                        <div class="bg-white rounded-2xl border border-primary/5 shadow-sm overflow-hidden">
                            <button type="button" @click="open = (open === 'gs_{{ $i }}') ? null : 'gs_{{ $i }}'"
                                    aria-controls="faq-gs-{{ $i }}"
                                    x-bind:aria-expanded="(open === 'gs_{{ $i }}').toString()"
                                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                                <span class="text-sm font-label font-semibold text-primary">{{ $faq['q'] }}</span>
                                <span class="material-symbols-outlined text-primary/40 text-[20px] transition-transform"
                                      :class="open === 'gs_{{ $i }}' ? 'rotate-180' : ''">expand_more</span>
                            </button>
                            <div id="faq-gs-{{ $i }}" x-show="open === 'gs_{{ $i }}'" x-collapse>
                                <p class="px-6 pb-4 text-sm font-label text-primary/70 leading-relaxed">{{ $faq['a'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Resume Builder --}}
                <div class="mb-6">
                    <h4 class="text-[10px] font-label text-primary/40 uppercase tracking-widest mb-3">Resume Builder</h4>
                    <div class="space-y-2">
                        @php
                            $resumeBuilder = [
                                ['q' => 'How do I download my resume as a PDF?', 'a' => 'Open your resume in the editor and click the "Export PDF" button in the top right corner. Your resume will be generated and downloaded automatically.'],
                                ['q' => 'Can I change the template after I\'ve started editing?', 'a' => 'Yes, you can switch templates at any time from the editor. Your content will be preserved, only the visual design will change.'],
                                ['q' => 'What sections can I add to my resume?', 'a' => 'You can add Personal Info, Work Experience, Education, Skills, and Target Job sections. Each section can be customized to fit your background.'],
                            ];
                        @endphp
                        @foreach($resumeBuilder as $i => $faq)
                        <div class="bg-white rounded-2xl border border-primary/5 shadow-sm overflow-hidden">
                            <button type="button" @click="open = (open === 'rb_{{ $i }}') ? null : 'rb_{{ $i }}'"
                                    aria-controls="faq-rb-{{ $i }}"
                                    x-bind:aria-expanded="(open === 'rb_{{ $i }}').toString()"
                                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                                <span class="text-sm font-label font-semibold text-primary">{{ $faq['q'] }}</span>
                                <span class="material-symbols-outlined text-primary/40 text-[20px] transition-transform"
                                      :class="open === 'rb_{{ $i }}' ? 'rotate-180' : ''">expand_more</span>
                            </button>
                            <div id="faq-rb-{{ $i }}" x-show="open === 'rb_{{ $i }}'" x-collapse>
                                <p class="px-6 pb-4 text-sm font-label text-primary/70 leading-relaxed">{{ $faq['a'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- AI Features --}}
                <div class="mb-6">
                    <h4 class="text-[10px] font-label text-primary/40 uppercase tracking-widest mb-3">AI Features</h4>
                    <div class="space-y-2">
                        @php
                            $aiFeatures = [
                                ['q' => 'How do I improve my ATS score?', 'a' => 'Use the ATS Analyzer feature to check how well your resume matches a job description. Paste the job posting, and our AI will identify missing keywords and suggest improvements.'],
                                ['q' => 'How many AI credits do I get?', 'a' => 'Basic users receive 10 AI credits per month. Premium users get 100 credits. Each AI action (ATS analysis, bullet optimization, etc.) uses a set number of credits.'],
                                ['q' => 'What does "AI Polish" do?', 'a' => 'AI Polish rewrites your resume bullet points to be more impactful, using strong action verbs and quantifiable achievements. It uses 1 credit per bullet point.'],
                            ];
                        @endphp
                        @foreach($aiFeatures as $i => $faq)
                        <div class="bg-white rounded-2xl border border-primary/5 shadow-sm overflow-hidden">
                            <button type="button" @click="open = (open === 'ai_{{ $i }}') ? null : 'ai_{{ $i }}'"
                                    aria-controls="faq-ai-{{ $i }}"
                                    x-bind:aria-expanded="(open === 'ai_{{ $i }}').toString()"
                                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                                <span class="text-sm font-label font-semibold text-primary">{{ $faq['q'] }}</span>
                                <span class="material-symbols-outlined text-primary/40 text-[20px] transition-transform"
                                      :class="open === 'ai_{{ $i }}' ? 'rotate-180' : ''">expand_more</span>
                            </button>
                            <div id="faq-ai-{{ $i }}" x-show="open === 'ai_{{ $i }}'" x-collapse>
                                <p class="px-6 pb-4 text-sm font-label text-primary/70 leading-relaxed">{{ $faq['a'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Billing --}}
                <div class="mb-6">
                    <h4 class="text-[10px] font-label text-primary/40 uppercase tracking-widest mb-3">Billing</h4>
                    <div class="space-y-2">
                        @php
                            $billing = [
                                ['q' => 'How do I upgrade to Premium?', 'a' => 'Go to the Upgrade page from your dashboard or settings. We support payment via Midtrans (bank transfer, e-wallet, and cards).'],
                                ['q' => 'What happens to my data if I cancel?', 'a' => 'Your resumes and data are preserved. You\'ll be downgraded to the Basic plan and your AI quota will be adjusted accordingly at the next billing cycle.'],
                            ];
                        @endphp
                        @foreach($billing as $i => $faq)
                        <div class="bg-white rounded-2xl border border-primary/5 shadow-sm overflow-hidden">
                            <button type="button" @click="open = (open === 'bi_{{ $i }}') ? null : 'bi_{{ $i }}'"
                                    aria-controls="faq-bi-{{ $i }}"
                                    x-bind:aria-expanded="(open === 'bi_{{ $i }}').toString()"
                                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                                <span class="text-sm font-label font-semibold text-primary">{{ $faq['q'] }}</span>
                                <span class="material-symbols-outlined text-primary/40 text-[20px] transition-transform"
                                      :class="open === 'bi_{{ $i }}' ? 'rotate-180' : ''">expand_more</span>
                            </button>
                            <div id="faq-bi-{{ $i }}" x-show="open === 'bi_{{ $i }}'" x-collapse>
                                <p class="px-6 pb-4 text-sm font-label text-primary/70 leading-relaxed">{{ $faq['a'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Technical --}}
                <div class="mb-6">
                    <h4 class="text-[10px] font-label text-primary/40 uppercase tracking-widest mb-3">Technical</h4>
                    <div class="space-y-2">
                        @php
                            $technical = [
                                ['q' => 'Is my data secure?', 'a' => 'Yes. All data is encrypted in transit (HTTPS) and at rest. We never share your personal information with third parties. You can delete your account and all associated data at any time from Settings.'],
                                ['q' => 'Why won\'t my PDF export?', 'a' => 'Make sure all required sections (Personal Info, Work Experience, Education, Skills) are filled in. If the issue persists, try a different browser or contact support.'],
                            ];
                        @endphp
                        @foreach($technical as $i => $faq)
                        <div class="bg-white rounded-2xl border border-primary/5 shadow-sm overflow-hidden">
                            <button type="button" @click="open = (open === 'te_{{ $i }}') ? null : 'te_{{ $i }}'"
                                    aria-controls="faq-te-{{ $i }}"
                                    x-bind:aria-expanded="(open === 'te_{{ $i }}').toString()"
                                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                                <span class="text-sm font-label font-semibold text-primary">{{ $faq['q'] }}</span>
                                <span class="material-symbols-outlined text-primary/40 text-[20px] transition-transform"
                                      :class="open === 'te_{{ $i }}' ? 'rotate-180' : ''">expand_more</span>
                            </button>
                            <div id="faq-te-{{ $i }}" x-show="open === 'te_{{ $i }}'" x-collapse>
                                <p class="px-6 pb-4 text-sm font-label text-primary/70 leading-relaxed">{{ $faq['a'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Contact Form --}}
            <section id="contact" class="bg-white rounded-3xl border border-primary/5 shadow-sm p-8 mb-12">
                <h3 class="font-headline text-2xl text-primary mb-2">Still need help?</h3>
                <p class="text-sm font-label text-primary/60 mb-6">Send us a message and we'll get back to you as soon as possible.</p>

                <form action="{{ route('help.contact') }}" method="POST" class="space-y-4">
                    @csrf
                    <x-ui.error-summary />
                    <div>
                        <label for="support-subject" class="block text-[11px] font-label text-primary/50 uppercase tracking-widest mb-1">Subject <span class="text-red-500" aria-hidden="true">*</span></label>
                        <input id="support-subject" type="text" name="subject" value="{{ old('subject') }}" required
                               placeholder="Briefly describe your issue..."
                               autocomplete="off"
                               aria-invalid="{{ $errors->has('subject') ? 'true' : 'false' }}"
                               aria-describedby="{{ $errors->has('subject') ? 'support-subject-error' : '' }}"
                               class="w-full bg-surface border border-primary/10 rounded-xl px-4 py-3 text-sm font-label text-primary placeholder:text-primary/40 focus:outline-none focus:border-primary/30">
                        @error('subject')
                            <p id="support-subject-error" class="text-xs text-red-600 mt-1" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="support-message" class="block text-[11px] font-label text-primary/50 uppercase tracking-widest mb-1">Message <span class="text-red-500" aria-hidden="true">*</span></label>
                        <textarea id="support-message" name="message" rows="5" required
                                  placeholder="Describe your issue in detail..."
                                  aria-invalid="{{ $errors->has('message') ? 'true' : 'false' }}"
                                  aria-describedby="{{ $errors->has('message') ? 'support-message-error' : '' }}"
                                  class="w-full bg-surface border border-primary/10 rounded-xl px-4 py-3 text-sm font-label text-primary placeholder:text-primary/40 focus:outline-none focus:border-primary/30 resize-none">{{ old('message') }}</textarea>
                        @error('message')
                            <p id="support-message-error" class="text-xs text-red-600 mt-1" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <x-ui.loading-button loading-text="Sending...">Send Message</x-ui.loading-button>
                    </div>
                </form>
            </section>

        </div>

        <footer class="pb-12 text-center text-primary/40 text-sm">
            <p>© 2026 Resumify - Curated with Integrity</p>
        </footer>
    </main>
@endsection

@extends('layouts.user.app')

@section('title', __('messages.help.hero_title') . ' - Resumify')

@section('content')
    <main class="flex-1 overflow-y-auto custom-scrollbar bg-primary/5 pb-20 md:pb-0">

        <div class="max-w-4xl mx-auto px-4 sm:px-6 md:px-12 py-8 md:py-16">

            {{-- Hero --}}
            <section class="text-center mb-10 md:mb-16 animate-fade-up">
                <h2 class="font-headline text-3xl md:text-5xl text-primary mb-4 tracking-tight">{{ __('messages.help.hero_title') }}</h2>
                <p class="font-body text-primary/60 text-lg max-w-2xl mx-auto leading-relaxed">
                    {{ __('messages.help.hero_subtitle') }}
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
                    <span>{{ __('messages.help.my_tickets') }}</span>
                </a>
            </div>

            {{-- FAQ --}}
            <section class="mb-16 animate-fade-up" style="animation-delay: 120ms" x-data="{ open: null }">
                <h3 class="font-headline text-2xl text-primary mb-8">{{ __('messages.help.faq_heading') }}</h3>

                @foreach(['getting_started' => 'gs', 'resume_builder' => 'rb', 'ai_features' => 'ai', 'billing' => 'bi', 'technical' => 'te'] as $group => $prefix)
                <div class="mb-6">
                    <h4 class="text-[10px] font-label text-primary/40 uppercase tracking-widest mb-3">{{ __('messages.help.faq.' . $group . '.label') }}</h4>
                    <div class="space-y-2">
                        @foreach(__('messages.help.faq.' . $group . '.items') as $i => $faq)
                        <div class="bg-white rounded-2xl border border-primary/5 shadow-sm overflow-hidden">
                            <button type="button" @click="open = (open === '{{ $prefix }}_{{ $i }}') ? null : '{{ $prefix }}_{{ $i }}'"
                                    aria-controls="faq-{{ $prefix }}-{{ $i }}"
                                    x-bind:aria-expanded="(open === '{{ $prefix }}_{{ $i }}').toString()"
                                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                                <span class="text-sm font-label font-semibold text-primary">{{ $faq['q'] }}</span>
                                <span class="material-symbols-outlined text-primary/40 text-[20px] transition-transform"
                                      :class="open === '{{ $prefix }}_{{ $i }}' ? 'rotate-180' : ''">expand_more</span>
                            </button>
                            <div id="faq-{{ $prefix }}-{{ $i }}" x-show="open === '{{ $prefix }}_{{ $i }}'" x-collapse>
                                <p class="px-6 pb-4 text-sm font-label text-primary/70 leading-relaxed">{{ $faq['a'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </section>

            {{-- Contact Form --}}
            <section id="contact" class="bg-white rounded-3xl border border-primary/5 shadow-sm p-8 mb-12">
                <h3 class="font-headline text-2xl text-primary mb-2">{{ __('messages.help.contact.heading') }}</h3>
                <p class="text-sm font-label text-primary/60 mb-6">{{ __('messages.help.contact.subtitle') }}</p>

                <form action="{{ route('help.contact') }}" method="POST" class="space-y-4">
                    @csrf
                    <x-ui.error-summary />
                    <div>
                        <label for="support-subject" class="block text-[11px] font-label text-primary/50 uppercase tracking-widest mb-1">{{ __('messages.help.contact.subject_label') }} <span class="text-red-500" aria-hidden="true">*</span></label>
                        <input id="support-subject" type="text" name="subject" value="{{ old('subject') }}" required
                               placeholder="{{ __('messages.help.contact.subject_placeholder') }}"
                               autocomplete="off"
                               aria-invalid="{{ $errors->has('subject') ? 'true' : 'false' }}"
                               aria-describedby="{{ $errors->has('subject') ? 'support-subject-error' : '' }}"
                               class="w-full bg-surface border border-primary/10 rounded-xl px-4 py-3 text-sm font-label text-primary placeholder:text-primary/40 focus:outline-none focus:border-primary/30">
                        @error('subject')
                            <p id="support-subject-error" class="text-xs text-red-600 mt-1" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="support-message" class="block text-[11px] font-label text-primary/50 uppercase tracking-widest mb-1">{{ __('messages.help.contact.message_label') }} <span class="text-red-500" aria-hidden="true">*</span></label>
                        <textarea id="support-message" name="message" rows="5" required
                                  placeholder="{{ __('messages.help.contact.message_placeholder') }}"
                                  aria-invalid="{{ $errors->has('message') ? 'true' : 'false' }}"
                                  aria-describedby="{{ $errors->has('message') ? 'support-message-error' : '' }}"
                                  class="w-full bg-surface border border-primary/10 rounded-xl px-4 py-3 text-sm font-label text-primary placeholder:text-primary/40 focus:outline-none focus:border-primary/30 resize-none">{{ old('message') }}</textarea>
                        @error('message')
                            <p id="support-message-error" class="text-xs text-red-600 mt-1" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <x-ui.loading-button loading-text="{{ __('messages.help.contact.sending') }}">{{ __('messages.help.contact.send') }}</x-ui.loading-button>
                    </div>
                </form>
            </section>

        </div>

        <footer class="pb-12 text-center text-primary/40 text-sm">
            <p>{{ __('messages.common.footer_copyright') }}</p>
        </footer>
    </main>
@endsection

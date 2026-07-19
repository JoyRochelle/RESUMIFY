@extends('layouts.landing_page.app')

@section('title', 'Templates | Choose a Template that Fits Your Career')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-8 pt-14 sm:pt-20 pb-8 text-center">
    <h1 class="text-4xl sm:text-5xl md:text-6xl font-headline font-bold tracking-tight mb-4 sm:mb-6 text-primary leading-tight animate-fade-up-blur">
        {!! __('messages.landing.templates.hero_title') !!}
    </h1>
    <p class="text-base sm:text-lg text-outline leading-relaxed font-body max-w-2xl mx-auto animate-fade-up" style="animation-delay: 120ms">
        {{ __('messages.landing.templates.hero_subtitle') }}
    </p>
</section>

{{-- Category Filter Tabs + Template Grid + Preview Overlay --}}
<section class="max-w-7xl mx-auto px-4 sm:px-8 pb-16" x-data="templateLibrary()">
    <div class="flex flex-wrap justify-center gap-3 mb-16 animate-fade-up" style="animation-delay: 200ms">
        <template x-for="tab in tabs" :key="tab.key">
            <button @click="activeCategory = tab.key"
                    :class="activeCategory === tab.key ? 'bg-secondary text-white border-secondary' : 'bg-transparent text-primary border-primary/20 hover:border-primary/40 hover:bg-primary/5'"
                    :aria-pressed="(activeCategory === tab.key).toString()"
                    class="min-h-11 px-6 py-2 rounded-full text-sm font-bold font-body border transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40"
                    x-text="tab.label">
            </button>
        </template>
    </div>

    {{-- Template Grid --}}
    <div data-anime-grid class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">

        @foreach($templates as $template)
        <div x-show="activeCategory === 'all' || activeCategory === '{{ $template->category }}'"
             data-anime-card
             @click="openPreview('{{ $template->id }}', '{{ $template->name }}', '{{ addslashes($template->description) }}', '{{ route('templates.demo', $template) }}', $event)"
             @keydown.enter="openPreview('{{ $template->id }}', '{{ $template->name }}', '{{ addslashes($template->description) }}', '{{ route('templates.demo', $template) }}', $event)"
             @keydown.space.prevent="openPreview('{{ $template->id }}', '{{ $template->name }}', '{{ addslashes($template->description) }}', '{{ route('templates.demo', $template) }}', $event)"
             role="button" tabindex="0" aria-label="{{ __('messages.landing.templates.preview_aria', ['title' => $template->name]) }}"
             class="cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40 rounded-lg">
            <x-landing_page.template-card
                title="{{ $template->name }}"
                category="{{ strtoupper(str_replace('_', ' ', $template->category)) }}"
                badge="{{ $template->badge }}"
                badgeColor="{{ $template->badge_color }}">

                {{-- Live iframe preview dengan data dummy John Doe --}}
                <iframe
                    src="{{ route('templates.demo', $template) }}"
                    class="pointer-events-none absolute top-0 left-0 template-card-iframe"
                    style="width: 794px; height: 1123px; border: none; transform-origin: top left;"
                    loading="lazy"
                    tabindex="-1">
                </iframe>

            </x-landing_page.template-card>
        </div>
        @endforeach

    </div>

    {{-- Full-Page Preview Overlay --}}
    <div x-show="previewOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-start justify-center overflow-y-auto"
         role="dialog" aria-modal="true" aria-labelledby="template-preview-title"
         @click.self="closePreview()" @keydown.escape.window="closePreview()">

        <div x-show="previewOpen" x-ref="previewPanel" tabindex="-1"
             x-transition:enter="transition ease-out duration-300 delay-100"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="bg-surface w-full max-w-4xl my-8 mx-4 rounded-lg border border-primary/10 shadow-2xl overflow-hidden flex flex-col outline-none">

            {{-- Modal Header --}}
            <div class="px-4 sm:px-6 py-4 border-b border-primary/10 bg-surface-container-low flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
                <div>
                    <h3 id="template-preview-title" class="text-lg sm:text-xl font-headline font-bold text-primary" x-text="previewName"></h3>
                    <p class="text-xs text-primary/60 mt-1 font-body hidden sm:block" x-text="previewDescription"></p>
                </div>
                <div class="flex items-center gap-3">
                    <a :href="'{{ route('register') }}'" class="inline-flex items-center gap-2 bg-secondary text-white px-4 sm:px-5 py-2 sm:py-2.5 rounded-full text-sm font-bold hover:bg-secondary/90 transition-all shadow-sm hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40">
                        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">edit_document</span>
                        <span class="hidden sm:inline">{{ __('messages.landing.templates.use_template_full') }}</span>
                        <span class="sm:hidden">{{ __('messages.landing.templates.use_template_short') }}</span>
                    </a>
                    <button @click="closePreview()" aria-label="{{ __('messages.landing.templates.close_preview') }}" class="inline-flex min-h-11 min-w-11 items-center justify-center text-primary/60 hover:text-primary transition-colors rounded-full hover:bg-primary/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40">
                        <span class="material-symbols-outlined" aria-hidden="true">close</span>
                    </button>
                </div>
            </div>

            {{-- Rendered Template Preview (iframe) --}}
            <div class="flex-1 bg-primary/5 p-6 overflow-y-auto custom-scrollbar">
                <div class="w-full max-w-[794px] mx-auto bg-white shadow-xl rounded-sm border border-primary/10 overflow-hidden" style="aspect-ratio: 210/297;">
                    <iframe x-ref="previewFrame" :src="previewUrl"
                            style="width: 794px; height: 1123px; transform-origin: 0 0; border: none;"
                            class="pointer-events-none"
                            x-effect="if (previewOpen && $refs.previewFrame) { 
                                const container = $refs.previewFrame.parentElement;
                                const scale = container.offsetWidth / 794;
                                $refs.previewFrame.style.transform = `scale(${scale})`;
                            }">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA Section: Haven't found the right fit? --}}
<section class="max-w-5xl mx-auto px-4 sm:px-8 py-12 sm:py-16">
    <div class="relative rounded-2xl overflow-hidden bg-primary min-h-[320px] flex flex-col items-center justify-center text-center p-12 md:p-16">
        {{-- Decorative drifting dot grid on the dark panel --}}
        <div class="absolute inset-0 dot-pattern" aria-hidden="true"></div>
        <div class="relative z-10 max-w-xl">
            <h2 class="text-3xl md:text-4xl font-headline font-bold mb-4 tracking-tight text-white leading-tight">
                {{ __('messages.landing.templates.cta.title') }}
            </h2>
            <p class="text-white/70 mb-8 font-body leading-relaxed text-sm md:text-base">
                {{ __('messages.landing.templates.cta.subtitle') }}
            </p>
            <x-landing_page.button variant="light" href="{{ route('home') }}" class="btn-shimmer btn-shimmer-dark">
                {{ __('messages.landing.templates.cta.button') }}
            </x-landing_page.button>
        </div>
    </div>
</section>

<script>
function scaleCardIframes() {
    document.querySelectorAll('.template-card-iframe').forEach(iframe => {
        // iframe.parentElement = aspect-[210/297] container (sama seperti dashboard)
        const container = iframe.parentElement;
        if (container && container.offsetWidth > 0) {
            const scale = container.offsetWidth / 794;
            iframe.style.transform = `scale(${scale})`;
        }
    });
}

function templateLibrary() {
    return {
        activeCategory: 'all',
        init() {
            // Let the anime.js layer restagger the grid after each filter change
            this.$watch('activeCategory', () => this.$nextTick(() => {
                window.dispatchEvent(new CustomEvent('templates:filtered'));
            }));
        },
        previewOpen: false,
        previewName: '',
        previewDescription: '',
        previewUrl: '',
        previewTrigger: null,
        tabs: [
            { key: 'all', label: {!! json_encode(__('messages.landing.templates.tab_all')) !!} },
            @foreach($templates->pluck('category')->unique() as $cat)
            { key: '{{ $cat }}', label: '{{ ucfirst($cat) }}' },
            @endforeach
        ],
        openPreview(id, name, description, url, event) {
            this.previewTrigger = event ? event.currentTarget : document.activeElement;
            this.previewName = name;
            this.previewDescription = description;
            this.previewUrl = url;
            this.previewOpen = true;
            document.body.style.overflow = 'hidden';
            this.$nextTick(() => {
                if (this.$refs.previewPanel) {
                    this.$refs.previewPanel.focus();
                }
                const frame = this.$refs.previewFrame;
                if (frame) {
                    const container = frame.parentElement;
                    const scale = container.offsetWidth / 794;
                    frame.style.transform = `scale(${scale})`;
                }
            });
        },
        closePreview() {
            this.previewOpen = false;
            this.previewUrl = '';
            document.body.style.overflow = '';
            if (this.previewTrigger && this.previewTrigger.focus) {
                this.previewTrigger.focus();
            }
            this.previewTrigger = null;
        }
    }
}

// wire:navigate swaps the page without ever firing DOMContentLoaded again,
// so when this script runs with the DOM already parsed (SPA visit) the
// iframes must be scaled immediately — waiting on the event leaves them
// at their natural 794px width ("zoomed in") until a hard refresh.
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', scaleCardIframes);
} else {
    scaleCardIframes();
}
// This inline script re-runs on every wire:navigate visit; bind the
// persistent listener only once.
if (!window.__templateCardScalerBound) {
    window.__templateCardScalerBound = true;
    window.addEventListener('resize', scaleCardIframes);
}
</script>
@endsection
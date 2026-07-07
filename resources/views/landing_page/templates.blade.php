@extends('layouts.landing_page.app')

@section('title', 'Templates | Choose a Template that Fits Your Career')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-8 pt-14 sm:pt-20 pb-8 text-center">
    <h1 class="text-4xl sm:text-5xl md:text-6xl font-headline font-bold tracking-tighter mb-4 sm:mb-6 text-primary leading-tight">
        {!! __('messages.landing.templates.hero_title') !!}
    </h1>
    <p class="text-base sm:text-lg text-outline leading-relaxed font-body max-w-2xl mx-auto">
        {{ __('messages.landing.templates.hero_subtitle') }}
    </p>
</section>

{{-- Category Filter Tabs + Template Grid + Preview Overlay --}}
<section class="max-w-7xl mx-auto px-4 sm:px-8 pb-16" x-data="templateLibrary()">
    <div class="flex flex-wrap justify-center gap-3 mb-16">
        <template x-for="tab in tabs" :key="tab.key">
            <button @click="activeCategory = tab.key"
                    :class="activeCategory === tab.key ? 'bg-secondary text-white border-secondary shadow-md' : 'bg-transparent text-primary border-primary/20 hover:border-primary/40 hover:bg-primary/5'"
                    class="px-6 py-2 rounded-full text-sm font-bold font-body border transition-all duration-300"
                    x-text="tab.label">
            </button>
        </template>
    </div>

    {{-- Template Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">

        @foreach($templates as $template)
        <div x-show="activeCategory === 'all' || activeCategory === '{{ $template->category }}'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             @click="openPreview('{{ $template->id }}', '{{ $template->name }}', '{{ addslashes($template->description) }}', '{{ route('templates.demo', $template) }}')"
             class="cursor-pointer">
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
         @click.self="closePreview()" @keydown.escape.window="closePreview()">

        <div x-show="previewOpen"
             x-transition:enter="transition ease-out duration-300 delay-100"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="bg-surface w-full max-w-4xl my-8 mx-4 rounded-2xl shadow-2xl overflow-hidden flex flex-col">

            {{-- Modal Header --}}
            <div class="px-4 sm:px-6 py-4 border-b border-primary/10 bg-surface-container-low flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
                <div>
                    <h3 class="text-lg sm:text-xl font-headline font-bold text-primary" x-text="previewName"></h3>
                    <p class="text-xs text-primary/60 mt-1 font-body hidden sm:block" x-text="previewDescription"></p>
                </div>
                <div class="flex items-center gap-3">
                    <a :href="'{{ route('register') }}'" class="inline-flex items-center gap-2 bg-secondary text-white px-4 sm:px-5 py-2 sm:py-2.5 rounded-full text-sm font-bold hover:bg-secondary/90 transition-all shadow-sm hover:shadow-md">
                        <span class="material-symbols-outlined text-[16px]">edit_document</span>
                        <span class="hidden sm:inline">{{ __('messages.landing.templates.use_template_full') }}</span>
                        <span class="sm:hidden">{{ __('messages.landing.templates.use_template_short') }}</span>
                    </a>
                    <button @click="closePreview()" class="text-primary/60 hover:text-primary transition-colors p-1.5 rounded-full hover:bg-primary/5">
                        <span class="material-symbols-outlined">close</span>
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
    <div class="relative rounded-2xl overflow-hidden min-h-[320px] flex flex-col items-center justify-center text-center p-12 md:p-16">
        {{-- Background gradient overlay --}}
        <div class="absolute inset-0 bg-gradient-to-br from-primary/90 via-primary/80 to-primary/70"></div>
        {{-- Nature texture overlay for depth --}}
        <div class="absolute inset-0 opacity-30" style="background: linear-gradient(135deg, rgba(34,60,30,0.6) 0%, rgba(79,59,47,0.4) 40%, rgba(120,90,60,0.3) 70%, rgba(79,59,47,0.5) 100%);"></div>
        
        <div class="relative z-10 max-w-xl">
            <h2 class="text-3xl md:text-4xl font-headline font-bold mb-4 tracking-tighter text-white leading-tight">
                {{ __('messages.landing.templates.cta.title') }}
            </h2>
            <p class="text-white/70 mb-8 font-body leading-relaxed text-sm md:text-base">
                {{ __('messages.landing.templates.cta.subtitle') }}
            </p>
            <a href="{{ route('home') }}" class="inline-block bg-white text-primary px-8 py-3 rounded-sm font-bold font-body text-sm hover:bg-white/90 transition-all active:scale-95 shadow-sm">
                {{ __('messages.landing.templates.cta.button') }}
            </a>
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
        previewOpen: false,
        previewName: '',
        previewDescription: '',
        previewUrl: '',
        tabs: [
            { key: 'all', label: {!! json_encode(__('messages.landing.templates.tab_all')) !!} },
            @foreach($templates->pluck('category')->unique() as $cat)
            { key: '{{ $cat }}', label: '{{ ucfirst($cat) }}' },
            @endforeach
        ],
        openPreview(id, name, description, url) {
            this.previewName = name;
            this.previewDescription = description;
            this.previewUrl = url;
            this.previewOpen = true;
            document.body.style.overflow = 'hidden';
            this.$nextTick(() => {
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
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    scaleCardIframes();
});
window.addEventListener('resize', scaleCardIframes);
</script>
@endsection
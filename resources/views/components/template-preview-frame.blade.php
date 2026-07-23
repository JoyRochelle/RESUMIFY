@props([
    'src',
    'template' => null,
    'title' => 'Template preview',
    'iframeClass' => 'template-thumbnail-iframe pointer-events-none transition-opacity duration-300 origin-top-left',
])

<div data-template-preview-shell class="absolute inset-0 bg-surface-container-low">
    <div data-template-preview-placeholder class="absolute inset-0 flex items-center justify-center bg-surface-container-low p-4 transition-opacity duration-300">
        <div class="h-[88%] w-[72%] rounded-sm border border-primary/10 bg-tertiary p-3 shadow-sm">
            <div class="mx-auto mb-3 h-2 w-2/3 rounded-full bg-primary/20"></div>
            <div class="mx-auto mb-4 h-1.5 w-1/2 rounded-full bg-secondary/30"></div>
            <div class="space-y-2">
                <div class="h-1.5 w-full rounded-full bg-primary/15"></div>
                <div class="h-1.5 w-5/6 rounded-full bg-primary/10"></div>
                <div class="h-1.5 w-4/6 rounded-full bg-primary/10"></div>
            </div>
            <div class="my-4 h-px bg-primary/10"></div>
            <div class="space-y-2">
                <div class="h-2 w-1/3 rounded-full bg-primary/20"></div>
                <div class="h-1.5 w-full rounded-full bg-primary/10"></div>
                <div class="h-1.5 w-11/12 rounded-full bg-primary/10"></div>
                <div class="h-1.5 w-3/4 rounded-full bg-primary/10"></div>
            </div>
            <div class="my-4 h-px bg-primary/10"></div>
            <div class="grid grid-cols-3 gap-2">
                <div class="h-1.5 rounded-full bg-secondary/25"></div>
                <div class="h-1.5 rounded-full bg-secondary/20"></div>
                <div class="h-1.5 rounded-full bg-secondary/15"></div>
            </div>
        </div>
    </div>

    @if($template && $template->thumbnail_url)
        <img
            data-template-preview-image
            src="{{ $template->thumbnail }}"
            alt="{{ $title }}"
            class="absolute inset-0 h-full w-full object-cover"
            loading="lazy"
            decoding="async"
            onload="this.closest('[data-template-preview-shell]')?.querySelector('[data-template-preview-placeholder]')?.classList.add('opacity-0')"
            onerror="this.hidden=true; this.classList.add('hidden'); this.closest('[data-template-preview-shell]')?.querySelector('iframe[data-template-preview-src]')?.classList.remove('hidden'); window.queueTemplatePreviewFrames?.(this.closest('[data-template-preview-shell]'))">
    @endif

    @if(! $template || ! $template->thumbnail_url)
        <iframe
            data-template-preview-src="{{ $src }}"
            title="{{ $title }}"
            scrolling="no"
            style="width: 794px; height: 1123px; transform-origin: top left; border: none; position: absolute; top: 0; left: 0;"
            class="{{ $iframeClass }} opacity-0"
            loading="lazy"
            tabindex="-1">
        </iframe>
    @else
        <iframe
            data-template-preview-src="{{ $src }}"
            title="{{ $title }}"
            scrolling="no"
            style="width: 794px; height: 1123px; transform-origin: top left; border: none; position: absolute; top: 0; left: 0;"
            class="{{ $iframeClass }} hidden opacity-0"
            loading="lazy"
            tabindex="-1">
        </iframe>
    @endif
</div>

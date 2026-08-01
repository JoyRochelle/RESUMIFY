@props(['compact' => false])

<button type="button" data-tour-trigger
        {{ $attributes->merge([
            'class' => 'inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-primary/15 px-3 py-2 font-label text-sm font-semibold text-primary/70 transition-colors hover:border-primary/30 hover:text-primary focus:outline-none focus:ring-2 focus:ring-secondary/40',
        ]) }}
        aria-label="{{ __('messages.tour.ui.start_aria') }}">
    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">school</span>
    <span @class(['hidden sm:inline' => $compact])>{{ __('messages.tour.ui.start') }}</span>
</button>

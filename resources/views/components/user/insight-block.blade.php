@props(['icon', 'label'])

<div data-anime-hover {{ $attributes->merge(['class' => 'flex gap-4 md:gap-5']) }}>
    <div data-anime-icon class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-primary/10 bg-primary/5 text-primary/70">
        <span class="material-symbols-outlined text-[22px]" aria-hidden="true">{{ $icon }}</span>
    </div>
    <div>
        <h4 class="font-label font-bold text-primary/80 uppercase tracking-widest text-xs mb-3">{{ $label }}</h4>
        <p class="font-headline text-lg md:text-2xl text-primary leading-snug">
            {{ $slot }}
        </p>
    </div>
</div>

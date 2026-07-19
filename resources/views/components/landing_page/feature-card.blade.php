@props(['title', 'icon', 'filled' => false])

<div data-anime-hover {{ $attributes->merge(['class' => 'animate-scroll-reveal group bg-surface-container-lowest p-8 rounded-lg border border-primary/10 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-200 ease-out']) }}>
    <div data-anime-icon class="mb-6 flex h-12 w-12 items-center justify-center rounded-xl bg-secondary/10 transition-colors duration-200 group-hover:bg-secondary/15">
        <span class="material-symbols-outlined text-secondary text-[26px]"
              style="{{ $filled ? "font-variation-settings: 'FILL' 1;" : "font-variation-settings: 'FILL' 0;" }}"
              aria-hidden="true">
            {{ $icon }}
        </span>
    </div>

    <h3 class="text-2xl font-headline font-bold text-primary mb-3 tracking-tight">{{ $title }}</h3>

    <p class="text-outline font-body leading-relaxed">
        {{ $slot }}
    </p>
</div>

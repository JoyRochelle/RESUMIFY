@props([
    'title',
    'description' => null,
    'icon' => 'inbox',
    'actionLabel' => null,
    'actionUrl' => null,
    'resetLabel' => null,
    'resetUrl' => null,
])

<section {{ $attributes->merge(['class' => 'rounded-lg border border-dashed border-primary/20 bg-surface-container-low/50 px-6 py-12 text-center']) }}>
    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-tertiary text-primary shadow-sm">
        <span class="material-symbols-outlined text-[28px]" aria-hidden="true">{{ $icon }}</span>
    </div>
    <h2 class="font-headline text-2xl font-bold text-primary">{{ $title }}</h2>
    @if($description)
        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-primary/60">{{ $description }}</p>
    @endif
    @if($actionLabel || $resetLabel || $slot->isNotEmpty())
        <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
            @if($actionLabel && $actionUrl)
                <a href="{{ $actionUrl }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-bold text-tertiary transition hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-secondary/40">
                    {{ $actionLabel }}
                </a>
            @endif
            @if($resetLabel && $resetUrl)
                <a href="{{ $resetUrl }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-primary/15 px-5 py-2.5 text-sm font-bold text-primary transition hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">
                    {{ $resetLabel }}
                </a>
            @endif
            {{ $slot }}
        </div>
    @endif
</section>

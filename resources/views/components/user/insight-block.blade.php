@props(['number', 'label'])

<div class="flex gap-6">
    <div class="text-5xl font-headline italic text-secondary/40 serif-number">{{ $number }}</div>
    <div>
        <h4 class="font-label font-bold text-primary/70 uppercase tracking-widest text-xs mb-3">{{ $label }}</h4>
        <p class="font-headline text-2xl text-primary leading-snug">
            {{ $slot }}
        </p>
    </div>
</div>
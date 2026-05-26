@props(['icon', 'title', 'description', 'variant' => 'default', 'iconFilled' => false])

@php
    $iconClasses = match($variant) {
        'accent' => 'bg-secondary/10 text-secondary group-hover:bg-secondary group-hover:text-tertiary',
        default => 'bg-primary/5 text-primary group-hover:bg-primary group-hover:text-tertiary',
    };
@endphp

<div class="bg-tertiary p-8 rounded-xl border border-primary/10 hover:shadow-lg transition-shadow duration-300 group cursor-pointer">
    <div class="w-12 h-12 {{ $iconClasses }} rounded-lg flex items-center justify-center mb-6 transition-colors">
        <span class="material-symbols-outlined text-3xl {{ $iconFilled ? 'icon-filled' : '' }}">{{ $icon }}</span>
    </div>
    <h3 class="font-headline text-2xl text-primary mb-3">{{ $title }}</h3>
    <p class="text-primary/60 leading-relaxed">{{ $description }}</p>
</div>

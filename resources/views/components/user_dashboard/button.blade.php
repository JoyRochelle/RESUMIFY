@props([
    'variant' => 'primary',
    'icon' => null,
    'iconClass' => '',
    'iconStyle' => ''
])

@php
    $baseClasses = 'flex items-center justify-center gap-2 font-bold transition-all';

    $variantClasses = match($variant) {
        'primary' => 'px-5 py-2 text-sm bg-primary text-tertiary rounded-lg hover:opacity-90',
        'ghost' => 'px-5 py-2 text-sm text-primary hover:bg-primary/5 rounded-lg',
        'dashed' => 'w-full py-4 border-2 border-dashed border-primary/20 rounded-lg text-primary/60 hover:text-primary hover:border-primary/50 hover:bg-primary/5',
        'pill' => 'bg-secondary text-tertiary px-3 py-1.5 rounded-full text-xs hover:brightness-110 shadow-md',
        'text' => 'text-primary/60 hover:text-primary',
        default => 'px-5 py-2 text-sm bg-primary text-tertiary rounded-lg hover:opacity-90',
    };
@endphp

<button {{ $attributes->merge(['class' => "$baseClasses $variantClasses"]) }}>
    @if($icon)
        <span class="material-symbols-outlined {{ $iconClass }}" style="{{ $iconStyle }}">{{ $icon }}</span>
    @endif
    {{ $slot }}
</button>
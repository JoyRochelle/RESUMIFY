@props([
    'label',
    'icon',
    'variant' => 'ghost',
])

@php
    $variantClasses = match($variant) {
        'danger' => 'text-red-600 hover:bg-red-50 hover:text-red-700',
        'secondary' => 'text-secondary hover:bg-secondary/10',
        default => 'text-primary/60 hover:bg-primary/5 hover:text-primary',
    };
@endphp

<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex min-h-11 min-w-11 items-center justify-center rounded-full transition focus:outline-none focus:ring-2 focus:ring-secondary/40 ' . $variantClasses]) }}
    aria-label="{{ $label }}"
    title="{{ $label }}"
>
    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">{{ $icon }}</span>
</button>

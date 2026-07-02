@props([
    'loadingText' => 'Working...',
    'variant' => 'primary',
    'icon' => null,
])

@php
    $variantClasses = match($variant) {
        'secondary' => 'bg-secondary text-white hover:bg-secondary/90',
        'danger' => 'bg-red-600 text-white hover:bg-red-700',
        'outline' => 'border border-primary/20 text-primary hover:bg-primary/5',
        'ghost' => 'text-primary/70 hover:bg-primary/5',
        default => 'bg-primary text-tertiary hover:bg-primary/90',
    };
@endphp

<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex min-h-11 items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-secondary/40 disabled:cursor-not-allowed disabled:opacity-60 ' . $variantClasses]) }}
    x-data="{ loading: false }"
    x-on:click.prevent="if (!$el.form) return; if (!$el.form.checkValidity()) { $el.form.reportValidity(); return; } loading = true; $el.disabled = true; setTimeout(() => $el.form.requestSubmit ? $el.form.requestSubmit() : $el.form.submit(), 0);"
    x-bind:aria-busy="loading.toString()"
>
    <span class="material-symbols-outlined animate-spin text-[18px]" x-show="loading" style="display:none" aria-hidden="true">progress_activity</span>
    @if($icon)
        <span class="material-symbols-outlined text-[18px]" x-show="!loading" aria-hidden="true">{{ $icon }}</span>
    @endif
    <span x-show="!loading">{{ $slot }}</span>
    <span x-show="loading" style="display:none">{{ $loadingText }}</span>
</button>

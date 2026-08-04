@props([
    'variant' => 'primary',
    'icon' => null,
    'iconClass' => ''
])

@php
    // The baked-in `inline-flex` cannot be overridden by a plain `hidden`/`flex`
    // class from the caller: Tailwind v4 emits unprefixed display utilities in
    // alphabetical order, so `.inline-flex` lands after `.hidden` and wins.
    // Hide a button with a breakpoint variant instead (`max-sm:hidden`, `lg:hidden`).
    $baseClasses = 'inline-flex min-h-11 items-center justify-center gap-2 font-bold transition-all duration-200 active:scale-[0.98] focus:outline-none focus:ring-2 disabled:cursor-not-allowed disabled:opacity-60';

    $variantClasses = match($variant) {
        'primary' => 'px-5 py-2 text-sm bg-primary text-tertiary rounded-lg hover:bg-primary/90 focus:ring-secondary/40',
        'ghost' => 'px-5 py-2 text-sm text-primary hover:bg-primary/5 rounded-lg focus:ring-secondary/40',
        'outline' => 'px-5 py-2 text-sm text-primary border border-primary/20 rounded-lg hover:bg-primary/5 focus:ring-secondary/40',
        'danger' => 'px-5 py-2 text-sm text-red-600 border border-red-300 rounded-lg hover:bg-red-600 hover:text-white focus:ring-red-300',
        'dashed' => 'w-full py-4 border-2 border-dashed border-primary/20 rounded-lg text-primary/60 hover:text-primary hover:border-primary/50 hover:bg-primary/5 focus:ring-secondary/40',
        'pill' => 'bg-secondary text-tertiary px-4 py-1.5 rounded-full text-xs hover:bg-secondary/90 focus:ring-secondary/40',
        'text' => 'px-3 py-2 text-sm text-primary/60 hover:text-primary hover:bg-primary/5 rounded-lg focus:ring-secondary/40',
        default => 'px-5 py-2 text-sm bg-primary text-tertiary rounded-lg hover:bg-primary/90 focus:ring-secondary/40',
    };
@endphp

<button {{ $attributes->merge(['class' => "$baseClasses $variantClasses"]) }}>
    @if($icon)
        <span class="material-symbols-outlined {{ $iconClass }}" aria-hidden="true">{{ $icon }}</span>
    @endif
    {{ $slot }}
</button>

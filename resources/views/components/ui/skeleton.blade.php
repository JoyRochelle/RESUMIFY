@props(['lines' => 3])

<div {{ $attributes->merge(['class' => 'animate-pulse space-y-3']) }} role="status" aria-label="Loading content">
    @for($i = 0; $i < $lines; $i++)
        <div class="h-3 rounded-full bg-primary/10 {{ $i === $lines - 1 ? 'w-2/3' : 'w-full' }}"></div>
    @endfor
</div>

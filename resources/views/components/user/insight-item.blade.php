@props(['title'])

<div class="flex items-start gap-4">
    <div class="w-6 h-6 rounded-full bg-secondary/20 flex items-center justify-center flex-shrink-0 mt-1">
        <span class="material-symbols-outlined text-secondary text-xs">done</span>
    </div>
    <p class="text-sm text-primary leading-relaxed">
        <span class="font-bold">{{ $title }}:</span> {{ $slot }}
    </p>
</div>

@props(['title', 'icon', 'isOpen' => false])

<div class="bg-tertiary rounded-lg border border-primary/10 shadow-sm">
    <div class="flex justify-between items-center cursor-pointer {{ $isOpen ? 'p-5 bg-surface-container-low rounded-t-lg border-b border-primary/10' : 'p-5 group' }}">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined {{ $isOpen ? 'text-primary' : 'text-primary/60 group-hover:text-primary transition-colors' }}">{{ $icon }}</span>
            <h3 class="font-bold text-primary">{{ $title }}</h3>
        </div>
        <span class="material-symbols-outlined {{ $isOpen ? 'text-primary' : 'text-primary/60 group-hover:text-primary transition-colors' }}">{{ $isOpen ? 'expand_less' : 'expand_more' }}</span>
    </div>
    
    @if($isOpen)
        <div class="p-6 space-y-8">
            {{ $slot }}
        </div>
    @endif
</div>
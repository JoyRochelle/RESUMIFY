@props(['title', 'icon', 'isOpen' => false])

<div x-data="{ open: {{ $isOpen ? 'true' : 'false' }} }" class="bg-tertiary rounded-lg border border-primary/10 shadow-sm transition-all duration-300">
    <div @click="open = !open" 
         :class="open ? 'p-5 bg-surface-container-low rounded-t-lg border-b border-primary/10' : 'p-5 group'"
         class="flex justify-between items-center cursor-pointer transition-colors duration-200">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined transition-colors duration-200"
                  :class="open ? 'text-primary' : 'text-primary/60 group-hover:text-primary'">
                {{ $icon }}
            </span>
            <h3 class="font-bold text-primary">{{ $title }}</h3>
        </div>
        <span class="material-symbols-outlined transition-transform duration-300"
              :class="open ? 'text-primary rotate-180' : 'text-primary/60 group-hover:text-primary'">
            expand_more
        </span>
    </div>
    
    <div x-show="open" 
         x-collapse 
         x-cloak
         class="overflow-hidden">
        <div class="p-6 space-y-8">
            {{ $slot }}
        </div>
    </div>
</div>
@props(['title', 'icon', 'filled' => false])

<div class="bg-surface-container-lowest p-10 rounded-sm group hover:translate-y-[-8px] transition-all duration-500 border border-primary/5 shadow-sm">
    
    <div class="w-14 h-14 bg-secondary/10 rounded-xl flex items-center justify-center mb-8 transition-colors group-hover:bg-secondary/20">
        <span class="material-symbols-outlined text-secondary text-3xl" 
              style="{{ $filled ? "font-variation-settings: 'FILL' 1;" : "font-variation-settings: 'FILL' 0;" }}">
            {{ $icon }}
        </span>
    </div>

    <h3 class="text-2xl font-headline text-primary mb-4 tracking-tighter">{{ $title }}</h3>
    
    <p class="text-outline font-body leading-relaxed">
        {{ $slot }}
    </p>
</div>
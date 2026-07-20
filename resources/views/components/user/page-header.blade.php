@props(['title', 'backUrl' => '#'])

<header
    class="animate-fade-up h-auto min-h-[64px] py-4 lg:py-0 flex flex-col lg:flex-row justify-between items-center px-4 lg:px-8 w-full border-b border-primary/10 bg-surface shrink-0 z-30 gap-4 lg:gap-0">
    <div class="flex items-center gap-4 w-full lg:w-auto">
        <a href="{{ $backUrl }}"
            class="flex items-center gap-2 text-primary/60 hover:text-primary transition-colors cursor-pointer"
            aria-label="Go back">
            <span class="material-symbols-outlined text-primary text-xl hover:-translate-x-1 transition-transform duration-200">arrow_back</span>
        </a>
        <h1 class="font-headline text-lg lg:text-xl font-bold text-primary tracking-tight">{{ $title }}</h1>
    </div>
    @if ($slot->isNotEmpty())
        <div class="flex items-center gap-3 w-full lg:w-auto justify-between lg:justify-end">
            {{ $slot }}
        </div>
    @endif
</header>

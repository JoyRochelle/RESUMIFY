@props(['title', 'date', 'url' => '#'])

<div class="group bg-tertiary rounded-lg border border-primary/10 hover:shadow-[0_16px_32px_rgba(79,59,47,0.08)] transition-all duration-500 overflow-hidden flex flex-col cursor-pointer">
    <div class="aspect-[3/4] bg-surface-container-low p-6 overflow-hidden">
        <div class="bg-tertiary h-full w-full shadow-sm rounded-sm p-4 space-y-3 transform group-hover:scale-105 group-hover:rotate-1 transition-transform duration-500 origin-top border border-primary/5">
            <div class="h-1.5 w-1/3 bg-primary/20 rounded-full"></div>
            <div class="h-1.5 w-2/3 bg-primary/10 rounded-full"></div>
            <div class="grid grid-cols-3 gap-2 py-4">
                <div class="h-24 bg-surface border border-primary/10 rounded"></div>
                <div class="col-span-2 space-y-2">
                    <div class="h-1 w-full bg-primary/10 rounded-full"></div>
                    <div class="h-1 w-full bg-primary/10 rounded-full"></div>
                    <div class="h-1 w-5/6 bg-primary/10 rounded-full"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="p-6 bg-tertiary">
        <h3 class="text-lg font-headline font-bold text-primary mb-1">{{ $title }}</h3>
        <p class="text-sm text-primary/60 font-label mb-6">Last edited: {{ $date }}</p>
        <div class="flex items-center justify-between">
            <a href="{{ $url }}" class="text-secondary font-label font-bold text-sm hover:underline flex items-center gap-1">
                <span class="material-symbols-outlined text-base" data-icon="edit">edit</span>
                Edit
            </a>
            <button class="p-1 hover:bg-surface-container-low rounded-full transition-colors text-primary/60">
                <span class="material-symbols-outlined" data-icon="more_vert">more_vert</span>
            </button>
        </div>
    </div>
</div>
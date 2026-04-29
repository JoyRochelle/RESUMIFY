@props(['question'])

<div class="bg-tertiary rounded-xl overflow-hidden border border-primary/10">
    <button class="w-full px-8 py-6 flex justify-between items-center text-left hover:bg-primary/5 transition-colors">
        <span class="font-headline text-xl text-primary">{{ $question }}</span>
        <span class="material-symbols-outlined text-primary">expand_more</span>
    </button>
</div>

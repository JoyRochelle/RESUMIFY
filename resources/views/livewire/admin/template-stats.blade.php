<div class="grid grid-cols-2 md:grid-cols-4 gap-6">
    <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
        <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-3">Total</h3>
        <p class="text-3xl font-headline text-primary">{{ number_format($total) }}</p>
    </div>
    <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
        <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-3">Active</h3>
        <p class="text-3xl font-headline text-secondary">{{ number_format($activeCount) }}</p>
    </div>
    <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
        <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-3">Premium</h3>
        <p class="text-3xl font-headline text-amber-500">{{ number_format($premiumCount) }}</p>
    </div>
    <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
        <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-3">Inactive</h3>
        <p class="text-3xl font-headline text-primary/40">{{ number_format($inactiveCount) }}</p>
    </div>
</div>

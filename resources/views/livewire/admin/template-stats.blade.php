<div class="grid grid-cols-2 md:grid-cols-4 gap-6">
    <div class="admin-card-pad">
        <h3 class="admin-section-title mb-3">Total</h3>
        <p class="text-3xl font-headline text-primary">{{ number_format($total) }}</p>
    </div>
    <div class="admin-card-pad">
        <h3 class="admin-section-title mb-3">Active</h3>
        <p class="text-3xl font-headline text-secondary">{{ number_format($activeCount) }}</p>
    </div>
    <div class="admin-card-pad">
        <h3 class="admin-section-title mb-3">Premium</h3>
        <p class="text-3xl font-headline text-amber-500">{{ number_format($premiumCount) }}</p>
    </div>
    <div class="admin-card-pad">
        <h3 class="admin-section-title mb-3">Inactive</h3>
        <p class="text-3xl font-headline text-primary/40">{{ number_format($inactiveCount) }}</p>
    </div>
</div>

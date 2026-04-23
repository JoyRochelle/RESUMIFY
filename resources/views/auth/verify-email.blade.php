<x-layouts.auth title="Verify Email | Resumify" class="flex items-center justify-center p-6">
    <div
        class="w-full max-w-md bg-surface-container-lowest rounded-3xl border border-outline-variant/30 p-8 md:p-12 shadow-2xl shadow-primary/5 transition-auth-card">
        {{-- Brand Anchor --}}
        <x-auth.brand class="mb-10" />

        {{-- The Verify Box Component --}}
        <x-auth.verify-box />
    </div>
</x-layouts.auth>

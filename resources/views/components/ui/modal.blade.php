@props([
    'id',
    'title',
    'description' => null,
    'maxWidth' => 'max-w-lg',
])

<div
    id="{{ $id }}"
    x-data="{
        open: false,
        trigger: null,
        openModal() {
            this.trigger = document.activeElement;
            this.open = true;
            this.$nextTick(() => {
                const first = this.$refs.panel.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex=\"-1\"])');
                (first || this.$refs.panel).focus();
            });
        },
        closeModal() {
            this.open = false;
            this.$nextTick(() => this.trigger && this.trigger.focus && this.trigger.focus());
        }
    }"
    x-on:open-modal.window="if ($event.detail === '{{ $id }}' || $event.detail?.id === '{{ $id }}') openModal()"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $id }}-title"
    @if($description) aria-describedby="{{ $id }}-description" @endif
    x-on:keydown.escape.window="open && closeModal()"
>
    <div class="absolute inset-0" aria-hidden="true" x-on:click="closeModal()"></div>
    <section
        x-ref="panel"
        tabindex="-1"
        class="relative flex max-h-[90vh] w-full {{ $maxWidth }} flex-col overflow-hidden rounded-lg border border-primary/10 bg-tertiary shadow-2xl outline-none"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
    >
        <header class="flex items-start justify-between gap-4 border-b border-primary/10 bg-surface-container-low px-6 py-4">
            <div>
                <h2 id="{{ $id }}-title" class="font-headline text-xl font-bold text-primary">{{ $title }}</h2>
                @if($description)
                    <p id="{{ $id }}-description" class="mt-1 text-sm text-primary/60">{{ $description }}</p>
                @endif
            </div>
            <button type="button"
                    class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-full text-primary/60 transition hover:bg-primary/5 hover:text-primary focus:outline-none focus:ring-2 focus:ring-secondary/40"
                    aria-label="Close {{ $title }}"
                    x-on:click="closeModal()">
                <span class="material-symbols-outlined" aria-hidden="true">close</span>
            </button>
        </header>
        <div class="overflow-y-auto p-6 custom-scrollbar">
            {{ $slot }}
        </div>
    </section>
</div>

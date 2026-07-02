<div x-data="{
    toasts: [],
    addToast(message, type = 'success') {
        const id = Date.now() + Math.random();
        this.toasts.push({ id, message, type });
        setTimeout(() => this.removeToast(id), 5000);
    },
    removeToast(id) {
        this.toasts = this.toasts.filter(toast => toast.id !== id);
    }
}"
@notify.window="addToast($event.detail.message, $event.detail.type || 'success')"
class="fixed bottom-20 right-5 z-50 flex flex-col items-end gap-3 md:bottom-5"
role="status"
aria-live="polite"
aria-atomic="true">
    @if(session('success'))
        <span x-init="$nextTick(() => addToast(@js(session('success')), 'success'))"></span>
    @endif
    @if(session('error'))
        <span x-init="$nextTick(() => addToast(@js(session('error')), 'error'))"></span>
    @endif
    @if(session('status'))
        <span x-init="$nextTick(() => addToast(@js(session('status')), 'info'))"></span>
    @endif

    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="true"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
             :class="{
                 'border-secondary bg-secondary/10 text-secondary': toast.type === 'success',
                 'border-red-500 bg-red-500/10 text-red-500': toast.type === 'error',
                 'border-primary bg-primary/10 text-primary': toast.type === 'info'
             }"
             class="flex items-center gap-3 rounded-lg border bg-surface px-5 py-3 shadow-lg backdrop-blur-md"
             x-bind:role="toast.type === 'error' ? 'alert' : 'status'"
             x-bind:aria-live="toast.type === 'error' ? 'assertive' : 'polite'">
            <span class="material-symbols-outlined icon-filled text-[20px]" x-show="toast.type === 'success'" aria-hidden="true">check_circle</span>
            <span class="material-symbols-outlined icon-filled text-[20px]" x-show="toast.type === 'error'" aria-hidden="true">error</span>
            <span class="material-symbols-outlined icon-filled text-[20px]" x-show="toast.type === 'info'" aria-hidden="true">info</span>
            <span class="font-body text-sm font-medium" x-text="toast.message"></span>
            <button type="button"
                    class="ml-1 inline-flex min-h-11 min-w-11 items-center justify-center rounded-full opacity-60 transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-secondary/40"
                    aria-label="Dismiss notification"
                    @click="removeToast(toast.id)">
                <span class="material-symbols-outlined text-[16px]" aria-hidden="true">close</span>
            </button>
        </div>
    </template>
</div>

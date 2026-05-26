<div x-data="{
    toasts: [],
    addToast(message, type = 'success') {
        const id = Date.now();
        this.toasts.push({ id, message, type });
        setTimeout(() => this.removeToast(id), 5000);
    },
    removeToast(id) {
        this.toasts = this.toasts.filter(toast => toast.id !== id);
    }
}" 
@notify.window="addToast($event.detail.message, $event.detail.type || 'success')"
class="fixed bottom-5 right-5 z-50 flex flex-col gap-3 items-end">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="true" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
             :class="{
                 'bg-secondary/10 border-secondary text-secondary': toast.type === 'success',
                 'bg-red-500/10 border-red-500 text-red-500': toast.type === 'error',
                 'bg-primary/10 border-primary text-primary': toast.type === 'info'
             }"
             class="border rounded-xl px-5 py-3 shadow-lg flex items-center gap-3 bg-surface backdrop-blur-md">
            
            <span class="material-symbols-outlined text-[20px] icon-filled" x-show="toast.type === 'success'">check_circle</span>
            <span class="material-symbols-outlined text-[20px] icon-filled" x-show="toast.type === 'error'">error</span>
            <span class="material-symbols-outlined text-[20px] icon-filled" x-show="toast.type === 'info'">info</span>
            
            <span class="font-body text-sm font-medium" x-text="toast.message"></span>
            
            <button @click="removeToast(toast.id)" class="opacity-50 hover:opacity-100 transition-opacity ml-2 focus:outline-none">
                <span class="material-symbols-outlined text-[16px]">close</span>
            </button>
        </div>
    </template>
</div>

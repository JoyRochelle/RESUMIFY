<div class="bg-white rounded-3xl shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 overflow-hidden flex flex-col">

    <!-- Template Preview (A4 aspect-ratio iframe, same pattern as user dashboard) -->
    <div class="relative w-full aspect-[210/297] bg-surface overflow-hidden flex-shrink-0"
         x-data="{}"
         x-init="
             const iframe = $el.querySelector('iframe');
             const scale = $el.offsetWidth / 794;
             if (iframe) {
                 iframe.style.transform = 'scale(' + scale + ')';
             }
         ">
        <iframe src="{{ route('admin.templates.preview', $template) }}"
                loading="lazy"
                tabindex="-1"
                style="width: 794px; height: 1123px; transform-origin: top left; border: none; position: absolute; top: 0; left: 0; pointer-events: none;">
        </iframe>

        <!-- Transparent click shield -->
        <div class="absolute inset-0 bg-transparent z-10"></div>

        <!-- Status badge (top-right) -->
        <div class="absolute top-3 right-3 z-20">
            <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-1 rounded-full
                {{ $template->is_active ? 'bg-secondary/20 text-secondary' : 'bg-primary/10 text-primary/40' }}">
                {{ $template->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <!-- Badges (top-left) -->
        <div class="absolute top-3 left-3 z-20 flex items-center gap-2">
            @if($template->is_premium)
                <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-1 rounded-full bg-amber-100 text-amber-700">Premium</span>
            @endif
            @if($template->badge)
                @php
                    $badgeColors = [
                        'blue'      => 'bg-blue-100 text-blue-700',
                        'secondary' => 'bg-secondary/20 text-secondary',
                        'purple'    => 'bg-purple-100 text-purple-700',
                        'green'     => 'bg-green-100 text-green-700',
                    ];
                @endphp
                <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-1 rounded-full {{ $badgeColors[$template->badge_color] ?? 'bg-primary/10 text-primary/60' }}">
                    {{ $template->badge }}
                </span>
            @endif
        </div>
    </div>

    <!-- Card Body -->
    <div class="p-5 flex-1 flex flex-col">
        <div class="flex items-start justify-between mb-1">
            <h3 class="text-sm font-label font-bold text-primary">{{ $template->name }}</h3>
            <span class="text-[10px] font-label text-primary/40 capitalize ml-2 flex-shrink-0">{{ $template->category }}</span>
        </div>

        @if($template->description)
            <p class="text-[11px] font-label text-primary/50 mb-3 line-clamp-2 flex-1">{{ $template->description }}</p>
        @else
            <div class="flex-1"></div>
        @endif

        <!-- Actions -->
        <div class="flex items-center justify-between mt-3 pt-3 border-t border-primary/5"
             x-data="{ showDelete: false }">
            <span class="text-[10px] font-label text-primary/40">Sort: {{ $template->sort_order }}</span>
            <div class="flex items-center gap-1">

                <!-- Preview -->
                <a href="{{ route('admin.templates.preview', $template) }}" target="_blank"
                   class="p-1.5 rounded-lg text-primary/40 hover:text-primary hover:bg-primary/5 transition"
                   title="Preview">
                    <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                </a>

                <!-- Edit -->
                <a href="{{ route('admin.templates.edit', $template) }}"
                   class="p-1.5 rounded-lg text-primary/40 hover:text-primary hover:bg-primary/5 transition"
                   title="Edit">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                </a>

                <!-- Toggle (Livewire — no page refresh) -->
                <button wire:click="toggle"
                        wire:loading.attr="disabled"
                        class="p-1.5 rounded-lg transition {{ $template->is_active ? 'text-secondary/60 hover:text-secondary hover:bg-secondary/5' : 'text-primary/40 hover:text-primary hover:bg-primary/5' }}"
                        title="{{ $template->is_active ? 'Deactivate' : 'Activate' }}">
                    <span wire:loading.remove wire:target="toggle"
                          class="material-symbols-outlined text-[18px]">
                        {{ $template->is_active ? 'toggle_on' : 'toggle_off' }}
                    </span>
                    <span wire:loading wire:target="toggle"
                          class="material-symbols-outlined text-[18px] animate-spin">autorenew</span>
                </button>

                <!-- Delete (triggers Alpine modal) -->
                <button @click="showDelete = true"
                        class="p-1.5 rounded-lg text-red-400/60 hover:text-red-500 hover:bg-red-50 transition"
                        title="Delete">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
            </div>

            <!-- Delete Confirmation Modal (scoped to this card via Alpine x-data above) -->
            <div x-show="showDelete" x-cloak
                 class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
                 @keydown.escape.window="showDelete = false">
                <div class="bg-white rounded-3xl shadow-xl p-8 max-w-sm w-full" @click.outside="showDelete = false">
                    <div class="text-center mb-6">
                        <span class="material-symbols-outlined text-red-400 text-[48px] block mb-3">delete_forever</span>
                        <h3 class="text-lg font-headline font-bold text-primary mb-2">Delete Template?</h3>
                        <p class="text-sm font-label text-primary/60">
                            Permanently delete
                            <span class="font-semibold text-primary">{{ $template->name }}</span>?
                            This cannot be undone.
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click="showDelete = false"
                                class="flex-1 py-2.5 rounded-xl border border-primary/10 text-sm font-label text-primary/60 hover:text-primary transition">
                            Cancel
                        </button>
                        <button wire:click="delete"
                                wire:loading.attr="disabled"
                                @click="showDelete = false"
                                class="flex-1 py-2.5 rounded-xl bg-red-500 text-white text-sm font-label hover:bg-red-600 transition">
                            <span wire:loading.remove wire:target="delete">Delete</span>
                            <span wire:loading wire:target="delete">Deleting…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

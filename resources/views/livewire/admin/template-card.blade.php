<div class="admin-card flex flex-col overflow-hidden">

    <!-- Status + Premium badges row (outside wire:ignore so they update on toggle) -->
    <div class="relative flex-shrink-0">
        <div class="absolute top-3 right-3 z-20" style="position:absolute">
            <span class="admin-badge
                {{ $template->is_active ? 'bg-secondary/20 text-secondary' : 'bg-primary/10 text-primary/40' }}">
                {{ $template->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <div class="absolute top-3 left-3 z-20 flex items-center gap-2" style="position:absolute">
            @if($template->is_premium)
                <span class="admin-badge bg-amber-100 text-amber-700">Premium</span>
            @endif
            @if($template->badge)
                @php
                    $badgeColors = [
                        'blue'      => 'bg-blue-100 text-blue-700',
                        'secondary' => 'bg-secondary/20 text-secondary',
                        'purple'    => 'bg-purple-100 text-purple-700',
                        'green'     => 'bg-green-100 text-green-700',
                        'slate'     => 'bg-slate-200 text-slate-700',
                        'teal'      => 'bg-teal-100 text-teal-700',
                        'gray'      => 'bg-gray-200 text-gray-700',
                    ];
                @endphp
                <span class="admin-badge {{ $badgeColors[$template->badge_color] ?? 'bg-primary/10 text-primary/60' }}">
                    {{ $template->badge }}
                </span>
            @endif
        </div>

        <!-- Template Preview — wire:ignore so Livewire never re-morphs it (keeps scale from x-init) -->
        <div wire:ignore
             class="relative w-full aspect-[210/297] bg-surface overflow-hidden"
             x-data="{}"
             x-init="
                 const iframe = $el.querySelector('iframe');
                 const scale = $el.offsetWidth / 794;
                 if (iframe) iframe.style.transform = 'scale(' + scale + ')';
             ">
            <iframe src="{{ route('admin.templates.preview', $template) }}"
                    scrolling="no"
                    loading="lazy"
                    tabindex="-1"
                    style="width: 794px; height: 1123px; transform-origin: top left; border: none; position: absolute; top: 0; left: 0; pointer-events: none;">
            </iframe>
            <!-- Transparent click shield -->
            <div class="absolute inset-0 z-10"></div>
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
        <div class="flex items-center justify-between mt-3 pt-3 border-t border-primary/5">
            <span class="text-[10px] font-label text-primary/40">Sort: {{ $template->sort_order }}</span>
            <div class="flex items-center gap-1">

                <!-- Preview -->
                <a href="{{ route('admin.templates.preview', $template) }}" target="_blank"
                   class="admin-icon-action"
                   title="Preview"
                   aria-label="Preview {{ $template->name }}">
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">open_in_new</span>
                </a>

                <!-- Edit -->
                <a href="{{ route('admin.templates.edit', $template) }}"
                   class="admin-icon-action"
                   title="Edit"
                   aria-label="Edit {{ $template->name }}">
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">edit</span>
                </a>

                <!-- Toggle (Livewire — no page refresh) -->
                <button wire:click="toggle"
                        wire:loading.attr="disabled"
                        class="admin-icon-action {{ $template->is_active ? 'text-secondary/60 hover:text-secondary hover:bg-secondary/5' : 'text-primary/40 hover:text-primary hover:bg-primary/5' }}"
                        title="{{ $template->is_active ? 'Deactivate' : 'Activate' }}"
                        aria-label="{{ $template->is_active ? 'Deactivate' : 'Activate' }} {{ $template->name }}">
                    <span wire:loading.remove wire:target="toggle"
                          class="material-symbols-outlined text-[18px]">
                        {{ $template->is_active ? 'toggle_on' : 'toggle_off' }}
                    </span>
                    <span wire:loading wire:target="toggle"
                          class="material-symbols-outlined text-[18px] animate-spin">autorenew</span>
                </button>

                <!-- Delete -->
                <button type="button"
                        x-data
                        @click="$dispatch('open-modal', 'delete-template-{{ $template->id }}')"
                        class="admin-icon-action text-red-500 hover:bg-red-50 hover:text-red-600"
                        title="Delete"
                        aria-label="Delete {{ $template->name }}">
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">delete</span>
                </button>
            </div>

            <x-ui.modal id="delete-template-{{ $template->id }}" title="Delete Template" description="This destructive action cannot be undone.">
                <x-ui.alert variant="error" title="Permanent deletion" class="mb-6">
                    Permanently delete <span class="font-semibold text-primary">{{ $template->name }}</span>?
                </x-ui.alert>
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" class="admin-btn-secondary" x-on:click="closeModal()">Cancel</button>
                    <button wire:click="delete"
                            wire:loading.attr="disabled"
                            class="admin-btn-danger">
                        <span wire:loading.remove wire:target="delete">Delete</span>
                        <span wire:loading wire:target="delete">Deleting...</span>
                    </button>
                </div>
            </x-ui.modal>
        </div>
    </div>

</div>

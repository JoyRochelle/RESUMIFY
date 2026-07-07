@props(['title', 'date', 'url' => '#', 'cvId' => null])

<article class="group bg-tertiary rounded-lg border border-primary/10 hover:shadow-[0_16px_32px_rgba(79,59,47,0.08)] transition-all duration-500 overflow-hidden flex flex-col">
    <div class="aspect-[210/297] bg-surface-container-low overflow-hidden relative border-b border-primary/5">
        <div class="absolute inset-0 bg-transparent z-10"></div>
        @if($cvId)
            <iframe src="{{ route('resumes.preview', $cvId) }}" 
                    style="width: 794px; height: 1123px; transform-origin: top left; border: none; position: absolute; top: 0; left: 0;"
                    class="cv-thumbnail-iframe pointer-events-none transition-transform duration-500 origin-top-left group-hover:opacity-90"
                    loading="lazy" tabindex="-1">
            </iframe>
        @else
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
        @endif
        
        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4 z-20">
            <span class="bg-secondary text-white text-xs px-3 py-1.5 rounded-full font-bold shadow-sm">{{ __('messages.resume_card.edit_manuscript') }}</span>
        </div>

        <!-- Clickable overlay to open resume -->
        <a href="{{ $url }}" class="absolute inset-0 z-30" aria-label="{{ __('messages.resume_card.edit_aria', ['title' => $title]) }}"></a>
    </div>
    <div class="p-6 bg-tertiary relative z-20">
        <div class="flex items-start justify-between gap-2 mb-1">
            <h3 class="text-lg font-headline font-bold text-primary leading-tight">{{ $title }}</h3>

        </div>
        <p class="text-sm text-primary/60 font-label mb-6">{{ __('messages.resume_card.last_edited', ['date' => $date]) }}</p>
        <div class="flex items-center justify-between border-t border-primary/5 pt-4">
            <a href="{{ $url }}" class="text-secondary font-label font-bold text-sm hover:underline flex items-center gap-1">
                <span class="material-symbols-outlined text-base" data-icon="edit">edit</span>
                {{ __('messages.resume_card.edit') }}
            </a>
            @if($cvId)
            <div class="flex items-center gap-1">
                {{-- Rename Button --}}
                <button type="button"
                        onclick="openRenameModal('{{ $cvId }}', {{ json_encode($title) }})"
                        title="{{ __('messages.resume_card.rename') }}"
                        aria-label="{{ __('messages.resume_card.rename_aria', ['title' => $title]) }}"
                        class="min-h-11 min-w-11 hover:bg-primary/5 hover:text-secondary rounded-full transition-colors text-primary/40 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-secondary/40">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">drive_file_rename_outline</span>
                </button>
                {{-- Duplicate Button --}}
                <form method="POST" action="{{ route('resumes.duplicate', $cvId) }}" class="inline">
                    @csrf
                    <button type="submit"
                            title="{{ __('messages.resume_card.duplicate') }}"
                            aria-label="{{ __('messages.resume_card.duplicate_aria', ['title' => $title]) }}"
                            class="min-h-11 min-w-11 hover:bg-blue-50 hover:text-blue-600 rounded-full transition-colors text-primary/40 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-secondary/40">
                        <span class="material-symbols-outlined text-[20px]" data-icon="content_copy" aria-hidden="true">content_copy</span>
                    </button>
                </form>
                {{-- Delete Button --}}
                <button type="button" onclick="openDeleteModal('{{ $cvId }}')"
                        title="{{ __('messages.resume_card.delete') }}"
                        aria-label="{{ __('messages.resume_card.delete_aria', ['title' => $title]) }}"
                        class="min-h-11 min-w-11 hover:bg-red-50 hover:text-red-600 rounded-full transition-colors text-primary/40 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-red-300">
                    <span class="material-symbols-outlined text-[20px]" data-icon="delete" aria-hidden="true">delete</span>
                </button>
            </div>
            @else
            <button type="button" class="min-h-11 min-w-11 hover:bg-surface-container-low rounded-full transition-colors text-primary/40 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-secondary/40" aria-label="More actions">
                <span class="material-symbols-outlined text-[20px]" data-icon="more_vert" aria-hidden="true">more_vert</span>
            </button>
            @endif
        </div>
    </div>
</article>

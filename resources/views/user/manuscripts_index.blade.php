@extends('layouts.user.app')

@section('title', 'Resumify - Your Manuscripts')

@section('content')
    @php
        $user = auth()->user();
        $cvs = $user->cvs()->latest('updated_at')->get();
    @endphp

    <main class="flex-1 p-4 sm:p-6 md:p-12 max-w-7xl mx-auto w-full pb-24 md:pb-12">
        <header class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-6 md:mb-8 animate-fade-up">
            <div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-headline text-primary tracking-tight leading-tight mb-2">{{ __('messages.manuscripts_page.title') }}</h1>
                <p class="text-primary/60 font-label text-sm max-w-md">{{ __('messages.manuscripts_page.subtitle') }}</p>
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-user.plan-badge :user="$user" />
                    {{-- <span class="inline-flex items-center rounded-full border border-primary/10 bg-tertiary px-3 py-1 text-sm font-label text-primary/80">
                        {{ __('messages.dashboard.resume_quota', ['used' => $user->getResumeQuotaUsed(), 'limit' => $user->getResumeLimit() ?? __('messages.dashboard.unlimited')]) }}
                    </span> --}}
                </div>
            </div>

            <x-user.btn-create />
        </header>

        <x-user.quota-status :user="$user" class="mb-10 md:mb-12 animate-fade-up" style="animation-delay: 100ms" />

        <section class="animate-fade-up" style="animation-delay: 160ms">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($cvs as $cv)
                <x-user.resume-card
                    title="{{ $cv->title ?: __('messages.dashboard.untitled_resume') }}"
                    date="{{ $cv->updated_at->diffForHumans() }}" 
                    url="{{ route('user.manuscript', ['cv_id' => $cv->id]) }}" 
                    cvId="{{ $cv->id }}" 
                />
                @endforeach

                <button type="button" onclick="{{ $user->canCreateResume() ? 'openCreateModal()' : '' }}"
                    @unless($user->canCreateResume()) disabled aria-describedby="manuscripts-create-limit" @endunless
                    class="w-full h-full group relative bg-surface-container-low/50 rounded-lg border-2 border-dashed {{ $user->canCreateResume() ? 'border-primary/20 hover:border-primary/50 hover:bg-surface-container-low cursor-pointer' : 'border-[#A16207]/30 cursor-not-allowed' }} transition-all duration-300 overflow-hidden flex flex-col items-center justify-center min-h-[200px] sm:min-h-[400px]">
                    <div class="flex flex-col items-center text-center p-8">
                        <div class="w-16 h-16 rounded-full bg-tertiary flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                            <span class="material-symbols-outlined text-primary text-3xl" data-icon="add">{{ $user->canCreateResume() ? 'add' : 'lock' }}</span>
                        </div>
                        <p class="font-headline text-xl text-primary mb-2">{{ $user->canCreateResume() ? __('messages.dashboard.start_new_manuscript') : __('messages.dashboard.resume_limit_reached') }}</p>
                        <p id="manuscripts-create-limit" class="text-sm text-primary/60 font-label max-w-[220px]">{{ $user->canCreateResume() ? __('messages.dashboard.start_new_manuscript_desc') : __('messages.dashboard.resume_limit_reached_desc') }}</p>
                    </div>
                </button>
            </div>
        </section>

    </main>

    <x-user.create-resume-modal :templates="$templates" />

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 bg-surface/80 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4"
         role="dialog"
         aria-modal="true"
         aria-labelledby="delete-modal-title"
         aria-describedby="delete-modal-description">
        <div id="delete-modal-content" class="bg-tertiary w-full max-w-md rounded-2xl shadow-2xl border border-primary/10 flex flex-col overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="p-6 border-b border-primary/10 flex justify-between items-center bg-surface-container-low">
                <h3 id="delete-modal-title" class="font-headline text-xl font-bold text-red-600 flex items-center gap-2">
                    <span class="material-symbols-outlined">warning</span> {{ __('messages.dashboard.delete_modal.title') }}
                </h3>
                <button type="button" onclick="closeDeleteModal()" aria-label="{{ __('messages.dashboard.delete_modal.close_aria') }}" class="text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full p-2 hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">close</button>
            </div>
            <div class="p-6 bg-surface">
                <p id="delete-modal-description" class="text-primary/80 mb-6">{{ __('messages.dashboard.delete_modal.body') }}</p>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 rounded-lg font-bold text-primary/70 hover:bg-primary/5 transition-colors">{{ __('messages.dashboard.delete_modal.cancel') }}</button>
                    <form id="delete-form" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 rounded-lg font-bold bg-red-600 text-white hover:bg-red-700 transition-colors shadow-md">{{ __('messages.dashboard.delete_modal.confirm') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Rename Modal -->
    <div id="rename-modal" class="fixed inset-0 bg-surface/80 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4"
         role="dialog"
         aria-modal="true"
         aria-labelledby="rename-modal-title">
        <div id="rename-modal-content" class="bg-tertiary w-full max-w-md rounded-2xl shadow-2xl border border-primary/10 flex flex-col overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="p-6 border-b border-primary/10 flex justify-between items-center bg-surface-container-low">
                <h3 id="rename-modal-title" class="font-headline text-xl font-bold text-primary">
                    {{ __('messages.dashboard.rename_modal.title') }}
                </h3>
                <button type="button" onclick="closeRenameModal()" aria-label="{{ __('messages.dashboard.rename_modal.close_aria') }}" class="text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full p-2 hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">close</button>
            </div>
            <div class="p-6 bg-surface">
                <form id="rename-form" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <label for="rename-title-input" class="block text-sm font-label font-semibold text-primary/70 mb-2">{{ __('messages.dashboard.rename_modal.label') }}</label>
                    <input
                        id="rename-title-input"
                        type="text"
                        name="title"
                        maxlength="100"
                        autocomplete="off"
                        placeholder="{{ __('messages.dashboard.rename_modal.placeholder') }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-primary/20 bg-tertiary text-primary font-body text-sm focus:outline-none focus:ring-2 focus:ring-secondary/50 focus:border-secondary transition-colors"
                    >
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeRenameModal()" class="px-4 py-2 rounded-lg font-bold text-primary/70 hover:bg-primary/5 transition-colors">{{ __('messages.dashboard.rename_modal.cancel') }}</button>
                        <button type="submit" class="px-5 py-2 rounded-lg font-bold bg-secondary text-white hover:bg-secondary/90 transition-colors shadow-md">{{ __('messages.dashboard.rename_modal.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let lastFocusedElement = null;

        function rememberFocus() {
            lastFocusedElement = document.activeElement;
        }

        function restoreFocus() {
            if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
                lastFocusedElement.focus();
            }
        }

        function openCreateModal() {
            rememberFocus();
            const modal = document.getElementById('create-modal');
            const modalContent = document.getElementById('create-modal-content');
            modal.classList.remove('hidden');
            void modal.offsetWidth; // Trigger reflow
            modal.style.opacity = '1';
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
            scaleThumbnails();
            setTimeout(() => document.getElementById('create-resume-title')?.focus(), 120);
        }

        function scaleThumbnails() {
            const iframes = document.querySelectorAll('.template-thumbnail-iframe, .cv-thumbnail-iframe');
            iframes.forEach(iframe => {
                const parent = iframe.parentElement;
                if (parent) {
                    const scale = parent.offsetWidth / 794;
                    iframe.style.transform = `scale(${scale})`;
                }
            });
        }
        
        // Ensure scale is maintained on window resize
        window.addEventListener('resize', scaleThumbnails);

        function closeCreateModal() {
            const modal = document.getElementById('create-modal');
            const modalContent = document.getElementById('create-modal-content');
            modal.style.opacity = '0';
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                restoreFocus();
            }, 300);
        }

        document.addEventListener('DOMContentLoaded', () => {
            scaleThumbnails();
            @if(request('create') === 'true')
                setTimeout(openCreateModal, 100);
            @endif
        });

        // Delete Modal Logic
        function openDeleteModal(cvId) {
            rememberFocus();
            const modal = document.getElementById('delete-modal');
            const modalContent = document.getElementById('delete-modal-content');
            const deleteForm = document.getElementById('delete-form');
            
            // Set form action dynamically
            deleteForm.action = `/resumes/${cvId}`;
            
            modal.classList.remove('hidden');
            void modal.offsetWidth; // Trigger reflow
            modal.style.opacity = '1';
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
            setTimeout(() => modal.querySelector('button')?.focus(), 120);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('delete-modal');
            const modalContent = document.getElementById('delete-modal-content');
            modal.style.opacity = '0';
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                restoreFocus();
            }, 300);
        }

        // Rename Modal Logic
        function openRenameModal(cvId, currentTitle) {
            rememberFocus();
            const modal = document.getElementById('rename-modal');
            const modalContent = document.getElementById('rename-modal-content');
            const renameForm = document.getElementById('rename-form');
            const titleInput = document.getElementById('rename-title-input');

            renameForm.action = `/resumes/${cvId}`;
            titleInput.value = currentTitle;

            modal.classList.remove('hidden');
            void modal.offsetWidth; // Trigger reflow
            modal.style.opacity = '1';
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');

            // Focus & select all text for quick editing
            setTimeout(() => {
                titleInput.focus();
                titleInput.select();
            }, 150);
        }

        function closeRenameModal() {
            const modal = document.getElementById('rename-modal');
            const modalContent = document.getElementById('rename-modal-content');
            modal.style.opacity = '0';
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                restoreFocus();
            }, 300);
        }

        // Close modals on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeRenameModal();
                closeDeleteModal();
                closeCreateModal();
            }
        });
    </script>
@endsection

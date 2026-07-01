@extends('layouts.user.app')

@section('title', 'Resumify - Dashboard')

@section('content')
    @php
        $user = auth()->user();
        $cvs = $user->cvs()->latest('updated_at')->get();
    @endphp

    <main class="flex-1 p-4 sm:p-6 md:p-12 max-w-7xl mx-auto w-full pb-24 md:pb-12">
        <header class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10 md:mb-16">
            <div>
                <h1 class="text-3xl sm:text-4xl md:text-6xl font-headline text-primary tracking-tight leading-tight mb-4">
                    Welcome, <br />{{ $user->name }}</h1>
                <div class="flex flex-wrap items-center gap-3">
                    <x-user.plan-badge :user="$user" />
                    <span class="inline-flex items-center gap-2 rounded-full border border-primary/10 bg-tertiary px-3 py-1 text-sm font-label text-primary/70">
                        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">visibility</span>
                        {{ $user->getResumeQuotaUsed() }}/{{ $user->getResumeLimit() ?? 'Unlimited' }} Resumes Created
                    </span>
                </div>
            </div>

            <x-user.btn-create />
        </header>

        <x-user.quota-status :user="$user" class="mb-10 md:mb-12" />

        <section>
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl font-body font-medium text-primary tracking-wide">Your Resumes</h2>
                <div class="h-px flex-1 mx-6 bg-primary/10 hidden md:block"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($cvs as $cv)
                    <x-user.resume-card title="{{ $cv->title ?: 'Untitled Resume' }}"
                        date="{{ $cv->updated_at->diffForHumans() }}"
                        url="{{ route('user.manuscript', ['cv_id' => $cv->id]) }}" cvId="{{ $cv->id }}" />
                @endforeach

                <button type="button" onclick="{{ $user->canCreateResume() ? 'openCreateModal()' : '' }}"
                    @unless($user->canCreateResume()) disabled aria-describedby="dashboard-create-limit" @endunless
                    class="w-full h-full group relative bg-surface-container-low/50 rounded-lg border-2 border-dashed {{ $user->canCreateResume() ? 'border-primary/20 hover:border-primary/50 hover:bg-surface-container-low cursor-pointer' : 'border-[#A16207]/30 cursor-not-allowed' }} transition-all duration-300 overflow-hidden flex flex-col items-center justify-center min-h-[200px] sm:min-h-[400px]">
                    <div class="flex flex-col items-center text-center p-8">
                        <div
                            class="w-16 h-16 rounded-full bg-tertiary flex items-center justify-center mb-4 group-hover:scale-105 transition-transform duration-200 shadow-sm">
                            <span class="material-symbols-outlined text-primary text-3xl" data-icon="add">{{ $user->canCreateResume() ? 'add' : 'lock' }}</span>
                        </div>
                        <p class="font-headline text-xl text-primary mb-2">{{ $user->canCreateResume() ? 'Start New Manuscript' : 'Resume Limit Reached' }}</p>
                        <p id="dashboard-create-limit" class="text-sm text-primary/60 font-label max-w-[220px]">
                            {{ $user->canCreateResume() ? 'Create your professional career narrative in minutes.' : 'Basic includes 1 resume. Upgrade from the plan card above for unlimited resumes.' }}
                        </p>
                    </div>
                </button>
            </div>
        </section>

        <section class="mt-24 grid grid-cols-1 md:grid-cols-2 gap-12 border-t border-primary/10 pt-12">

            <x-user.insight-block number="01" label="DAILY TIP">
                "Use strong action verbs to give weight to your professional narrative."
            </x-user.insight-block>

            <x-user.insight-block number="02" label="ATS Analyzer">
                Check how well your resume matches a job description with our <a href="{{ route('user.ai-assistant') }}"
                    class="text-secondary font-bold hover:underline">ATS Analyzer</a> — get a keyword score and actionable
                suggestions in seconds.
            </x-user.insight-block>

        </section>
    </main>

    <x-user.create-resume-modal :templates="$templates" />

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal"
        class="fixed inset-0 bg-surface/80 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="delete-modal-title"
        aria-describedby="delete-modal-description">
        <div id="delete-modal-content"
            class="bg-tertiary w-full max-w-md rounded-2xl shadow-2xl border border-primary/10 flex flex-col overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="p-6 border-b border-primary/10 flex justify-between items-center bg-surface-container-low">
                <h3 id="delete-modal-title" class="font-headline text-xl font-bold text-red-600 flex items-center gap-2">
                    <span class="material-symbols-outlined">warning</span> Delete Resume
                </h3>
                <button type="button" onclick="closeDeleteModal()"
                    aria-label="Close delete confirmation"
                    class="text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full p-2 hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">close</button>
            </div>
            <div class="p-6 bg-surface">
                <p id="delete-modal-description" class="text-primary/80 mb-6">Are you sure you want to delete this resume? This action cannot be undone.
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 rounded-lg font-bold text-primary/70 hover:bg-primary/5 transition-colors">Cancel</button>
                    <form id="delete-form" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 rounded-lg font-bold bg-red-600 text-white hover:bg-red-700 transition-colors shadow-md">Yes,
                            Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Rename Modal -->
    <div id="rename-modal"
        class="fixed inset-0 bg-surface/80 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="rename-modal-title">
        <div id="rename-modal-content"
            class="bg-tertiary w-full max-w-md rounded-2xl shadow-2xl border border-primary/10 flex flex-col overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="p-6 border-b border-primary/10 flex justify-between items-center bg-surface-container-low">
                <h3 id="rename-modal-title" class="font-headline text-xl font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">drive_file_rename_outline</span> Rename Resume
                </h3>
                <button type="button" onclick="closeRenameModal()"
                    aria-label="Close rename dialog"
                    class="text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full p-2 hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">close</button>
            </div>
            <div class="p-6 bg-surface">
                <form id="rename-form" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <label for="rename-title-input"
                        class="block text-sm font-label font-semibold text-primary/70 mb-2">Resume Title</label>
                    <input id="rename-title-input" type="text" name="title" maxlength="100" autocomplete="off"
                        placeholder="Enter resume title..."
                        class="w-full px-4 py-2.5 rounded-lg border border-primary/20 bg-tertiary text-primary font-body text-sm focus:outline-none focus:ring-2 focus:ring-secondary/50 focus:border-secondary transition-colors">
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeRenameModal()"
                            class="px-4 py-2 rounded-lg font-bold text-primary/70 hover:bg-primary/5 transition-colors">Cancel</button>
                        <button type="submit"
                            class="px-5 py-2 rounded-lg font-bold bg-secondary text-white hover:bg-secondary/90 transition-colors shadow-md">Save</button>
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
            @if (request('create') === 'true')
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

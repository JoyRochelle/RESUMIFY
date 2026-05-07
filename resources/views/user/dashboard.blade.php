@extends('layouts.user.app')

@section('title', 'Resumify - Dashboard')

@section('content')
    <main class="flex-1 p-8 md:p-12 max-w-7xl mx-auto w-full">
        <header class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
            <div>
                <h1 class="text-5xl md:text-6xl font-headline text-primary tracking-tight leading-tight mb-4">Welcome, <br/>{{ auth()->user()->name }}</h1>
                <div class="flex flex-wrap items-center gap-4">
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-secondary text-tertiary text-sm font-label font-semibold">
                        {{ auth()->user()->isPremium() ? 'Premium Member' : 'Basic Member' }}
                    </span>
                    <span class="text-primary/60 font-label text-sm">AI Quota Remaining: <span class="serif-number font-bold text-primary">{{ auth()->user()->getQuotaRemaining() }}</span>/{{ auth()->user()->getQuotaLimit() }}</span>
                </div>
            </div>
            
            <x-user.btn-create />
        </header>

        <section>
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl font-body font-medium text-primary tracking-wide">Your Resumes</h2>
                <div class="h-px flex-1 mx-6 bg-primary/10 hidden md:block"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <x-user.resume-card title="Senior Product Designer" date="2 days ago" url="{{ route('user.manuscript') }}" />
                <x-user.resume-card title="UX Research Lead" date="1 week ago" url="{{ route('user.manuscript') }}" />

                <button type="button" onclick="openCreateModal()" class="w-full h-full group relative bg-surface-container-low/50 rounded-lg border-2 border-dashed border-primary/20 hover:border-primary/50 hover:bg-surface-container-low transition-all duration-500 overflow-hidden flex flex-col items-center justify-center min-h-[400px] cursor-pointer">
                    <div class="flex flex-col items-center text-center p-8">
                        <div class="w-16 h-16 rounded-full bg-tertiary flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                            <span class="material-symbols-outlined text-primary text-3xl" data-icon="add">add</span>
                        </div>
                        <p class="font-headline text-xl text-primary mb-2">Start New Manuscript</p>
                        <p class="text-sm text-primary/60 font-label max-w-[200px]">Create your professional career narrative in minutes.</p>
                    </div>
                </button>
            </div>
        </section>

        <section class="mt-24 grid grid-cols-1 md:grid-cols-2 gap-12 border-t border-primary/10 pt-12">
            
            <x-user.insight-block number="01" label="DAILY TIP">
                "Use strong action verbs to give weight to your professional narrative."
            </x-user.insight-block>

            <x-user.insight-block number="02" label="AI Insight">
                Your 'Senior Product Designer' resume has a <span class="text-secondary font-bold">92%</span> match for 2024 tech industry standards.
            </x-user.insight-block>

        </section>
    </main>

    <!-- Template Selection Modal for New Resumes -->
    <div id="create-modal" class="fixed inset-0 bg-surface/80 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4">
        <div id="create-modal-content" class="bg-tertiary w-full max-w-5xl h-[85vh] rounded-2xl shadow-2xl border border-primary/10 flex flex-col overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="p-6 border-b border-primary/10 flex justify-between items-center bg-surface-container-low">
                <h3 class="font-headline text-2xl font-bold text-primary flex items-center gap-3">
                    <span class="material-symbols-outlined text-secondary">layers</span> Choose Your Starting Point
                </h3>
                <button onclick="closeCreateModal()" class="text-primary/60 hover:text-primary transition-colors material-symbols-outlined rounded-full p-1 hover:bg-primary/5">close</button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar bg-surface flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($templates as $template)
                    <form action="{{ route('resumes.store') }}" method="POST" class="cursor-pointer group relative border border-primary/10 rounded-xl overflow-hidden hover:border-secondary transition-all hover:shadow-lg hover:-translate-y-1 bg-tertiary">
                        @csrf
                        <input type="hidden" name="title" value="My Professional Resume">
                        <input type="hidden" name="template_id" value="{{ $template->id }}">
                        
                        <div class="relative w-full aspect-[210/297] bg-surface-container-low overflow-hidden border-b border-primary/5">
                            <iframe src="{{ route('templates.preview', $template) }}" 
                                    style="width: 794px; height: 1123px; transform-origin: top left; border: none; position: absolute; top: 0; left: 0;"
                                    class="template-thumbnail-iframe pointer-events-none transition-transform duration-500 origin-top-left"
                                    loading="lazy" tabindex="-1">
                            </iframe>
                            <div class="absolute inset-0 bg-transparent z-10"></div>
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4 z-20">
                                <span class="bg-secondary text-white text-xs px-3 py-1.5 rounded-full font-bold shadow-sm">Use Template</span>
                            </div>
                        </div>
                        <div class="p-4 flex justify-between items-center">
                            <div>
                                <h4 class="font-bold text-sm text-primary group-hover:text-secondary transition-colors">{{ $template->name }}</h4>
                                <p class="text-[11px] text-primary/60 mt-1 line-clamp-1">{{ $template->is_premium ? 'Premium' : 'Free' }}</p>
                            </div>
                        </div>
                        
                        <button type="submit" class="absolute inset-0 w-full h-full opacity-0 z-30 cursor-pointer"></button>
                    </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        function openCreateModal() {
            const modal = document.getElementById('create-modal');
            const modalContent = document.getElementById('create-modal-content');
            modal.classList.remove('hidden');
            void modal.offsetWidth; // Trigger reflow
            modal.style.opacity = '1';
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
            scaleThumbnails();
        }

        function scaleThumbnails() {
            const iframes = document.querySelectorAll('.template-thumbnail-iframe');
            iframes.forEach(iframe => {
                const parent = iframe.parentElement;
                if (parent) {
                    const scale = parent.offsetWidth / 794;
                    iframe.style.transform = `scale(${scale})`;
                }
            });
        }
        
        // Ensure scale is maintained on window resize
        window.addEventListener('resize', () => {
            if (!document.getElementById('create-modal').classList.contains('hidden')) {
                scaleThumbnails();
            }
        });

        function closeCreateModal() {
            const modal = document.getElementById('create-modal');
            const modalContent = document.getElementById('create-modal-content');
            modal.style.opacity = '0';
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        document.addEventListener('DOMContentLoaded', () => {
            @if(request('create') === 'true')
                setTimeout(openCreateModal, 100);
            @endif
        });
    </script>
@endsection

@extends('layouts.admin.app')

@section('title', 'Template Catalog - Admin Dashboard')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-10" x-data="templateCatalog()">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-headline font-bold text-primary mb-2">Template Catalog</h1>
            <p class="text-sm font-label text-primary/60">Manage resume templates available to users.</p>
        </div>
        <a href="{{ route('admin.templates.create') }}"
           class="flex items-center space-x-2 bg-primary text-white px-5 py-2.5 rounded-xl text-sm font-label hover:bg-primary/90 transition shadow-sm">
            <span class="material-symbols-outlined text-[18px]">add</span>
            <span>New Template</span>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-secondary/10 border border-secondary/20 text-secondary text-sm font-label px-5 py-3 rounded-xl flex items-center space-x-2">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-3">Total</h3>
            <p class="text-3xl font-headline text-primary">{{ number_format($templates->total()) }}</p>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-3">Active</h3>
            <p class="text-3xl font-headline text-secondary">{{ number_format($activeCount) }}</p>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-3">Premium</h3>
            <p class="text-3xl font-headline text-amber-500">{{ number_format($premiumCount) }}</p>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-3">Inactive</h3>
            <p class="text-3xl font-headline text-primary/40">{{ number_format($inactiveCount) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.templates.index') }}" class="flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[200px] bg-white rounded-xl border border-primary/10 flex items-center px-4 h-11 shadow-sm">
            <span class="material-symbols-outlined text-primary/40 mr-3 text-[18px]">search</span>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="Search by name..."
                   class="bg-transparent border-none focus:outline-none text-sm font-label w-full text-primary placeholder:text-primary/40">
        </div>

        <select name="category"
                class="bg-white rounded-xl border border-primary/10 px-4 h-11 text-sm font-label text-primary shadow-sm focus:outline-none cursor-pointer">
            <option value="">All Categories</option>
            <option value="professional" {{ ($category ?? '') === 'professional' ? 'selected' : '' }}>Professional</option>
            <option value="creative"     {{ ($category ?? '') === 'creative'     ? 'selected' : '' }}>Creative</option>
            <option value="technology"   {{ ($category ?? '') === 'technology'   ? 'selected' : '' }}>Technology</option>
            <option value="managerial"   {{ ($category ?? '') === 'managerial'   ? 'selected' : '' }}>Managerial</option>
        </select>

        <select name="status"
                class="bg-white rounded-xl border border-primary/10 px-4 h-11 text-sm font-label text-primary shadow-sm focus:outline-none cursor-pointer">
            <option value="">All Status</option>
            <option value="active"   {{ ($status ?? '') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ ($status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        <button type="submit"
                class="bg-primary text-white px-5 h-11 rounded-xl text-sm font-label hover:bg-primary/90 transition shadow-sm">
            Filter
        </button>

        @if(($search ?? '') || ($category ?? '') || ($status ?? ''))
            <a href="{{ route('admin.templates.index') }}"
               class="text-sm font-label text-primary/50 hover:text-primary transition">Clear</a>
        @endif
    </form>

    <!-- Template Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
        <div class="bg-white rounded-3xl shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 overflow-hidden flex flex-col">

            <!-- Thumbnail -->
            <div class="relative h-44 bg-surface overflow-hidden">
                <img src="{{ $template->thumbnail ?? asset('images/template-placeholder.png') }}"
                     alt="{{ $template->name }}"
                     class="w-full h-full object-cover object-top">
                <div class="absolute top-3 left-3 flex items-center gap-2">
                    @if($template->is_premium)
                        <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-1 rounded-full bg-amber-100 text-amber-700">Premium</span>
                    @endif
                    @if($template->badge)
                        @php
                            $badgeColors = ['blue' => 'bg-blue-100 text-blue-700', 'secondary' => 'bg-secondary/20 text-secondary', 'purple' => 'bg-purple-100 text-purple-700', 'green' => 'bg-green-100 text-green-700'];
                        @endphp
                        <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-1 rounded-full {{ $badgeColors[$template->badge_color] ?? 'bg-primary/10 text-primary/60' }}">{{ $template->badge }}</span>
                    @endif
                </div>
                <div class="absolute top-3 right-3">
                    <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-1 rounded-full {{ $template->is_active ? 'bg-secondary/20 text-secondary' : 'bg-primary/10 text-primary/40' }}">
                        {{ $template->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <!-- Info -->
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

                <div class="flex items-center justify-between mt-3 pt-3 border-t border-primary/5">
                    <span class="text-[10px] font-label text-primary/40">Sort: {{ $template->sort_order }}</span>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('admin.templates.preview', $template) }}" target="_blank"
                           class="p-1.5 rounded-lg text-primary/40 hover:text-primary hover:bg-primary/5 transition"
                           title="Preview">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </a>
                        <a href="{{ route('admin.templates.edit', $template) }}"
                           class="p-1.5 rounded-lg text-primary/40 hover:text-primary hover:bg-primary/5 transition"
                           title="Edit">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </a>
                        <form action="{{ route('admin.templates.toggle', $template) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="p-1.5 rounded-lg transition {{ $template->is_active ? 'text-secondary/60 hover:text-secondary hover:bg-secondary/5' : 'text-primary/40 hover:text-primary hover:bg-primary/5' }}"
                                    title="{{ $template->is_active ? 'Deactivate' : 'Activate' }}">
                                <span class="material-symbols-outlined text-[18px]">{{ $template->is_active ? 'toggle_on' : 'toggle_off' }}</span>
                            </button>
                        </form>
                        <button @click="confirmDelete('{{ route('admin.templates.destroy', $template) }}', '{{ addslashes($template->name) }}')"
                                class="p-1.5 rounded-lg text-red-400/60 hover:text-red-500 hover:bg-red-50 transition"
                                title="Delete">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="lg:col-span-3 bg-white rounded-3xl p-16 text-center border border-primary/5">
            <span class="material-symbols-outlined text-primary/20 text-[48px] block mb-2">style</span>
            <p class="text-sm font-label text-primary/40 mb-4">No templates found</p>
            <a href="{{ route('admin.templates.create') }}"
               class="text-sm font-label text-primary/60 hover:text-primary underline underline-offset-2">
                Create your first template
            </a>
        </div>
        @endforelse
    </div>

    @if($templates->hasPages())
        <div class="flex justify-center">{{ $templates->links() }}</div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak
         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
         @keydown.escape.window="showDeleteModal = false">
        <div class="bg-white rounded-3xl shadow-xl p-8 max-w-sm w-full" @click.outside="showDeleteModal = false">
            <div class="text-center mb-6">
                <span class="material-symbols-outlined text-red-400 text-[48px] block mb-3">delete_forever</span>
                <h3 class="text-lg font-headline font-bold text-primary mb-2">Delete Template?</h3>
                <p class="text-sm font-label text-primary/60">
                    You are about to permanently delete <span class="font-semibold text-primary" x-text="deleteTemplateName"></span>.
                    This cannot be undone.
                </p>
            </div>
            <form :action="deleteUrl" method="POST" class="flex gap-3">
                @csrf @method('DELETE')
                <button type="button" @click="showDeleteModal = false"
                        class="flex-1 py-2.5 rounded-xl border border-primary/10 text-sm font-label text-primary/60 hover:text-primary transition">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-red-500 text-white text-sm font-label hover:bg-red-600 transition">
                    Delete
                </button>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function templateCatalog() {
    return {
        showDeleteModal: false,
        deleteUrl: '',
        deleteTemplateName: '',
        confirmDelete(url, name) {
            this.deleteUrl = url;
            this.deleteTemplateName = name;
            this.showDeleteModal = true;
        },
    };
}
</script>
@endpush
@endsection

@extends('layouts.admin.app')

@section('title', 'Template Catalog - Admin Dashboard')

@section('content')
<div class="admin-shell"
     x-data="{ deletedCount: 0 }"
     @template-deleted.window="deletedCount++">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-headline font-bold text-primary mb-2">Template Catalog</h1>
            <p class="text-sm font-label text-primary/60">Manage resume templates available to users.</p>
        </div>
        <a href="{{ route('admin.templates.create') }}"
           class="admin-btn-primary">
            <span class="material-symbols-outlined text-[18px]">add</span>
            <span>New Template</span>
        </a>
    </div>

    <!-- Stats (live via Livewire — updates on toggle/delete) -->
    <livewire:admin.template-stats />

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.templates.index') }}" class="flex flex-wrap items-center gap-4">
        <div class="flex h-11 min-w-[200px] flex-1 items-center rounded-lg border border-primary/10 bg-tertiary px-4 shadow-sm transition focus-within:ring-2 focus-within:ring-secondary/30">
            <span class="material-symbols-outlined text-primary/40 mr-3 text-[18px]">search</span>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="Search by name..."
                   class="bg-transparent border-none focus:outline-none text-sm font-label w-full text-primary placeholder:text-primary/40">
        </div>

        <select name="category"
                class="admin-filter-field h-11 cursor-pointer">
            <option value="">All Categories</option>
            <option value="professional" {{ ($category ?? '') === 'professional' ? 'selected' : '' }}>Professional</option>
            <option value="creative"     {{ ($category ?? '') === 'creative'     ? 'selected' : '' }}>Creative</option>
            <option value="technology"   {{ ($category ?? '') === 'technology'   ? 'selected' : '' }}>Technology</option>
            <option value="managerial"   {{ ($category ?? '') === 'managerial'   ? 'selected' : '' }}>Managerial</option>
        </select>

        <select name="status"
                class="admin-filter-field h-11 cursor-pointer">
            <option value="">All Status</option>
            <option value="active"   {{ ($status ?? '') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ ($status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        <x-ui.loading-button loading-text="Filtering..." icon="filter_list">Filter</x-ui.loading-button>

        @if(($search ?? '') || ($category ?? '') || ($status ?? ''))
            <a href="{{ route('admin.templates.index') }}"
               class="text-sm font-label text-primary/50 hover:text-primary transition">Clear</a>
        @endif
    </form>

    <!-- Template Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
            <livewire:admin.template-card :template="$template" :key="'card-' . $template->id" />
        @empty
            <x-ui.empty-state
                title="No templates found"
                description="Try clearing filters or create the first resume template."
                icon="style"
                action-label="Create Template"
                :action-url="route('admin.templates.create')"
                class="lg:col-span-3" />
        @endforelse
    </div>

    @if($templates->hasPages())
        <div class="flex justify-center">{{ $templates->links() }}</div>
    @endif

</div>
@endsection

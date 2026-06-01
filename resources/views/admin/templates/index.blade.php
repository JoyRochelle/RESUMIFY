@extends('layouts.admin.app')

@section('title', 'Template Catalog - Admin Dashboard')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-10"
     x-data="{ deletedCount: 0 }"
     @template-deleted.window="deletedCount++">

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

    <!-- Stats (live via Livewire — updates on toggle/delete) -->
    <livewire:admin.template-stats />

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
            <livewire:admin.template-card :template="$template" :key="'card-' . $template->id" />
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

</div>
@endsection

@extends('layouts.admin.app')

@section('title', 'Edit Template - Admin Dashboard')

@section('content')
<div class="max-w-3xl mx-auto space-y-8 pb-10">

    <!-- Breadcrumb -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-sm font-label text-primary/50 mb-2">
                <a href="{{ route('admin.templates.index') }}" class="hover:text-primary transition-colors">Template Catalog</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary">Edit</span>
            </div>
            <h1 class="text-2xl font-headline font-bold text-primary">{{ $template->name }}</h1>
        </div>
        <a href="{{ route('admin.templates.preview', $template) }}" target="_blank"
           class="flex items-center space-x-2 text-sm font-label text-primary/60 hover:text-primary bg-white border border-primary/10 px-4 py-2 rounded-xl shadow-sm transition-colors">
            <span class="material-symbols-outlined text-[18px]">visibility</span>
            <span>Preview</span>
        </a>
    </div>

    @if(session('success'))
        <x-ui.alert variant="success">{{ session('success') }}</x-ui.alert>
    @endif

    <x-ui.error-summary />

    <form action="{{ route('admin.templates.update', $template) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Info -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 space-y-5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest">Basic Info</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="template-name" class="block text-[11px] font-label text-primary/50 uppercase tracking-widest mb-1.5">Name <span class="text-red-400" aria-hidden="true">*</span></label>
                    <input id="template-name" type="text" name="name" value="{{ old('name', $template->name) }}" required
                           autocomplete="off"
                           aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                           aria-describedby="{{ $errors->has('name') ? 'template-name-error' : '' }}"
                           class="w-full bg-surface border border-primary/10 rounded-xl px-4 py-2.5 text-sm font-label text-primary placeholder:text-primary/40 focus:outline-none focus:border-primary/30">
                    @error('name')<p id="template-name-error" class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="template-blade-path" class="block text-[11px] font-label text-primary/50 uppercase tracking-widest mb-1.5">Blade Path <span class="text-red-400" aria-hidden="true">*</span></label>
                    <input id="template-blade-path" type="text" name="blade_path" value="{{ old('blade_path', $template->blade_path) }}" required
                           autocomplete="off"
                           aria-invalid="{{ $errors->has('blade_path') ? 'true' : 'false' }}"
                           aria-describedby="{{ $errors->has('blade_path') ? 'template-blade-path-error' : '' }}"
                           class="w-full bg-surface border border-primary/10 rounded-xl px-4 py-2.5 text-sm font-label text-primary placeholder:text-primary/40 focus:outline-none focus:border-primary/30">
                    @error('blade_path')<p id="template-blade-path-error" class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="template-category" class="block text-[11px] font-label text-primary/50 uppercase tracking-widest mb-1.5">Category <span class="text-red-400" aria-hidden="true">*</span></label>
                    <select id="template-category" name="category" required
                            aria-invalid="{{ $errors->has('category') ? 'true' : 'false' }}"
                            aria-describedby="{{ $errors->has('category') ? 'template-category-error' : '' }}"
                            class="w-full bg-surface border border-primary/10 rounded-xl px-4 py-2.5 text-sm font-label text-primary focus:outline-none focus:border-primary/30">
                        <option value="professional" {{ old('category', $template->category) === 'professional' ? 'selected' : '' }}>Professional</option>
                        <option value="creative"     {{ old('category', $template->category) === 'creative'     ? 'selected' : '' }}>Creative</option>
                        <option value="technology"   {{ old('category', $template->category) === 'technology'   ? 'selected' : '' }}>Technology</option>
                        <option value="managerial"   {{ old('category', $template->category) === 'managerial'   ? 'selected' : '' }}>Managerial</option>
                    </select>
                    @error('category')<p id="template-category-error" class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="template-sort-order" class="block text-[11px] font-label text-primary/50 uppercase tracking-widest mb-1.5">Sort Order</label>
                    <input id="template-sort-order" type="number" name="sort_order" value="{{ old('sort_order', $template->sort_order) }}" min="0"
                           aria-invalid="{{ $errors->has('sort_order') ? 'true' : 'false' }}"
                           aria-describedby="{{ $errors->has('sort_order') ? 'template-sort-order-error' : '' }}"
                           class="w-full bg-surface border border-primary/10 rounded-xl px-4 py-2.5 text-sm font-label text-primary focus:outline-none focus:border-primary/30">
                    @error('sort_order')<p id="template-sort-order-error" class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="template-description" class="block text-[11px] font-label text-primary/50 uppercase tracking-widest mb-1.5">Description</label>
                <textarea id="template-description" name="description" rows="3"
                          aria-invalid="{{ $errors->has('description') ? 'true' : 'false' }}"
                          aria-describedby="{{ $errors->has('description') ? 'template-description-error' : '' }}"
                          class="w-full bg-surface border border-primary/10 rounded-xl px-4 py-2.5 text-sm font-label text-primary placeholder:text-primary/40 focus:outline-none focus:border-primary/30 resize-none">{{ old('description', $template->description) }}</textarea>
                @error('description')<p id="template-description-error" class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- Badge & Flags -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 space-y-5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest">Badge & Flags</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="template-badge" class="block text-[11px] font-label text-primary/50 uppercase tracking-widest mb-1.5">Badge Label</label>
                    <input id="template-badge" type="text" name="badge" value="{{ old('badge', $template->badge) }}" maxlength="30"
                           aria-invalid="{{ $errors->has('badge') ? 'true' : 'false' }}"
                           aria-describedby="{{ $errors->has('badge') ? 'template-badge-error' : '' }}"
                           class="w-full bg-surface border border-primary/10 rounded-xl px-4 py-2.5 text-sm font-label text-primary placeholder:text-primary/40 focus:outline-none focus:border-primary/30">
                    @error('badge')<p id="template-badge-error" class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="template-badge-color" class="block text-[11px] font-label text-primary/50 uppercase tracking-widest mb-1.5">Badge Color</label>
                    <select id="template-badge-color" name="badge_color"
                            aria-invalid="{{ $errors->has('badge_color') ? 'true' : 'false' }}"
                            aria-describedby="{{ $errors->has('badge_color') ? 'template-badge-color-error' : '' }}"
                            class="w-full bg-surface border border-primary/10 rounded-xl px-4 py-2.5 text-sm font-label text-primary focus:outline-none focus:border-primary/30">
                        <option value="">None</option>
                        <option value="blue"      {{ old('badge_color', $template->badge_color) === 'blue'      ? 'selected' : '' }}>Blue</option>
                        <option value="secondary" {{ old('badge_color', $template->badge_color) === 'secondary' ? 'selected' : '' }}>Secondary</option>
                        <option value="purple"    {{ old('badge_color', $template->badge_color) === 'purple'    ? 'selected' : '' }}>Purple</option>
                        <option value="green"     {{ old('badge_color', $template->badge_color) === 'green'     ? 'selected' : '' }}>Green</option>
                    </select>
                    @error('badge_color')<p id="template-badge-color-error" class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center gap-8">
                <label for="template-is-premium" class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_premium" value="0">
                    <input id="template-is-premium" type="checkbox" name="is_premium" value="1" {{ old('is_premium', $template->is_premium) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-primary/20 text-primary focus:ring-primary/20">
                    <span class="text-sm font-label text-primary">Premium only</span>
                </label>
                <label for="template-is-active" class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input id="template-is-active" type="checkbox" name="is_active" value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-primary/20 text-primary focus:ring-primary/20">
                    <span class="text-sm font-label text-primary">Active (visible to users)</span>
                </label>
            </div>
        </div>

        <!-- Thumbnail -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 space-y-4">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest">Thumbnail</h3>
            <div x-data="{ preview: null }">
                @if($template->thumbnail_url)
                    <div class="mb-3">
                        <p class="text-[11px] font-label text-primary/40 mb-2 uppercase tracking-widest">Current</p>
                        <img src="{{ $template->thumbnail }}" alt="Current thumbnail"
                             class="h-32 w-auto rounded-xl object-cover border border-primary/10">
                    </div>
                @endif
                <label for="template-thumbnail" class="block text-[11px] font-label text-primary/50 uppercase tracking-widest mb-1.5">Replace Image</label>
                <input id="template-thumbnail" type="file" name="thumbnail" accept="image/jpg,image/jpeg,image/png,image/webp"
                       @change="preview = URL.createObjectURL($event.target.files[0])"
                       aria-describedby="template-thumbnail-hint"
                       class="w-full text-sm font-label text-primary/60 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-label file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                <p id="template-thumbnail-hint" class="text-[11px] font-label text-primary/40 mt-1">JPG, PNG, WebP — max 2 MB. Leave empty to keep current.</p>
                <div x-show="preview" class="mt-3">
                    <img :src="preview" alt="New preview" class="h-32 w-auto rounded-xl object-cover border border-primary/10">
                </div>
            </div>
        </div>

        <!-- Style Config -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 space-y-4">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest">Style Config (JSON)</h3>
            <label for="template-style-config" class="sr-only">Style Config JSON</label>
            <textarea id="template-style-config" name="style_config" rows="5"
                      aria-invalid="{{ $errors->has('style_config') ? 'true' : 'false' }}"
                      aria-describedby="{{ $errors->has('style_config') ? 'template-style-config-error' : '' }}"
                      class="w-full bg-surface border border-primary/10 rounded-xl px-4 py-2.5 text-sm font-mono text-primary placeholder:text-primary/40 focus:outline-none focus:border-primary/30 resize-none">{{ old('style_config', is_array($template->style_config) ? json_encode($template->style_config, JSON_PRETTY_PRINT) : $template->style_config) }}</textarea>
            @error('style_config')
                <p id="template-style-config-error" class="text-xs text-red-600 mt-1" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.templates.index') }}"
               class="text-sm font-label text-primary/50 hover:text-primary transition">← Cancel</a>
            <x-ui.loading-button loading-text="Saving...">Save Changes</x-ui.loading-button>
        </div>
    </form>

</div>
@endsection

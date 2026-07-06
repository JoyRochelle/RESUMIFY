@extends('layouts.admin.app')

@section('title', 'Edit Template - Admin Dashboard')

@section('content')
<div class="mx-auto w-full max-w-3xl space-y-8 pb-24 md:pb-12">

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
           class="admin-btn-secondary">
            <span class="material-symbols-outlined text-[18px]">visibility</span>
            <span>Preview</span>
        </a>
    </div>

    <x-ui.error-summary />

    <form action="{{ route('admin.templates.update', $template) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Info -->
        <div class="admin-card-pad space-y-5">
            <h3 class="admin-section-title">Basic Info</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-ui.form-input label="Name" name="name" :value="old('name', $template->name)" required id="template-name" autocomplete="off" />
                <x-ui.form-input label="Blade Path" name="blade_path" :value="old('blade_path', $template->blade_path)" required id="template-blade-path" autocomplete="off" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="template-category" class="block text-[11px] font-label text-primary/50 uppercase tracking-widest mb-1.5">Category <span class="text-red-400" aria-hidden="true">*</span></label>
                    <select id="template-category" name="category" required
                            aria-invalid="{{ $errors->has('category') ? 'true' : 'false' }}"
                            aria-describedby="{{ $errors->has('category') ? 'template-category-error' : '' }}"
                            class="w-full bg-surface border border-primary/10 rounded-lg px-4 py-2.5 text-sm font-label text-primary focus:outline-none focus:border-primary/30">
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
                           class="w-full bg-surface border border-primary/10 rounded-lg px-4 py-2.5 text-sm font-label text-primary focus:outline-none focus:border-primary/30">
                    @error('sort_order')<p id="template-sort-order-error" class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="template-description" class="block text-[11px] font-label text-primary/50 uppercase tracking-widest mb-1.5">Description</label>
                <textarea id="template-description" name="description" rows="3"
                          aria-invalid="{{ $errors->has('description') ? 'true' : 'false' }}"
                          aria-describedby="{{ $errors->has('description') ? 'template-description-error' : '' }}"
                          class="w-full bg-surface border border-primary/10 rounded-lg px-4 py-2.5 text-sm font-label text-primary placeholder:text-primary/40 focus:outline-none focus:border-primary/30 resize-none">{{ old('description', $template->description) }}</textarea>
                @error('description')<p id="template-description-error" class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- Badge & Flags -->
        <div class="admin-card-pad space-y-5">
            <h3 class="admin-section-title">Badge & Flags</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-ui.form-input label="Badge Label" name="badge" :value="old('badge', $template->badge ?? '')" maxlength="30" id="template-badge" />
                <div>
                    <label for="template-badge-color" class="block text-[11px] font-label text-primary/50 uppercase tracking-widest mb-1.5">Badge Color</label>
                    <select id="template-badge-color" name="badge_color"
                            aria-invalid="{{ $errors->has('badge_color') ? 'true' : 'false' }}"
                            aria-describedby="{{ $errors->has('badge_color') ? 'template-badge-color-error' : '' }}"
                            class="w-full bg-surface border border-primary/10 rounded-lg px-4 py-2.5 text-sm font-label text-primary focus:outline-none focus:border-primary/30">
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
        <div class="admin-card-pad space-y-4">
            <h3 class="admin-section-title">Thumbnail</h3>
            <div x-data="{ preview: null }">
                @if($template->thumbnail_url)
                    <div class="mb-3">
                        <p class="text-[11px] font-label text-primary/40 mb-2 uppercase tracking-widest">Current</p>
                        <img src="{{ $template->thumbnail }}" alt="Current thumbnail"
                             class="h-32 w-auto rounded-lg object-cover border border-primary/10">
                    </div>
                @endif
                <label for="template-thumbnail" class="block text-[11px] font-label text-primary/50 uppercase tracking-widest mb-1.5">Replace Image</label>
                <input id="template-thumbnail" type="file" name="thumbnail" accept="image/jpg,image/jpeg,image/png,image/webp"
                       @change="preview = URL.createObjectURL($event.target.files[0])"
                       aria-describedby="template-thumbnail-hint"
                       class="w-full text-sm font-label text-primary/60 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-label file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                <p id="template-thumbnail-hint" class="text-[11px] font-label text-primary/40 mt-1">JPG, PNG, WebP — max 2 MB. Leave empty to keep current.</p>
                <div x-show="preview" class="mt-3">
                    <img :src="preview" alt="New preview" class="h-32 w-auto rounded-lg object-cover border border-primary/10">
                </div>
            </div>
        </div>

        <!-- Style Config -->
        <div class="admin-card-pad space-y-4">
            <h3 class="admin-section-title">Style Config (JSON)</h3>
            <label for="template-style-config" class="sr-only">Style Config JSON</label>
            <textarea id="template-style-config" name="style_config" rows="5"
                      aria-invalid="{{ $errors->has('style_config') ? 'true' : 'false' }}"
                      aria-describedby="{{ $errors->has('style_config') ? 'template-style-config-error' : '' }}"
                      class="w-full bg-surface border border-primary/10 rounded-lg px-4 py-2.5 text-sm font-mono text-primary placeholder:text-primary/40 focus:outline-none focus:border-primary/30 resize-none">{{ old('style_config', is_array($template->style_config) ? json_encode($template->style_config, JSON_PRETTY_PRINT) : $template->style_config) }}</textarea>
            @error('style_config')
                <p id="template-style-config-error" class="text-xs text-red-600 mt-1" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.templates.index') }}" class="admin-btn-secondary">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_back</span>
                <span>Cancel</span>
            </a>
            <x-ui.loading-button loading-text="Saving..." icon="save">Save Changes</x-ui.loading-button>
        </div>
    </form>

</div>
@endsection

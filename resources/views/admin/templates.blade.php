@extends('layouts.admin.app')

@section('title', 'Template Catalog - Admin Dashboard')

@section('content')
<div class="admin-shell">
    <div>
        <h1 class="text-3xl font-headline font-bold text-primary mb-2">Template Catalog</h1>
        <p class="text-sm font-label text-primary/60">Manage the resume templates available to users.</p>
    </div>

    <x-ui.empty-state
        title="Template catalog moved"
        description="Use the unified template catalog to search, filter, preview, activate, and delete templates with the current admin design system."
        icon="style"
        action-label="Open Template Catalog"
        :action-url="route('admin.templates.index')" />
</div>
@endsection

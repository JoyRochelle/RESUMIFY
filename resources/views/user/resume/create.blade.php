@extends('layouts.user.app')

@section('title', 'Create Resume - Resumify')

@section('content')
    <main class="flex-1 p-4 sm:p-6 md:p-12 max-w-5xl mx-auto w-full pb-24 md:pb-12">
        <x-user.page-header title="Create Resume" :back-url="route('user.manuscript')" />

        <section class="mt-8 rounded-lg border border-primary/10 bg-tertiary p-6 shadow-sm">
            <x-ui.error-summary />

            <form action="{{ route('resumes.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="title" class="mb-2 block text-xs font-bold uppercase tracking-wider text-primary/60">
                        Resume Title <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <input type="text"
                           name="title"
                           id="title"
                           value="{{ old('title') }}"
                           placeholder="e.g. Senior Product Designer"
                           required
                           autocomplete="off"
                           aria-invalid="{{ $errors->has('title') ? 'true' : 'false' }}"
                           aria-describedby="{{ $errors->has('title') ? 'title-error' : 'title-hint' }}"
                           class="w-full rounded-lg border border-primary/20 bg-surface px-4 py-3 text-primary outline-none transition placeholder:text-primary/35 focus:border-secondary focus:ring-2 focus:ring-secondary/20">
                    @error('title')
                        <p id="title-error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @else
                        <p id="title-hint" class="mt-2 text-sm text-primary/50">Use a title that tells you which role this resume targets.</p>
                    @enderror
                </div>

                <div>
                    <label for="template_id" class="mb-2 block text-xs font-bold uppercase tracking-wider text-primary/60">
                        Choose Template <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <select name="template_id"
                            id="template_id"
                            required
                            aria-invalid="{{ $errors->has('template_id') ? 'true' : 'false' }}"
                            aria-describedby="{{ $errors->has('template_id') ? 'template-id-error' : 'template-id-hint' }}"
                            class="w-full rounded-lg border border-primary/20 bg-surface px-4 py-3 text-primary outline-none transition focus:border-secondary focus:ring-2 focus:ring-secondary/20">
                        <option value="">Select a template</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                {{ $template->name }}{{ $template->is_premium ? ' (Premium)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('template_id')
                        <p id="template-id-error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @else
                        <p id="template-id-hint" class="mt-2 text-sm text-primary/50">You can change the template later from the manuscript editor.</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-3 border-t border-primary/10 pt-6 sm:flex-row sm:justify-end">
                    <a href="{{ route('user.manuscript') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-primary/15 px-5 py-2.5 text-sm font-bold text-primary transition hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">Cancel</a>
                    <x-ui.loading-button loading-text="Creating..." icon="add">Create Resume</x-ui.loading-button>
                </div>
            </form>
        </section>
    </main>
@endsection

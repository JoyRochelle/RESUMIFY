@extends('layouts.user.app')

@section('title', __('messages.resume.edit.title') . ': ' . $cv->title . ' - Resumify')

@section('content')
    <main class="flex-1 p-4 sm:p-6 md:p-12 max-w-5xl mx-auto w-full pb-24 md:pb-12">
        <x-user.page-header title="{{ __('messages.resume.edit.title') }}" :back-url="route('user.manuscript', ['cv_id' => $cv->id])" />

        <section class="mt-8 space-y-6">
            @if(session('success'))
                <x-ui.alert variant="success">{{ session('success') }}</x-ui.alert>
            @endif
            <x-ui.error-summary />

            <article class="rounded-lg border border-primary/10 bg-tertiary p-6 shadow-sm">
                <h2 class="font-headline text-2xl font-bold text-primary">{{ __('messages.resume.edit.details_heading') }}</h2>
                <form action="{{ route('resumes.update', $cv) }}" method="POST" class="mt-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="title" class="mb-2 block text-xs font-bold uppercase tracking-wider text-primary/60">{{ __('messages.resume.edit.title_label') }}</label>
                        <input type="text"
                               name="title"
                               id="title"
                               value="{{ old('title', $cv->title) }}"
                               required
                               autocomplete="off"
                               aria-invalid="{{ $errors->has('title') ? 'true' : 'false' }}"
                               aria-describedby="{{ $errors->has('title') ? 'title-error' : null }}"
                               class="w-full rounded-lg border border-primary/20 bg-surface px-4 py-3 text-primary outline-none transition focus:border-secondary focus:ring-2 focus:ring-secondary/20">
                        @error('title')
                            <p id="title-error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-start gap-3 rounded-lg border border-primary/10 bg-surface p-4">
                        <input type="hidden" name="is_public" value="0">
                        <input type="checkbox" name="is_public" id="is_public" value="1" {{ old('is_public', $cv->is_public) ? 'checked' : '' }} class="mt-1 h-5 w-5 rounded border-primary/30 text-secondary focus:ring-secondary/30">
                        <label for="is_public" class="text-sm leading-6 text-primary">
                            <span class="block font-bold">{{ __('messages.resume.edit.make_public') }}</span>
                            <span class="text-primary/60">{{ __('messages.resume.edit.make_public_desc') }}</span>
                        </label>
                    </div>

                    <x-ui.loading-button loading-text="{{ __('messages.resume.edit.saving') }}" icon="save">{{ __('messages.resume.edit.save_changes') }}</x-ui.loading-button>
                </form>
            </article>

            <section class="space-y-4">
                <h2 class="font-headline text-2xl font-bold text-primary">{{ __('messages.resume.edit.sections_heading') }}</h2>
                @foreach($cv->sections as $section)
                    <article class="rounded-lg border border-primary/10 bg-tertiary p-6 shadow-sm">
                        <div class="mb-5 flex flex-wrap items-center gap-3">
                            <h3 class="font-headline text-xl font-bold text-primary">{{ $section->title }}</h3>
                            <span class="rounded-full bg-secondary/10 px-3 py-1 text-xs font-bold uppercase tracking-wide text-secondary">{{ str_replace('_', ' ', $section->type) }}</span>
                        </div>
                        <form action="{{ route('resumes.sections.update', [$cv, $section]) }}" method="POST" class="space-y-5">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="section-title-{{ $section->id }}" class="mb-2 block text-xs font-bold uppercase tracking-wider text-primary/60">{{ __('messages.resume.edit.section_title_label') }}</label>
                                <input type="text" name="title" id="section-title-{{ $section->id }}" value="{{ old('title', $section->title) }}" class="w-full rounded-lg border border-primary/20 bg-surface px-4 py-3 text-primary outline-none transition focus:border-secondary focus:ring-2 focus:ring-secondary/20">
                            </div>

                            <div>
                                <label for="section-content-{{ $section->id }}" class="mb-2 block text-xs font-bold uppercase tracking-wider text-primary/60">{{ __('messages.resume.edit.content_json_label') }}</label>
                                <textarea name="content" id="section-content-{{ $section->id }}" rows="5" placeholder='e.g. {"name": "John Doe", "email": "john@example.com"}' class="w-full rounded-lg border border-primary/20 bg-surface px-4 py-3 font-mono text-sm text-primary outline-none transition focus:border-secondary focus:ring-2 focus:ring-secondary/20">{{ old('content', $section->content ? json_encode($section->content, JSON_PRETTY_PRINT) : '') }}</textarea>
                            </div>

                            <x-ui.loading-button variant="outline" loading-text="{{ __('messages.resume.edit.updating') }}">{{ __('messages.resume.edit.update_section') }}</x-ui.loading-button>
                        </form>
                    </article>
                @endforeach
            </section>
        </section>
    </main>
@endsection

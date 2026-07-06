@extends('layouts.user.app')

@section('title', $cv->title . ' - Resumify')

@section('content')
    <main class="flex-1 p-4 sm:p-6 md:p-12 max-w-6xl mx-auto w-full pb-24 md:pb-12">
        <x-user.page-header :title="$cv->title" :back-url="route('user.manuscript')">
            <a href="{{ route('resumes.edit', $cv) }}" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-primary px-5 py-2.5 text-sm font-bold text-tertiary transition hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-secondary/40">Edit</a>
        </x-user.page-header>

        <section class="mt-8 space-y-6">
            <article class="rounded-lg border border-primary/10 bg-tertiary p-6 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h2 class="font-headline text-2xl font-bold text-primary">{{ $cv->title }}</h2>
                        <dl class="mt-3 grid gap-3 text-sm text-primary/70 sm:grid-cols-3">
                            <div>
                                <dt class="font-bold text-primary">Template</dt>
                                <dd>{{ $cv->template->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold text-primary">Visibility</dt>
                                <dd>{{ $cv->is_public ? 'Public' : 'Private' }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold text-primary">Updated</dt>
                                <dd>{{ $cv->updated_at->diffForHumans() }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <form action="{{ route('resumes.duplicate', $cv) }}" method="POST">
                            @csrf
                            <x-ui.loading-button variant="outline" loading-text="Duplicating...">Duplicate</x-ui.loading-button>
                        </form>
                        <form action="{{ route('resumes.destroy', $cv) }}" method="POST" x-data="{ confirming: false }">
                            @csrf
                            @method('DELETE')
                            <button type="button" x-show="!confirming" x-on:click="confirming = true" class="inline-flex min-h-11 items-center rounded-lg border border-red-200 px-4 py-2 text-sm font-bold text-red-600 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-300">Delete</button>
                            <div x-show="confirming" class="flex items-center gap-2" style="display:none">
                                <button type="button" x-on:click="confirming = false" class="inline-flex min-h-11 items-center rounded-lg px-3 py-2 text-sm font-bold text-primary/60 hover:bg-primary/5">Cancel</button>
                                <x-ui.loading-button variant="danger" loading-text="Deleting...">Confirm</x-ui.loading-button>
                            </div>
                        </form>
                    </div>
                </div>
            </article>

            @forelse($cv->sections as $section)
                <article class="rounded-lg border border-primary/10 bg-tertiary shadow-sm">
                    <header class="flex flex-wrap items-center gap-3 border-b border-primary/10 bg-surface-container-low px-6 py-4">
                        <h2 class="font-headline text-xl font-bold text-primary">{{ $section->title }}</h2>
                        <span class="rounded-full bg-secondary/10 px-3 py-1 text-xs font-bold uppercase tracking-wide text-secondary">{{ str_replace('_', ' ', $section->type) }}</span>
                    </header>
                    <div class="p-6">
                        @if($section->content)
                            <pre class="overflow-x-auto rounded-lg bg-surface p-4 text-sm leading-6 text-primary">{{ json_encode($section->content, JSON_PRETTY_PRINT) }}</pre>
                        @else
                            <p class="text-sm text-primary/60">No content yet.</p>
                        @endif
                    </div>
                </article>
            @empty
                <x-ui.empty-state
                    icon="subject"
                    title="No sections yet"
                    description="Open the editor to add the core sections for this resume."
                    :action-url="route('user.manuscript', ['cv_id' => $cv->id])"
                    action-label="Open Editor"
                />
            @endforelse
        </section>
    </main>
@endsection

@extends('layouts.user.app')

@section('title', __('messages.resume.index.title') . ' - Resumify')

@section('content')
    <main class="flex-1 p-4 sm:p-6 md:p-12 max-w-7xl mx-auto w-full pb-24 md:pb-12">
        <x-user.page-header title="{{ __('messages.resume.index.title') }}" :back-url="route('dashboard')">
            <a href="{{ route('resumes.create') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-bold text-tertiary transition hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-secondary/40">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">add</span>
                {{ __('messages.resume.index.new_resume') }}
            </a>
        </x-user.page-header>

        <section class="mt-8 space-y-6">
            @if(session('success'))
                <x-ui.alert variant="success">{{ session('success') }}</x-ui.alert>
            @endif

            @if($resumes->isEmpty())
                <x-ui.empty-state
                    icon="description"
                    title="{{ __('messages.resume.index.empty.title') }}"
                    description="{{ __('messages.resume.index.empty.description') }}"
                    :action-url="route('resumes.create')"
                    action-label="{{ __('messages.resume.index.empty.action') }}"
                />
            @else
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($resumes as $resume)
                        <article class="rounded-lg border border-primary/10 bg-tertiary p-5 shadow-sm transition hover:shadow-lg">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h2 class="font-headline text-xl font-bold text-primary">{{ $resume->title }}</h2>
                                    <p class="mt-1 text-sm text-primary/60">{{ __('messages.resume.index.updated') }} {{ $resume->updated_at->diffForHumans() }}</p>
                                </div>
                                <span class="rounded-full bg-secondary/10 px-3 py-1 text-xs font-bold text-secondary">
                                    {{ $resume->template->name ?? __('messages.resume.index.no_template') }}
                                </span>
                            </div>

                            <div class="mt-6 flex flex-wrap items-center gap-2 border-t border-primary/10 pt-4">
                                <a href="{{ route('resumes.show', $resume) }}" class="inline-flex min-h-11 items-center rounded-lg border border-primary/15 px-4 py-2 text-sm font-bold text-primary transition hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">{{ __('messages.resume.index.view') }}</a>
                                <a href="{{ route('resumes.edit', $resume) }}" class="inline-flex min-h-11 items-center rounded-lg bg-primary px-4 py-2 text-sm font-bold text-tertiary transition hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-secondary/40">{{ __('messages.resume.index.edit') }}</a>
                                <form action="{{ route('resumes.duplicate', $resume) }}" method="POST">
                                    @csrf
                                    <x-ui.loading-button variant="outline" loading-text="{{ __('messages.resume.index.duplicating') }}">{{ __('messages.resume.index.duplicate') }}</x-ui.loading-button>
                                </form>
                                <form action="{{ route('resumes.destroy', $resume) }}" method="POST" x-data="{ confirming: false }">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" x-show="!confirming" x-on:click="confirming = true" class="inline-flex min-h-11 items-center rounded-lg border border-red-200 px-4 py-2 text-sm font-bold text-red-600 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-300">{{ __('messages.resume.index.delete') }}</button>
                                    <div x-show="confirming" class="flex items-center gap-2" style="display:none">
                                        <button type="button" x-on:click="confirming = false" class="inline-flex min-h-11 items-center rounded-lg px-3 py-2 text-sm font-bold text-primary/60 hover:bg-primary/5">{{ __('messages.resume.index.cancel') }}</button>
                                        <x-ui.loading-button variant="danger" loading-text="{{ __('messages.resume.index.deleting') }}">{{ __('messages.resume.index.confirm') }}</x-ui.loading-button>
                                    </div>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </main>
@endsection

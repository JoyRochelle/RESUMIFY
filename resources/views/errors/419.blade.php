@extends(auth()->check() ? 'layouts.user.app' : 'layouts.landing_page.app')

@section('title', 'Page expired - Resumify')

@section('content')
    <main class="flex min-h-screen items-center justify-center px-4 py-16">
        <x-ui.empty-state
            icon="history"
            title="Page expired"
            description="This page has expired because you were away for a while. Please refresh and try again."
            :action-url="auth()->check() ? route('dashboard') : route('home')"
            :action-label="auth()->check() ? 'Back to Dashboard' : 'Back to Home'"
        />
    </main>
@endsection

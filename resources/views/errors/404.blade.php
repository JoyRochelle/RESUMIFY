@extends(auth()->check() ? 'layouts.user.app' : 'layouts.landing_page.app')

@section('title', 'Page not found - Resumify')

@section('content')
    <main class="flex min-h-screen items-center justify-center px-4 py-16">
        <x-ui.empty-state
            icon="travel_explore"
            title="Page not found"
            description="The page may have moved, or the link you followed is no longer available."
            :action-url="auth()->check() ? route('dashboard') : route('home')"
            :action-label="auth()->check() ? 'Back to Dashboard' : 'Back to Home'"
        />
    </main>
@endsection

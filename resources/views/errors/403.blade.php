@extends(auth()->check() ? 'layouts.user.app' : 'layouts.landing_page.app')

@section('title', 'Access unavailable - Resumify')

@section('content')
    <main class="flex min-h-screen items-center justify-center px-4 py-16">
        <x-ui.empty-state
            icon="lock"
            title="You do not have access"
            description="Your account does not have permission to open this area. Return to a safe page and continue from there."
            :action-url="auth()->check() ? route('dashboard') : route('home')"
            :action-label="auth()->check() ? 'Back to Dashboard' : 'Back to Home'"
        />
    </main>
@endsection

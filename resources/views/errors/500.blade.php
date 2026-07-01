@extends(auth()->check() ? 'layouts.user.app' : 'layouts.landing_page.app')

@section('title', 'Something went wrong - Resumify')

@section('content')
    <main class="flex min-h-screen items-center justify-center px-4 py-16">
        <x-ui.empty-state
            icon="priority_high"
            title="Something went wrong"
            description="We could not complete that request. Try returning to your workspace and starting again."
            :action-url="auth()->check() ? route('dashboard') : route('home')"
            :action-label="auth()->check() ? 'Back to Dashboard' : 'Back to Home'"
        />
    </main>
@endsection

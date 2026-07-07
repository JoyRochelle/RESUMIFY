@extends(auth()->check() ? 'layouts.user.app' : 'layouts.landing_page.app')

@section('title', 'Bad request - Resumify')

@section('content')
    <main class="flex min-h-screen items-center justify-center px-4 py-16">
        <x-ui.empty-state
            icon="block"
            title="Bad request"
            description="We could not understand that request. Check the link you followed and try again."
            :action-url="auth()->check() ? route('dashboard') : route('home')"
            :action-label="auth()->check() ? 'Back to Dashboard' : 'Back to Home'"
        />
    </main>
@endsection

@extends(auth()->check() ? 'layouts.user.app' : 'layouts.landing_page.app')

@section('title', 'Authentication required - Resumify')

@section('content')
    <main class="flex min-h-screen items-center justify-center px-4 py-16">
        <x-ui.empty-state
            icon="lock_open"
            title="Please sign in"
            description="You need to be signed in to view this page. Log in to your account and try again."
            action-url="{{ route('login') }}"
            action-label="Sign In"
        />
    </main>
@endsection

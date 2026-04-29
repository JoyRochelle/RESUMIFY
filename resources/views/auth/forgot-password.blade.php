@extends('layouts.user_dashboard.app')

@section('title', 'Forgot Password - Resumify')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="card-title text-center mb-2">Forgot Password</h2>
                    <p class="text-center text-muted mb-4">
                        Enter your email and we'll send you a link to reset your password.
                    </p>

                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Send Password Reset Link</button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        <a href="{{ route('login') }}" class="text-decoration-none">Back to Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

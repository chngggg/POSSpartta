@extends('layouts.app')

@section('content')
<div class="container" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="col-md-6">
        <div class="card shadow-lg border-0" style="border-radius: 12px;">

            <div class="card-header text-center bg-white border-0" style="font-size: 24px; font-weight: 600;">
                {{ __('Welcome Back') }}
            </div>

            <div class="card-body px-4 py-4">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email Address') }}</label>
                        <input id="email" type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autofocus
                            placeholder="Enter your email">

                        @error('email')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password" required
                            placeholder="Enter your password">

                        @error('password')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    {{-- Remember --}}
                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>

                    {{-- Button --}}
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary" style="padding: 10px; font-weight: 500;">
                            {{ __('Login') }}
                        </button>
                    </div>

                    {{-- Forgot --}}
                    <div class="text-center">
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="text-decoration: none;">
                            {{ __('Forgot Your Password?') }}
                        </a>
                        @endif
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
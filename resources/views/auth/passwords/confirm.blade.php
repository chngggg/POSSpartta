@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="col-md-6">
        <div class="card shadow-lg border-0" style="border-radius: 12px;">

            <div class="card-header text-center bg-white border-0">
                {{ __('Confirm Password') }}
            </div>

            <div class="card-body px-4 py-4">

                <p class="text-center mb-4">
                    {{ __('Please confirm your password before continuing.') }}
                </p>

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <div class="mb-3">
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Enter your password" required>

                        @error('password')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="d-grid mb-2">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Confirm') }}
                        </button>
                    </div>

                    <div class="text-center">
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">
                            {{ __('Forgot Password?') }}
                        </a>
                        @endif
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection
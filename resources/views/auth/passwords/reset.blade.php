@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="col-md-6">
        <div class="card shadow-lg border-0" style="border-radius: 12px;">

            <div class="card-header text-center bg-white border-0">
                {{ __('Reset Password') }}
            </div>

            <div class="card-body px-4 py-4">

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <input type="email" name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ $email ?? old('email') }}" required>

                        @error('email')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="New password" required>

                        @error('password')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <input type="password" name="password_confirmation"
                            class="form-control"
                            placeholder="Confirm password" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Reset Password') }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection
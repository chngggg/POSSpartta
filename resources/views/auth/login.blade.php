@extends('layouts.guest')

@section('title', 'Login - SparttaPOS')

@section('content')
<style>
    .login-page {
        min-height: 100vh;
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
        url('{{ asset("images/tampak depan toko.jpg") }}') no-repeat center center/cover;
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .login-page::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        backdrop-filter: blur(8px);
        background: rgba(0, 0, 0, 0.3);
    }

    .login-card {
        position: relative;
        z-index: 10;
        background: rgba(20, 20, 20, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        border: 1px solid rgba(212, 175, 55, 0.3);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        width: 100%;
        max-width: 450px;
        animation: fadeInUp 0.6s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-header {
        text-align: center;
        padding: 40px 32px 20px 32px;
        border-bottom: 1px solid rgba(212, 175, 55, 0.2);
    }

    .login-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #d4af37 0%, #f5c542 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: 2px;
        margin-bottom: 8px;
    }

    .login-header p {
        color: #d4af37;
        font-size: 0.8rem;
        letter-spacing: 3px;
        font-weight: 500;
    }

    .login-body {
        padding: 32px;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #c0c0c0;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .form-group label i {
        color: #d4af37;
        margin-right: 8px;
        width: 20px;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        background: #1a1a1a;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: #ffffff;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #d4af37;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
    }

    .form-control::placeholder {
        color: #6c6c6c;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 24px;
    }

    .checkbox-group input {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #d4af37;
    }

    .checkbox-group label {
        color: #b0b0b0;
        font-size: 0.85rem;
        cursor: pointer;
        margin: 0;
    }

    .btn-login {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #d4af37, #b8942e);
        border: none;
        border-radius: 12px;
        color: #0a0a0a;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(212, 175, 55, 0.3);
    }

    .forgot-link {
        text-align: center;
        margin-top: 20px;
    }

    .forgot-link a {
        color: #b0b0b0;
        text-decoration: none;
        font-size: 0.8rem;
        transition: color 0.3s ease;
    }

    .forgot-link a:hover {
        color: #d4af37;
    }

    .error-message {
        color: #e74c3c;
        font-size: 0.75rem;
        margin-top: 5px;
    }

    .alert-danger {
        background: rgba(231, 76, 60, 0.1);
        border: 1px solid rgba(231, 76, 60, 0.3);
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 20px;
        color: #e74c3c;
        font-size: 0.85rem;
    }

    @media (max-width: 768px) {
        .login-card {
            margin: 20px;
            max-width: calc(100% - 40px);
        }

        .login-header {
            padding: 30px 24px 16px 24px;
        }

        .login-header h1 {
            font-size: 2rem;
        }

        .login-body {
            padding: 24px;
        }
    }
</style>

<div class="login-page">
    <div class="login-card">
        <div class="login-header">
            <h1>SPARTTA POS</h1>
            <p>Berkah Barang Cino</p>
        </div>

        <div class="login-body">
            @if($errors->any())
            <div class="alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input id="email" type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}"
                        placeholder="masukkan@email.com"
                        required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input id="password" type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        name="password"
                        placeholder="••••••••"
                        required>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Remember Me</label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i> Login
                </button>

                <div class="forgot-link">
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        <i class="fas fa-key me-1"></i> Forgot Your Password?
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@extends('layouts.master')

@section('title', 'Pengaturan Profile')

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <h4>
            <i class="fas fa-user-cog me-2"></i>
            Pengaturan Akun
        </h4>
        <p class="text-muted">Kelola informasi akun dan keamanan Anda</p>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Flash Messages -->
            @if(session('success'))
            <div class="alert-custom alert-success mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert-custom alert-error mb-4">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            </div>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Profile -->
        <div class="col-md-4">
            <div class="profile-card">
                <div class="profile-sidebar">
                    <div class="profile-avatar">
                        @if(auth()->user()->avatar)
                        <img id="avatar_img" src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar">
                        @else
                        <div class="avatar-placeholder" id="avatar_placeholder">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        @endif
                    </div>
                    <button type="button" class="btn-upload" id="upload_btn">
                        <i class="fas fa-camera me-1"></i> Ganti Foto
                    </button>
                    <input type="file" id="avatar_input" accept="image/*" style="display: none;">

                    <div class="mt-4">
                        <div class="info-item">
                            <label><i class="fas fa-user"></i> Nama</label>
                            <div class="info-value">{{ auth()->user()->name }}</div>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-envelope"></i> Email</label>
                            <div class="info-value">{{ auth()->user()->email }}</div>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-tag"></i> Role</label>
                            <div class="info-value">{{ auth()->user()->role->name }}</div>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-calendar"></i> Bergabung</label>
                            <div class="info-value">{{ auth()->user()->created_at->format('d F Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Edit Profile -->
        <div class="col-md-8">
            <div class="form-card mb-4">
                <h5 class="mb-4" style="color: var(--gold);">
                    <i class="fas fa-edit me-2"></i> Edit Profil
                </h5>

                <form action="{{ route('settings.profile.update') }}" method="POST" id="profileForm">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">
                            <i class="fas fa-user"></i> Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-gold">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Form Ganti Password -->
            <div class="form-card">
                <h5 class="mb-4" style="color: var(--gold);">
                    <i class="fas fa-lock me-2"></i> Ganti Password
                </h5>

                <form action="{{ route('settings.profile.password') }}" method="POST" id="changePasswordForm">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="current_password">
                            <i class="fas fa-key"></i> Password Saat Ini <span class="text-danger">*</span>
                        </label>
                        <div class="password-input-group">
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                id="current_password" name="current_password" required>
                            <button type="button" class="password-toggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password">
                            <i class="fas fa-lock"></i> Password Baru <span class="text-danger">*</span>
                        </label>
                        <div class="password-input-group">
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                id="new_password" name="new_password" minlength="6" required>
                            <button type="button" class="password-toggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">Minimal 6 karakter</small>
                        @error('new_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">
                            <i class="fas fa-check-circle"></i> Konfirmasi Password Baru <span class="text-danger">*</span>
                        </label>
                        <div class="password-input-group">
                            <input type="password" class="form-control"
                                id="confirm_password" name="new_password_confirmation" required>
                            <button type="button" class="password-toggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-gold">
                            <i class="fas fa-save me-2"></i> Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/js/profile.js') }}"></script>
@endpush
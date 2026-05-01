@extends('layouts.master')

@section('title', 'Manajemen User')

@section('content')
<div class="user-management">
    <!-- Header -->
    <div class="user-header">
        <div>
            <h4>
                <i class="fas fa-users me-2"></i>
                Manajemen User
            </h4>
            <p class="text-muted">Kelola user yang memiliki akses ke sistem SparttaPOS</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-gold">
            <i class="fas fa-plus-circle me-2"></i>
            Tambah User Baru
        </a>
    </div>
    <br>
    <!-- Stats Cards -->
    <div class="user-stats">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(212, 175, 55, 0.1); color: var(--gold);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $users->total() }}</h3>
                <p>Total User</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(46, 204, 113, 0.1); color: #2ecc71;">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $users->where('is_active', true)->count() }}</h3>
                <p>Aktif</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(231, 76, 60, 0.1); color: #e74c3c;">
                <i class="fas fa-user-slash"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $users->where('is_active', false)->count() }}</h3>
                <p>Nonaktif</p>
            </div>
        </div>
    </div>

    <!-- Premium Table -->
    <div class="table-premium-container">
        <table class="table-premium">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> No</th>
                    <th><i class="fas fa-user"></i> Nama</th>
                    <th><i class="fas fa-envelope"></i> Email</th>
                    <th><i class="fas fa-tag"></i> Role</th>
                    <th><i class="fas fa-calendar-alt"></i> Bergabung</th>
                    <th><i class="fas fa-toggle-on"></i> Status</th>
                    <th><i class="fas fa-cog"></i> Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $user)
                <tr>
                    <td data-label="No">{{ $users->firstItem() + $index }}</td>
                    <td data-label="Nama">
                        <div class="user-info">
                            <div class="user-avatar">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="user-name">{{ $user->name }}</div>
                                @if($user->id === auth()->id())
                                <span class="badge-gold" style="font-size: 0.6rem;">Anda</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td data-label="Email">
                        <i class="fas fa-envelope me-1" style="color: var(--text-muted);"></i>
                        {{ $user->email }}
                    </td>
                    <td data-label="Role">
                        @php
                        $roleClass = match($user->role->slug) {
                        'super-admin' => 'role-super-admin',
                        'admin' => 'role-admin',
                        default => 'role-employee'
                        };
                        $roleIcon = match($user->role->slug) {
                        'super-admin' => 'fa-crown',
                        'admin' => 'fa-shield-alt',
                        default => 'fa-user'
                        };
                        @endphp
                        <span class="role-badge {{ $roleClass }}">
                            <i class="fas {{ $roleIcon }}"></i>
                            {{ $user->role->name }}
                        </span>
                    </td>
                    <td data-label="Bergabung">
                        <div class="date-cell">
                            <i class="far fa-calendar-alt"></i>
                            {{ $user->created_at->format('d M Y') }}
                        </div>
                    </td>
                    <td data-label="Status">
                        <div class="toggle-wrapper">
                            <label class="toggle-switch">
                                <input type="checkbox"
                                    class="toggle-status"
                                    data-id="{{ $user->id }}"
                                    {{ $user->is_active ? 'checked' : '' }}
                                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                            <span class="toggle-status-text {{ $user->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </td>
                    <td data-label="Aksi">
                        <div class="action-buttons">
                            <a href="{{ route('users.edit', $user) }}" class="btn-icon edit" title="Edit User">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <button type="button" class="btn-icon delete"
                                data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                title="Hapus User">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="table-premium-empty">
                            <i class="fas fa-users-slash"></i>
                            <h5>Belum Ada User</h5>
                            <p>Belum ada user yang terdaftar di sistem</p>
                            <a href="{{ route('users.create') }}" class="btn btn-gold mt-3">
                                <i class="fas fa-plus-circle me-2"></i>Tambah User Pertama
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="pagination-premium">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>


@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user-management.css') }}">
@endpush

@push('scripts')
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash-alt me-2" style="color: var(--danger);"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus user <strong id="deleteUserName"></strong>?</p>
                <p class="text-muted small mb-0">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-gold" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus User</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush
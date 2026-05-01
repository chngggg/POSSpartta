@extends('layouts.master')

@section('title', 'Semua Notifikasi')

@section('content')
<div class="notification-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 style="color: var(--text-gold);">
            <i class="fas fa-bell me-2"></i>
            Semua Notifikasi
        </h4>
        <div>
            <button id="markAllReadBtn" class="btn btn-outline-gold btn-sm">
                <i class="fas fa-check-double me-1"></i>
                Tandai semua sudah dibaca
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="notification-filters mb-4">
        <div class="btn-group" role="group">
            <button type="button" class="filter-btn active" data-filter="all">
                <i class="fas fa-bell me-1"></i> Semua
            </button>
            <button type="button" class="filter-btn" data-filter="unread">
                <i class="fas fa-envelope me-1"></i> Belum Dibaca
                @if($unreadCount > 0)
                <span class="badge bg-danger ms-1">{{ $unreadCount }}</span>
                @endif
            </button>
            <button type="button" class="filter-btn" data-filter="read">
                <i class="fas fa-check-circle me-1"></i> Sudah Dibaca
            </button>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="notification-list">
        @forelse($notifications as $notification)
        <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }}"
            data-id="{{ $notification->id }}"
            data-status="{{ $notification->is_read ? 'read' : 'unread' }}">
            <div class="d-flex gap-3">
                <div class="notification-icon" style="background: {{ $notification->color }}20; color: {{ $notification->color }}">
                    <i class="fas {{ $notification->icon }}"></i>
                </div>
                <div class="notification-content flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="notification-title">
                                {{ $notification->title }}
                                @if(!$notification->is_read)
                                <span class="badge bg-warning text-dark ms-2">Baru</span>
                                @endif
                            </div>
                            <div class="notification-message">{{ $notification->message }}</div>
                            <div class="notification-time mt-2">
                                <i class="far fa-clock me-1"></i>
                                {{ $notification->created_at->diffForHumans() }}
                                <span class="mx-2">•</span>
                                <i class="far fa-calendar-alt me-1"></i>
                                {{ $notification->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                        <div class="notification-actions d-flex gap-2">
                            @if(!$notification->is_read)
                            <button class="btn btn-sm btn-link mark-read-btn"
                                data-id="{{ $notification->id }}"
                                title="Tandai sudah dibaca">
                                <i class="fas fa-check-circle" style="color: var(--success);"></i>
                            </button>
                            @endif
                            <button class="btn btn-sm btn-link delete-btn"
                                data-id="{{ $notification->id }}"
                                title="Hapus notifikasi">
                                <i class="fas fa-trash-alt" style="color: var(--danger);"></i>
                            </button>
                        </div>
                    </div>
                    @if($notification->link)
                    <div class="mt-2">
                        <a href="{{ $notification->link }}" class="btn btn-sm btn-gold">
                            <i class="fas fa-arrow-right me-1"></i> Lihat Detail
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="fas fa-bell-slash fa-4x mb-3" style="color: var(--text-muted); opacity: 0.5;"></i>
            <h5 style="color: var(--text-secondary);">Tidak ada notifikasi</h5>
            <p class="text-muted">Belum ada notifikasi untuk saat ini</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
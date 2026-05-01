@extends('layouts.master')

@section('title', 'Manajemen Kategori')

@section('content')
<div class="category-management">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h4>
                <i class="fas fa-tags me-2"></i>
                Manajemen Kategori
            </h4>
            <p class="text-muted">Kelola kategori sparepart yang tersedia di sistem</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('categories.create') }}" class="btn btn-gold">
                <i class="fas fa-plus-circle me-2"></i>
                Tambah Kategori
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="category-stats">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(212, 175, 55, 0.1); color: var(--gold);">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $categories->total() }}</h3>
                <p>Total Kategori</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(46, 204, 113, 0.1); color: #2ecc71;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $categories->where('is_active', true)->count() }}</h3>
                <p>Aktif</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(231, 76, 60, 0.1); color: #e74c3c;">
                <i class="fas fa-ban"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $categories->where('is_active', false)->count() }}</h3>
                <p>Nonaktif</p>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="search-bar">
        <div class="search-input-wrapper">
            <i class="fas fa-search"></i>
            <input type="text"
                id="searchCategory"
                class="search-input"
                placeholder="Cari kategori berdasarkan nama..."
                value="{{ request('search') }}">
        </div>
    </div>

    <!-- Premium Table -->
    <div class="table-premium-container">
        <table class="table-premium">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> No</th>
                    <th><i class="fas fa-tag"></i> Nama Kategori</th>
                    <th><i class="fas fa-link"></i> Slug</th>
                    <th><i class="fas fa-align-left"></i> Deskripsi</th>
                    <th><i class="fas fa-microchip"></i> Total Sparepart</th>
                    <th><i class="fas fa-toggle-on"></i> Status</th>
                    <th><i class="fas fa-cog"></i> Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $index => $category)
                @php
                $sparepartCount = $category->spareparts()->count();
                @endphp
                <tr>
                    <td data-label="No">{{ $categories->firstItem() + $index }}</td>
                    <td data-label="Nama Kategori">
                        <div class="category-name">
                            <div class="category-icon">
                                <i class="fas fa-tag"></i>
                            </div>
                            <div>
                                <div class="category-title">{{ $category->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td data-label="Slug">
                        <code class="code-badge">{{ $category->slug }}</code>
                    </td>
                    <td data-label="Deskripsi" class="description-cell">
                        {{ $category->description ?? '-' }}
                    </td>
                    <td data-label="Total Sparepart">
                        <span class="stock-badge {{ $sparepartCount > 0 ? 'stock-normal' : 'stock-critical' }}">
                            <i class="fas fa-microchip me-1"></i>
                            {{ $sparepartCount }} sparepart
                        </span>
                    </td>
                    <td data-label="Status">
                        <span class="status-badge {{ $category->is_active ? 'status-active' : 'status-inactive' }}">
                            <i class="fas {{ $category->is_active ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                            {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td data-label="Aksi">
                        <div class="action-buttons">
                            <a href="{{ route('categories.edit', $category) }}"
                                class="btn-icon edit"
                                title="Edit Kategori">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button"
                                class="btn-icon delete delete-category"
                                data-id="{{ $category->id }}"
                                data-name="{{ $category->name }}"
                                data-has-sparepart="{{ $sparepartCount > 0 ? 'true' : 'false' }}"
                                title="Hapus Kategori">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="table-premium-empty">
                            <i class="fas fa-tags"></i>
                            <h5>Belum Ada Kategori</h5>
                            <p>Belum ada kategori yang terdaftar di sistem</p>
                            <a href="{{ route('categories.create') }}" class="btn btn-gold mt-3">
                                <i class="fas fa-plus-circle me-2"></i>Tambah Kategori Pertama
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($categories->hasPages())
        <div class="pagination-premium">
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>


@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/category.css') }}">
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('searchCategory');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const keyword = this.value;
                    const currentUrl = new URL(window.location.href);
                    if (keyword) {
                        currentUrl.searchParams.set('search', keyword);
                    } else {
                        currentUrl.searchParams.delete('search');
                    }
                    window.location.href = currentUrl.toString();
                }, 500);
            });
        }

        // Delete modal handling
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const deleteButtons = document.querySelectorAll('.delete-category');
        const warningMessage = document.getElementById('warningMessage');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const categoryId = this.dataset.id;
                const categoryName = this.dataset.name;
                const hasSparepart = this.dataset.hasSparepart === 'true';

                document.getElementById('deleteCategoryName').textContent = categoryName;
                document.getElementById('deleteForm').action = `/categories/${categoryId}`;

                if (hasSparepart) {
                    warningMessage.style.display = 'block';
                    confirmDeleteBtn.disabled = true;
                    confirmDeleteBtn.style.opacity = '0.5';
                    confirmDeleteBtn.style.cursor = 'not-allowed';
                } else {
                    warningMessage.style.display = 'none';
                    confirmDeleteBtn.disabled = false;
                    confirmDeleteBtn.style.opacity = '1';
                    confirmDeleteBtn.style.cursor = 'pointer';
                }

                deleteModal.show();
            });
        });
    });
</script>
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
                <p>Apakah Anda yakin ingin menghapus kategori <strong id="deleteCategoryName"></strong>?</p>
                <p class="text-muted small mb-0">Tindakan ini tidak dapat dibatalkan.</p>
                <div id="warningMessage" class="text-danger small mt-2" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i>
                    Kategori ini memiliki sparepart. Tidak dapat dihapus!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-gold" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash-alt me-1"></i> Hapus Kategori
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush
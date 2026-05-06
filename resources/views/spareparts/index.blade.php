@extends('layouts.master')

@section('title', 'Data Sparepart')

@section('content')
<div class="sparepart-management">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h4>
                <i class="fas fa-microchip me-2"></i>
                Data Sparepart
            </h4>
            <p class="text-muted">Kelola data sparepart yang tersedia di sistem</p>
        </div>
        <div class="header-actions">
            @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
            <a href="{{ route('spareparts.create') }}" class="btn btn-gold">
                <i class="fas fa-plus-circle me-2"></i>
                Tambah Sparepart
            </a>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="user-stats">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(212, 175, 55, 0.1); color: var(--gold);">
                <i class="fas fa-microchip"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $spareparts->total() }}</h3>
                <p>Total Sparepart</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(46, 204, 113, 0.1); color: #2ecc71;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $spareparts->where('stock', '>', 0)->count() }}</h3>
                <p>Tersedia</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(243, 156, 18, 0.1); color: #f39c12;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $spareparts->filter(fn($s) => $s->isLowStock())->count() }}</h3>
                <p>Stok Menipis</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(52, 152, 219, 0.1); color: #3498db;">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $spareparts->pluck('category_id')->unique()->count() }}</h3>
                <p>Kategori Terpakai</p>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="search-bar">
        <div class="search-input-wrapper">
            <i class="fas fa-search"></i>
            <input type="text"
                id="searchSparepart"
                class="search-input"
                placeholder="Cari sparepart berdasarkan kode atau nama..."
                value="{{ request('search') }}">
        </div>
    </div>

    <!-- Premium Table -->
    <div class="table-premium-container">
        <table class="table-premium">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> No</th>
                    <th><i class="fas fa-barcode"></i> Kode</th>
                    <th><i class="fas fa-microchip"></i> Nama</th>
                    <th><i class="fas fa-tag"></i> Kategori</th>
                    <th><i class="fas fa-money-bill-wave"></i> Harga Jual</th>
                    <th><i class="fas fa-boxes"></i> Stok</th>
                    <th><i class="fas fa-qrcode"></i> Barcode</th>
                    <th><i class="fas fa-cog"></i> Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($spareparts as $index => $sparepart)
                @php
                $stockClass = 'stock-normal';
                if ($sparepart->stock <= 0) {
                    $stockClass='stock-critical' ;
                    } elseif ($sparepart->isLowStock()) {
                    $stockClass = 'stock-low';
                    }
                    @endphp
                    <tr>
                        <td data-label="No">{{ $spareparts->firstItem() + $index }}</td>
                        <td data-label="Kode">
                            <code class="code-badge">{{ $sparepart->code }}</code>
                        </td>
                        <td data-label="Nama">
                            <div class="sparepart-name">
                                <i class="fas fa-microchip me-1" style="color: var(--gold);"></i>
                                {{ $sparepart->name }}
                            </div>
                        </td>
                        <td data-label="Kategori">
                            <span class="category-badge">
                                <i class="fas fa-tag me-1"></i>
                                {{ $sparepart->category->name }}
                            </span>
                        </td>
                        <td data-label="Harga Jual" class="price-format">
                            Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }}
                        </td>
                        <td data-label="Stok">
                            <span class="stock-badge {{ $stockClass }}"
                                data-stock="{{ $sparepart->stock }}"
                                data-min-stock="{{ $sparepart->min_stock }}"
                                data-name="{{ $sparepart->name }}">
                                <i class="fas {{ $sparepart->stock <= 0 ? 'fa-times-circle' : ($sparepart->isLowStock() ? 'fa-exclamation-triangle' : 'fa-check-circle') }} me-1"></i>
                                {{ $sparepart->stock }} pcs
                            </span>
                            @if($sparepart->stock <= $sparepart->min_stock && $sparepart->stock > 0)
                                <small class="text-warning d-block">Min: {{ $sparepart->min_stock }} pcs</small>
                                @elseif($sparepart->stock <= 0)
                                    <small class="text-danger d-block">Habis!</small>
                                    @endif
                        </td>
                        <td data-label="Barcode">
                            <img src="{{ route('spareparts.barcode', $sparepart->code) }}"
                                alt="Barcode"
                                class="barcode-img"
                                title="Klik untuk perbesar"
                                onclick="window.open(this.src, '_blank')">
                        </td>
                        <td data-label="Aksi">
                            <div class="action-buttons">
                                <!-- Tombol Lihat - SEMUA ROLE -->
                                <a href="{{ route('spareparts.show', $sparepart) }}"
                                    class="btn-icon view"
                                    title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <!-- Tombol Edit - HANYA ADMIN & SUPER ADMIN -->
                                @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                                <a href="{{ route('spareparts.edit', $sparepart) }}"
                                    class="btn-icon edit"
                                    title="Edit Sparepart">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif

                                <!-- Tombol Hapus - HANYA ADMIN & SUPER ADMIN -->
                                @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                                <button type="button"
                                    class="btn-icon delete delete-sparepart"
                                    data-id="{{ $sparepart->id }}"
                                    data-name="{{ $sparepart->name }}"
                                    title="Hapus Sparepart">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="table-premium-empty">
                                <i class="fas fa-microchip-slash"></i>
                                <h5>Belum Ada Sparepart</h5>
                                <p>Belum ada sparepart yang terdaftar di sistem</p>
                                <a href="{{ route('spareparts.create') }}" class="btn btn-gold mt-3">
                                    <i class="fas fa-plus-circle me-2"></i>Tambah Sparepart Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($spareparts->hasPages())
        <div class="pagination-premium">
            {{ $spareparts->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/sparepart.css') }}">
@endpush

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-trash-alt me-2" style="color: var(--danger);"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus sparepart <strong id="deleteSparepartName"></strong>?</p>
                <p class="text-muted small mb-0">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-gold" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" data-base-url="{{ url('/spareparts') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Hapus Sparepart
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
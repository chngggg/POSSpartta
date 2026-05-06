@extends('layouts.master')

@section('title', 'Stock Opname')

@section('content')
<div class="stock-container">
    <div class="page-header">
        <div>
            <h4>
                <i class="fas fa-clipboard-list me-2"></i>
                Stock Opname
            </h4>
            <p class="text-muted">Kelola data stock opname / perhitungan fisik barang</p>
        </div>
        <a href="{{ route('stock.opname.create') }}" class="btn btn-gold">
            <i class="fas fa-plus-circle me-2"></i>
            Stock Opname Baru
        </a>
    </div>

    <div class="table-premium-container">
        <table class="table-premium">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> No</th>
                    <th><i class="fas fa-barcode"></i> No. Opname</th>
                    <th><i class="fas fa-calendar"></i> Tanggal</th>
                    <th><i class="fas fa-chart-line"></i> Periode</th>
                    <th><i class="fas fa-user"></i> Dibuat oleh</th>
                    <th><i class="fas fa-flag-checkered"></i> Status</th>
                    <th><i class="fas fa-cog"></i> Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockOpnames as $index => $opname)
                <tr>
                    <td data-label="No">{{ $stockOpnames->firstItem() + $index }}</td>
                    <td data-label="No. Opname">
                        <code>{{ $opname->opname_number }}</code>
                    </td>
                    <td data-label="Tanggal">{{ $opname->opname_date->format('d/m/Y') }}</td>
                    <td data-label="Periode">{{ $opname->period }}</td>
                    <td data-label="Dibuat oleh">{{ $opname->creator->name }}</td>
                    <td data-label="Status">
                        <span class="status-badge status-{{ $opname->status }}">
                            {{ ucfirst($opname->status) }}
                        </span>
                    </td>
                    <td data-label="Aksi">
                        <div class="action-buttons">
                            <a href="{{ route('stock.opname.show', $opname) }}" class="btn-icon view" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('stock.opname.print', $opname) }}" class="btn-icon" title="Print Berita Acara" target="_blank">
                                <i class="fas fa-print"></i>
                            </a>
                            <button type="button" class="btn-icon delete delete-item"
                                data-id="{{ $opname->id }}"
                                data-name="{{ $opname->opname_number }}"
                                title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="table-premium-empty">
                            <i class="fas fa-clipboard-list"></i>
                            <h5>Belum Ada Stock Opname</h5>
                            <p>Belum ada data stock opname yang tersimpan</p>
                            <a href="{{ route('stock.opname.create') }}" class="btn btn-gold mt-3">
                                <i class="fas fa-plus-circle me-2"></i>Buat Stock Opname Pertama
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($stockOpnames->hasPages())
        <div class="pagination-premium">
            {{ $stockOpnames->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stock.css') }}">
@endpush

<!-- Delete Modal -->
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
                <p>Apakah Anda yakin ingin menghapus stock opname <strong id="deleteItemName"></strong>?</p>
                <p class="text-muted small mb-0">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-gold" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash-alt me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>
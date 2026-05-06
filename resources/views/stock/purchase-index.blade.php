@extends('layouts.master')

@section('title', 'Bukti Barang Masuk')

@section('content')
<div class="stock-container">
    <div class="page-header">
        <div>
            <h4>
                <i class="fas fa-download me-2"></i>
                Bukti Barang Masuk
            </h4>
            <p class="text-muted">Kelola bukti penerimaan barang / purchase receipt</p>
        </div>
        <a href="{{ route('stock.purchase.create') }}" class="btn btn-gold">
            <i class="fas fa-plus-circle me-2"></i>
            Tambah Barang Masuk
        </a>
    </div>

    <div class="table-premium-container">
        <table class="table-premium">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> No</th>
                    <th><i class="fas fa-barcode"></i> No. Receipt</th>
                    <th><i class="fas fa-calendar"></i> Tanggal</th>
                    <th><i class="fas fa-truck"></i> Supplier</th>
                    <th><i class="fas fa-file-invoice"></i> Invoice</th>
                    <th><i class="fas fa-flag-checkered"></i> Status</th>
                    <th><i class="fas fa-cog"></i> Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipts as $index => $receipt)
                <tr>
                    <td data-label="No">{{ $receipts->firstItem() + $index }}</td>
                    <td data-label="No. Receipt">
                        <code>{{ $receipt->receipt_number }}</code>
                    </td>
                    <td data-label="Tanggal">{{ $receipt->receipt_date->format('d/m/Y') }}</td>
                    <td data-label="Supplier">{{ $receipt->supplier->name ?? '-' }}</td>
                    <td data-label="Invoice">{{ $receipt->invoice_number ?? '-' }}</td>
                    <td data-label="Status">
                        <span class="status-badge status-{{ $receipt->status }}">
                            {{ ucfirst($receipt->status) }}
                        </span>
                    </td>
                    <td data-label="Aksi">
                        <div class="action-buttons">
                            <a href="{{ route('stock.purchase.show', $receipt) }}" class="btn-icon view" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button type="button" class="btn-icon delete delete-item"
                                data-id="{{ $receipt->id }}"
                                data-name="{{ $receipt->receipt_number }}"
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
                            <i class="fas fa-download"></i>
                            <h5>Belum Ada Data Barang Masuk</h5>
                            <p>Belum ada data bukti barang masuk yang tersimpan</p>
                            <a href="{{ route('stock.purchase.create') }}" class="btn btn-gold mt-3">
                                <i class="fas fa-plus-circle me-2"></i>Tambah Barang Masuk
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($receipts->hasPages())
        <div class="pagination-premium">
            {{ $receipts->links('pagination::bootstrap-5') }}
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
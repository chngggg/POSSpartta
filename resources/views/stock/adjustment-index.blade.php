@extends('layouts.master')

@section('title', 'Penyesuaian Stok')

@section('content')
<div class="stock-container">
    <div class="page-header">
        <div>
            <h4>
                <i class="fas fa-exchange-alt me-2"></i>
                Penyesuaian Stok
            </h4>
            <p class="text-muted">Kelola penyesuaian stok barang (adjustment)</p>
        </div>
        <a href="{{ route('stock.adjustment.create') }}" class="btn btn-gold">
            <i class="fas fa-plus-circle me-2"></i>
            Penyesuaian Stok
        </a>
    </div>

    <div class="table-premium-container">
        <table class="table-premium">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> No</th>
                    <th><i class="fas fa-barcode"></i> No. Adjustment</th>
                    <th><i class="fas fa-calendar"></i> Tanggal</th>
                    <th><i class="fas fa-exchange-alt"></i> Jenis</th>
                    <th><i class="fas fa-align-left"></i> Alasan</th>
                    <th><i class="fas fa-flag-checkered"></i> Status</th>
                    <th><i class="fas fa-cog"></i> Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($adjustments as $index => $adjustment)
                <tr>
                    <td data-label="No">{{ $adjustments->firstItem() + $index }}</td>
                    <td data-label="No. Adjustment">
                        <code>{{ $adjustment->adjustment_number }}</code>
                    </td>
                    <td data-label="Tanggal">{{ $adjustment->adjustment_date->format('d/m/Y') }}</td>
                    <td data-label="Jenis">
                        @php
                        $typeClass = $adjustment->type == 'in' ? 'stock-normal' : ($adjustment->type == 'out' ? 'stock-critical' : 'stock-low');
                        $typeIcon = $adjustment->type == 'in' ? 'fa-plus-circle' : ($adjustment->type == 'out' ? 'fa-minus-circle' : 'fa-edit');
                        @endphp
                        <span class="stock-badge {{ $typeClass }}">
                            <i class="fas {{ $typeIcon }} me-1"></i>
                            {{ $adjustment->type == 'in' ? 'Penambahan' : ($adjustment->type == 'out' ? 'Pengurangan' : 'Koreksi') }}
                        </span>
                    </td>
                    <td data-label="Alasan">{{ Str::limit($adjustment->reason, 50) }}</td>
                    <td data-label="Status">
                        <span class="status-badge status-{{ $adjustment->status }}">
                            {{ ucfirst($adjustment->status) }}
                        </span>
                    </td>
                    <td data-label="Aksi">
                        <div class="action-buttons">
                            <button type="button" class="btn-icon delete delete-item"
                                data-id="{{ $adjustment->id }}"
                                data-name="{{ $adjustment->adjustment_number }}"
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
                            <i class="fas fa-exchange-alt"></i>
                            <h5>Belum Ada Data Penyesuaian Stok</h5>
                            <p>Belum ada data penyesuaian stok yang tersimpan</p>
                            <a href="{{ route('stock.adjustment.create') }}" class="btn btn-gold mt-3">
                                <i class="fas fa-plus-circle me-2"></i>Buat Penyesuaian Stok
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($adjustments->hasPages())
        <div class="pagination-premium">
            {{ $adjustments->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

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
                <p>Apakah Anda yakin ingin menghapus penyesuaian stok <strong id="deleteItemName"></strong>?</p>
                <p class="text-muted small mb-0">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-gold" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stock.css') }}">
@endpush
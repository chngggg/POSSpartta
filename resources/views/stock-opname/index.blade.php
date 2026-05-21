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
        <a href="{{ route('stock-opname.create') }}" class="btn btn-gold">
            <i class="fas fa-plus-circle me-2"></i>
            Stock Opname Baru
        </a>
    </div>

    <div class="table-premium-container">
        <table class="table-premium">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. Opname</th>
                    <th>Tanggal</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockOpnames as $index => $opname)
                <tr>
                    <td data-label="No">{{ $loop->iteration }}</td>
                    <td data-label="No. Opname">
                        <code>{{ $opname->opname_number }}</code>
                    </td>
                    <td data-label="Tanggal">{{ $opname->opname_date->format('d/m/Y') }}</td>
                    <td data-label="Periode">{{ $opname->period }}</a>
                    <td data-label="Status">
                        <span class="status-badge status-{{ $opname->status }}">
                            {{ $opname->status == 'draft' ? 'Draft' : 'Selesai' }}
                        </span>
                        </a>
                    <td data-label="Aksi">
                        <div class="action-buttons">
                            <a href="{{ route('stock-opname.show', $opname) }}" class="btn-icon view" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('stock-opname.print', $opname) }}" class="btn-icon" title="Print" target="_blank">
                                <i class="fas fa-print"></i>
                            </a>
                            <button type="button" class="btn-icon delete delete-item"
                                data-id="{{ $opname->id }}"
                                data-name="{{ $opname->opname_number }}"
                                title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        </a>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="table-premium-empty">
                            <i class="fas fa-clipboard-list"></i>
                            <h5>Belum Ada Stock Opname</h5>
                            <p>Belum ada data stock opname yang tersimpan</p>
                            <a href="{{ route('stock-opname.create') }}" class="btn btn-gold mt-3">
                                <i class="fas fa-plus-circle me-2"></i>Buat Stock Opname Pertama
                            </a>
                        </div>
                        </a>
        </table>
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
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete handler
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const deleteButtons = document.querySelectorAll('.delete-item');

        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                document.getElementById('deleteItemName').textContent = name;
                document.getElementById('deleteForm').action = `/stock-opname/${id}`;
                deleteModal.show();
            });
        });
    });
</script>
@endpush
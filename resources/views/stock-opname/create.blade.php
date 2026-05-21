@extends('layouts.master')

@section('title', 'Tambah Stock Opname')

@section('content')
<div class="stock-container">
    <div class="page-header">
        <div>
            <h4>
                <i class="fas fa-plus-circle me-2"></i>
                Stock Opname Baru
            </h4>
            <p class="text-muted">Lakukan perhitungan fisik barang (stock opname)</p>
        </div>
        <a href="{{ route('stock-opname.index') }}" class="btn btn-outline-gold">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('stock-opname.store') }}">
            @csrf

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="period">Periode Opname <span class="text-danger">*</span></label>
                        <input type="month" class="form-control" id="period" name="period" value="{{ date('Y-m') }}" required>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="notes">Catatan</label>
                        <textarea class="form-control" id="notes" name="notes" rows="1" placeholder="Catatan tentang stock opname (opsional)"></textarea>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Stok Sistem</th>
                            <th>Stok Fisik</th>
                            <th>Selisih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($spareparts as $index => $sparepart)
                        <tr>
                            <td><code>{{ $sparepart->code }}</code>
                                <input type="hidden" name="items[{{ $index }}][sparepart_id]" value="{{ $sparepart->id }}">
                            </td>
                            <td>{{ $sparepart->name }}</td>
                            <td class="system-stock" data-stock="{{ $sparepart->stock }}">{{ number_format($sparepart->stock) }} pcs</td>
                            <td>
                                <input type="number"
                                    name="items[{{ $index }}][physical_stock]"
                                    class="form-control physical-stock"
                                    data-system="{{ $sparepart->stock }}"
                                    style="width: 120px"
                                    value="{{ $sparepart->stock }}">
                            </td>
                            <td class="diff-cell">0</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-actions mt-4">
                <button type="submit" name="status" value="draft" class="btn btn-outline-gold">
                    <i class="fas fa-save me-2"></i> Simpan Draft
                </button>
                <button type="submit" name="status" value="completed" class="btn btn-gold">
                    <i class="fas fa-check-circle me-2"></i> Finalisasi
                </button>
                <a href="{{ route('stock-opname.index') }}" class="btn btn-outline-gold">
                    <i class="fas fa-times me-2"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.physical-stock');

        inputs.forEach(input => {
            input.addEventListener('input', function() {
                const systemStock = parseInt(this.dataset.system);
                const physicalStock = parseInt(this.value) || 0;
                const difference = physicalStock - systemStock;
                const diffCell = this.closest('tr').querySelector('.difference-cell');

                diffCell.textContent = difference;
                diffCell.className = `difference-cell ${difference >= 0 ? 'text-success' : 'text-danger'}`;
            });
        });
    });
</script>
@endpush
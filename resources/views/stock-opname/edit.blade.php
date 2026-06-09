@extends('layouts.master')

@section('title', 'Edit Stock Opname')

@section('content')
<div class="stock-container">
    <div class="page-header">
        <div>
            <h4>
                <i class="fas fa-edit me-2"></i>
                Edit Stock Opname
            </h4>
            <p class="text-muted">{{ $stockOpname->opname_number }}</p>
        </div>
        <a href="{{ route('stock-opname.index') }}" class="btn btn-outline-gold">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="form-card">
        <form id="stockOpnameForm" method="POST" action="{{ route('stock-opname.update', $stockOpname) }}">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor Opname</label>
                        <input type="text" class="form-control" value="{{ $stockOpname->opname_number }}" readonly disabled>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Opname</label>
                        <input type="text" class="form-control" value="{{ $stockOpname->opname_date->format('d/m/Y') }}" readonly disabled>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="period">Periode Opname <span class="text-danger">*</span></label>
                        <input type="month" class="form-control @error('period') is-invalid @enderror"
                            id="period" name="period" value="{{ old('period', $stockOpname->period) }}" required>
                        @error('period')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group mb-4">
                <label for="notes">Catatan</label>
                <textarea class="form-control" id="notes" name="notes" rows="2"
                    placeholder="Catatan tentang stock opname (opsional)">{{ old('notes', $stockOpname->notes) }}</textarea>
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
                        @foreach($stockOpname->items as $index => $item)
                        <tr>
                            <td data-label="Kode">
                                <code>{{ $item->sparepart->code }}</code>
                                <input type="hidden" name="items[{{ $index }}][sparepart_id]" value="{{ $item->sparepart_id }}">
                            </td>
                            <td data-label="Nama Barang">{{ $item->sparepart->name }}</td>
                            <td data-label="Stok Sistem" class="system-stock" data-stock="{{ $item->system_stock }}">
                                {{ number_format($item->system_stock) }} pcs
                            </td>
                            <td data-label="Stok Fisik">
                                <input type="number"
                                    name="items[{{ $index }}][physical_stock]"
                                    class="form-control physical-stock"
                                    data-system="{{ $item->system_stock }}"
                                    style="width: 120px"
                                    value="{{ old("items.{$index}.physical_stock", $item->physical_stock) }}">
                            </td>
                            <td data-label="Selisih" class="diff-cell">
                                @php
                                $diff = $item->physical_stock - $item->system_stock;
                                @endphp
                                {{ $diff >= 0 ? '+' : '' }}{{ $diff }}
                            </td>
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

        const calculateDifference = (input) => {
            const systemStock = parseInt(input.dataset.system) || 0;
            const physicalStock = parseInt(input.value) || 0;
            const difference = physicalStock - systemStock;

            const row = input.closest('tr');
            const diffCell = row.querySelector('.diff-cell');

            if (diffCell) {
                diffCell.textContent = difference >= 0 ? `+${difference}` : `${difference}`;
                diffCell.className = `diff-cell ${difference > 0 ? 'diff-up' : (difference < 0 ? 'diff-down' : 'diff-zero')}`;
            }
        };

        inputs.forEach(input => {
            input.addEventListener('input', function() {
                calculateDifference(this);
            });
        });

        const form = document.getElementById('stockOpnameForm');

        form.addEventListener('submit', function(e) {
            const submitter = e.submitter;
            const status = submitter ? submitter.value : null;

            if (status === 'draft') {
                return true;
            }

            const physicalInputs = document.querySelectorAll('.physical-stock');
            let hasEmpty = false;

            physicalInputs.forEach(input => {
                if (input.value.trim() === '') {
                    hasEmpty = true;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (hasEmpty) {
                e.preventDefault();
                if (window.stock && window.stock.showToast) {
                    window.stock.showToast('Mohon isi semua stok fisik sebelum finalisasi!', 'error');
                }
                return false;
            }

            if (status === 'completed') {
                if (!confirm('Anda yakin ingin memfinalisasi stock opname ini?\n\nStok barang akan diperbarui sesuai stok fisik dan tidak dapat diubah kembali!')) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    });
</script>
@endpush
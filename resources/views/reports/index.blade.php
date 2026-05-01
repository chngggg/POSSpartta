@extends('layouts.master')

@section('title', 'Laporan Kartu Stok')

@section('content')
<div class="stock-container">
    <div class="page-header">
        <div>
            <h4>
                <i class="fas fa-print me-2"></i>
                Laporan Kartu Stok
            </h4>
            <p class="text-muted">Laporan mutasi stok per sparepart</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-gold">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="form-card mb-4">
        <form method="GET" action="{{ route('reports.stock-card') }}" class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Pilih Sparepart</label>
                <select name="sparepart_id" class="form-control" required>
                    <option value="">-- Pilih Sparepart --</option>
                    @foreach($spareparts as $sparepart)
                    <option value="{{ $sparepart->id }}" {{ ($selectedSparepart->id ?? '') == $sparepart->id ? 'selected' : '' }}>
                        {{ $sparepart->code }} - {{ $sparepart->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Awal</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-gold w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    @if($selectedSparepart)
    <div class="table-premium-container">
        <table class="table-premium">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis Transaksi</th>
                    <th>Stok Awal</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Stok Akhir</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockCards as $card)
                <tr>
                    <td data-label="Tanggal">{{ $card->date->format('d/m/Y') }}</td>
                    <td data-label="Jenis Transaksi">
                        @php
                        $typeLabels = ['purchase' => 'Pembelian', 'sale' => 'Penjualan', 'adjustment' => 'Penyesuaian', 'opname' => 'Stock Opname'];
                        @endphp
                        {{ $typeLabels[$card->reference_type] ?? ucfirst($card->reference_type) }}
                    </td>
                    <td data-label="Stok Awal">{{ number_format($card->beginning_stock) }}</td>
                    <td data-label="Masuk">{{ number_format($card->stock_in) }}</td>
                    <td data-label="Keluar">{{ number_format($card->stock_out) }}</td>
                    <td data-label="Stok Akhir">{{ number_format($card->ending_stock) }}</td>
                    <td data-label="Keterangan">{{ $card->description ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <i class="fas fa-database"></i> Tidak ada data untuk periode yang dipilih
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stock.css') }}">
@endpush
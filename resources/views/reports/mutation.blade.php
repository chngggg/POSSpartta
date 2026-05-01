@extends('layouts.master')

@section('title', 'Laporan Mutasi Barang')

@section('content')
<div class="stock-container">
    <div class="page-header">
        <div>
            <h4>
                <i class="fas fa-exchange-alt me-2"></i>
                Laporan Mutasi Barang
            </h4>
            <p class="text-muted">Laporan barang masuk dan keluar</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-gold">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="form-card mb-4">
        <form method="GET" action="{{ route('reports.mutation') }}" class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Tanggal Awal</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-5">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-gold w-100">
                    <i class="fas fa-search me-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="chart-container">
                <h6><i class="fas fa-download me-2"></i> Barang Masuk (Pembelian)</h6>
                <div class="table-responsive">
                    <table class="table-premium">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Receipt</th>
                                <th>Supplier</th>
                                <th>Jumlah Item</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($incoming as $item)
                            <tr>
                                <td>{{ $item->receipt_date->format('d/m/Y') }}</td>
                                <td>{{ $item->receipt_number }}</td>
                                <td>{{ $item->supplier->name ?? '-' }}</td>
                                <td>{{ $item->items->sum('quantity') }} pcs</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container">
                <h6><i class="fas fa-upload me-2"></i> Barang Keluar (Penjualan)</h6>
                <div class="table-responsive">
                    <table class="table-premium">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>ID Transaksi</th>
                                <th>Total</th>
                                <th>Metode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($outgoing as $sale)
                            <tr>
                                <td>{{ $sale->created_at->format('d/m/Y') }}</td>
                                <td>{{ $sale->transaction_id }}</td>
                                <td>{{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                                <td>{{ ucfirst($sale->payment_method) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="chart-container mt-4">
        <h6><i class="fas fa-edit me-2"></i> Penyesuaian Stok</h6>
        <div class="table-responsive">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Adjustment</th>
                        <th>Jenis</th>
                        <th>Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adjustments as $adj)
                    <tr>
                        <td>{{ $adj->adjustment_date->format('d/m/Y') }}</td>
                        <td>{{ $adj->adjustment_number }}</td>
                        <td>{{ $adj->type == 'in' ? 'Penambahan' : ($adj->type == 'out' ? 'Pengurangan' : 'Koreksi') }}</td>
                        <td>{{ $adj->reason }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stock.css') }}">
@endpush
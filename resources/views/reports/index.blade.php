@extends('layouts.master')

@section('title', 'Dashboard Laporan')

@section('content')
<div class="stock-container">
    <div class="page-header">
        <div>
            <h4>
                <i class="fas fa-chart-line me-2"></i>
                Dashboard Laporan
            </h4>
            <p class="text-muted">Ringkasan laporan inventory dan keuangan</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('reports.stock-card') }}" class="btn btn-outline-gold">
                <i class="fas fa-print me-2"></i> Laporan Kartu Stok
            </a>
        </div>
    </div>

    <!-- Filter Periode -->
    <div class="form-card mb-4">
        <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Periode Laporan</label>
                <input type="month" name="period" class="form-control" value="{{ $period ?? date('Y-m') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-gold">
                    <i class="fas fa-filter me-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Statistik Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(212, 175, 55, 0.1); color: var(--gold);">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($totalSales ?? 0, 0, ',', '.') }}</h3>
                <p>Total Penjualan</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(46, 204, 113, 0.1); color: #2ecc71;">
                <i class="fas fa-download"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($totalPurchase ?? 0, 0, ',', '.') }}</h3>
                <p>Total Pembelian</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(243, 156, 18, 0.1); color: #f39c12;">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalAdjustmentIn ?? 0 }}</h3>
                <p>Penyesuaian (+)</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(231, 76, 60, 0.1); color: #e74c3c;">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalAdjustmentOut ?? 0 }}</h3>
                <p>Penyesuaian (-)</p>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="row">
        <div class="col-md-6">
            <div class="chart-container">
                <h6><i class="fas fa-chart-line me-2"></i> Grafik Penjualan vs Pembelian (Tahunan)</h6>
                <canvas id="salesPurchaseChart" height="250"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container">
                <h6><i class="fas fa-chart-bar me-2"></i> Top 10 Sparepart Stok Terbanyak</h6>
                <canvas id="topSparepartChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabel Top Sparepart Dibeli -->
    <div class="table-premium-container mt-4">
        <h6 class="p-3"><i class="fas fa-trophy me-2"></i> Top 10 Sparepart Paling Banyak Dibeli</h6>
        <table class="table-premium">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Sparepart</th>
                    <th>Total Dibeli</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topPurchased ?? [] as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ number_format($item->total_quantity) }} pcs</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4">Belum ada data pembelian</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stock.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Grafik Penjualan vs Pembelian
        const monthlySales = @json($monthlySales ?? []);
        const monthlyPurchase = @json($monthlyPurchase ?? []);

        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        const salesData = [];
        const purchaseData = [];

        for (let i = 1; i <= 12; i++) {
            salesData.push(monthlySales[i] || 0);
            purchaseData.push(monthlyPurchase[i] || 0);
        }

        new Chart(document.getElementById('salesPurchaseChart'), {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                        label: 'Penjualan',
                        data: salesData,
                        backgroundColor: 'rgba(212, 175, 55, 0.7)',
                        borderColor: '#d4af37',
                        borderWidth: 1
                    },
                    {
                        label: 'Pembelian',
                        data: purchaseData,
                        backgroundColor: 'rgba(46, 204, 113, 0.7)',
                        borderColor: '#2ecc71',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // Grafik Top Sparepart Stok Terbanyak
        const topSpareparts = @json($topSpareparts ?? []);
        const topNames = topSpareparts.map(s => s.name);
        const topStocks = topSpareparts.map(s => s.stock);

        if (topNames.length > 0) {
            new Chart(document.getElementById('topSparepartChart'), {
                type: 'bar',
                data: {
                    labels: topNames,
                    datasets: [{
                        label: 'Stok Tersedia',
                        data: topStocks,
                        backgroundColor: 'rgba(212, 175, 55, 0.7)',
                        borderColor: '#d4af37',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    indexAxis: 'y',
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.raw.toLocaleString('id-ID') + ' pcs';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
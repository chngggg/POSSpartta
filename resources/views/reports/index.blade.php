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
                <small class="text-muted">(Nominal)</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(231, 76, 60, 0.1); color: #e74c3c;">
                <i class="fas fa-upload"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($totalOutgoingQuantity ?? 0) }} pcs</h3>
                <p>Barang Keluar</p>
                <small class="text-muted">(Total item terjual)</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(46, 204, 113, 0.1); color: #2ecc71;">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($totalAdjustmentIn ?? 0) }} pcs</h3>
                <p>Penyesuaian (+)</p>
                <small class="text-muted">Koreksi tambah stok</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(243, 156, 18, 0.1); color: #f39c12;">
                <i class="fas fa-minus-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($totalAdjustmentOut ?? 0) }} pcs</h3>
                <p>Penyesuaian (-)</p>
                <small class="text-muted">Koreksi kurang stok</small>
            </div>
        </div>
    </div>

    <!-- Grafik Dual Axis: Penjualan (Rp) vs Barang Keluar (pcs) -->
    <div class="row">
        <div class="col-md-12">
            <div class="chart-container">
                <h6><i class="fas fa-chart-line me-2"></i> Grafik Penjualan vs Barang Keluar (Per Bulan)</h6>
                <canvas id="salesVsOutgoingChart" height="350"></canvas>
            </div>
        </div>
    </div>

    <!-- Top 10 Sparepart Stok Terbanyak -->
    <div class="table-premium-container mt-4">
        <h6 class="p-3"><i class="fas fa-trophy me-2"></i> Top 10 Sparepart Stok Terbanyak</h6>
        <table class="table-premium">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Sparepart</th>
                    <th>Stok Tersedia</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topSpareparts ?? [] as $index => $item)
                <tr>
                    <td data-label="No">{{ $loop->iteration }}</td>
                    <td data-label="Kode">{{ $item->code }}</td>
                    <td data-label="Nama Sparepart">{{ $item->name }}</td>
                    <td data-label="Stok Tersedia">{{ number_format($item->stock) }} pcs</a>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <i class="fas fa-database"></i> Belum ada data sparepart
                        </a>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- History Penjualan Terbaru -->
    <div class="table-premium-container mt-4">
        <h6 class="p-3"><i class="fas fa-history me-2"></i> History Penjualan Terbaru</h6>
        <table class="table-premium">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Transaksi</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions ?? [] as $item)
                <tr>
                    <td data-label="No">{{ $loop->iteration }}</td>
                    <td data-label="ID Transaksi">
                        <code>{{ $item->transaction_id }}</code>
                        </a>
                    <td data-label="Tanggal">{{ $item->created_at->format('d/m/Y H:i') }}</a>
                    <td data-label="Total">{{ number_format($item->total_amount, 0, ',', '.') }}</a>
                    <td data-label="Metode">
                        <span class="status-badge">{{ ucfirst($item->payment_method) }}</span>
                        </a>
                    <td data-label="Aksi">
                        <a href="{{ route('reports.transaction-detail', $item->id) }}" class="btn-icon view" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        </a>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <i class="fas fa-database"></i> Belum ada data penjualan
                        </a>
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
        // Data dari server
        const monthlySales = @json($monthlySales ?? []); // Data nominal penjualan per bulan (Rp)
        const monthlyOutgoingQuantities = @json($monthlyOutgoingQuantities ?? []); // Data barang keluar per bulan (pcs)
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

        // Siapkan data array
        const salesData = [];
        const outgoingData = [];

        for (let i = 1; i <= 12; i++) {
            salesData.push(monthlySales[i] || 0);
            outgoingData.push(monthlyOutgoingQuantities[i] || 0);
        }

        const ctx = document.getElementById('salesVsOutgoingChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                            label: 'Penjualan (Rp)',
                            data: salesData,
                            type: 'bar',
                            backgroundColor: 'rgba(212, 175, 55, 0.7)',
                            borderColor: '#d4af37',
                            borderWidth: 2,
                            borderRadius: 8,
                            yAxisID: 'y',
                            order: 1
                        },
                        {
                            label: 'Barang Keluar (pcs)',
                            data: outgoingData,
                            type: 'line',
                            backgroundColor: 'rgba(231, 76, 60, 0.1)',
                            borderColor: '#e74c3c',
                            borderWidth: 3,
                            pointBackgroundColor: '#e74c3c',
                            pointBorderColor: '#ffffff',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            tension: 0.3,
                            fill: false,
                            yAxisID: 'y1',
                            order: 0
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    let value = context.raw;
                                    if (context.dataset.label === 'Penjualan (Rp)') {
                                        return label + ': Rp ' + value.toLocaleString('id-ID');
                                    } else {
                                        return label + ': ' + value.toLocaleString('id-ID') + ' pcs';
                                    }
                                }
                            },
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#d4af37',
                            bodyColor: '#ffffff',
                            borderColor: '#d4af37',
                            borderWidth: 1
                        },
                        legend: {
                            labels: {
                                color: '#e0e0e0',
                                font: {
                                    size: 12,
                                    family: 'Inter'
                                },
                                usePointStyle: true,
                                boxWidth: 10
                            },
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Nominal Penjualan (Rp)',
                                color: '#d4af37',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                color: '#e0e0e0',
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                    }
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.1)'
                            }
                        },
                        y1: {
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Jumlah Barang Keluar (pcs)',
                                color: '#e74c3c',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                color: '#e74c3c',
                                callback: function(value) {
                                    return value.toLocaleString('id-ID') + ' pcs';
                                }
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        x: {
                            ticks: {
                                color: '#e0e0e0'
                            },
                            grid: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Bulan',
                                color: '#e0e0e0',
                                font: {
                                    size: 12
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
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Stock Opname - {{ $stockOpname->opname_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            background: white;
            padding: 20px;
        }

        .document {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 24px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .header h3 {
            font-size: 16px;
            font-weight: normal;
        }

        .info-section {
            margin-bottom: 20px;
            border: 1px solid #000;
            padding: 10px;
        }

        .info-row {
            display: flex;
            margin-bottom: 5px;
        }

        .info-label {
            width: 150px;
            font-weight: bold;
        }

        .info-value {
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }

        th {
            background: #f0f0f0;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
        }

        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature-item {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }

        .no-print {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            margin: 5px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }

        .btn-primary {
            background: #d4af37;
            color: #000;
        }

        .btn-secondary {
            background: #333;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="document">
        <div class="header">
            <h1>BERITA ACARA STOCK OPNAME</h1>
            <h3>PERHITUNGAN FISIK PERSEDIAAN BARANG</h3>
            <h3>SPARTTA POS</h3>
        </div>

        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Nomor Opname</div>
                <div class="info-value">: {{ $stockOpname->opname_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Opname</div>
                <div class="info-value">: {{ $stockOpname->opname_date->format('d F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Periode</div>
                <div class="info-value">: {{ $stockOpname->period }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Dibuat oleh</div>
                <div class="info-value">: {{ $stockOpname->creator->name }}</div>
            </div>
            @if($stockOpname->notes)
            <div class="info-row">
                <div class="info-label">Catatan</div>
                <div class="info-value">: {{ $stockOpname->notes }}</div>
            </div>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Stok Sistem</th>
                    <th>Stok Fisik</th>
                    <th>Selisih</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockOpname->items as $index => $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->sparepart->code }}</td>
                    <td style="text-align: left;">{{ $item->sparepart->name }}</td>
                    <td>{{ number_format($item->system_stock) }}</td>
                    <td>{{ number_format($item->physical_stock) }}</td>
                    <td>{{ $item->difference >= 0 ? '+' : '' }}{{ number_format($item->difference) }}</td>
                    <td>{{ $item->notes ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f0f0f0;">
                    <td colspan="3"><strong>Total</strong></td>
                    <td><strong>{{ number_format($stockOpname->items->sum('system_stock')) }}</strong></td>
                    <td><strong>{{ number_format($stockOpname->items->sum('physical_stock')) }}</strong></td>
                    <td colspan="2"><strong>{{ number_format($stockOpname->items->sum('difference')) }}</strong></td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <p>Demikian berita acara ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <div class="signature">
            <div class="signature-item">
                <div>Mengetahui,</div>
                <div>Pimpinan</div>
                <div class="signature-line">_________________</div>
                <div>Nama: _________________</div>
            </div>
            <div class="signature-item">
                <div>Petugas Opname</div>
                <div class="signature-line">_________________</div>
                <div>Nama: {{ $stockOpname->creator->name }}</div>
            </div>
            <div class="signature-item">
                <div>Mengetahui,</div>
                <div>Supervisor</div>
                <div class="signature-line">_________________</div>
                <div>Nama: _________________</div>
            </div>
        </div>
    </div>

    <div class="no-print">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak
        </button>
        <button class="btn btn-secondary" onclick="window.close()">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>
</body>

</html>
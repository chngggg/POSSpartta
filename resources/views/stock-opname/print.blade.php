<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Berita Acara Stock Opname - {{ $stockOpname->opname_number }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            padding: 20px;
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
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        th {
            background: #f0f0f0;
        }

        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
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
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>BERITA ACARA STOCK OPNAME</h1>
        <h3>SPARTTA POS</h3>
    </div>

    <table>
        <tr>
            <td width="30%"><strong>Nomor Opname</strong></td>
            <td>{{ $stockOpname->opname_number }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Opname</strong></td>
            <td>{{ $stockOpname->opname_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td><strong>Periode</strong></td>
            <td>{{ $stockOpname->period }}</td>
        </tr>
        <tr>
            <td><strong>Petugas</strong></td>
            <td>{{ $stockOpname->creator->name }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Stok Sistem</th>
                <th>Stok Fisik</th>
                <th>Selisih</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockOpname->items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->sparepart->code }}</td>
                <td style="text-align:left">{{ $item->sparepart->name }}</td>
                <td>{{ number_format($item->system_stock) }}</td>
                <td>{{ number_format($item->physical_stock) }}</td>
                <td>{{ $item->difference >= 0 ? '+' : '' }}{{ number_format($item->difference) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <div class="signature-item">
            <div>Mengetahui,<br>Pimpinan</div>
            <div class="signature-line">_________________</div>
        </div>
        <div class="signature-item">
            <div>Petugas Opname</div>
            <div class="signature-line">_________________</div>
            <div>{{ $stockOpname->creator->name }}</div>
        </div>
    </div>

    <div class="no-print">
        <button class="btn" onclick="window.print()">🖨️ Cetak</button>
        <button class="btn" onclick="window.close()">❌ Tutup</button>
    </div>
</body>

</html>
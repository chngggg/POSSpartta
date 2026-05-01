<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode - {{ $sparepart->code }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/barcode-print.css') }}">
</head>

<body>
    <div class="barcode-container">
        <div class="barcode-header">
            <h2>
                <i class="fas fa-barcode"></i>
                Barcode Produk
            </h2>
            <p>{{ $sparepart->name }}</p>
        </div>

        <div class="barcode-card">
            <div class="barcode-image">
                <img src="{{ $barcodeBase64 }}" alt="Barcode {{ $sparepart->code }}" style="max-width: 100%; height: auto;">
            </div>
            <div class="barcode-code">
                {{ $sparepart->code }}
            </div>
        </div>

        <div class="product-info">
            <div class="info-row">
                <span class="label">Kode Produk</span>
                <span class="value">{{ $sparepart->code }}</span>
            </div>
            <div class="info-row">
                <span class="label">Nama Produk</span>
                <span class="value">{{ $sparepart->name }}</span>
            </div>
            <div class="info-row">
                <span class="label">Kategori</span>
                <span class="value">{{ $sparepart->category->name }}</span>
            </div>
            <div class="info-row">
                <span class="label">Harga Jual</span>
                <span class="value">Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="action-buttons">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak Barcode
            </button>
            <a href="{{ route('barcode.download', $sparepart->code) }}" class="btn btn-secondary">
                <i class="fas fa-download"></i> Download PNG
            </a>
            <button class="btn btn-danger" onclick="window.close()">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
    </div>

    <script>
        if (window.location.search.includes('print=true')) {
            window.print();
        }
    </script>
</body>

</html>
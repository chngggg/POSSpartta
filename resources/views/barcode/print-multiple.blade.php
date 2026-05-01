<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Barcode - SPARTTA POS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/barcode-print-multiple.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>

<body>
    <!-- Settings Panel -->
    <div class="settings-panel">
        <div class="settings-title">
            <i class="fas fa-sliders-h"></i> Pengaturan Layout Barcode
        </div>
        <div class="settings-row">
            <div class="setting-group">
                <label><i class="fas fa-columns"></i> Jumlah Kolom</label>
                <select id="columnCount">
                    <option value="2">2 Kolom</option>
                    <option value="3">3 Kolom</option>
                    <option value="4" selected>4 Kolom</option>
                    <option value="5">5 Kolom</option>
                    <option value="6">6 Kolom</option>
                </select>
            </div>
            <div class="setting-group">
                <label><i class="fas fa-arrows-alt"></i> Ukuran Barcode</label>
                <select id="barcodeSize">
                    <option value="small">Kecil</option>
                    <option value="medium" selected>Sedang</option>
                    <option value="large">Besar</option>
                </select>
            </div>
            <div class="setting-group">
                <label><i class="fas fa-border-all"></i> Margin (px)</label>
                <input type="number" id="marginSize" value="15" min="5" max="50">
            </div>
            <div class="setting-group">
                <label><i class="fas fa-font"></i> Tampilkan Nama</label>
                <select id="showName">
                    <option value="yes" selected>Ya</option>
                    <option value="no">Tidak</option>
                </select>
            </div>
        </div>
        <div class="settings-row">
            <div class="setting-group">
                <label><i class="fas fa-header"></i> Tampilkan Header</label>
                <select id="showHeader">
                    <option value="yes" selected>Ya (Tampilkan)</option>
                    <option value="no">Tidak (Sembunyikan)</option>
                </select>
            </div>
            <div class="setting-group">
                <label><i class="fas fa-building"></i> Judul Header</label>
                <input type="text" id="headerTitle" value="SPARTTA POS" placeholder="Judul header">
            </div>
            <div class="setting-group">
                <label><i class="fas fa-align-center"></i> Sub Judul</label>
                <input type="text" id="headerSubtitle" value="Daftar Barcode Sparepart" placeholder="Sub judul header">
            </div>
            <div class="setting-group">
                <label><i class="fas fa-calendar"></i> Tampilkan Tanggal</label>
                <select id="showDate">
                    <option value="yes" selected>Ya</option>
                    <option value="no">Tidak</option>
                </select>
            </div>
        </div>
        <div class="export-buttons">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak Langsung
            </button>
            <button class="btn btn-export" id="exportWordBtn">
                <i class="fas fa-file-word"></i> Export ke Word
            </button>
            <button class="btn btn-pdf" id="exportPdfBtn">
                <i class="fas fa-file-pdf"></i> Export ke PDF
            </button>
            <button class="btn btn-secondary" onclick="window.close()">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
    </div>

    <!-- Preview Area -->
    <div class="preview-area">
        <div class="preview-header">
            <h5><i class="fas fa-eye"></i> Preview Barcode</h5>
            <div class="layout-controls">
                <button class="layout-btn" data-layout="2">2 Kolom</button>
                <button class="layout-btn" data-layout="3">3 Kolom</button>
                <button class="layout-btn active" data-layout="4">4 Kolom</button>
                <button class="layout-btn" data-layout="5">5 Kolom</button>
                <button class="layout-btn" data-layout="6">6 Kolom</button>
            </div>
        </div>
        <div id="printContainer" class="print-container">
            <div class="print-header" id="printHeader">
                <h1 id="headerTitleDisplay">SPARTTA POS</h1>
                <p id="headerSubtitleDisplay">Daftar Barcode Sparepart</p>
                <p id="headerDateDisplay">Tanggal: {{ date('d/m/Y H:i:s') }}</p>
                <p id="headerTotalDisplay">Total: {{ count($spareparts) }} barcode</p>
            </div>
            <div class="barcode-grid" id="barcodeGrid">
                @forelse($spareparts as $sparepart)
                <div class="barcode-item">
                    <img src="{{ $barcodes[$sparepart->code] }}" alt="Barcode {{ $sparepart->code }}">
                    <div class="barcode-code">{{ $sparepart->code }}</div>
                    <div class="barcode-name">{{ $sparepart->name }}</div>
                </div>
                @empty
                <div class="empty-state">
                    <i class="fas fa-barcode"></i>
                    <p>Tidak ada barcode yang dipilih</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        function updateLayout() {
            const columnCount = document.getElementById('columnCount').value;
            const barcodeSize = document.getElementById('barcodeSize').value;
            const marginSize = document.getElementById('marginSize').value;
            const showName = document.getElementById('showName').value;
            const showHeader = document.getElementById('showHeader').value;
            const headerTitle = document.getElementById('headerTitle').value;
            const headerSubtitle = document.getElementById('headerSubtitle').value;
            const showDate = document.getElementById('showDate').value;

            // Update grid columns
            const grid = document.getElementById('barcodeGrid');
            grid.style.display = 'grid';
            grid.style.gridTemplateColumns = `repeat(${columnCount}, 1fr)`;
            grid.style.gap = `${marginSize}px`;

            // Update header visibility
            const printHeader = document.getElementById('printHeader');
            const headerTitleDisplay = document.getElementById('headerTitleDisplay');
            const headerSubtitleDisplay = document.getElementById('headerSubtitleDisplay');
            const headerDateDisplay = document.getElementById('headerDateDisplay');
            const headerTotalDisplay = document.getElementById('headerTotalDisplay');

            if (showHeader === 'no') {
                printHeader.style.display = 'none';
            } else {
                printHeader.style.display = 'block';
                headerTitleDisplay.textContent = headerTitle || 'SPARTTA POS';
                headerSubtitleDisplay.textContent = headerSubtitle || 'Daftar Barcode Sparepart';
                headerDateDisplay.style.display = showDate === 'yes' ? 'block' : 'none';
                headerTotalDisplay.style.display = 'block';
            }

            // Update barcode size
            const items = document.querySelectorAll('.barcode-item');
            items.forEach(item => {
                let imgSize = '100%';
                let fontSize = '0.75rem';
                let nameFontSize = '0.65rem';
                let padding = '12px';

                if (barcodeSize === 'small') {
                    imgSize = '70%';
                    fontSize = '0.65rem';
                    nameFontSize = '0.55rem';
                    padding = '8px';
                } else if (barcodeSize === 'large') {
                    imgSize = '100%';
                    fontSize = '0.9rem';
                    nameFontSize = '0.75rem';
                    padding = '18px';
                }

                item.style.padding = padding;
                const img = item.querySelector('img');
                if (img) img.style.maxWidth = imgSize;

                const codeDiv = item.querySelector('.barcode-code');
                if (codeDiv) codeDiv.style.fontSize = fontSize;

                const nameDiv = item.querySelector('.barcode-name');
                if (nameDiv) {
                    nameDiv.style.display = showName === 'yes' ? 'block' : 'none';
                    nameDiv.style.fontSize = nameFontSize;
                }
            });
        }

        function exportToWord() {
            const printContainer = document.getElementById('printContainer').cloneNode(true);
            const styleContent = `
                .print-header { text-align: center; margin-bottom: 20px; border-bottom: 1px solid #ccc; }
                .print-header h1 { color: #d4af37; }
                .barcode-grid { display: grid; gap: 15px; }
                .barcode-item { border: 1px solid #ddd; border-radius: 8px; padding: 12px; text-align: center; }
                .barcode-item img { max-width: 100%; }
                .barcode-code { font-family: monospace; font-size: 0.75rem; margin-top: 8px; }
                .barcode-name { font-size: 0.65rem; color: #666; }
            `;

            const style = document.createElement('style');
            style.textContent = styleContent;
            printContainer.prepend(style);

            const htmlContent = `<!DOCTYPE html>
            <html>
            <head><meta charset="UTF-8"><title>Barcode SPARTTA POS</title></head>
            <body>${printContainer.outerHTML}</body>
            </html>`;

            const blob = new Blob([htmlContent], {
                type: 'application/msword'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.href = url;
            link.download = `barcode_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.doc`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        async function exportToPDF() {
            const element = document.getElementById('printContainer');
            const settingsPanel = document.querySelector('.settings-panel');
            const previewHeader = document.querySelector('.preview-header');

            settingsPanel.style.display = 'none';
            previewHeader.style.display = 'none';

            try {
                const canvas = await html2canvas(element, {
                    scale: 2,
                    backgroundColor: '#ffffff',
                    logging: false
                });

                const imgData = canvas.toDataURL('image/png');
                const {
                    jsPDF
                } = window.jspdf;
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });

                const imgWidth = 210;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                let heightLeft = imgHeight;
                let position = 0;
                let page = 1;

                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pdf.internal.pageSize.height;

                while (heightLeft > 0) {
                    position = heightLeft - imgHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pdf.internal.pageSize.height;
                    page++;
                }

                pdf.save(`barcode_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.pdf`);
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal membuat PDF. Silakan coba lagi.');
            } finally {
                settingsPanel.style.display = 'block';
                previewHeader.style.display = 'flex';
            }
        }

        // Event listeners
        document.getElementById('columnCount').addEventListener('change', updateLayout);
        document.getElementById('barcodeSize').addEventListener('change', updateLayout);
        document.getElementById('marginSize').addEventListener('input', updateLayout);
        document.getElementById('showName').addEventListener('change', updateLayout);
        document.getElementById('showHeader').addEventListener('change', updateLayout);
        document.getElementById('headerTitle').addEventListener('input', updateLayout);
        document.getElementById('headerSubtitle').addEventListener('input', updateLayout);
        document.getElementById('showDate').addEventListener('change', updateLayout);
        document.getElementById('exportWordBtn').addEventListener('click', exportToWord);
        document.getElementById('exportPdfBtn').addEventListener('click', exportToPDF);

        // Layout buttons
        document.querySelectorAll('.layout-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.layout-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('columnCount').value = this.dataset.layout;
                updateLayout();
            });
        });

        // Initialize layout
        updateLayout();
    </script>
</body>

</html>
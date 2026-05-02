<?php

use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\StockCardController;
use App\Http\Controllers\PurchaseReceiptController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReportController;
use App\Models\Sparepart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =============================================
// Public Routes
// =============================================
Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Auth::routes();

// =============================================
// Authenticated Routes
// =============================================
Route::middleware(['auth'])->group(function () {

    // -----------------------------------------
    // Dashboard - Semua role bisa akses
    // -----------------------------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // -----------------------------------------
    // API Routes (AJAX)
    // -----------------------------------------
    Route::get('/api/dashboard/stats', [DashboardController::class, 'getStats'])->name('api.dashboard.stats');
    Route::post('/dashboard/update-target', [DashboardController::class, 'updateTarget'])->name('dashboard.update-target');
    // -----------------------------------------
    // Barcode Routes - Semua role bisa akses
    // -----------------------------------------
    Route::get('/spareparts/barcode/{code}', [SparepartController::class, 'showBarcode'])->name('spareparts.barcode');
    Route::get('/barcode/generate', function () {
        $spareparts = Sparepart::all();

        return view('barcode.generate', compact('spareparts'));
    })->name('barcode.generate');

    // -----------------------------------------
    // Barcode Print Multiple - Semua role bisa akses
    // -----------------------------------------
    Route::post('/barcode/print-multiple', [BarcodeController::class, 'printMultiple'])->name('barcode.print-multiple');
    Route::get('/barcode/{code}', [BarcodeController::class, 'show'])->name('barcode.show');
    Route::get('/barcode/{code}/download', [BarcodeController::class, 'download'])->name('barcode.download');

    // -----------------------------------------
    // POS / Kasir Routes - Semua role bisa akses
    // -----------------------------------------
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/transaction', [PosController::class, 'store'])->name('pos.store');
    Route::get('/pos/search', [PosController::class, 'search'])->name('pos.search');
    Route::get('/pos/search-by-barcode', [PosController::class, 'getByBarcode'])->name('pos.search-by-barcode');
    Route::get('/pos/generate-qris', [PosController::class, 'generateQRIS'])->name('pos.generate-qris');

    // =============================================
    // Stock Opname Routes
    // =============================================
    Route::middleware(['auth', 'role:super-admin,admin'])->prefix('stock')->name('stock.')->group(function () {
        // Stock Opname Routes
        Route::resource('opname', StockOpnameController::class);
        Route::get('opname/{id}/print', [StockOpnameController::class, 'printBeritaAcara'])->name('opname.print');
        Route::get('opname/{id}/export', [StockOpnameController::class, 'export'])->name('opname.export');

        // Stock Card (Kartu Persediaan)
        Route::get('card', [StockCardController::class, 'index'])->name('card.index');
        Route::get('card/{sparepart}', [StockCardController::class, 'show'])->name('card.show');

        // Purchase Receipt (Bukti Barang Masuk)
        Route::resource('purchase', PurchaseReceiptController::class);
        Route::post('purchase/{purchaseReceipt}/attachment', [PurchaseReceiptController::class, 'uploadAttachment'])->name('purchase.attachment');

        // Stock Adjustment (Penyesuaian Stok)
        Route::resource('adjustment', StockAdjustmentController::class);

        // Supplier
        Route::resource('supplier', SupplierController::class);
    });

    // Reports / Laporan Routes
    Route::middleware(['auth', 'role:super-admin,admin'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/stock-card', [ReportController::class, 'stockCard'])->name('stock-card');
        Route::get('/mutation', [ReportController::class, 'mutation'])->name('mutation');
        Route::get('/stock-opname', [ReportController::class, 'stockOpname'])->name('stock-opname');
        Route::get('/summary', [ReportController::class, 'summary'])->name('summary');
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
    });

    // -----------------------------------------
    // Settings / Profile - Semua role bisa akses
    // -----------------------------------------
    Route::get('/settings/profile', function () {
        return view('settings.profile');
    })->name('settings.profile');

    // -----------------------------------------
    // Notification Routes - Semua role bisa akses
    // -----------------------------------------
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/latest', [NotificationController::class, 'getLatest'])->name('latest');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // -----------------------------------------
    // Routes untuk Super Admin & Admin Only
    // (Management: Sparepart, Kategori)
    // -----------------------------------------
    Route::middleware(['role:super-admin,admin'])->group(function () {

        // Sparepart Management (CRUD Full)
        Route::resource('spareparts', SparepartController::class);

        // Category Management (CRUD)
        Route::resource('categories', CategoryController::class);
    });

    // -----------------------------------------
    // Routes untuk Super Admin Only
    // -----------------------------------------
    Route::middleware(['role:super-admin'])->group(function () {

        // User Management (Full CRUD)
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });
});

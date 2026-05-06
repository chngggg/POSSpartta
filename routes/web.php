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
use App\Http\Controllers\ProfileController;
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
    // STOCK (SEMUA ROLE BISA AKSES VIEW)
    // =============================================
    Route::prefix('stock')->name('stock.')->group(function () {

        // ✅ SEMUA ROLE (termasuk karyawan)
        Route::get('opname', [StockOpnameController::class, 'index'])->name('opname.index');
        Route::get('opname/{id}', [StockOpnameController::class, 'show'])->name('opname.show');
        Route::get('opname/{id}/print', [StockOpnameController::class, 'printBeritaAcara'])->name('opname.print');
        Route::get('opname/{id}/export', [StockOpnameController::class, 'export'])->name('opname.export');

        // ❌ KHUSUS ADMIN
        Route::middleware(['role:super-admin,admin'])->group(function () {
            Route::get('opname/create', [StockOpnameController::class, 'create'])->name('opname.create');
            Route::post('opname', [StockOpnameController::class, 'store'])->name('opname.store');
            Route::get('opname/{id}/edit', [StockOpnameController::class, 'edit'])->name('opname.edit');
            Route::put('opname/{id}', [StockOpnameController::class, 'update'])->name('opname.update');
            Route::delete('opname/{id}', [StockOpnameController::class, 'destroy'])->name('opname.destroy');
        });
    });

    // =============================================
    // REPORTS (SEMUA ROLE BISA AKSES)
    // =============================================
    Route::prefix('reports')->name('reports.')->group(function () {
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
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profile/upload-avatar', [App\Http\Controllers\ProfileController::class, 'uploadAvatar'])->name('profile.upload-avatar');
    });

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

<?php

use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\UserController;
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

    // -----------------------------------------
    // Stock Opname / Scan Barcode - Semua role bisa akses
    // -----------------------------------------
    Route::get('/stock-opname', function () {
        return view('stock.opname');
    })->name('stock.opname');

    // -----------------------------------------
    // Reports / Laporan - Semua role bisa akses
    // -----------------------------------------
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');

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

<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\Category;
use App\Services\SparepartCodeService;
use App\Services\BarcodeService;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    protected $barcodeService;

    public function __construct(BarcodeService $barcodeService)
    {
        $this->barcodeService = $barcodeService;

        // Hanya method yang memerlukan akses admin yang dibatasi
        // Method index, show, showBarcode, getLowStock, search TIDAK dibatasi
        $this->middleware(['auth', 'role:super-admin,admin'])->only([
            'create',
            'store',
            'edit',
            'update',
            'destroy',
            'updateStock',
            'bulkImport',
            'export'
        ]);
    }

    /**
     * Display a listing of spareparts (Semua role bisa akses)
     */
    public function index()
    {
        $spareparts = Sparepart::with('category')->paginate(10);
        return view('spareparts.index', compact('spareparts'));
    }

    /**
     * Show form create sparepart (Hanya Admin & Super Admin)
     */
    public function create()
    {
        $categories = Category::all();
        $autoCode = SparepartCodeService::generateCode();
        return view('spareparts.create', compact('categories', 'autoCode'));
    }

    /**
     * Store new sparepart (Hanya Admin & Super Admin)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:spareparts',
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'purchase_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'stock' => 'required|integer',
            'min_stock' => 'required|integer',
            'brand' => 'nullable',
            'location_rack' => 'nullable',
        ]);

        $sparepart = Sparepart::create($validated);

        // Generate dan simpan barcode otomatis
        $this->barcodeService->saveToStorage($sparepart->code, $sparepart->code . '.png');

        // Cek dan kirim notifikasi jika stok menipis
        if ($sparepart->isLowStock()) {
            NotificationController::sendLowStockAlert($sparepart);
        }

        return redirect()->route('spareparts.index')
            ->with('success', 'Sparepart berhasil ditambahkan. Kode: ' . $sparepart->code);
    }

    /**
     * Display detail sparepart (Semua role bisa akses)
     */
    public function show(Sparepart $sparepart)
    {
        $barcodeHtml = $this->barcodeService->generateForBlade($sparepart->code);
        return view('spareparts.show', compact('sparepart', 'barcodeHtml'));
    }

    /**
     * Show form edit sparepart (Hanya Admin & Super Admin)
     */
    public function edit(Sparepart $sparepart)
    {
        $categories = Category::all();
        return view('spareparts.edit', compact('sparepart', 'categories'));
    }

    /**
     * Update sparepart (Hanya Admin & Super Admin)
     */
    public function update(Request $request, Sparepart $sparepart)
    {
        // Simpan stok lama sebelum update
        $oldStock = $sparepart->stock;
        $oldMinStock = $sparepart->min_stock;

        $validated = $request->validate([
            'code' => 'required|unique:spareparts,code,' . $sparepart->id,
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'purchase_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'stock' => 'required|integer',
            'min_stock' => 'required|integer',
            'brand' => 'nullable',
            'location_rack' => 'nullable',
        ]);

        $sparepart->update($validated);

        // Cek apakah stok berubah menjadi menipis
        if ($sparepart->isLowStock() && $oldStock > $sparepart->min_stock) {
            NotificationController::sendLowStockAlert($sparepart);
        }

        // Jika stok sudah diisi kembali di atas minimum, kirim notifikasi pemulihan
        if (!$sparepart->isLowStock() && $oldStock <= $oldMinStock) {
            $this->sendStockRestoredNotification($sparepart);
        }

        // Update barcode jika kode berubah
        if ($oldStock !== $sparepart->code) {
            $this->barcodeService->saveToStorage($sparepart->code, $sparepart->code . '.png');
        }

        return redirect()->route('spareparts.index')
            ->with('success', 'Sparepart berhasil diupdate');
    }

    /**
     * Delete sparepart (Hanya Admin & Super Admin)
     */
    public function destroy(Sparepart $sparepart)
    {
        $sparepart->delete();
        return redirect()->route('spareparts.index')
            ->with('success', 'Sparepart berhasil dihapus');
    }

    /**
     * Display barcode image langsung (Semua role bisa akses)
     */
    public function showBarcode($code)
    {
        return $this->barcodeService->generateAsImage($code);
    }

    /**
     * Update stock only (quick stock adjustment) - Hanya Admin & Super Admin
     */
    public function updateStock(Request $request, Sparepart $sparepart)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
            'type' => 'required|in:add,subtract,set'
        ]);

        $oldStock = $sparepart->stock;

        switch ($request->type) {
            case 'add':
                $newStock = $sparepart->stock + $request->stock;
                break;
            case 'subtract':
                $newStock = $sparepart->stock - $request->stock;
                break;
            default:
                $newStock = $request->stock;
        }

        // Pastikan stok tidak negatif
        if ($newStock < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak boleh negatif'
            ], 422);
        }

        $sparepart->update(['stock' => $newStock]);

        // Kirim notifikasi jika stok menipis
        if ($sparepart->isLowStock() && $oldStock > $sparepart->min_stock) {
            NotificationController::sendLowStockAlert($sparepart);
        }

        // Kirim notifikasi jika stok pulih
        if (!$sparepart->isLowStock() && $oldStock <= $sparepart->min_stock) {
            $this->sendStockRestoredNotification($sparepart);
        }

        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil diupdate',
            'new_stock' => $newStock,
            'is_low_stock' => $sparepart->isLowStock()
        ]);
    }

    /**
     * Send notification when stock is restored
     */
    protected function sendStockRestoredNotification($sparepart)
    {
        $admins = \App\Models\User::whereHas('role', function ($q) {
            $q->whereIn('slug', ['super-admin', 'admin']);
        })->get();

        foreach ($admins as $admin) {
            NotificationController::create(
                $admin->id,
                'Stok Telah Dipulihkan! ✓',
                "Sparepart {$sparepart->name} ({$sparepart->code}) stok sekarang {$sparepart->stock} pcs. Stok sudah aman.",
                'success',
                route('spareparts.edit', $sparepart),
                'fa-check-circle'
            );
        }
    }

    /**
     * Bulk import spareparts (Hanya Admin & Super Admin)
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls'
        ]);

        // Implementation for bulk import
        // This will be added later if needed

        return redirect()->route('spareparts.index')
            ->with('success', 'Bulk import berhasil');
    }

    /**
     * Export spareparts to CSV/Excel (Hanya Admin & Super Admin)
     */
    public function export()
    {
        $spareparts = Sparepart::with('category')->get();

        // Implementation for export
        // This will be added later if needed

        return redirect()->route('spareparts.index')
            ->with('success', 'Export berhasil');
    }

    /**
     * Get low stock spareparts (for API) - Semua role bisa akses
     */
    public function getLowStock()
    {
        $lowStockItems = Sparepart::whereColumn('stock', '<=', 'min_stock')
            ->with('category')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $lowStockItems,
            'count' => $lowStockItems->count()
        ]);
    }

    /**
     * Search spareparts by code or name (Semua role bisa akses)
     */
    public function search(Request $request)
    {
        $search = $request->get('q');

        $spareparts = Sparepart::where('code', 'like', "%{$search}%")
            ->orWhere('name', 'like', "%{$search}%")
            ->with('category')
            ->limit(10)
            ->get();

        if ($request->ajax()) {
            return response()->json($spareparts);
        }

        return view('spareparts.search', compact('spareparts', 'search'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display POS page
     */
    public function index()
    {
        $categories = Category::where('is_active', true)->get();
        $spareparts = Sparepart::where('is_active', true)
            ->where('stock', '>', 0)
            ->with('category')
            ->get();

        return view('pos.index', compact('categories', 'spareparts'));
    }

    /**
     * Search sparepart by code or name (AJAX)
     */
    public function search(Request $request)
    {
        $search = $request->get('q');

        $spareparts = Sparepart::where('is_active', true)
            ->where('stock', '>', 0)
            ->where(function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->with('category')
            ->limit(10)
            ->get();

        return response()->json($spareparts);
    }

    /**
     * Get sparepart detail by barcode scan (AJAX)
     */
    public function getByBarcode(Request $request)
    {
        $barcode = $request->get('barcode');

        $sparepart = Sparepart::where('code', $barcode)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->with('category')
            ->first();

        if (!$sparepart) {
            return response()->json([
                'success' => false,
                'message' => 'Sparepart tidak ditemukan atau stok habis'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $sparepart
        ]);
    }

    /**
     * Process transaction (Store to database)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:spareparts,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total_amount' => 'required|numeric',
            'payment_amount' => 'required|numeric',
            'change_amount' => 'required|numeric',
            'payment_method' => 'required|in:cash,qris,transfer',
            'bank' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $transactionId = 'TRX-' . date('Ymd') . '-' . strtoupper(uniqid());

            // Kurangi stok untuk setiap item
            foreach ($validated['items'] as $item) {
                $sparepart = Sparepart::find($item['id']);

                if (!$sparepart) {
                    throw new \Exception("Sparepart tidak ditemukan");
                }

                if ($sparepart->stock < $item['quantity']) {
                    throw new \Exception("Stok {$sparepart->name} tidak mencukupi (tersisa {$sparepart->stock})");
                }

                $sparepart->decrement('stock', $item['quantity']);
            }

            // Simpan transaksi ke database
            $transaction = Transaction::create([
                'transaction_id' => $transactionId,
                'user_id' => auth()->id(),
                'items' => $validated['items'],
                'total_amount' => $validated['total_amount'],
                'payment_amount' => $validated['payment_amount'],
                'change_amount' => $validated['change_amount'],
                'payment_method' => $validated['payment_method'],
                'bank' => $validated['payment_method'] === 'transfer' ? $validated['bank'] : null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'transaction_id' => $transactionId,
                'transaction' => $transaction
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate QR Code for payment
     */
    public function generateQRIS(Request $request)
    {
        try {
            $amount = $request->get('amount', 0);
            $transactionId = 'QRIS-' . date('Ymd') . '-' . strtoupper(uniqid());

            $qrisData = json_encode([
                'merchant_id' => 'SPARTTA001',
                'merchant_name' => 'SPARTTA POS',
                'merchant_city' => 'Semarang',
                'amount' => (int) $amount,
                'transaction_id' => $transactionId,
                'timestamp' => now()->toIso8601String()
            ]);

            $qrCode = base64_encode(
                QrCode::format('png')->size(250)->errorCorrection('H')->generate($qrisData)
            );

            return response()->json([
                'success' => true,
                'qr_code' => $qrCode,
                'transaction_id' => $transactionId,
                'amount' => $amount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate QR Code: ' . $e->getMessage()
            ], 500);
        }
    }
}

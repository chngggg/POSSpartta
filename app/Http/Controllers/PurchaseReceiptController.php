<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\Supplier;
use App\Models\PurchaseReceipt;
use App\Models\PurchaseReceiptItem;
use App\Models\StockCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin,admin']);
    }

    public function index()
    {
        $receipts = PurchaseReceipt::with('supplier', 'creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('stock.purchase-index', compact('receipts'));
    }

    public function create()
    {
        $spareparts = Sparepart::all();
        $suppliers = Supplier::all();
        return view('stock.purchase-create', compact('spareparts', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receipt_date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'invoice_number' => 'nullable|string',
            'items' => 'required|array',
            'items.*.sparepart_id' => 'required|exists:spareparts,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $receiptNumber = PurchaseReceipt::generateNumber();

            $receipt = PurchaseReceipt::create([
                'receipt_number' => $receiptNumber,
                'receipt_date' => $request->receipt_date,
                'supplier_id' => $request->supplier_id,
                'invoice_number' => $request->invoice_number,
                'notes' => $request->notes,
                'status' => 'approved',
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                PurchaseReceiptItem::create([
                    'purchase_receipt_id' => $receipt->id,
                    'sparepart_id' => $item['sparepart_id'],
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                ]);

                // Update sparepart stock
                $sparepart = Sparepart::find($item['sparepart_id']);
                $oldStock = $sparepart->stock;
                $sparepart->increment('stock', $item['quantity']);

                // Record to stock card
                StockCard::record(
                    $sparepart->id,
                    'in',
                    $item['quantity'],
                    "Barang masuk dari purchase order {$receiptNumber}",
                    $receipt->id
                );
            }

            DB::commit();

            return redirect()->route('stock.purchase.index')
                ->with('success', 'Bukti barang masuk berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(PurchaseReceipt $purchaseReceipt)
    {
        $purchaseReceipt->load('items.sparepart', 'supplier', 'creator');
        return view('stock.purchase-show', compact('purchaseReceipt'));
    }

    public function destroy(PurchaseReceipt $purchaseReceipt)
    {
        $purchaseReceipt->delete();
        return redirect()->route('stock.purchase.index')
            ->with('success', 'Bukti barang masuk berhasil dihapus!');
    }

    public function uploadAttachment(Request $request, PurchaseReceipt $purchaseReceipt)
    {
        $request->validate([
            'attachment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $path = $request->file('attachment')->store('purchase-receipts', 'public');
        $purchaseReceipt->update(['attachment_path' => $path]);

        return redirect()->back()->with('success', 'File berhasil diupload!');
    }
}

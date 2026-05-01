<?php

namespace App\Http\Controllers;

use App\Services\BarcodeService;
use App\Models\Sparepart;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    protected $barcodeService;

    public function __construct(BarcodeService $barcodeService)
    {
        $this->barcodeService = $barcodeService;
        $this->middleware('auth');
    }

    /**
     * Display barcode page for printing
     */
    public function show($code)
    {
        $sparepart = Sparepart::where('code', $code)->firstOrFail();
        // Gunakan base64 PNG untuk print yang lebih reliable
        $barcodeBase64 = $this->barcodeService->generateAsBase64($code, 3, 80);

        return view('barcode.show', compact('sparepart', 'barcodeBase64'));
    }

    /**
     * Generate barcode image for download
     */
    public function download($code)
    {
        $sparepart = Sparepart::where('code', $code)->firstOrFail();
        $barcode = $this->barcodeService->generateAsImage($code, 3, 80);
        return $barcode->header('Content-Disposition', 'attachment; filename="barcode-' . $code . '.png"');
    }

    /**
     * Print multiple barcodes
     */
    public function printMultiple(Request $request)
    {
        $codes = $request->get('codes', []);

        if (empty($codes)) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Pilih sparepart yang akan dicetak barcodenya']);
            }
            return redirect()->back()->with('error', 'Pilih sparepart yang akan dicetak barcodenya');
        }

        $spareparts = Sparepart::whereIn('code', $codes)->get();
        $barcodes = [];

        foreach ($spareparts as $sparepart) {
            $barcodes[$sparepart->code] = $this->barcodeService->generateAsBase64($sparepart->code, 2, 60);
        }

        return view('barcode.print-multiple', compact('spareparts', 'barcodes'));
    }

    /**
     * Generate barcode in new window
     */
    public function generate($code)
    {
        return $this->barcodeService->generateAsImage($code, 3, 80);
    }
}

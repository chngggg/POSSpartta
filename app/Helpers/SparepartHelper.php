<?php

namespace App\Helpers;

use App\Services\SparepartCodeService;
use App\Services\BarcodeService;

class SparepartHelper
{
    /**
     * Generate kode sparepart otomatis
     */
    public static function generateCode($prefix = 'SPR', $padding = 3)
    {
        return SparepartCodeService::generateCode($prefix, $padding);
    }

    /**
     * Generate barcode dari kode sparepart
     */
    public static function generateBarcode($code, $asHtml = false)
    {
        $barcodeService = new BarcodeService();

        if ($asHtml) {
            return $barcodeService->generateForBlade($code);
        }

        return $barcodeService->generateAsImage($code);
    }
}

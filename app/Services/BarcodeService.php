<?php

namespace App\Services;

use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Illuminate\Support\Facades\Storage;

class BarcodeService
{
    protected $generatorPNG;
    protected $generatorHTML;

    public function __construct()
    {
        $this->generatorPNG = new BarcodeGeneratorPNG();
        $this->generatorHTML = new BarcodeGeneratorHTML();
    }

    /**
     * Generate barcode sebagai PNG image (return response)
     * 
     * @param string $code
     * @param int $widthFactor (1-10, default 2)
     * @param int $height (default 50)
     * @return \Illuminate\Http\Response
     */
    public function generateAsImage($code, int $widthFactor = 2, int $height = 50)
    {
        $barcode = $this->generatorPNG->getBarcode(
            $code,
            $this->generatorPNG::TYPE_CODE_128,
            $widthFactor,
            $height
        );

        return response($barcode)->header('Content-Type', 'image/png');
    }

    /**
     * Generate barcode sebagai base64 PNG untuk print
     * 
     * @param string $code
     * @param int $widthFactor
     * @param int $height
     * @return string
     */
    public function generateAsBase64($code, int $widthFactor = 2, int $height = 50): string
    {
        $barcode = $this->generatorPNG->getBarcode(
            $code,
            $this->generatorPNG::TYPE_CODE_128,
            $widthFactor,
            $height
        );

        return 'data:image/png;base64,' . base64_encode($barcode);
    }

    /**
     * Generate barcode dan simpan ke storage
     * 
     * @param string $code
     * @param string $filename
     * @param int $widthFactor
     * @param int $height
     * @return string Path file yang disimpan
     */
    public function saveToStorage($code, $filename = null, int $widthFactor = 2, int $height = 50): string
    {
        $barcode = $this->generatorPNG->getBarcode(
            $code,
            $this->generatorPNG::TYPE_CODE_128,
            $widthFactor,
            $height
        );

        if (!$filename) {
            $filename = 'barcode-' . $code . '.png';
        }

        $path = 'barcodes/' . $filename;
        Storage::disk('public')->put($path, $barcode);

        return Storage::url($path);
    }

    /**
     * Generate barcode untuk ditampilkan di Blade (HTML)
     * 
     * @param string $code
     * @param int $widthFactor
     * @param int $height
     * @return string HTML barcode
     */
    public function generateForBlade($code, int $widthFactor = 2, int $height = 50): string
    {
        return $this->generatorHTML->getBarcode(
            $code,
            $this->generatorHTML::TYPE_CODE_128,
            $widthFactor,
            $height
        );
    }

    /**
     * Generate barcode dengan custom warna
     * 
     * @param string $code
     * @param array $color RGB array [R, G, B]
     * @return \Illuminate\Http\Response
     */
    public function generateWithColor($code, array $color = [0, 0, 0])
    {
        $colorString = implode(',', $color);
        $barcode = $this->generatorPNG->getBarcode(
            $code,
            $this->generatorPNG::TYPE_CODE_128,
            2,
            50,
            $colorString
        );

        return response($barcode)->header('Content-Type', 'image/png');
    }

    /**
     * Batch generate multiple barcodes
     * 
     * @param array $codes
     * @return array
     */
    public function batchGenerate(array $codes): array
    {
        $results = [];
        foreach ($codes as $code) {
            $results[$code] = $this->saveToStorage($code);
        }
        return $results;
    }
}

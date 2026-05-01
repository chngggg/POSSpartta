<?php

namespace App\Services;

use App\Models\Sparepart;
use Illuminate\Support\Str;

class SparepartCodeService
{
    /**
     * Format kode sparepart: SPR-001, SPR-002, dst.
     * 
     * @param string $prefix - Prefix kode (default: SPR)
     * @param int $padding - Jumlah digit angka (default: 3)
     * @return string
     */
    public static function generateCode(string $prefix = 'SPR', int $padding = 3): string
    {
        // Ambil kode terakhir berdasarkan prefix
        $lastCode = Sparepart::where('code', 'like', $prefix . '-%')
            ->orderBy('code', 'desc')
            ->first();

        if (!$lastCode) {
            // Jika belum ada, mulai dari 1
            $number = 1;
        } else {
            // Extract angka dari kode terakhir
            $lastNumber = (int) Str::afterLast($lastCode->code, '-');
            $number = $lastNumber + 1;
        }

        // Format angka dengan leading zeros (001, 002, dst)
        $formattedNumber = str_pad($number, $padding, '0', STR_PAD_LEFT);

        return $prefix . '-' . $formattedNumber;
    }

    /**
     * Generate kode dengan custom format
     * Contoh: SPR-2026-001 (tahun + nomor urut)
     * 
     * @param string $prefix
     * @return string
     */
    public static function generateCodeWithYear(string $prefix = 'SPR'): string
    {
        $year = date('Y');
        $lastCode = Sparepart::where('code', 'like', $prefix . '-' . $year . '-%')
            ->orderBy('code', 'desc')
            ->first();

        if (!$lastCode) {
            $number = 1;
        } else {
            $lastNumber = (int) Str::afterLast($lastCode->code, '-');
            $number = $lastNumber + 1;
        }

        $formattedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);

        return $prefix . '-' . $year . '-' . $formattedNumber;
    }

    /**
     * Validasi apakah kode sudah digunakan
     * 
     * @param string $code
     * @return bool
     */
    public static function isCodeExists(string $code): bool
    {
        return Sparepart::where('code', $code)->exists();
    }

    /**
     * Generate multiple codes sekaligus
     * 
     * @param int $count
     * @param string $prefix
     * @return array
     */
    public static function generateMultipleCodes(int $count, string $prefix = 'SPR'): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = self::generateCode($prefix);
        }
        return $codes;
    }
}

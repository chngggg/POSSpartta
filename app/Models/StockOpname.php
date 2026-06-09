<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    protected $fillable = [
        'opname_number',
        'opname_date',
        'period',
        'created_by',
        'status',
        'notes'
    ];

    protected $casts = [
        'opname_date' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    public static function generateNumber()
    {
        $year = date('Y');
        $month = date('m');
        $prefix = 'OPN-' . $year . $month . '-';

        // Cari nomor terakhir dengan prefix yang sama
        $last = self::where('opname_number', 'LIKE', $prefix . '%')
            ->orderBy('opname_number', 'desc')
            ->first();

        if ($last) {
            // Ambil 4 digit terakhir
            $lastNumber = (int) substr($last->opname_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        $candidate = $prefix . $newNumber;

        // Double check untuk memastikan benar-benar unik
        while (self::where('opname_number', $candidate)->exists()) {
            $newNumber = str_pad((int)$newNumber + 1, 4, '0', STR_PAD_LEFT);
            $candidate = $prefix . $newNumber;
        }

        return $candidate;
    }
}

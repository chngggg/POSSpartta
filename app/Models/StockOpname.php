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
        'verified_by',
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

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    public static function generateNumber()
    {
        $year = date('Y');
        $month = date('m');
        $last = self::whereYear('created_at', $year)->count() + 1;
        return 'OPN-' . $year . $month . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}

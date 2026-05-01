<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockAdjustment extends Model
{
    protected $fillable = [
        'adjustment_number',
        'adjustment_date',
        'type',
        'reason',
        'attachment_path',
        'created_by',
        'approved_by',
        'status'
    ];

    protected $casts = [
        'adjustment_date' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }

    public static function generateNumber()
    {
        $year = date('Y');
        $month = date('m');
        $last = self::whereYear('created_at', $year)->count() + 1;
        return 'ADJ-' . $year . $month . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}

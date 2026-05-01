<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseReceipt extends Model
{
    protected $fillable = [
        'receipt_number',
        'receipt_date',
        'supplier_id',
        'invoice_number',
        'notes',
        'attachment_path',
        'status',
        'created_by'
    ];

    protected $casts = [
        'receipt_date' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseReceiptItem::class);
    }

    public static function generateNumber()
    {
        $year = date('Y');
        $month = date('m');
        $last = self::whereYear('created_at', $year)->count() + 1;
        return 'PO-' . $year . $month . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'icon',
        'link',
        'is_read',
        'data'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update(['is_read' => true]);
        }
    }

    public function getIconAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return match ($this->type) {
            'success' => 'fa-check-circle',
            'warning' => 'fa-exclamation-triangle',
            'danger' => 'fa-times-circle',
            default => 'fa-info-circle',
        };
    }

    public function getColorAttribute()
    {
        return match ($this->type) {
            'success' => '#2ecc71',
            'warning' => '#f39c12',
            'danger' => '#e74c3c',
            default => '#d4af37',
        };
    }
}

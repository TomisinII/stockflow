<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'product_id',
        'adjustment_type',
        'quantity',
        'reason',
        'reference',
        'notes',
        'adjusted_by',
        'adjustment_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'adjustment_date' => 'date',
        'quantity' => 'integer',
    ];

    /**
     * Get the product this adjustment belongs to
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who made this adjustment
     */
    public function adjuster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    /**
     * Scope to get stock in adjustments
     */
    public function scopeStockIn($query)
    {
        return $query->where('adjustment_type', 'in');
    }

    /**
     * Scope to get stock out adjustments
     */
    public function scopeStockOut($query)
    {
        return $query->where('adjustment_type', 'out');
    }

    /**
     * Scope to get corrections
     */
    public function scopeCorrection($query)
    {
        return $query->where('adjustment_type', 'correction');
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('adjustment_date', [$startDate, $endDate]);
    }

    /**
     * Scope to get recent adjustments
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('adjustment_date', '>=', now()->subDays($days));
    }

    /**
     * Get formatted adjustment type for display
     */
    public function getFormattedTypeAttribute(): string
    {
        return match($this->adjustment_type) {
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'correction' => 'Correction',
            default => ucfirst($this->adjustment_type),
        };
    }

    /**
     * Get badge color based on adjustment type
     */
    public function getTypeBadgeAttribute(): array
    {
        return match($this->adjustment_type) {
            'in' => [
                'label' => 'Stock In',
                'class' => 'bg-green-100 text-green-800',
            ],
            'out' => [
                'label' => 'Stock Out',
                'class' => 'bg-red-100 text-red-800',
            ],
            'correction' => [
                'label' => 'Correction',
                'class' => 'bg-blue-100 text-blue-800',
            ],
            default => [
                'label' => ucfirst($this->adjustment_type),
                'class' => 'bg-gray-100 text-gray-800',
            ],
        };
    }

    /**
     * Get absolute quantity value
     * (In case quantity is stored as negative for 'out')
     */
    public function getAbsoluteQuantityAttribute(): int
    {
        return abs($this->quantity);
    }
}

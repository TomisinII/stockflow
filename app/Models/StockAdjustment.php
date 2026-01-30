<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
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

    protected $casts = [
        'adjustment_date' => 'datetime',
        'quantity' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function adjuster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    // Scopes
    public function scopeStockIn($query)
    {
        return $query->where('adjustment_type', 'in');
    }

    public function scopeStockOut($query)
    {
        return $query->where('adjustment_type', 'out');
    }

    public function scopeCorrection($query)
    {
        return $query->where('adjustment_type', 'correction');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('reference', 'like', '%' . $search . '%')
                ->orWhere('reason', 'like', '%' . $search . '%')
                ->orWhereHas('product', function($productQuery) use ($search) {
                    $productQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('sku', 'like', '%' . $search . '%');
                });
        });
    }

    // Accessors
    public function getFormattedTypeAttribute(): string
    {
        return match($this->adjustment_type) {
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'correction' => 'Correction',
            default => ucfirst($this->adjustment_type),
        };
    }

    public function getTypeBadgeAttribute(): array
    {
        return match($this->adjustment_type) {
            'in' => [
                'label' => 'Stock In',
                'icon' => '↑',
                'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800',
            ],
            'out' => [
                'label' => 'Stock Out',
                'icon' => '↓',
                'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800',
            ],
            'correction' => [
                'label' => 'Correction',
                'icon' => '↻',
                'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400 border border-gray-200 dark:border-gray-600',
            ],
            default => [
                'label' => ucfirst($this->adjustment_type),
                'icon' => '',
                'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400 border border-gray-200 dark:border-gray-600',
            ],
        };
    }

    public function getAbsoluteQuantityAttribute(): int
    {
        return abs($this->quantity);
    }

    public function getFormattedQuantityAttribute(): string
    {
        if ($this->adjustment_type === 'in') {
            return '+' . $this->absolute_quantity;
        } elseif ($this->adjustment_type === 'out') {
            return '-' . $this->absolute_quantity;
        }

        return ($this->quantity >= 0 ? '+' : '') . $this->quantity;
    }

    public function getQuantityColorAttribute(): string
    {
        if ($this->adjustment_type === 'in') {
            return 'text-green-600 dark:text-green-400';
        } elseif ($this->adjustment_type === 'out') {
            return 'text-red-600 dark:text-red-400';
        }

        return $this->quantity >= 0
            ? 'text-green-600 dark:text-green-400'
            : 'text-red-600 dark:text-red-400';
    }

    public function getStockBeforeAttribute(): int
    {
        if (!$this->product) {
            return 0;
        }
        return $this->product->current_stock - $this->quantity;
    }

    public function getStockAfterAttribute(): int
    {
        if (!$this->product) {
            return 0;
        }
        return $this->product->current_stock;
    }

    public function getReasonDisplayAttribute(): string
    {
        return match($this->reason) {
            'purchase' => 'Purchase Order',
            'sale' => 'Sale',
            'damaged' => 'Damaged',
            'expired' => 'Expired',
            'theft' => 'Theft',
            'stocktake' => 'Stocktake',
            'return' => 'Customer Return',
            'transfer_in' => 'Transfer In',
            'transfer_out' => 'Transfer Out',
            default => ucfirst(str_replace('_', ' ', $this->reason)),
        };
    }
}

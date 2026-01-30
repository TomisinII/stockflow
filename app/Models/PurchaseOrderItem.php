<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity_ordered',
        'quantity_received',
        'unit_cost',
        'subtotal',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'quantity_ordered' => 'integer',
        'quantity_received' => 'integer',
        'unit_cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Boot method - automatically calculate subtotal
     */
    protected static function booted(): void
    {
        // Before saving, calculate subtotal
        static::saving(function (PurchaseOrderItem $item) {
            $item->subtotal = $item->quantity_ordered * $item->unit_cost;
        });

        // After saving, recalculate PO total
        static::saved(function (PurchaseOrderItem $item) {
            $item->purchaseOrder->recalculateTotal();
        });

        // After deleting, recalculate PO total
        static::deleted(function (PurchaseOrderItem $item) {
            $item->purchaseOrder->recalculateTotal();
        });
    }

    /**
     * Get the purchase order this item belongs to
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the product for this line item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if item is fully received
     */
    public function isFullyReceived(): bool
    {
        return $this->quantity_received >= $this->quantity_ordered;
    }

    /**
     * Check if item is partially received
     */
    public function isPartiallyReceived(): bool
    {
        return $this->quantity_received > 0 && $this->quantity_received < $this->quantity_ordered;
    }

    /**
     * Check if item has not been received
     */
    public function isNotReceived(): bool
    {
        return $this->quantity_received === 0;
    }

    /**
     * Get remaining quantity to receive
     */
    public function getRemainingQuantityAttribute(): int
    {
        return max(0, $this->quantity_ordered - $this->quantity_received);
    }

    /**
     * Get receiving progress percentage for this item
     */
    public function getReceivingProgressAttribute(): float
    {
        if ($this->quantity_ordered === 0) {
            return 0;
        }

        return round(($this->quantity_received / $this->quantity_ordered) * 100, 2);
    }

    /**
     * Get formatted unit cost
     */
    public function getFormattedUnitCostAttribute(): string
    {
        return '₦' . number_format($this->unit_cost, 2);
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return '₦' . number_format($this->subtotal, 2);
    }

    /**
     * Get receiving status badge
     */
    public function getReceivingStatusBadgeAttribute(): array
    {
        if ($this->isFullyReceived()) {
            return [
                'label' => 'Fully Received',
                'class' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            ];
        } elseif ($this->isPartiallyReceived()) {
            return [
                'label' => 'Partially Received',
                'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
            ];
        } else {
            return [
                'label' => 'Not Received',
                'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            ];
        }
    }

    /**
     * Receive a specific quantity
     *
     * @param int $quantity Quantity to receive
     * @return bool Success status
     */
    public function receiveQuantity(int $quantity): bool
    {
        if ($quantity <= 0) {
            return false;
        }

        $newQuantityReceived = $this->quantity_received + $quantity;

        // Don't allow receiving more than ordered
        if ($newQuantityReceived > $this->quantity_ordered) {
            $newQuantityReceived = $this->quantity_ordered;
        }

        return $this->update([
            'quantity_received' => $newQuantityReceived,
        ]);
    }

    /**
     * Set received quantity to a specific value
     *
     * @param int $quantity New received quantity
     * @return bool Success status
     */
    public function setReceivedQuantity(int $quantity): bool
    {
        if ($quantity < 0 || $quantity > $this->quantity_ordered) {
            return false;
        }

        return $this->update([
            'quantity_received' => $quantity,
        ]);
    }

    /**
     * Get pending quantity
     */
    public function getPendingQuantityAttribute(): int
    {
        return $this->quantity_ordered - ($this->quantity_received ?? 0);
    }

    /**
     * Mark as fully received
     */
    public function markAsFullyReceived(): bool
    {
        return $this->update([
            'quantity_received' => $this->quantity_ordered,
        ]);
    }

    /**
     * Calculate the value of remaining items
     */
    public function getRemainingValueAttribute(): float
    {
        return $this->remaining_quantity * $this->unit_cost;
    }

    /**
     * Get received percentage
     */
    public function getReceivedPercentageAttribute(): float
    {
        if ($this->quantity_ordered === 0) {
            return 0;
        }

        return round((($this->quantity_received ?? 0) / $this->quantity_ordered) * 100, 2);
    }

    /**
     * Get formatted received status
     */
    public function getReceivedStatusAttribute(): string
    {
        if ($this->quantity_received === null) {
            return 'Not Received';
        }

        if ($this->isFullyReceived()) {
            return 'Fully Received';
        }

        if ($this->quantity_received > 0) {
            return 'Partially Received';
        }

        return 'Not Received';
    }

    /**
     * Calculate the value of received items
     */
    public function getReceivedValueAttribute(): float
    {
        return $this->quantity_received * $this->unit_cost;
    }

    /**
     * Get formatted remaining value
     */
    public function getFormattedRemainingValueAttribute(): string
    {
        return '₦' . number_format($this->remaining_value, 2);
    }

    /**
     * Get formatted received value
     */
    public function getFormattedReceivedValueAttribute(): string
    {
        return '₦' . number_format($this->received_value, 2);
    }

    /**
     * Check if the unit cost differs from product's current cost price
     */
    public function hasPriceDifference(): bool
    {
        return $this->unit_cost != $this->product->cost_price;
    }

    /**
     * Get price difference from product's current cost price
     */
    public function getPriceDifferenceAttribute(): float
    {
        return $this->unit_cost - $this->product->cost_price;
    }

    /**
     * Get formatted price difference
     */
    public function getFormattedPriceDifferenceAttribute(): string
    {
        $diff = $this->price_difference;
        $sign = $diff > 0 ? '+' : '';
        return $sign . '₦' . number_format(abs($diff), 2);
    }

    /**
     * Scope to get fully received items
     */
    public function scopeFullyReceived($query)
    {
        return $query->whereColumn('quantity_received', '>=', 'quantity_ordered');
    }

    /**
     * Scope to get partially received items
     */
    public function scopePartiallyReceived($query)
    {
        return $query->where('quantity_received', '>', 0)
            ->whereColumn('quantity_received', '<', 'quantity_ordered');
    }

    /**
     * Scope to get not received items
     */
    public function scopeNotReceived($query)
    {
        return $query->where('quantity_received', 0);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class PurchaseOrder extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'po_number',
        'supplier_id',
        'order_date',
        'expected_delivery_date',
        'status',
        'total_amount',
        'notes',
        'created_by',
        'received_by',
        'received_at',
        'sent_at', // Add this for better timeline tracking
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'received_at' => 'datetime',
        'sent_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Convert empty strings to null for nullable fields
            if ($model->expected_delivery_date === '') {
                $model->expected_delivery_date = null;
            }
            if ($model->notes === '') {
                $model->notes = null;
            }

            // Auto-set sent_at timestamp when status changes to sent
            if ($model->isDirty('status') && $model->status === 'sent' && !$model->sent_at) {
                $model->sent_at = now();
            }
        });
    }

    /**
     * Get the supplier for this purchase order
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the user who created this PO
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who received this PO
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get all line items for this purchase order
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Scope to get draft POs
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope to get sent POs
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope to get received POs
     */
    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    /**
     * Scope to get cancelled POs
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope to get pending POs (draft or sent)
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'sent']);
    }

    /**
     * Check if PO can be edited
     * Only drafts can be edited
     */
    public function canBeEdited(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if PO can be sent
     */
    public function canBeSent(): bool
    {
        return $this->status === 'draft' && $this->items()->count() > 0;
    }

    /**
     * Check if PO can be received
     */
    public function canBeReceived(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Check if PO can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['draft', 'sent']);
    }

    /**
     * Check if PO is overdue
     */
    public function isOverdue(): bool
    {
        if (!$this->expected_delivery_date || $this->status === 'received') {
            return false;
        }

        return now()->gt($this->expected_delivery_date);
    }

    /**
     * Get days until delivery (negative if overdue)
     */
    public function getDaysUntilDelivery(): ?int
    {
        if (!$this->expected_delivery_date || $this->status === 'received') {
            return null;
        }

        return now()->diffInDays($this->expected_delivery_date, false);
    }

    /**
     * Get formatted delivery status
     */
    public function getDeliveryStatusAttribute(): ?string
    {
        $days = $this->getDaysUntilDelivery();

        if ($days === null) {
            return null;
        }

        if ($days < 0) {
            return 'Overdue by ' . abs($days) . ' ' . (abs($days) === 1 ? 'day' : 'days');
        } elseif ($days === 0) {
            return 'Due today';
        } else {
            return 'Due in ' . $days . ' ' . ($days === 1 ? 'day' : 'days');
        }
    }

    /**
     * Get status badge color for UI
     */
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'draft' => [
                'label' => 'Draft',
                'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600',
            ],
            'sent' => [
                'label' => 'Sent',
                'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800',
            ],
            'received' => [
                'label' => 'Received',
                'class' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800',
            ],
            'cancelled' => [
                'label' => 'Cancelled',
                'class' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800',
            ],
            default => [
                'label' => ucfirst($this->status),
                'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600',
            ],
        };
    }

    /**
     * Check if PO is fully received
     */
    public function isFullyReceived(): bool
    {
        if ($this->status !== 'received') {
            return false;
        }

        foreach ($this->items as $item) {
            if ($item->quantity_received < $item->quantity_ordered) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if PO is partially received
     */
    public function isPartiallyReceived(): bool
    {
        if ($this->status !== 'received') {
            return false;
        }

        $hasReceivedItems = false;
        $hasUnreceivedItems = false;

        foreach ($this->items as $item) {
            if ($item->quantity_received > 0) {
                $hasReceivedItems = true;
            }
            if ($item->quantity_received < $item->quantity_ordered) {
                $hasUnreceivedItems = true;
            }
        }

        return $hasReceivedItems && $hasUnreceivedItems;
    }

    /**
     * Get receiving progress percentage
     */
    public function getReceivingProgressAttribute(): float
    {
        $totalOrdered = $this->items->sum('quantity_ordered');
        $totalReceived = $this->items->sum('quantity_received');

        if ($totalOrdered === 0) {
            return 0;
        }

        return round(($totalReceived / $totalOrdered) * 100, 2);
    }

    /**
     * Get total items count
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->count();
    }

    /**
     * Get total quantity ordered
     */
    public function getTotalQuantityOrderedAttribute(): int
    {
        return $this->items->sum('quantity_ordered');
    }

    /**
     * Get total quantity received
     */
    public function getTotalQuantityReceivedAttribute(): int
    {
        return $this->items->sum('quantity_received');
    }

    /**
     * Get remaining quantity to receive
     */
    public function getRemainingQuantityAttribute(): int
    {
        return $this->total_quantity_ordered - $this->total_quantity_received;
    }

    /**
     * Get total quantity to receive
     */
    public function getTotalQuantityToReceive(): int
    {
        if ($this->status === 'received') {
            return 0;
        }

        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->quantity_ordered - ($item->quantity_received ?? 0);
        }
        return $total;
    }

    /**
     * Format the total amount with currency
     */
    public function getFormattedTotalAttribute(): string
    {
        return '₦' . number_format($this->total_amount, 2);
    }

    /**
     * Get short formatted total (for cards/lists)
     */
    public function getShortFormattedTotalAttribute(): string
    {
        $amount = $this->total_amount;

        if ($amount >= 1000000) {
            return '₦' . number_format($amount / 1000000, 1) . 'M';
        } elseif ($amount >= 1000) {
            return '₦' . number_format($amount / 1000, 1) . 'K';
        }

        return '₦' . number_format($amount, 0);
    }

    /**
     * Get receiving summary
     */
    public function getReceivingSummary(): array
    {
        $totalOrdered = 0;
        $totalReceived = 0;
        $pendingItems = [];

        foreach ($this->items as $item) {
            $totalOrdered += $item->quantity_ordered;
            $totalReceived += $item->quantity_received ?? 0;

            $pending = $item->quantity_ordered - ($item->quantity_received ?? 0);
            if ($pending > 0) {
                $pendingItems[] = [
                    'product' => $item->product->name,
                    'pending' => $pending,
                    'sku' => $item->product->sku,
                ];
            }
        }

        return [
            'total_ordered' => $totalOrdered,
            'total_received' => $totalReceived,
            'pending_total' => $totalOrdered - $totalReceived,
            'progress_percentage' => $totalOrdered > 0 ? round(($totalReceived / $totalOrdered) * 100, 2) : 0,
            'pending_items' => $pendingItems,
            'is_complete' => $totalReceived === $totalOrdered,
        ];
    }

    /**
     * Send the purchase order to supplier (update status)
     */
    public function send(): bool
    {
        if (!$this->canBeSent()) {
            return false;
        }

        $this->sent_at = now();

        return $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark the purchase order as received
     */
    public function markAsReceived(?int $receivedBy = null): bool
    {
        if (!$this->canBeReceived()) {
            return false;
        }

        return $this->update([
            'status' => 'received',
            'received_by' => $receivedBy ?? Auth::id(),
            'received_at' => now(),
        ]);
    }

    /**
     * Cancel the purchase order
     */
    public function cancel(): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        return $this->update(['status' => 'cancelled']);
    }

    /**
     * Recalculate total amount from items
     */
    public function recalculateTotal(): void
    {
        $this->total_amount = $this->items->sum('subtotal');
        $this->save();
    }

    /**
     * Clone the purchase order for repeat orders
     */
    public function duplicate(): self
    {
        $newPO = $this->replicate([
            'po_number',
            'status',
            'received_by',
            'received_at',
            'sent_at',
        ]);

        // Generate new PO number
        $year = now()->format('Y');
        $lastPO = static::whereYear('created_at', now()->year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastPO ? (int) substr($lastPO->po_number, -4) + 1 : 1;
        $newPO->po_number = 'PO-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $newPO->status = 'draft';
        $newPO->order_date = now();
        $newPO->created_by = Auth::id();
        $newPO->save();

        // Duplicate items
        foreach ($this->items as $item) {
            $newItem = $item->replicate(['purchase_order_id', 'quantity_received']);
            $newItem->purchase_order_id = $newPO->id;
            $newItem->quantity_received = 0;
            $newItem->save();
        }

        return $newPO;
    }

    /**
     * Get timeline events for this PO
     */
    public function getTimelineAttribute(): array
    {
        $timeline = [];

        // Created event
        $timeline[] = [
            'type' => 'created',
            'icon' => 'plus',
            'color' => 'blue',
            'title' => 'Order Created',
            'description' => 'by ' . $this->creator->name,
            'timestamp' => $this->created_at,
        ];

        // Sent event
        if ($this->sent_at) {
            $timeline[] = [
                'type' => 'sent',
                'icon' => 'send',
                'color' => 'sky',
                'title' => 'Order Sent',
                'description' => 'Sent to ' . $this->supplier->company_name,
                'timestamp' => $this->sent_at,
            ];
        }

        // Received event
        if ($this->received_at) {
            $timeline[] = [
                'type' => 'received',
                'icon' => 'check',
                'color' => 'green',
                'title' => 'Order Received',
                'description' => 'by ' . $this->receiver->name,
                'timestamp' => $this->received_at,
            ];
        }

        // Cancelled event
        if ($this->status === 'cancelled') {
            $timeline[] = [
                'type' => 'cancelled',
                'icon' => 'x',
                'color' => 'red',
                'title' => 'Order Cancelled',
                'description' => null,
                'timestamp' => $this->updated_at,
            ];
        }

        return $timeline;
    }
}

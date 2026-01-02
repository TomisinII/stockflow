<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'received_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

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
     * Get status badge color for UI
     */
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'draft' => [
                'label' => 'Draft',
                'class' => 'bg-gray-100 text-gray-800',
            ],
            'sent' => [
                'label' => 'Sent',
                'class' => 'bg-blue-100 text-blue-800',
            ],
            'received' => [
                'label' => 'Received',
                'class' => 'bg-green-100 text-green-800',
            ],
            'cancelled' => [
                'label' => 'Cancelled',
                'class' => 'bg-red-100 text-red-800',
            ],
            default => [
                'label' => ucfirst($this->status),
                'class' => 'bg-gray-100 text-gray-800',
            ],
        };
    }

    /**
     * Check if PO is fully received
     * Compares quantity_ordered vs quantity_received across all items
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
     * Get receiving progress percentage
     */
    public function getReceivingProgressAttribute(): float
    {
        $totalOrdered = $this->items->sum('quantity_ordered');
        $totalReceived = $this->items->sum('quantity_received');

        if ($totalOrdered === 0) {
            return 0;
        }

        return ($totalReceived / $totalOrdered) * 100;
    }
}

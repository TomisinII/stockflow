<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'payment_terms',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     * Cast status to enum-like behavior for better type safety
     */
    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get all products supplied by this supplier
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get all purchase orders from this supplier
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Scope to get only active suppliers
     * Usage: Supplier::active()->get()
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only inactive suppliers
     * Usage: Supplier::inactive()->get()
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope to get suppliers with recent orders
     * Usage: Supplier::withRecentOrders()->get()
     */
    public function scopeWithRecentOrders($query, $days = 30)
    {
        return $query->whereHas('purchaseOrders', function ($q) use ($days) {
            $q->where('order_date', '>=', now()->subDays($days));
        });
    }

    /**
     * Get the full address as a single string
     * Usage: $supplier->fullAddress
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get initials from company name
     * Usage: $supplier->initials
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->company_name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->company_name, 0, 2));
    }

    /**
     * Get total amount spent with this supplier (received orders only)
     * Usage: $supplier->totalSpent
     */
    public function getTotalSpentAttribute(): float
    {
        return $this->purchaseOrders()
            ->where('status', 'received')
            ->sum('total_amount');
    }

    /**
     * Get count of pending purchase orders
     * Usage: $supplier->pendingOrdersCount
     */
    public function getPendingOrdersCountAttribute(): int
    {
        return $this->purchaseOrders()
            ->whereIn('status', ['draft', 'sent'])
            ->count();
    }

    /**
     * Get count of active products from this supplier
     * Usage: $supplier->activeProductsCount
     */
    public function getActiveProductsCountAttribute(): int
    {
        return $this->products()
            ->where('status', 'active')
            ->count();
    }

    /**
     * Get count of low stock products from this supplier
     * Usage: $supplier->lowStockProductsCount
     */
    public function getLowStockProductsCountAttribute(): int
    {
        return $this->products()
            ->lowStock()
            ->count();
    }

    /**
     * Check if supplier has any recent orders (last 30 days)
     * Usage: if ($supplier->hasRecentOrders) { ... }
     */
    public function getHasRecentOrdersAttribute(): bool
    {
        return $this->purchaseOrders()
            ->where('order_date', '>=', now()->subDays(30))
            ->exists();
    }

    /**
     * Check if supplier is active
     * Usage: if ($supplier->isActive) { ... }
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }
}

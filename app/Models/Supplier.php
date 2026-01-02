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
}

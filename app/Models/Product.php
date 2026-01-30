<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'description',
        'category_id',
        'supplier_id',
        'unit_of_measure',
        'cost_price',
        'selling_price',
        'current_stock',
        'minimum_stock',
        'maximum_stock',
        'image_path',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'current_stock' => 'integer',
        'minimum_stock' => 'integer',
        'maximum_stock' => 'integer',
    ];

    /**
     * Get the category this product belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the supplier for this product
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get all stock adjustments for this product
     */
    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class);
    }

    /**
     * Get all purchase order items for this product
     */
    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function recentAdjustments()
    {
        return $this->stockAdjustments()->latest()->take(10);
    }
    
    /**
     * Scope to get only active products
     * Usage: Product::active()->get()
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get low stock products
     * Usage: Product::lowStock()->get()
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock')
                     ->where('current_stock', '>', 0);
    }

    /**
     * Scope to get out of stock products
     * Usage: Product::outOfStock()->get()
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', 0);
    }

    /**
     * Scope to get products in a specific category
     * Usage: Product::inCategory($categoryId)->get()
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Check if product is low on stock
     * Usage: if ($product->isLowStock()) { ... }
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock && $this->current_stock > 0;
    }

    /**
     * Check if product is out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->current_stock === 0;
    }

    /**
     * Get stock status as string with color
     * Returns: ['status' => 'In Stock', 'color' => 'green']
     */
    public function getStockStatusAttribute(): array
    {
        if ($this->isOutOfStock()) {
            return [
                'status' => 'Out of Stock',
                'color' => 'red',
                'badge' => 'bg-red-100 text-red-800',
            ];
        }

        if ($this->isLowStock()) {
            return [
                'status' => 'Low Stock',
                'color' => 'amber',
                'badge' => 'bg-amber-100 text-amber-800',
            ];
        }

        return [
            'status' => 'In Stock',
            'color' => 'green',
            'badge' => 'bg-green-100 text-green-800',
        ];
    }

    /**
     * Calculate profit margin percentage
     * Usage: $product->profitMargin (returns 25.5 for 25.5%)
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->cost_price == 0) {
            return 0;
        }

        return (($this->selling_price - $this->cost_price) / $this->cost_price) * 100;
    }

    /**
     * Get total stock value (cost_price × current_stock)
     */
    public function getStockValueAttribute(): float
    {
        return $this->cost_price * $this->current_stock;
    }

    public function addStock($quantity, $reason, $reference = null, $notes = null, $adjustedBy = null, $adjustmentDate = null)
    {
        $this->increment('current_stock', $quantity);

        return $this->stockAdjustments()->create([
            'adjustment_type' => 'in',
            'quantity' => $quantity,
            'previous_stock' => $this->current_stock - $quantity,
            'new_stock' => $this->current_stock,
            'reason' => $reason,
            'reference' => $reference,
            'notes' => $notes,
            'adjusted_by' => $adjustedBy ?? Auth::id(),
            'adjustment_date' => $adjustmentDate ?? now(),
        ]);
    }
}

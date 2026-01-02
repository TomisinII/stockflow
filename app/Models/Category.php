<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     * These fields can be filled using create() or update()
     */
    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'parent_id',
    ];

    /**
     * Get all products in this category
     * One category has many products
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the parent category (for hierarchical categories)
     * Example: "Laptops" belongs to "Electronics"
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get all child categories
     * Example: "Electronics" has many children like "Laptops", "Phones"
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Scope to get only top-level categories (no parent)
     * Usage: Category::topLevel()->get()
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get active categories with products
     * Usage: Category::withProducts()->get()
     */
    public function scopeWithProducts($query)
    {
        return $query->has('products');
    }
}

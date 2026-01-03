<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockAdjustment;
use Livewire\Component;

class Dashboard extends Component
{
    // Computed Properties
    public function getTotalProductsProperty()
    {
        return Product::where('status', 'active')->count();
    }

    public function getProductsAddedThisWeekProperty()
    {
        return Product::where('created_at', '>=', now()->startOfWeek())->count();
    }

    public function getTotalStockValueProperty()
    {
        return Product::where('status', 'active')
            ->get()
            ->sum(fn($product) => $product->cost_price * $product->current_stock);
    }

    public function getStockValueIncreaseProperty()
    {
        // This would require historical data - placeholder for now
        return 12.5;
    }

    public function getLowStockCountProperty()
    {
        return Product::whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)
            ->count();
    }

    public function getPendingPurchaseOrdersProperty()
    {
        return PurchaseOrder::whereIn('status', ['draft', 'sent'])->count();
    }

    public function getInStockCountProperty()
    {
        return Product::where('status', 'active')
            ->whereColumn('current_stock', '>', 'minimum_stock')
            ->count();
    }

    public function getOutOfStockCountProperty()
    {
        return Product::where('current_stock', 0)->count();
    }

    public function getStockStatusProperty()
    {
        $total = $this->totalProducts ?: 1; // Avoid division by zero

        return [
            'inStock' => round(($this->inStockCount / $total) * 100),
            'lowStock' => round(($this->lowStockCount / $total) * 100),
            'outOfStock' => round(($this->outOfStockCount / $total) * 100),
        ];
    }

    public function getRecentAdjustmentsProperty()
    {
        return StockAdjustment::with(['product', 'adjuster'])
            ->latest('created_at')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}

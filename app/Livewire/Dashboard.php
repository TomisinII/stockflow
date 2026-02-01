<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockAdjustment;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Dashboard extends Component
{
    public function getTotalProductsProperty()
    {
        return Product::active()->count();
    }

    public function getProductsAddedThisWeekProperty()
    {
        return Product::where('created_at', '>=', now()->startOfWeek())->count();
    }

    public function getTotalStockValueProperty()
    {
        return Product::active()
            ->get()
            ->sum(fn($product) => $product->stockValue);
    }

    public function getStockValueIncreaseProperty()
    {
        // This would require historical data - placeholder for now
        return 12.5;
    }

    public function getLowStockCountProperty()
    {
        return Product::lowStock()->count();
    }

    public function getPendingPurchaseOrdersProperty()
    {
        return PurchaseOrder::whereIn('status', ['draft', 'sent'])->count();
    }

    public function getInStockCountProperty()
    {
        return Product::active()
            ->whereColumn('current_stock', '>', 'minimum_stock')
            ->count();
    }

    public function getOutOfStockCountProperty()
    {
        return Product::outOfStock()->count();
    }

    public function getStockStatusProperty()
    {
        $total = $this->totalProducts ?: 1;

        return [
            'inStock' => round(($this->inStockCount / $total) * 100),
            'lowStock' => round(($this->lowStockCount / $total) * 100),
            'outOfStock' => round(($this->outOfStockCount / $total) * 100),
        ];
    }

    public function getRecentAdjustmentsProperty()
    {
        return StockAdjustment::with(['product', 'adjuster'])
            ->whereHas('product') // Only get adjustments with valid products
            ->whereHas('adjuster') // Only get adjustments with valid users
            ->latest('created_at')
            ->limit(5)
            ->get();
    }

    public function exportDashboardReport()
    {
        try {
            $filename = 'dashboard-report-' . now()->format('Y-m-d-His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            $callback = function() {
                $file = fopen('php://output', 'w');

                // Add UTF-8 BOM for Excel compatibility
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // Report Header
                fputcsv($file, ['StockFlow Dashboard Report']);
                fputcsv($file, ['Generated on: ' . now()->format('F d, Y H:i:s')]);
                fputcsv($file, []); // Empty row

                // Summary Statistics
                fputcsv($file, ['SUMMARY STATISTICS']);
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['Total Products', $this->totalProducts]);
                fputcsv($file, ['Products Added This Week', $this->productsAddedThisWeek]);
                fputcsv($file, ['Total Stock Value (₦)', number_format($this->totalStockValue, 2)]);
                fputcsv($file, ['Low Stock Items', $this->lowStockCount]);
                fputcsv($file, ['Out of Stock Items', $this->outOfStockCount]);
                fputcsv($file, ['In Stock Items', $this->inStockCount]);
                fputcsv($file, ['Pending Purchase Orders', $this->pendingPurchaseOrders]);
                fputcsv($file, []); // Empty row

                // Stock Status Breakdown
                fputcsv($file, ['STOCK STATUS BREAKDOWN']);
                fputcsv($file, ['Status', 'Count', 'Percentage']);
                fputcsv($file, ['In Stock', $this->inStockCount, $this->stockStatus['inStock'] . '%']);
                fputcsv($file, ['Low Stock', $this->lowStockCount, $this->stockStatus['lowStock'] . '%']);
                fputcsv($file, ['Out of Stock', $this->outOfStockCount, $this->stockStatus['outOfStock'] . '%']);
                fputcsv($file, []); // Empty row

                // Recent Stock Adjustments
                fputcsv($file, ['RECENT STOCK ADJUSTMENTS']);
                fputcsv($file, ['Product', 'Type', 'Quantity', 'Adjusted By', 'Date', 'Time']);

                foreach ($this->recentAdjustments as $adjustment) {
                    fputcsv($file, [
                        $adjustment->product?->name ?? 'N/A',
                        $adjustment->formattedType,
                        ($adjustment->adjustment_type === 'in' ? '+' : '-') . $adjustment->absoluteQuantity,
                        $adjustment->adjuster?->name ?? 'Unknown User',
                        $adjustment->created_at->format('Y-m-d'),
                        $adjustment->created_at->format('H:i:s'),
                    ]);
                }
                fputcsv($file, []); // Empty row

                // Low Stock Products
                $lowStockProducts = Product::lowStock()
                    ->with('category')
                    ->get();

                if ($lowStockProducts->count() > 0) {
                    fputcsv($file, ['LOW STOCK PRODUCTS']);
                    fputcsv($file, ['Product Name', 'SKU', 'Category', 'Current Stock', 'Minimum Stock', 'Stock Value (₦)']);

                    foreach ($lowStockProducts as $product) {
                        fputcsv($file, [
                            $product->name,
                            $product->sku,
                            $product->category?->name ?? 'N/A',
                            $product->current_stock,
                            $product->minimum_stock,
                            number_format($product->stockValue, 2),
                        ]);
                    }
                    fputcsv($file, []); // Empty row
                }

                // Out of Stock Products
                $outOfStockProducts = Product::outOfStock()
                    ->with('category')
                    ->get();

                if ($outOfStockProducts->count() > 0) {
                    fputcsv($file, ['OUT OF STOCK PRODUCTS']);
                    fputcsv($file, ['Product Name', 'SKU', 'Category', 'Minimum Stock']);

                    foreach ($outOfStockProducts as $product) {
                        fputcsv($file, [
                            $product->name,
                            $product->sku,
                            $product->category?->name ?? 'N/A',
                            $product->minimum_stock,
                        ]);
                    }
                    fputcsv($file, []); // Empty row
                }

                // Stock by Category
                $categoryStats = Product::select('category_id')
                    ->selectRaw('COUNT(*) as product_count')
                    ->selectRaw('SUM(current_stock) as total_stock')
                    ->selectRaw('SUM(current_stock * cost_price) as total_value')
                    ->with('category')
                    ->groupBy('category_id')
                    ->get();

                if ($categoryStats->count() > 0) {
                    fputcsv($file, ['STOCK BY CATEGORY']);
                    fputcsv($file, ['Category', 'Product Count', 'Total Stock', 'Total Value (₦)']);

                    foreach ($categoryStats as $stat) {
                        fputcsv($file, [
                            $stat->category?->name ?? 'Uncategorized',
                            $stat->product_count,
                            $stat->total_stock,
                            number_format($stat->total_value, 2),
                        ]);
                    }
                }

                fclose($file);
            };

            return new StreamedResponse($callback, 200, $headers);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Failed to export report: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}

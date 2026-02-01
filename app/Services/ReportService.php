<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Category;
use Carbon\Carbon;

class ReportService
{
    /**
     * Generate current stock report
     */
    public function currentStockReport(): array
    {
        $products = Product::with(['category:id,name', 'supplier:id,company_name'])
            ->where('status', 'active')
            ->get();

        $totalValue = $products->sum(function ($product) {
            return $product->current_stock * $product->cost_price;
        });

        $totalSellingValue = $products->sum(function ($product) {
            return $product->current_stock * $product->selling_price;
        });

        return [
            'report_date' => now()->toDateTimeString(),
            'total_products' => $products->count(),
            'total_stock_units' => $products->sum('current_stock'),
            'total_cost_value' => $totalValue,
            'total_selling_value' => $totalSellingValue,
            'potential_profit' => $totalSellingValue - $totalValue,
            'products' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category->name ?? 'N/A',
                    'supplier' => $product->supplier->company_name ?? 'N/A',
                    'current_stock' => $product->current_stock,
                    'unit' => $product->unit_of_measure,
                    'cost_price' => $product->cost_price,
                    'selling_price' => $product->selling_price,
                    'stock_value' => $product->current_stock * $product->cost_price,
                    'status' => $product->stockStatus['status'],
                ];
            }),
        ];
    }

    /**
     * Generate low stock report
     */
    public function lowStockReport(): array
    {
        $lowStockProducts = Product::with(['category:id,name', 'supplier:id,company_name'])
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)
            ->where('status', 'active')
            ->orderBy('current_stock', 'asc')
            ->get();

        $outOfStockProducts = Product::with(['category:id,name', 'supplier:id,company_name'])
            ->where('current_stock', 0)
            ->where('status', 'active')
            ->get();

        return [
            'report_date' => now()->toDateTimeString(),
            'low_stock_count' => $lowStockProducts->count(),
            'out_of_stock_count' => $outOfStockProducts->count(),
            'total_critical' => $lowStockProducts->count() + $outOfStockProducts->count(),
            'low_stock_products' => $lowStockProducts->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category->name ?? 'N/A',
                    'supplier' => $product->supplier->company_name ?? 'N/A',
                    'current_stock' => $product->current_stock,
                    'minimum_stock' => $product->minimum_stock,
                    'shortage' => $product->minimum_stock - $product->current_stock,
                    'unit' => $product->unit_of_measure,
                    'reorder_value' => ($product->minimum_stock - $product->current_stock) * $product->cost_price,
                ];
            }),
            'out_of_stock_products' => $outOfStockProducts->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category->name ?? 'N/A',
                    'supplier' => $product->supplier->company_name ?? 'N/A',
                    'minimum_stock' => $product->minimum_stock,
                    'unit' => $product->unit_of_measure,
                    'reorder_value' => $product->minimum_stock * $product->cost_price,
                ];
            }),
        ];
    }

    /**
     * Generate stock valuation report
     */
    public function stockValuationReport(): array
    {
        $products = Product::where('status', 'active')->get();

        $byCategory = Product::selectRaw('
                category_id,
                SUM(current_stock * cost_price) as cost_value,
                SUM(current_stock * selling_price) as selling_value,
                SUM(current_stock) as total_units,
                COUNT(*) as product_count
            ')
            ->where('status', 'active')
            ->groupBy('category_id')
            ->with('category:id,name,color')
            ->get();

        return [
            'report_date' => now()->toDateTimeString(),
            'total_cost_value' => $products->sum(fn($p) => $p->current_stock * $p->cost_price),
            'total_selling_value' => $products->sum(fn($p) => $p->current_stock * $p->selling_price),
            'total_units' => $products->sum('current_stock'),
            'total_products' => $products->count(),
            'by_category' => $byCategory->map(function ($item) {
                return [
                    'category' => $item->category->name ?? 'Uncategorized',
                    'cost_value' => $item->cost_value,
                    'selling_value' => $item->selling_value,
                    'potential_profit' => $item->selling_value - $item->cost_value,
                    'total_units' => $item->total_units,
                    'product_count' => $item->product_count,
                ];
            }),
        ];
    }

    /**
     * Generate dead stock report
     */
    public function deadStockReport(int $days = 90): array
    {
        $cutoffDate = now()->subDays($days);

        $deadStock = Product::where('status', 'active')
            ->where('current_stock', '>', 0)
            ->whereDoesntHave('stockAdjustments', function ($query) use ($cutoffDate) {
                $query->where('adjustment_date', '>=', $cutoffDate);
            })
            ->with(['category:id,name', 'supplier:id,company_name'])
            ->get();

        $totalValue = $deadStock->sum(fn($p) => $p->current_stock * $p->cost_price);

        return [
            'report_date' => now()->toDateTimeString(),
            'cutoff_days' => $days,
            'cutoff_date' => $cutoffDate->toDateString(),
            'dead_stock_count' => $deadStock->count(),
            'total_units' => $deadStock->sum('current_stock'),
            'total_value_locked' => $totalValue,
            'products' => $deadStock->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category->name ?? 'N/A',
                    'supplier' => $product->supplier->company_name ?? 'N/A',
                    'current_stock' => $product->current_stock,
                    'unit' => $product->unit_of_measure,
                    'cost_price' => $product->cost_price,
                    'value_locked' => $product->current_stock * $product->cost_price,
                    'last_movement' => $product->stockAdjustments()->latest('adjustment_date')->first()?->adjustment_date,
                ];
            }),
        ];
    }

    /**
     * Generate stock movement report
     */
    public function stockMovementReport(Carbon $startDate, Carbon $endDate): array
    {
        $adjustments = StockAdjustment::whereBetween('adjustment_date', [$startDate, $endDate])
            ->with(['product:id,name,sku', 'user:id,name'])
            ->get();

        $summary = [
            'stock_in' => $adjustments->where('adjustment_type', 'in')->sum('quantity'),
            'stock_out' => $adjustments->where('adjustment_type', 'out')->sum('quantity'),
            'corrections' => $adjustments->where('adjustment_type', 'correction')->count(),
        ];

        $byReason = $adjustments->groupBy('reason')->map(function ($group, $reason) {
            return [
                'reason' => $reason,
                'count' => $group->count(),
                'total_quantity' => $group->sum('quantity'),
            ];
        })->values();

        return [
            'report_date' => now()->toDateTimeString(),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'total_adjustments' => $adjustments->count(),
            'summary' => $summary,
            'by_reason' => $byReason,
            'adjustments' => $adjustments->map(function ($adjustment) {
                return [
                    'date' => $adjustment->adjustment_date->toDateString(),
                    'product' => $adjustment->product->name ?? 'N/A',
                    'sku' => $adjustment->product->sku ?? 'N/A',
                    'type' => $adjustment->adjustment_type,
                    'quantity' => $adjustment->quantity,
                    'reason' => $adjustment->reason,
                    'reference' => $adjustment->reference,
                    'adjusted_by' => $adjustment->user->name ?? 'N/A',
                ];
            }),
        ];
    }

    /**
     * Generate purchase order summary report
     */
    public function purchaseOrderSummaryReport(Carbon $startDate, Carbon $endDate): array
    {
        $orders = PurchaseOrder::whereBetween('order_date', [$startDate, $endDate])
            ->with(['supplier:id,company_name', 'items.product'])
            ->get();

        $byStatus = $orders->groupBy('status')->map(function ($group, $status) {
            return [
                'status' => $status,
                'count' => $group->count(),
                'total_value' => $group->sum('total_amount'),
            ];
        });

        $bySupplier = $orders->groupBy('supplier_id')->map(function ($group) {
            $supplier = $group->first()->supplier;
            return [
                'supplier' => $supplier->company_name ?? 'N/A',
                'order_count' => $group->count(),
                'total_spent' => $group->sum('total_amount'),
                'average_order_value' => $group->avg('total_amount'),
            ];
        })->values()->sortByDesc('total_spent')->values();

        return [
            'report_date' => now()->toDateTimeString(),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'total_orders' => $orders->count(),
            'total_value' => $orders->sum('total_amount'),
            'average_order_value' => $orders->avg('total_amount'),
            'by_status' => $byStatus,
            'by_supplier' => $bySupplier->take(10), // Top 10 suppliers
            'orders' => $orders->map(function ($order) {
                return [
                    'po_number' => $order->po_number,
                    'supplier' => $order->supplier->company_name ?? 'N/A',
                    'order_date' => $order->order_date->toDateString(),
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                    'items_count' => $order->items->count(),
                ];
            }),
        ];
    }

    /**
     * Generate supplier performance report
     */
    public function supplierPerformanceReport(): array
    {
        $suppliers = Supplier::withCount([
                'purchaseOrders',
                'products'
            ])
            ->with(['purchaseOrders' => function ($query) {
                $query->where('status', 'received');
            }])
            ->where('status', 'active')
            ->get();

        $performance = $suppliers->map(function ($supplier) {
            $receivedOrders = $supplier->purchaseOrders;
            $totalOrders = $receivedOrders->count();

            $onTimeDeliveries = 0;
            $totalDeliveryDays = 0;

            foreach ($receivedOrders as $order) {
                if ($order->received_at && $order->expected_delivery_date) {
                    if ($order->received_at->lte($order->expected_delivery_date)) {
                        $onTimeDeliveries++;
                    }
                    $totalDeliveryDays += $order->order_date->diffInDays($order->received_at);
                }
            }

            return [
                'supplier' => $supplier->company_name,
                'total_orders' => $totalOrders,
                'total_spent' => $receivedOrders->sum('total_amount'),
                'products_supplied' => $supplier->products_count,
                'on_time_delivery_rate' => $totalOrders > 0 ? round(($onTimeDeliveries / $totalOrders) * 100, 2) : 0,
                'average_delivery_days' => $totalOrders > 0 ? round($totalDeliveryDays / $totalOrders, 1) : 0,
            ];
        })->sortByDesc('total_spent')->values();

        return [
            'report_date' => now()->toDateTimeString(),
            'total_suppliers' => $suppliers->count(),
            'performance' => $performance,
        ];
    }

    /**
     * Generate category-wise stock report
     */
    public function categoryWiseStockReport(): array
    {
        $categories = Category::withCount('products')
            ->with(['products' => function ($query) {
                $query->where('status', 'active');
            }])
            ->get();

        $categoryData = $categories->map(function ($category) {
            $products = $category->products;

            return [
                'category' => $category->name,
                'product_count' => $products->count(),
                'total_units' => $products->sum('current_stock'),
                'total_value' => $products->sum(fn($p) => $p->current_stock * $p->cost_price),
                'low_stock_items' => $products->filter(fn($p) => $p->current_stock <= $p->minimum_stock)->count(),
                'out_of_stock_items' => $products->where('current_stock', 0)->count(),
            ];
        })->sortByDesc('total_value')->values();

        return [
            'report_date' => now()->toDateTimeString(),
            'total_categories' => $categories->count(),
            'categories' => $categoryData,
        ];
    }

    /**
     * Export report to CSV
     */
    public function exportToCSV(array $data, string $filename): string
    {
        $csv = fopen('php://temp', 'r+');

        // Add UTF-8 BOM
        fprintf($csv, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write headers
        if (!empty($data)) {
            fputcsv($csv, array_keys(reset($data)));

            // Write data
            foreach ($data as $row) {
                fputcsv($csv, $row);
            }
        }

        rewind($csv);
        $output = stream_get_contents($csv);
        fclose($csv);

        return $output;
    }
}

<?php

namespace App\Livewire\Reports;

use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockAdjustment;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{
    public $startDate;
    public $endDate;

    public function mount()
    {
        // Default to last 2 years for comprehensive data
        $this->startDate = now()->subYears(2)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function updatedStartDate()
    {
        $this->dispatch('dateRangeUpdated');
    }

    public function updatedEndDate()
    {
        $this->dispatch('dateRangeUpdated');
    }

    public function resetDateRange()
    {
        $this->startDate = now()->subYears(2)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->dispatch('dateRangeUpdated');
    }

    // ========================================
    // EXPORT METHODS
    // ========================================

    /**
     * Export Current Stock Report
     */
    public function exportCurrentStock($format = 'csv')
    {
        $products = Product::with(['category', 'supplier'])
            ->active()
            ->orderBy('name')
            ->get();

        if ($format === 'csv') {
            return $this->exportCurrentStockCSV($products);
        } elseif ($format === 'pdf') {
            return $this->exportCurrentStockPDF($products);
        }
    }

    /**
     * Export Low Stock Report
     */
    public function exportLowStock($format = 'csv')
    {
        $products = Product::with(['category', 'supplier'])
            ->lowStock()
            ->orderBy('current_stock')
            ->get();

        if ($format === 'csv') {
            return $this->exportLowStockCSV($products);
        } elseif ($format === 'pdf') {
            return $this->exportLowStockPDF($products);
        }
    }

    /**
     * Export Stock Valuation Report
     */
    public function exportStockValuation($format = 'csv')
    {
        $products = Product::with(['category', 'supplier'])
            ->active()
            ->where('current_stock', '>', 0)
            ->orderBy('name')
            ->get();

        $totalCostValue = $products->sum('stock_value');
        $totalSellingValue = $products->sum(fn($p) => $p->selling_price * $p->current_stock);

        if ($format === 'csv') {
            return $this->exportStockValuationCSV($products, $totalCostValue, $totalSellingValue);
        } elseif ($format === 'pdf') {
            return $this->exportStockValuationPDF($products, $totalCostValue, $totalSellingValue);
        }
    }

    /**
     * Export Stock Movement Report
     */
    public function exportStockMovement($format = 'csv')
    {
        $startDate = $this->startDate;
        $endDate = $this->endDate;

        $adjustments = StockAdjustment::with(['product.category', 'adjustedBy'])
            ->whereBetween('adjustment_date', [$startDate, $endDate])
            ->orderBy('adjustment_date', 'desc')
            ->get();

        if ($format === 'csv') {
            return $this->exportStockMovementCSV($adjustments, $startDate, $endDate);
        } elseif ($format === 'pdf') {
            return $this->exportStockMovementPDF($adjustments, $startDate, $endDate);
        }
    }

    /**
     * Export Purchase Orders Report
     */
    public function exportPurchaseOrders($format = 'csv')
    {
        $startDate = $this->startDate;
        $endDate = $this->endDate;

        $orders = PurchaseOrder::with(['supplier', 'creator', 'items.product'])
            ->whereBetween('order_date', [$startDate, $endDate])
            ->orderBy('order_date', 'desc')
            ->get();

        if ($format === 'csv') {
            return $this->exportPurchaseOrdersCSV($orders, $startDate, $endDate);
        } elseif ($format === 'pdf') {
            return $this->exportPurchaseOrdersPDF($orders, $startDate, $endDate);
        }
    }

    /**
     * Export Supplier Performance Report
     */
    public function exportSupplierPerformance($format = 'csv')
    {
        $startDate = $this->startDate;
        $endDate = $this->endDate;

        $suppliers = Supplier::with(['purchaseOrders' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('order_date', [$startDate, $endDate]);
        }])->get();

        $suppliersData = $suppliers->map(function ($supplier) {
            $orders = $supplier->purchaseOrders;
            $receivedOrders = $orders->where('status', 'received');

            return [
                'supplier' => $supplier,
                'total_orders' => $orders->count(),
                'received_orders' => $receivedOrders->count(),
                'total_spent' => $receivedOrders->sum('total_amount'),
                'avg_delivery_time' => $this->calculateAverageDeliveryTime($receivedOrders),
                'on_time_delivery_rate' => $this->calculateOnTimeDeliveryRate($receivedOrders),
            ];
        })->filter(fn($s) => $s['total_orders'] > 0)->sortByDesc('total_spent');

        if ($format === 'csv') {
            return $this->exportSupplierPerformanceCSV($suppliersData, $startDate, $endDate);
        } elseif ($format === 'pdf') {
            return $this->exportSupplierPerformancePDF($suppliersData, $startDate, $endDate);
        }
    }

    // ========================================
    // CSV EXPORT IMPLEMENTATIONS
    // ========================================

    private function exportCurrentStockCSV($products)
    {
        $filename = 'current-stock-report-' . now()->format('Y-m-d-His') . '.csv';

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($file, ['StockFlow - Current Stock Report']);
            fputcsv($file, ['Generated on: ' . now()->format('F d, Y H:i:s')]);
            fputcsv($file, []);

            fputcsv($file, ['Product Name', 'SKU', 'Category', 'Supplier', 'Current Stock', 'Unit', 'Minimum Stock', 'Status', 'Cost Price (₦)', 'Selling Price (₦)', 'Stock Value (₦)']);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->name,
                    $product->sku,
                    $product->category->name ?? 'N/A',
                    $product->supplier->company_name ?? 'N/A',
                    $product->current_stock,
                    $product->unit_of_measure,
                    $product->minimum_stock,
                    $product->stockStatus['status'],
                    number_format($product->cost_price, 2),
                    number_format($product->selling_price, 2),
                    number_format($product->stock_value, 2),
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['SUMMARY']);
            fputcsv($file, ['Total Products', $products->count()]);
            fputcsv($file, ['Total Stock Value (₦)', number_format($products->sum('stock_value'), 2)]);

            fclose($file);
        };

        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function exportLowStockCSV($products)
    {
        $filename = 'low-stock-report-' . now()->format('Y-m-d-His') . '.csv';

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['StockFlow - Low Stock Report']);
            fputcsv($file, ['Generated on: ' . now()->format('F d, Y H:i:s')]);
            fputcsv($file, []);

            fputcsv($file, ['Product Name', 'SKU', 'Category', 'Supplier', 'Current Stock', 'Minimum Stock', 'Below Minimum', 'Unit', 'Status']);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->name,
                    $product->sku,
                    $product->category->name ?? 'N/A',
                    $product->supplier->company_name ?? 'N/A',
                    $product->current_stock,
                    $product->minimum_stock,
                    $product->minimum_stock - $product->current_stock,
                    $product->unit_of_measure,
                    $product->stockStatus['status'],
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['SUMMARY']);
            fputcsv($file, ['Total Low Stock Products', $products->count()]);

            fclose($file);
        };

        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function exportStockValuationCSV($products, $totalCostValue, $totalSellingValue)
    {
        $filename = 'stock-valuation-report-' . now()->format('Y-m-d-His') . '.csv';

        $callback = function () use ($products, $totalCostValue, $totalSellingValue) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['StockFlow - Stock Valuation Report']);
            fputcsv($file, ['Generated on: ' . now()->format('F d, Y H:i:s')]);
            fputcsv($file, []);

            fputcsv($file, ['Product Name', 'SKU', 'Category', 'Current Stock', 'Unit', 'Cost Price (₦)', 'Selling Price (₦)', 'Cost Value (₦)', 'Selling Value (₦)', 'Potential Profit (₦)']);

            foreach ($products as $product) {
                $sellingValue = $product->selling_price * $product->current_stock;
                $potentialProfit = $sellingValue - $product->stock_value;

                fputcsv($file, [
                    $product->name,
                    $product->sku,
                    $product->category->name ?? 'N/A',
                    $product->current_stock,
                    $product->unit_of_measure,
                    number_format($product->cost_price, 2),
                    number_format($product->selling_price, 2),
                    number_format($product->stock_value, 2),
                    number_format($sellingValue, 2),
                    number_format($potentialProfit, 2),
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['SUMMARY']);
            fputcsv($file, ['Total Products', $products->count()]);
            fputcsv($file, ['Total Cost Value (₦)', number_format($totalCostValue, 2)]);
            fputcsv($file, ['Total Selling Value (₦)', number_format($totalSellingValue, 2)]);
            fputcsv($file, ['Potential Profit (₦)', number_format($totalSellingValue - $totalCostValue, 2)]);

            fclose($file);
        };

        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function exportStockMovementCSV($adjustments, $startDate, $endDate)
    {
        $filename = 'stock-movement-report-' . now()->format('Y-m-d-His') . '.csv';

        $callback = function () use ($adjustments, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['StockFlow - Stock Movement Report']);
            fputcsv($file, ['Period: ' . Carbon::parse($startDate)->format('M d, Y') . ' - ' . Carbon::parse($endDate)->format('M d, Y')]);
            fputcsv($file, ['Generated on: ' . now()->format('F d, Y H:i:s')]);
            fputcsv($file, []);

            fputcsv($file, ['Date', 'Product Name', 'SKU', 'Category', 'Type', 'Quantity', 'Reason', 'Reference', 'Adjusted By']);

            foreach ($adjustments as $adjustment) {
                fputcsv($file, [
                    $adjustment->adjustment_date->format('Y-m-d'),
                    $adjustment->product->name ?? 'N/A',
                    $adjustment->product->sku ?? 'N/A',
                    $adjustment->product->category->name ?? 'N/A',
                    ucfirst($adjustment->adjustment_type),
                    $adjustment->quantity,
                    $adjustment->reason,
                    $adjustment->reference ?? 'N/A',
                    $adjustment->adjustedBy->name ?? 'System',
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['SUMMARY']);
            fputcsv($file, ['Total Adjustments', $adjustments->count()]);
            fputcsv($file, ['Stock In', $adjustments->where('adjustment_type', 'in')->sum('quantity')]);
            fputcsv($file, ['Stock Out', abs($adjustments->where('adjustment_type', 'out')->sum('quantity'))]);

            fclose($file);
        };

        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function exportPurchaseOrdersCSV($orders, $startDate, $endDate)
    {
        $filename = 'purchase-orders-report-' . now()->format('Y-m-d-His') . '.csv';

        $callback = function () use ($orders, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['StockFlow - Purchase Orders Report']);
            fputcsv($file, ['Period: ' . Carbon::parse($startDate)->format('M d, Y') . ' - ' . Carbon::parse($endDate)->format('M d, Y')]);
            fputcsv($file, ['Generated on: ' . now()->format('F d, Y H:i:s')]);
            fputcsv($file, []);

            fputcsv($file, ['PO Number', 'Supplier', 'Order Date', 'Expected Delivery', 'Status', 'Total Items', 'Total Amount (₦)', 'Created By']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->po_number,
                    $order->supplier->company_name ?? 'N/A',
                    $order->order_date->format('Y-m-d'),
                    $order->expected_delivery_date ? $order->expected_delivery_date->format('Y-m-d') : 'N/A',
                    ucfirst($order->status),
                    $order->items->count(),
                    number_format($order->total_amount, 2),
                    $order->creator->name ?? 'N/A',
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['SUMMARY']);
            fputcsv($file, ['Total Orders', $orders->count()]);
            fputcsv($file, ['Total Amount (₦)', number_format($orders->sum('total_amount'), 2)]);
            fputcsv($file, ['Draft Orders', $orders->where('status', 'draft')->count()]);
            fputcsv($file, ['Sent Orders', $orders->where('status', 'sent')->count()]);
            fputcsv($file, ['Received Orders', $orders->where('status', 'received')->count()]);

            fclose($file);
        };

        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function exportSupplierPerformanceCSV($suppliersData, $startDate, $endDate)
    {
        $filename = 'supplier-performance-report-' . now()->format('Y-m-d-His') . '.csv';

        $callback = function () use ($suppliersData, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['StockFlow - Supplier Performance Report']);
            fputcsv($file, ['Period: ' . Carbon::parse($startDate)->format('M d, Y') . ' - ' . Carbon::parse($endDate)->format('M d, Y')]);
            fputcsv($file, ['Generated on: ' . now()->format('F d, Y H:i:s')]);
            fputcsv($file, []);

            fputcsv($file, ['Supplier Name', 'Total Orders', 'Received Orders', 'Total Spent (₦)', 'Avg Delivery Time (days)', 'On-Time Delivery Rate (%)']);

            foreach ($suppliersData as $data) {
                fputcsv($file, [
                    $data['supplier']->company_name,
                    $data['total_orders'],
                    $data['received_orders'],
                    number_format($data['total_spent'], 2),
                    $data['avg_delivery_time'] ?? 'N/A',
                    $data['on_time_delivery_rate'] ? number_format($data['on_time_delivery_rate'], 1) : 'N/A',
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['SUMMARY']);
            fputcsv($file, ['Total Suppliers', $suppliersData->count()]);
            fputcsv($file, ['Total Orders', $suppliersData->sum('total_orders')]);
            fputcsv($file, ['Total Spent (₦)', number_format($suppliersData->sum('total_spent'), 2)]);

            fclose($file);
        };

        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ========================================
    // PDF EXPORT IMPLEMENTATIONS
    // ========================================

    private function exportCurrentStockPDF($products)
    {
        $pdf = Pdf::loadView('pdf.current-stock', [
            'products' => $products,
            'totalStockValue' => $products->sum('stock_value'),
            'generatedAt' => now()->format('F d, Y H:i:s'),
        ]);

        $fileName = 'current-stock-report-' . now()->format('Y-m-d-His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function exportLowStockPDF($products)
    {
        $pdf = Pdf::loadView('pdf.low-stock', [
            'products' => $products,
            'generatedAt' => now()->format('F d, Y H:i:s'),
        ]);

        $fileName = 'low-stock-report-' . now()->format('Y-m-d-His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function exportStockValuationPDF($products, $totalCostValue, $totalSellingValue)
    {
        $pdf = Pdf::loadView('pdf.stock-valuation', [
            'products' => $products,
            'totalCostValue' => $totalCostValue,
            'totalSellingValue' => $totalSellingValue,
            'potentialProfit' => $totalSellingValue - $totalCostValue,
            'generatedAt' => now()->format('F d, Y H:i:s'),
        ]);

        $fileName = 'stock-valuation-report-' . now()->format('Y-m-d-His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function exportStockMovementPDF($adjustments, $startDate, $endDate)
    {
        $pdf = Pdf::loadView('pdf.stock-movement', [
            'adjustments' => $adjustments,
            'startDate' => Carbon::parse($startDate)->format('M d, Y'),
            'endDate' => Carbon::parse($endDate)->format('M d, Y'),
            'stockIn' => $adjustments->where('adjustment_type', 'in')->sum('quantity'),
            'stockOut' => abs($adjustments->where('adjustment_type', 'out')->sum('quantity')),
            'generatedAt' => now()->format('F d, Y H:i:s'),
        ]);

        $fileName = 'stock-movement-report-' . now()->format('Y-m-d-His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function exportPurchaseOrdersPDF($orders, $startDate, $endDate)
    {
        $pdf = Pdf::loadView('pdf.purchase-orders', [
            'orders' => $orders,
            'startDate' => Carbon::parse($startDate)->format('M d, Y'),
            'endDate' => Carbon::parse($endDate)->format('M d, Y'),
            'totalAmount' => $orders->sum('total_amount'),
            'generatedAt' => now()->format('F d, Y H:i:s'),
        ]);

        $fileName = 'purchase-orders-report-' . now()->format('Y-m-d-His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function exportSupplierPerformancePDF($suppliersData, $startDate, $endDate)
    {
        $pdf = Pdf::loadView('pdf.supplier-performance', [
            'suppliers' => $suppliersData,
            'startDate' => Carbon::parse($startDate)->format('M d, Y'),
            'endDate' => Carbon::parse($endDate)->format('M d, Y'),
            'totalSpent' => $suppliersData->sum('total_spent'),
            'generatedAt' => now()->format('F d, Y H:i:s'),
        ]);

        $fileName = 'supplier-performance-report-' . now()->format('Y-m-d-His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    private function calculateAverageDeliveryTime($orders)
    {
        if ($orders->isEmpty()) {
            return null;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($orders as $order) {
            if ($order->order_date && $order->received_at) {
                $totalDays += $order->order_date->diffInDays($order->received_at);
                $count++;
            }
        }

        return $count > 0 ? round($totalDays / $count, 1) : null;
    }

    private function calculateOnTimeDeliveryRate($orders)
    {
        if ($orders->isEmpty()) {
            return null;
        }

        $onTimeCount = 0;
        $totalWithExpectedDate = 0;

        foreach ($orders as $order) {
            if ($order->expected_delivery_date && $order->received_at) {
                $totalWithExpectedDate++;
                if ($order->received_at->lte($order->expected_delivery_date)) {
                    $onTimeCount++;
                }
            }
        }

        return $totalWithExpectedDate > 0 ? ($onTimeCount / $totalWithExpectedDate) * 100 : null;
    }

    // ========================================
    // DASHBOARD DATA PROPERTIES
    // ========================================

    public function getTotalProductsProperty()
    {
        return Product::active()->count();
    }

    public function getLowStockCountProperty()
    {
        return Product::lowStock()->count();
    }

    public function getTotalStockValueProperty()
    {
        return Product::active()->get()->sum('stock_value');
    }

    public function getStockMovementThisMonthProperty()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $stockIn = StockAdjustment::where('adjustment_type', 'in')
            ->whereBetween('adjustment_date', [$startOfMonth, $endOfMonth])
            ->sum('quantity');

        $stockOut = StockAdjustment::where('adjustment_type', 'out')
            ->whereBetween('adjustment_date', [$startOfMonth, $endOfMonth])
            ->sum('quantity');

        return $stockIn - abs($stockOut);
    }

    public function getPurchaseOrdersThisMonthProperty()
    {
        return PurchaseOrder::whereBetween('order_date', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])->count();
    }

    public function getActiveSuppliersProperty()
    {
        return Supplier::active()->count();
    }

    public function getInventoryByCategoryProperty()
    {
        $categories = Category::withCount('products')
            ->having('products_count', '>', 0)
            ->get();

        $total = $categories->sum('products_count');

        return $categories->map(function ($category) use ($total) {
            $percentage = $total > 0 ? round(($category->products_count / $total) * 100) : 0;
            return [
                'name' => $category->name,
                'count' => $category->products_count,
                'percentage' => $percentage,
                'color' => $category->color ?? '#6B7280',
            ];
        })->sortByDesc('count')->values()->toArray();
    }

    public function getStockMovementTrendsProperty()
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $stockIn = StockAdjustment::where('adjustment_type', 'in')
                ->whereBetween('adjustment_date', [$startOfMonth, $endOfMonth])
                ->sum('quantity');

            $stockOut = abs(StockAdjustment::where('adjustment_type', 'out')
                ->whereBetween('adjustment_date', [$startOfMonth, $endOfMonth])
                ->sum('quantity'));

            $months->push([
                'month' => $date->format('M'),
                'stock_in' => $stockIn,
                'stock_out' => $stockOut,
            ]);
        }

        return $months->toArray();
    }

    public function getTopMovingProductsProperty()
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        return Product::select('products.*')
            ->withCount(['stockAdjustments' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('adjustment_date', [$startDate, $endDate]);
            }])
            ->having('stock_adjustments_count', '>', 0)
            ->orderBy('stock_adjustments_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($product) {
                return [
                    'name' => $product->name,
                    'movements' => $product->stock_adjustments_count,
                ];
            })
            ->toArray();
    }

    public function getLowStockAlertsProperty()
    {
        return Product::lowStock()
            ->with('category')
            ->orderBy('current_stock')
            ->limit(5)
            ->get()
            ->map(function ($product) {
                $belowMin = $product->minimum_stock - $product->current_stock;
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category->name,
                    'current_stock' => $product->current_stock,
                    'minimum_stock' => $product->minimum_stock,
                    'below_minimum' => $belowMin,
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.reports.index', [
            'totalProducts' => $this->totalProducts,
            'lowStockCount' => $this->lowStockCount,
            'totalStockValue' => $this->totalStockValue,
            'stockMovementThisMonth' => $this->stockMovementThisMonth,
            'purchaseOrdersThisMonth' => $this->purchaseOrdersThisMonth,
            'activeSuppliers' => $this->activeSuppliers,
            'inventoryByCategory' => $this->inventoryByCategory,
            'stockMovementTrends' => $this->stockMovementTrends,
            'topMovingProducts' => $this->topMovingProducts,
            'lowStockAlerts' => $this->lowStockAlerts,
        ]);
    }
}

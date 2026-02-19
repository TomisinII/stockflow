<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $supplierFilter = '';
    public $statusFilter = '';
    public $stockFilter = '';
    public $perPage = 10;

    public $showFilters = false;
    public $selectedProducts = [];
    public $selectAll = false;

    public $productToDelete = null;
    public $filteredSupplierName = null; 

    protected $listeners = [
        'confirmed' => 'handleConfirmed',
        'cancelled' => 'handleCancelled',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'supplierFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'stockFilter' => ['except' => ''],
    ];

    public function mount()
    {
        $this->authorize('viewAny', Product::class);

        if (request()->query('action') === 'create-product') {
            $this->dispatch('open-modal', 'create-product');
        }

        // Check if we should filter by supplier
        if (request()->query('supplier')) {
            $supplierId = request()->query('supplier');
            $this->supplierFilter = $supplierId;
            
            // Get the supplier name for display
            $supplier = Supplier::find($supplierId);
            if ($supplier) {
                $this->filteredSupplierName = $supplier->company_name;
            }
        }

        // Check for low stock and out of stock items on page load
        $this->checkStockLevelsOnLoad();
    }

    /**
     * Check stock levels when page loads and create notifications if needed
     */
    protected function checkStockLevelsOnLoad()
    {
        try {
            $notificationService = app(NotificationService::class);

            // Get products that are out of stock
            $outOfStockProducts = Product::outOfStock()
                ->where('status', 'active')
                ->get();

            // Only create notifications if there are critical stock issues
            // and user is admin or manager
            if (Auth::user()->hasAnyRole(['Admin', 'Manager'])) {

                // Check if we already notified about these today
                $todayNotifications = \App\Models\Notification::where('user_id', Auth::id())
                    ->whereDate('created_at', today())
                    ->where('type', 'danger')
                    ->where('title', 'like', '%Out of Stock%')
                    ->count();

                // Only notify once per day about general stock issues
                if ($todayNotifications === 0 && $outOfStockProducts->count() > 0) {
                    $notificationService->create(
                        Auth::user(),
                        'danger',
                        'Stock Alert: ' . $outOfStockProducts->count() . ' Products Out of Stock',
                        "You have {$outOfStockProducts->count()} products that are currently out of stock. Please review and reorder.",
                        [
                            'out_of_stock_count' => $outOfStockProducts->count(),
                            'link' => route('products.index', ['stockFilter' => 'out']),
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            // Silently fail - don't interrupt page load
            Log::error('Failed to check stock levels on load: ' . $e->getMessage());
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingSupplierFilter()
    {
        $this->resetPage();
        
        // Update filtered supplier name when filter changes
        if ($this->supplierFilter) {
            $supplier = Supplier::find($this->supplierFilter);
            $this->filteredSupplierName = $supplier ? $supplier->company_name : null;
        } else {
            $this->filteredSupplierName = null;
        }
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingStockFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'categoryFilter', 'supplierFilter', 'statusFilter', 'stockFilter', 'filteredSupplierName']);
        $this->resetPage();
    }

    public function clearSupplierFilter()
    {
        $this->supplierFilter = '';
        $this->filteredSupplierName = null;
        $this->resetPage();
    }

    public function getProductsProperty()
    {
        $query = Product::with(['category', 'supplier'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->categoryFilter, fn($q) => $q->inCategory($this->categoryFilter))
            ->when($this->supplierFilter, fn($q) => $q->where('supplier_id', $this->supplierFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->stockFilter, function ($q) {
                if ($this->stockFilter === 'low') {
                    $q->lowStock();
                } elseif ($this->stockFilter === 'out') {
                    $q->outOfStock();
                } elseif ($this->stockFilter === 'in') {
                    $q->whereColumn('current_stock', '>', 'minimum_stock');
                }
            });

        return $query->latest()->paginate($this->perPage);
    }

    #[On('product-created')]
    #[On('product-updated')]
    #[On('product-deleted')]
    public function refreshProducts()
    {
        $this->resetPage();
    }

    public function confirmDelete($productId)
    {
        $product = Product::findOrFail($productId);
        $this->productToDelete = $productId;

        $stockWarning = '';
        if ($product->current_stock > 0) {
            $stockWarning = " This product has {$product->current_stock} units in stock.";
        }

        $this->dispatch('showConfirmModal', [
            'title' => 'Delete Product',
            'message' => "Are you sure you want to delete '{$product->name}'?{$stockWarning} This action cannot be undone and all associated data will be permanently removed.",
            'confirmText' => 'Delete',
            'confirmColor' => 'red',
            'cancelText' => 'Cancel',
            'icon' => 'danger',
        ]);
    }

    public function handleConfirmed()
    {
        if ($this->productToDelete) {
            $product = Product::find($this->productToDelete);

            $this->authorize('delete', $product);

            if ($product) {
                try {
                    $productName = $product->name;
                    $productStock = $product->current_stock;
                    $productValue = $product->stockValue;

                    // Delete the product
                    $product->delete();

                    // Notify admins and managers about product deletion
                    $notificationService = app(NotificationService::class);
                    $notificationService->notifyAdminsAndManagers(
                        'warning',
                        'Product Deleted',
                        "'{$productName}' was deleted by " . Auth::user()->name .
                        ($productStock > 0 ? " ({$productStock} units valued at ₦" . number_format($productValue, 2) . " removed from inventory)" : ""),
                        [
                            'product_name' => $productName,
                            'deleted_by' => Auth::user()->name,
                            'stock_removed' => $productStock,
                            'value_removed' => $productValue,
                        ]
                    );

                    $this->dispatch('notification-created');

                    $this->dispatch('toast', [
                        'message' => 'Product deleted successfully.',
                        'type' => 'success'
                    ]);

                    $this->productToDelete = null;
                    $this->dispatch('product-deleted');

                } catch (\Exception $e) {
                    $this->dispatch('toast', [
                        'message' => 'Failed to delete product: ' . $e->getMessage(),
                        'type' => 'error'
                    ]);
                }
            }
        }
    }

    public function handleCancelled()
    {
        $this->productToDelete = null;
    }

    /**
     * Bulk activate selected products
     */
    public function bulkActivate()
    {
        try {
            if (empty($this->selectedProducts)) {
                $this->dispatch('toast', [
                    'message' => 'Please select products first',
                    'type' => 'warning'
                ]);
                return;
            }

            $count = Product::whereIn('id', $this->selectedProducts)
                ->update(['status' => 'active']);

            $notificationService = app(NotificationService::class);
            $notificationService->notifyAdminsAndManagers(
                'info',
                'Bulk Product Activation',
                Auth::user()->name . " activated {$count} products",
                [
                    'action' => 'bulk_activate',
                    'count' => $count,
                    'performed_by' => Auth::user()->name,
                ]
            );

            $this->selectedProducts = [];
            $this->selectAll = false;

            $this->dispatch('toast', [
                'message' => "{$count} products activated successfully",
                'type' => 'success'
            ]);

            $this->dispatch('product-updated');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to activate products: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Bulk deactivate selected products
     */
    public function bulkDeactivate()
    {
        try {
            if (empty($this->selectedProducts)) {
                $this->dispatch('toast', [
                    'message' => 'Please select products first',
                    'type' => 'warning'
                ]);
                return;
            }

            $count = Product::whereIn('id', $this->selectedProducts)
                ->update(['status' => 'inactive']);

            $notificationService = app(NotificationService::class);
            $notificationService->notifyAdminsAndManagers(
                'warning',
                'Bulk Product Deactivation',
                Auth::user()->name . " deactivated {$count} products",
                [
                    'action' => 'bulk_deactivate',
                    'count' => $count,
                    'performed_by' => Auth::user()->name,
                ]
            );

            $this->selectedProducts = [];
            $this->selectAll = false;

            $this->dispatch('toast', [
                'message' => "{$count} products deactivated successfully",
                'type' => 'success'
            ]);

            $this->dispatch('product-updated');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to deactivate products: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function exportProducts()
    {
        $this->authorize('export_reports');
        
        try {
            $filename = 'products-export-' . now()->format('Y-m-d-His') . '.csv';

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
                fputcsv($file, ['StockFlow Products Export']);
                fputcsv($file, ['Generated on: ' . now()->format('F d, Y H:i:s')]);
                fputcsv($file, ['Generated by: ' . Auth::user()->name]);
                fputcsv($file, ['Filters Applied: ' . $this->getAppliedFiltersText()]);
                fputcsv($file, []); // Empty row

                // Column Headers
                fputcsv($file, [
                    'SKU',
                    'Product Name',
                    'Barcode',
                    'Category',
                    'Supplier',
                    'Unit of Measure',
                    'Cost Price (₦)',
                    'Selling Price (₦)',
                    'Current Stock',
                    'Minimum Stock',
                    'Maximum Stock',
                    'Stock Status',
                    'Stock Value (₦)',
                    'Status',
                    'Created Date',
                ]);

                // Get all products matching current filters
                $products = Product::with(['category', 'supplier'])
                    ->when($this->search, function ($q) {
                        $q->where(function ($query) {
                            $query->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('sku', 'like', '%' . $this->search . '%')
                                ->orWhere('barcode', 'like', '%' . $this->search . '%');
                        });
                    })
                    ->when($this->categoryFilter, fn($q) => $q->where('category_id', $this->categoryFilter))
                    ->when($this->supplierFilter, fn($q) => $q->where('supplier_id', $this->supplierFilter))
                    ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
                    ->when($this->stockFilter, function ($q) {
                        if ($this->stockFilter === 'low') {
                            $q->lowStock();
                        } elseif ($this->stockFilter === 'out') {
                            $q->outOfStock();
                        } elseif ($this->stockFilter === 'in') {
                            $q->whereColumn('current_stock', '>', 'minimum_stock');
                        }
                    })
                    ->latest()
                    ->get();

                // Product Rows
                foreach ($products as $product) {
                    fputcsv($file, [
                        $product->sku,
                        $product->name,
                        $product->barcode ?? 'N/A',
                        $product->category->name ?? 'Uncategorized',
                        $product->supplier->company_name ?? 'N/A',
                        $product->unit_of_measure,
                        number_format($product->cost_price, 2),
                        number_format($product->selling_price, 2),
                        $product->current_stock,
                        $product->minimum_stock,
                        $product->maximum_stock ?? 'N/A',
                        $product->stockStatus['status'],
                        number_format($product->stockValue, 2),
                        ucfirst($product->status),
                        $product->created_at->format('Y-m-d'),
                    ]);
                }

                // Summary Statistics
                fputcsv($file, []); // Empty row
                fputcsv($file, ['SUMMARY STATISTICS']);
                fputcsv($file, ['Total Products Exported', $products->count()]);
                fputcsv($file, ['Total Stock Value (₦)', number_format($products->sum(fn($p) => $p->stockValue), 2)]);
                fputcsv($file, ['Total Stock Units', number_format($products->sum('current_stock'))]);
                fputcsv($file, ['Active Products', $products->where('status', 'active')->count()]);
                fputcsv($file, ['Inactive Products', $products->where('status', 'inactive')->count()]);
                fputcsv($file, ['Low Stock Items', $products->filter(fn($p) => $p->isLowStock())->count()]);
                fputcsv($file, ['Out of Stock Items', $products->filter(fn($p) => $p->isOutOfStock())->count()]);

                // Products by Category
                fputcsv($file, []); // Empty row
                fputcsv($file, ['PRODUCTS BY CATEGORY']);
                fputcsv($file, ['Category', 'Count', 'Total Value (₦)']);

                $productsByCategory = $products->groupBy('category_id');
                foreach ($productsByCategory as $categoryId => $categoryProducts) {
                    $category = $categoryProducts->first()->category;
                    fputcsv($file, [
                        $category ? $category->name : 'Uncategorized',
                        $categoryProducts->count(),
                        number_format($categoryProducts->sum(fn($p) => $p->stockValue), 2),
                    ]);
                }

                fclose($file);
            };

            // Create notification about export
            $notificationService = app(NotificationService::class);
            $notificationService->create(
                Auth::user(),
                'info',
                'Products Export Completed',
                "You exported products data to CSV at " . now()->format('g:i A'),
                [
                    'action' => 'export_products',
                    'filename' => $filename,
                    'filters' => $this->getAppliedFiltersText(),
                ]
            );

            $this->dispatch('notification-created');

            return new StreamedResponse($callback, 200, $headers);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Failed to export products: ' . $e->getMessage()
            ]);
        }
    }

    private function getAppliedFiltersText()
    {
        $filters = [];

        if ($this->search) {
            $filters[] = 'Search: "' . $this->search . '"';
        }

        if ($this->categoryFilter) {
            $category = Category::find($this->categoryFilter);
            $filters[] = 'Category: ' . ($category ? $category->name : 'Unknown');
        }

        if ($this->supplierFilter) {
            $supplier = Supplier::find($this->supplierFilter);
            $filters[] = 'Supplier: ' . ($supplier ? $supplier->company_name : 'Unknown');
        }

        if ($this->statusFilter) {
            $filters[] = 'Status: ' . ucfirst($this->statusFilter);
        }

        if ($this->stockFilter) {
            $stockFilterNames = [
                'low' => 'Low Stock',
                'out' => 'Out of Stock',
                'in' => 'In Stock',
            ];
            $filters[] = 'Stock Level: ' . ($stockFilterNames[$this->stockFilter] ?? 'All');
        }

        return $filters ? implode(', ', $filters) : 'None';
    }

    public function render()
    {
        return view('livewire.products.index', [
            'products' => $this->products,
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::where('status', 'active')->orderBy('company_name')->get(),
        ]);
    }
}
<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
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
        if (request()->query('action') === 'create-product') {
            $this->dispatch('open-modal', 'create-product');
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
        $this->reset(['search', 'categoryFilter', 'supplierFilter', 'statusFilter', 'stockFilter']);
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

        $this->dispatch('showConfirmModal', [
            'title' => 'Delete Product',
            'message' => "Are you sure you want to delete '{$product->name}'? This action cannot be undone and all associated data will be permanently removed.",
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

            if ($product) {
                $product->delete();

                $this->dispatch('toast', [
                    'message' => 'Product deleted successfully.',
                    'type' => 'success'
                ]);

                $this->productToDelete = null;
            }
        }
    }

    public function handleCancelled()
    {
        $this->productToDelete = null;
    }

    public function exportProducts()
    {
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

                fclose($file);
            };

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

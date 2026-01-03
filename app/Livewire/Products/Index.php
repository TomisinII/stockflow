<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

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

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'supplierFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'stockFilter' => ['except' => ''],
    ];

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
            ->when($this->categoryFilter, fn($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->supplierFilter, fn($q) => $q->where('supplier_id', $this->supplierFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->stockFilter, function ($q) {
                if ($this->stockFilter === 'low') {
                    $q->lowStock();
                } elseif ($this->stockFilter === 'out') {
                    $q->outOfStock();
                } elseif ($this->stockFilter === 'in') {
                    $q->where('current_stock', '>', 'minimum_stock');
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

    public function deleteProduct($productId)
    {
        $product = Product::findOrFail($productId);

        $product->delete();

        session()->flash('message', 'Product deleted successfully.');
        $this->dispatch('product-deleted');
    }

    public function exportProducts()
    {
        // Export logic will be implemented later
        session()->flash('message', 'Export feature coming soon!');
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

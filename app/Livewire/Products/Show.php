<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;

class Show extends Component
{
    public Product $product;
    public $showStockHistory = true;
    public $productToDelete = null;

    protected $listeners = [
        'confirmed' => 'handleConfirmed',
        'cancelled' => 'handleCancelled',
    ];

    public function mount(Product $product)
    {
        $this->product = $product->load(['category', 'supplier', 'stockAdjustments.user']);
    }

    #[On('product-updated')]
    public function refreshProduct()
    {
        $this->product->refresh();
        $this->product->load(['category', 'supplier', 'stockAdjustments.user']);
    }

    #[On('product-deleted')]
    public function handleProductDeleted()
    {
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function confirmDelete($productId)
    {
        $product = Product::findOrFail($productId);
        $this->productToDelete = $productId;

        $this->dispatch('showConfirmModal', [
            'title' => 'Delete Product',
            'message' => "Are you sure you want to delete '{$product->name}'? This action cannot be undone and all associated data will be permanently removed.",
            'confirmText' => 'Delete Product',
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
                $productName = $product->name;
                $product->delete();

                $this->productToDelete = null;

                return redirect()->route('products.index')->with('toast', [
                    'type' => 'success',
                    'message' => "Product '{$productName}' deleted successfully.",
                ]);
            }
        }
    }

    public function handleCancelled()
    {
        $this->productToDelete = null;
    }

    public function render()
    {
        return view('livewire.products.show', [
            'recentAdjustments' => $this->product->stockAdjustments()
                ->with('user')
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }
}

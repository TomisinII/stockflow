<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
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

    public function confirmDelete($productId)
    {
        $product = Product::findOrFail($productId);
        $this->productToDelete = $productId;

        $stockWarning = '';
        if ($product->current_stock > 0) {
            $stockWarning = " This product has {$product->current_stock} units in stock worth ₦" . number_format($product->stockValue, 2) . ".";
        }

        $this->dispatch('showConfirmModal', [
            'title' => 'Delete Product',
            'message' => "Are you sure you want to delete '{$product->name}'?{$stockWarning} This action cannot be undone and all associated data will be permanently removed.",
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
                try {
                    $productName = $product->name;
                    $productSku = $product->sku;
                    $productStock = $product->current_stock;
                    $productValue = $product->stockValue;

                    // Delete the product
                    $product->delete();

                    // Notify admins and managers about product deletion
                    $notificationService = app(NotificationService::class);
                    $notificationService->notifyAdminsAndManagers(
                        'warning',
                        'Product Deleted',
                        "'{$productName}' (SKU: {$productSku}) was deleted by " . Auth::user()->name .
                        ($productStock > 0 ? " ({$productStock} units valued at ₦" . number_format($productValue, 2) . " removed from inventory)" : ""),
                        [
                            'product_name' => $productName,
                            'product_sku' => $productSku,
                            'deleted_by' => Auth::user()->name,
                            'stock_removed' => $productStock,
                            'value_removed' => $productValue,
                            'link' => route('products.index'),
                        ]
                    );

                    $this->dispatch('notification-created');

                    $this->productToDelete = null;

                    return redirect()->route('products.index')->with('toast', [
                        'type' => 'success',
                        'message' => "Product '{$productName}' deleted successfully.",
                    ]);

                } catch (\Exception $e) {
                    $this->dispatch('toast', [
                        'message' => 'Failed to delete product: ' . $e->getMessage(),
                        'type' => 'error'
                    ]);

                    $this->productToDelete = null;
                }
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

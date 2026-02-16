<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Services\NotificationService;
use App\Services\BarcodeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class Show extends Component
{
    public Product $product;
    public $showStockHistory = true;
    public $productToDelete = null;
    public $showQuickAdjustModal = false;
    public $activeTab = 'recent'; // recent, all

    protected $listeners = [
        'confirmed' => 'handleConfirmed',
        'cancelled' => 'handleCancelled',
        'adjustment-created' => 'handleAdjustmentCreated',
    ];

    public function mount(Product $product)
    {
        $this->product = $product->load(['category', 'supplier', 'stockAdjustments.adjuster']);
    }

    #[On('product-updated')]
    public function refreshProduct()
    {
        $this->product->refresh();
        $this->product->load(['category', 'supplier', 'stockAdjustments.adjuster']);
    }

    public function openQuickAdjust()
    {
        $this->showQuickAdjustModal = true;
        $this->dispatch('open-modal', 'quick-adjust');
    }

    #[On('adjustment-created')]
    public function handleAdjustmentCreated()
    {
        $this->showQuickAdjustModal = false;
        $this->refreshProduct();
        
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Stock adjustment saved successfully!'
        ]);
    }

    public function printBarcode()
    {
        try {
            $barcodeService = app(BarcodeService::class);
            
            // Generate barcode label HTML
            $html = $barcodeService->generateBarcodeLabel($this->product, 'standard');
            
            // Store temporarily for printing
            $filename = 'barcode-' . $this->product->sku . '-' . time() . '.html';
            $path = storage_path('app/temp/' . $filename);
            
            // Create temp directory if it doesn't exist
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            file_put_contents($path, $html);
            
            // Dispatch event to open print window
            $this->dispatch('print-barcode', [
                'url' => route('barcode.print', ['product' => $this->product->id])
            ]);
            
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Barcode ready for printing!'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Failed to generate barcode: ' . $e->getMessage()
            ]);
        }
    }

    public function viewFullHistory()
    {
        return redirect()->route('stock_adjustments.index', [
            'product' => $this->product->id
        ]);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
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
        $adjustmentsQuery = $this->product->stockAdjustments()
            ->with('adjuster')
            ->latest();

        if ($this->activeTab === 'recent') {
            $adjustmentsQuery->take(10);
        }

        return view('livewire.products.show', [
            'stockAdjustments' => $adjustmentsQuery->get(),
        ]);
    }
}
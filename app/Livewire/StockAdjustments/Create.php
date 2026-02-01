<?php

namespace App\Livewire\StockAdjustments;

use App\Models\StockAdjustment;
use App\Models\Product;
use App\Services\NotificationService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $product_id = '';
    public $adjustment_type = 'in';
    public $quantity = '';
    public $reason = '';
    public $reference = '';
    public $notes = '';

    public $selectedProduct = null;
    public $searchProduct = '';
    public $filteredProducts = [];
    public $showProductDropdown = false;

    public $adjustmentTypes = [
        'in' => 'Stock In',
        'out' => 'Stock Out',
        'correction' => 'Correction'
    ];

    public $reasons = [
        'purchase' => 'Purchase',
        'sale' => 'Sale',
        'damaged' => 'Damaged',
        'expired' => 'Expired',
        'theft' => 'Theft',
        'stocktake' => 'Stocktake',
        'return' => 'Customer Return',
        'transfer_in' => 'Transfer In',
        'transfer_out' => 'Transfer Out',
        'other' => 'Other'
    ];

    public function mount($productId = null)
    {
        if ($productId) {
            $this->product_id = $productId;
            $this->selectedProduct = Product::find($productId);
            if ($this->selectedProduct) {
                $this->searchProduct = $this->selectedProduct->name . ' (' . $this->selectedProduct->sku . ')';
            }
        }

        // Set default reason based on adjustment type
        $this->reason = 'purchase';
    }

    public function updatedSearchProduct($value)
    {
        if (strlen($value) >= 2) {
            $this->filteredProducts = Product::query()
                ->where('status', 'active')
                ->where(function($query) use ($value) {
                    $query->where('name', 'like', '%' . $value . '%')
                          ->orWhere('sku', 'like', '%' . $value . '%');
                })
                ->limit(10)
                ->get();
            $this->showProductDropdown = true;
        } else {
            $this->filteredProducts = [];
            $this->showProductDropdown = false;
        }
    }

    public function selectProduct($productId)
    {
        $this->product_id = $productId;
        $this->selectedProduct = Product::find($productId);
        $this->searchProduct = $this->selectedProduct->name . ' (' . $this->selectedProduct->sku . ')';
        $this->filteredProducts = [];
        $this->showProductDropdown = false;
    }

    public function clearProduct()
    {
        $this->product_id = '';
        $this->selectedProduct = null;
        $this->searchProduct = '';
        $this->filteredProducts = [];
    }

    public function updatedAdjustmentType($value)
    {
        // Set default reason based on type
        if ($value === 'in') {
            $this->reason = 'purchase';
        } elseif ($value === 'out') {
            $this->reason = 'sale';
        } else {
            $this->reason = 'stocktake';
        }
    }

    protected function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'adjustment_type' => 'required|in:in,out,correction',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ];
    }

    protected $messages = [
        'product_id.required' => 'Please select a product',
        'quantity.min' => 'Quantity must be at least 1',
        'quantity.required' => 'Please enter a quantity',
    ];

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($this->product_id);
            $oldStock = $product->current_stock;

            // Calculate the actual quantity change
            if ($this->adjustment_type === 'out') {
                // Check if we have enough stock
                if ($product->current_stock < $this->quantity) {
                    $this->addError('quantity',
                        'Insufficient stock. Available: ' . $product->current_stock . ' ' . $product->unit_of_measure
                    );
                    DB::rollBack();
                    return;
                }
                $quantityChange = -$this->quantity;
            } elseif ($this->adjustment_type === 'in') {
                $quantityChange = $this->quantity;
            } else {
                // For correction, the quantity is the new total stock value
                $quantityChange = $this->quantity - $product->current_stock;
            }

            // Create the adjustment record
            $stockAdjustment = StockAdjustment::create([
                'product_id' => $this->product_id,
                'adjustment_type' => $this->adjustment_type,
                'quantity' => $quantityChange,
                'reason' => $this->reason,
                'reference' => $this->reference,
                'notes' => $this->notes,
                'adjusted_by' => Auth::id(),
                'adjustment_date' => now(),
            ]);

            // Update product stock
            $product->current_stock += $quantityChange;
            $product->save();

            // Create notification for the adjustment
            $notificationService = app(NotificationService::class);

            $adjustmentTypeText = [
                'in' => 'Stock In',
                'out' => 'Stock Out',
                'correction' => 'Stock Correction'
            ][$this->adjustment_type];

            $notificationService->create(
                Auth::user(),
                'info',
                'Stock Adjustment Completed',
                "{$adjustmentTypeText} for {$product->name}: {$this->formatQuantityChange($quantityChange)} {$product->unit_of_measure}. Stock: {$oldStock} → {$product->current_stock}",
                [
                    'action' => 'stock_adjustment',
                    'stock_adjustment_id' => $stockAdjustment->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'adjustment_type' => $this->adjustment_type,
                    'quantity_change' => $quantityChange,
                    'old_stock' => $oldStock,
                    'new_stock' => $product->current_stock,
                    'reason' => $this->reason,
                    'link' => route('products.show', $product),
                ]
            );

            // Check if stock levels trigger alerts
            if ($product->current_stock <= 0) {
                // Out of stock alert
                $notificationService->createOutOfStockAlert(Auth::user(), $product);
            } elseif ($product->current_stock <= $product->minimum_stock) {
                // Low stock alert
                $notificationService->createLowStockAlert(Auth::user(), $product);
            }

            DB::commit();

            // Reset form
            $this->reset(['product_id', 'quantity', 'reference', 'notes', 'selectedProduct', 'searchProduct']);
            $this->adjustment_type = 'in';
            $this->reason = 'purchase';

            // Close modal and notify
            $this->dispatch('close-modal', 'create-adjustment');
            $this->dispatch('adjustment-created');
            $this->dispatch('notification-created');

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Failed to create adjustment: ' . $e->getMessage()
            ]);
        }
    }

    private function formatQuantityChange($quantity)
    {
        if ($quantity > 0) {
            return '+' . $quantity;
        }
        return (string) $quantity;
    }

    public function closeModal()
    {
        $this->reset();
        $this->dispatch('close-modal', 'create-adjustment');
    }

    public function render()
    {
        return view('livewire.stock-adjustments.create');
    }
}

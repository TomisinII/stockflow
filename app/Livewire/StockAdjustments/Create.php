<?php

namespace App\Livewire\StockAdjustments;

use App\Models\StockAdjustment;
use App\Models\Product;
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

            // Calculate the actual quantity change
            if ($this->adjustment_type === 'out') {
                // Check if we have enough stock
                if ($product->current_stock < $this->quantity) {
                    $this->addError('quantity',
                        'Insufficient stock. Available: ' . $product->current_stock . ' ' . $product->unit_of_measure
                    );
                    return;
                }
                $quantityChange = -$this->quantity;
            } elseif ($this->adjustment_type === 'in') {
                $quantityChange = $this->quantity;
            } else {
                // For correction, the quantity is the difference
                $quantityChange = $this->quantity - $product->current_stock;
            }

            // Create the adjustment record
            StockAdjustment::create([
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

            DB::commit();

            // Reset form
            $this->reset(['product_id', 'quantity', 'reference', 'notes', 'selectedProduct', 'searchProduct']);
            $this->adjustment_type = 'in';
            $this->reason = 'purchase';

            // Close modal and notify
            $this->dispatch('close-modal', 'create-adjustment');
            $this->dispatch('adjustment-created');

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Failed to create adjustment: ' . $e->getMessage()
            ]);
        }
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

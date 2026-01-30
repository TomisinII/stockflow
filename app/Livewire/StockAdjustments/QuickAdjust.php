<?php

namespace App\Livewire\StockAdjustments;

use App\Models\StockAdjustment;
use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class QuickAdjust extends Component
{
    public Product $product;
    public $adjustment_type = 'in';
    public $quantity = '';
    public $reason = '';
    public $reference = '';
    public $notes = '';
    public $adjustment_date;

    public $reasons = [
        'purchase' => 'Purchase',
        'sale' => 'Sale',
        'damaged' => 'Damaged',
        'expired' => 'Expired',
        'theft' => 'Theft/Loss',
        'stocktake' => 'Stocktake',
        'return' => 'Customer Return',
        'other' => 'Other'
    ];

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->adjustment_date = now()->format('Y-m-d');
        $this->reason = 'purchase';
    }

    protected $rules = [
        'adjustment_type' => 'required|in:in,out,correction',
        'quantity' => 'required|integer|min:1',
        'reason' => 'required|string|max:255',
        'reference' => 'nullable|string|max:100',
        'notes' => 'nullable|string',
        'adjustment_date' => 'required|date',
    ];

    public function save()
    {
        $this->validate();

        // Check if adjustment would make stock negative
        if ($this->adjustment_type === 'out' && $this->product->current_stock < $this->quantity) {
            $this->addError('quantity', 'Cannot adjust stock out more than available stock (Current: ' . $this->product->current_stock . ')');
            return;
        }

        // For corrections, calculate the difference
        if ($this->adjustment_type === 'correction') {
            $quantity = $this->quantity - $this->product->current_stock;
        } else {
            $quantity = $this->adjustment_type === 'in' ? $this->quantity : -$this->quantity;
        }

        // Create the adjustment
        $adjustment = StockAdjustment::create([
            'product_id' => $this->product->id,
            'adjustment_type' => $this->adjustment_type,
            'quantity' => $quantity,
            'reason' => $this->reason,
            'reference' => $this->reference,
            'notes' => $this->notes,
            'adjusted_by' => Auth::id(),
            'adjustment_date' => $this->adjustment_date,
        ]);

        // Update product stock
        $this->product->current_stock += $quantity;
        $this->product->save();

        // Dispatch event
        $this->dispatch('adjustment-created');

        // Reset form
        $this->quantity = '';
        $this->reference = '';
        $this->notes = '';
    }

    public function closeModal()
    {
        $this->reset(['quantity', 'reference', 'notes']);
        $this->dispatch('close-modal', 'quick-adjust');
    }

    public function render()
    {
        return view('livewire.stock-adjustments.quick-adjust');
    }
}

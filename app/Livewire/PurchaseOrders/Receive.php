<?php

namespace App\Livewire\PurchaseOrders;

use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Receive extends Component
{
    public PurchaseOrder $purchaseOrder;
    public $items = [];
    public $received_at;
    public $notes = '';

    protected $rules = [
        'items.*.quantity_received' => 'required|integer|min:0',
        'received_at' => 'required|date',
        'notes' => 'nullable|string',
    ];

    public function mount($purchaseOrderId)
    {
        $this->purchaseOrder = PurchaseOrder::with(['items.product'])->findOrFail($purchaseOrderId);
        $this->received_at = now()->format('Y-m-d');

        // Initialize items array
        foreach ($this->purchaseOrder->items as $item) {
            $this->items[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'sku' => $item->product->sku,
                'quantity_ordered' => $item->quantity_ordered,
                'quantity_received' => $item->quantity_ordered, // Default to full quantity
                'unit_cost' => $item->unit_cost,
            ];
        }
    }

    public function receiveAll()
    {
        foreach ($this->items as $index => $item) {
            $this->items[$index]['quantity_received'] = $item['quantity_ordered'];
        }
    }

    public function receive()
    {
        $this->validate();

        DB::transaction(function () {
            // Update purchase order status
            $this->purchaseOrder->update([
                'status' => 'received',
                'received_by' => Auth::id(),
                'received_at' => $this->received_at,
            ]);

            // Update each item and adjust stock
            foreach ($this->items as $item) {
                $poItem = $this->purchaseOrder->items()->find($item['id']);

                $poItem->update([
                    'quantity_received' => $item['quantity_received'],
                ]);

                // Update product stock
                $product = $poItem->product;
                $product->increment('current_stock', $item['quantity_received']);

                // Create stock adjustment record
                $product->stockAdjustments()->create([
                    'adjustment_type' => 'in',
                    'quantity' => $item['quantity_received'],
                    'reason' => 'Purchase Order Received',
                    'reference' => $this->purchaseOrder->po_number,
                    'notes' => $this->notes,
                    'adjusted_by' => Auth::id(),
                    'adjustment_date' => $this->received_at,
                ]);
            }
        });

        $this->closeModal();
        $this->dispatch('purchase-order-received');
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', 'receive-purchase-order-' . $this->purchaseOrder->id);
    }

    public function render()
    {
        return view('livewire.purchase-orders.receive');
    }
}

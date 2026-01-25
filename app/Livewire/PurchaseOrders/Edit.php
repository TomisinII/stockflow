<?php

namespace App\Livewire\PurchaseOrders;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Edit extends Component
{
    public PurchaseOrder $purchaseOrder;
    public $supplier_id;
    public $order_date;
    public $expected_delivery_date;
    public $notes;
    public $status;

    // Products management
    public $selectedProducts = [];
    public $productSearch = '';
    public $showProductDropdown = false;

    protected $rules = [
        'supplier_id' => 'required|exists:suppliers,id',
        'order_date' => 'required|date',
        'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
        'notes' => 'nullable|string',
        'status' => 'required|in:draft,sent',
        'selectedProducts.*.product_id' => 'required|exists:products,id',
        'selectedProducts.*.quantity' => 'required|integer|min:1',
        'selectedProducts.*.unit_cost' => 'required|numeric|min:0',
    ];

    protected $messages = [
        'supplier_id.required' => 'Please select a supplier',
        'order_date.required' => 'Order date is required',
        'expected_delivery_date.after_or_equal' => 'Expected delivery must be on or after order date',
        'selectedProducts.*.quantity.required' => 'Quantity is required',
        'selectedProducts.*.quantity.min' => 'Quantity must be at least 1',
        'selectedProducts.*.unit_cost.required' => 'Unit cost is required',
    ];

    public function mount($purchaseOrderId)
    {
        $this->purchaseOrder = PurchaseOrder::with(['items.product'])->findOrFail($purchaseOrderId);

        // Populate form fields
        $this->supplier_id = $this->purchaseOrder->supplier_id;
        $this->order_date = $this->purchaseOrder->order_date->format('Y-m-d');
        $this->expected_delivery_date = $this->purchaseOrder->expected_delivery_date
            ? $this->purchaseOrder->expected_delivery_date->format('Y-m-d')
            : '';
        $this->notes = $this->purchaseOrder->notes;
        $this->status = $this->purchaseOrder->status;

        // Load existing items with proper type casting
        foreach ($this->purchaseOrder->items as $item) {
            $this->selectedProducts[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'sku' => $item->product->sku,
                'quantity' => (int) $item->quantity_ordered,
                'unit_cost' => (float) $item->unit_cost,
            ];
        }
    }

    public function addProduct($productId)
    {
        $product = Product::find($productId);

        if (!$product) return;

        // Check if product already added
        $exists = collect($this->selectedProducts)->firstWhere('product_id', $productId);

        if ($exists) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Product already added to this order'
            ]);
            return;
        }

        $this->selectedProducts[] = [
            'id' => null, // New item
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 1,
            'unit_cost' => (float) $product->cost_price,
        ];

        $this->productSearch = '';
        $this->showProductDropdown = false;
    }

    public function removeProduct($index)
    {
        unset($this->selectedProducts[$index]);
        $this->selectedProducts = array_values($this->selectedProducts);
    }

    public function updatedProductSearch()
    {
        $this->showProductDropdown = strlen($this->productSearch) > 0;
    }

    public function getSearchResults()
    {
        if (strlen($this->productSearch) < 1) {
            return collect();
        }

        $selectedIds = collect($this->selectedProducts)->pluck('product_id');

        return Product::where('status', 'active')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->productSearch . '%')
                    ->orWhere('sku', 'like', '%' . $this->productSearch . '%');
            })
            ->whereNotIn('id', $selectedIds)
            ->limit(10)
            ->get();
    }

    public function calculateTotal()
    {
        return collect($this->selectedProducts)->sum(function ($item) {
            $quantity = (float) ($item['quantity'] ?? 0);
            $unitCost = (float) ($item['unit_cost'] ?? 0);
            return $quantity * $unitCost;
        });
    }

    public function update()
    {
        if (empty($this->selectedProducts)) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Please add at least one product to the purchase order'
            ]);
            return;
        }

        $this->validate();

        DB::transaction(function () {
            // Update purchase order
            $this->purchaseOrder->update([
                'supplier_id' => $this->supplier_id,
                'order_date' => $this->order_date,
                'expected_delivery_date' => $this->expected_delivery_date ?: null,
                'status' => $this->status,
                'total_amount' => $this->calculateTotal(),
                'notes' => $this->notes ?: null,
            ]);

            // Delete removed items
            $existingItemIds = collect($this->selectedProducts)
                ->filter(fn($item) => isset($item['id']) && $item['id'])
                ->pluck('id');

            $this->purchaseOrder->items()
                ->whereNotIn('id', $existingItemIds)
                ->delete();

            // Update or create items
            foreach ($this->selectedProducts as $product) {
                $quantity = (int) $product['quantity'];
                $unitCost = (float) $product['unit_cost'];

                if (isset($product['id']) && $product['id']) {
                    // Update existing item
                    $this->purchaseOrder->items()->where('id', $product['id'])->update([
                        'product_id' => $product['product_id'],
                        'quantity_ordered' => $quantity,
                        'unit_cost' => $unitCost,
                        'subtotal' => $quantity * $unitCost,
                    ]);
                } else {
                    // Create new item
                    $this->purchaseOrder->items()->create([
                        'product_id' => $product['product_id'],
                        'quantity_ordered' => $quantity,
                        'unit_cost' => $unitCost,
                        'subtotal' => $quantity * $unitCost,
                    ]);
                }
            }
        });

        $this->closeModal();
        $this->dispatch('purchase-order-updated');
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', 'edit-purchase-order-' . $this->purchaseOrder->id);
    }

    public function render()
    {
        return view('livewire.purchase-orders.edit', [
            'suppliers' => Supplier::where('status', 'active')->orderBy('company_name')->get(),
            'searchResults' => $this->getSearchResults(),
            'totalAmount' => $this->calculateTotal(),
        ]);
    }
}

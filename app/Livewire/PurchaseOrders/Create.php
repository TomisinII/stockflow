<?php

namespace App\Livewire\PurchaseOrders;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $supplier_id = '';
    public $order_date;
    public $expected_delivery_date = '';
    public $notes = '';
    public $status = 'draft';
    public $preselectedSupplierId = null;

    // Products management
    public $selectedProducts = [];
    public $productSearch = '';
    public $showProductDropdown = false;

    // Auto-generated PO number
    public $po_number;

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

    public function mount($preselectedSupplierId = null)
    {
        $this->authorize('create', PurchaseOrder::class);
        
        $this->order_date = now()->format('Y-m-d');
        $this->po_number = $this->generatePONumber();
        
        // Set the preselected supplier if provided
        if ($preselectedSupplierId) {
            $this->preselectedSupplierId = $preselectedSupplierId;
            $this->supplier_id = $preselectedSupplierId;
        }
    }

    public function generatePONumber()
    {
        $year = date('Y');
        $lastPO = PurchaseOrder::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastPO ? (intval(substr($lastPO->po_number, -4)) + 1) : 1;

        return 'PO-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
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

    public function save($sendToSupplier = false)
    {
        if (empty($this->selectedProducts)) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Please add at least one product to the purchase order'
            ]);
            return;
        }

        $this->status = $sendToSupplier ? 'sent' : 'draft';

        $this->validate();

        $purchaseOrder = null;

        DB::transaction(function () use (&$purchaseOrder) {
            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $this->po_number,
                'supplier_id' => $this->supplier_id,
                'order_date' => $this->order_date,
                'expected_delivery_date' => $this->expected_delivery_date ?: null,
                'status' => $this->status,
                'total_amount' => $this->calculateTotal(),
                'notes' => $this->notes ?: null,
                'created_by' => Auth::id(),
            ]);

            foreach ($this->selectedProducts as $product) {
                $quantity = (int) $product['quantity'];
                $unitCost = (float) $product['unit_cost'];

                $purchaseOrder->items()->create([
                    'product_id' => $product['product_id'],
                    'quantity_ordered' => $quantity,
                    'unit_cost' => $unitCost,
                    'subtotal' => $quantity * $unitCost,
                ]);
            }
        });

        // Create notification about new purchase order
        if ($purchaseOrder) {
            $notificationService = app(NotificationService::class);
            $supplier = Supplier::find($this->supplier_id);
            $itemsCount = count($this->selectedProducts);
            $totalAmount = $this->calculateTotal();

            $notificationService->notifyAdminsAndManagers(
                type: $this->status === 'sent' ? 'info' : 'success',
                title: 'Purchase Order Created',
                message: "{$purchaseOrder->po_number} for {$supplier->company_name} was created by " . Auth::user()->name . " with {$itemsCount} items totaling ₦" . number_format($totalAmount, 2) . ". Status: " . ucfirst($this->status) . ".",
                data: [
                    'po_number' => $purchaseOrder->po_number,
                    'supplier_id' => $supplier->id,
                    'supplier_name' => $supplier->company_name,
                    'items_count' => $itemsCount,
                    'total_amount' => $totalAmount,
                    'status' => $this->status,
                    'created_by' => Auth::user()->name,
                    'link' => route('purchase_orders.show', $purchaseOrder),
                ]
            );

            $this->dispatch('notification-created');
        }

        $this->closeModal();
        $this->dispatch('purchase-order-created');
    }

    public function saveAsDraft()
    {
        $this->save(false);
    }

    public function saveAndSend()
    {
        $this->save(true);
    }

    public function closeModal()
    {
        $this->reset();
        $this->dispatch('close-modal', 'create-purchase-order');
    }

    public function render()
    {
        return view('livewire.purchase-orders.create', [
            'suppliers' => Supplier::where('status', 'active')->orderBy('company_name')->get(),
            'searchResults' => $this->getSearchResults(),
            'totalAmount' => $this->calculateTotal(),
        ]);
    }
}
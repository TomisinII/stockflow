<?php

namespace App\Livewire\PurchaseOrders;

use App\Models\PurchaseOrder;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Receive extends Component
{
    public PurchaseOrder $purchaseOrder;
    public $items = [];
    public $received_at;
    public $notes = '';

    // Add this property to store summary data
    public $summary = [];

    protected $rules = [
        'items.*.quantity_received' => 'required|integer|min:0',
        'received_at' => 'required|date',
        'notes' => 'nullable|string',
    ];

    public function mount($purchaseOrderId)
    {
        $this->purchaseOrder = PurchaseOrder::with(['items.product', 'supplier'])->findOrFail($purchaseOrderId);

        $this->authorize('recieve', $this->purchaseOrder);

        // Check if PO can be received
        if (!$this->purchaseOrder->canBeReceived()) {
            abort(403, 'This purchase order cannot be received.');
        }

        $this->received_at = now()->format('Y-m-d');

        // Initialize items array using model methods
        foreach ($this->purchaseOrder->items as $item) {
            $this->items[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'sku' => $item->product->sku,
                'quantity_ordered' => $item->quantity_ordered,
                'quantity_received' => $item->remaining_quantity, // Show remaining quantity
                'unit_cost' => $item->unit_cost,
                'formatted_unit_cost' => $item->formatted_unit_cost,
                'remaining_quantity' => $item->remaining_quantity,
                'is_fully_received' => $item->isFullyReceived(),
                'receiving_progress' => $item->receiving_progress,
            ];
        }

        // Initialize summary
        $this->calculateSummary();
    }

    public function calculateSummary()
    {
        $summary = $this->purchaseOrder->receiving_summary ?? [];

        // Add current session's progress
        $currentReceived = 0;
        foreach ($this->items as $item) {
            $currentReceived += $item['quantity_received'] ?? 0;
        }

        $totalOrdered = $summary['total_ordered'] ?? $this->purchaseOrder->total_quantity_ordered;

        $this->summary = [
            'total_ordered' => $totalOrdered,
            'total_received' => $summary['total_received'] ?? 0,
            'total_to_receive' => $summary['total_to_receive'] ?? 0,
            'progress_percentage' => $summary['progress_percentage'] ?? 0,
            'current_session_received' => $currentReceived,
            'current_session_progress' => $totalOrdered > 0
                ? round(($currentReceived / $totalOrdered) * 100, 2)
                : 0,
        ];
    }

    public function updatedItems()
    {
        // Recalculate summary when items change
        $this->calculateSummary();
    }

    public function receiveAll()
    {
        foreach ($this->items as $index => $item) {
            $this->items[$index]['quantity_received'] = $item['remaining_quantity'];
        }

        $this->calculateSummary();
    }

    public function validateQuantity($index)
    {
        $this->validateOnly("items.{$index}.quantity_received", [
            "items.{$index}.quantity_received" => [
                'required',
                'integer',
                'min:0',
                'max:' . $this->items[$index]['remaining_quantity']
            ],
        ]);
    }

    public function receive()
    {
        // Validate all items
        $this->validate();

        $totalItemsReceived = 0;
        $productsUpdated = [];

        DB::transaction(function () use (&$totalItemsReceived, &$productsUpdated) {
            // Update purchase order status
            $this->purchaseOrder->update([
                'status' => 'received',
                'received_by' => Auth::id(),
                'received_at' => $this->received_at,
            ]);

            // Update each item and adjust stock
            foreach ($this->items as $item) {
                $quantityReceived = (int) $item['quantity_received'];

                if ($quantityReceived > 0) {
                    $poItem = $this->purchaseOrder->items()->find($item['id']);

                    $poItem->update([
                        'quantity_received' => $poItem->quantity_received + $quantityReceived,
                    ]);

                    // Update product stock
                    $product = $poItem->product;
                    $oldStock = $product->current_stock;
                    $product->increment('current_stock', $quantityReceived);

                    $productsUpdated[] = [
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'quantity_received' => $quantityReceived,
                        'old_stock' => $oldStock,
                        'new_stock' => $product->fresh()->current_stock,
                    ];

                    // Create stock adjustment record
                    $product->stockAdjustments()->create([
                        'adjustment_type' => 'in',
                        'quantity' => $quantityReceived,
                        'reason' => 'Purchase Order Received',
                        'reference' => $this->purchaseOrder->po_number,
                        'notes' => $this->notes,
                        'adjusted_by' => Auth::id(),
                        'adjustment_date' => $this->received_at,
                    ]);

                    $totalItemsReceived += $quantityReceived;
                }
            }
        });

        // Create notification about PO being received
        $notificationService = app(NotificationService::class);

        // Use the helper method from NotificationService
        $notificationService->createPurchaseOrderReceived(
            Auth::user(),
            $this->purchaseOrder->fresh()
        );

        // Also notify all admins and managers
        $notificationService->notifyAdminsAndManagers(
            type: 'success',
            title: 'Purchase Order Received',
            message: "{$this->purchaseOrder->po_number} from {$this->purchaseOrder->supplier->company_name} has been received by " . Auth::user()->name . ". Total items received: {$totalItemsReceived}. Inventory has been updated.",
            data: [
                'po_number' => $this->purchaseOrder->po_number,
                'supplier_name' => $this->purchaseOrder->supplier->company_name,
                'total_items_received' => $totalItemsReceived,
                'products_updated' => $productsUpdated,
                'received_by' => Auth::user()->name,
                'received_at' => $this->received_at,
                'link' => route('purchase_orders.show', $this->purchaseOrder),
            ]
        );

        $this->dispatch('notification-created');

        $this->closeModal();
        $this->dispatch('purchase-order-received');

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Purchase order received! {$totalItemsReceived} items added to inventory."
        ]);
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

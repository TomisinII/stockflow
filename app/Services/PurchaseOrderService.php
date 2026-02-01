<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Services\StockService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderService
{
    protected $stockService;
    protected $notificationService;

    public function __construct(
        StockService $stockService,
        NotificationService $notificationService
    ) {
        $this->stockService = $stockService;
        $this->notificationService = $notificationService;
    }

    /**
     * Generate unique PO number
     */
    public function generatePONumber(): string
    {
        $year = now()->year;
        $lastPO = PurchaseOrder::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastPO ? intval(substr($lastPO->po_number, -4)) + 1 : 1;

        return sprintf('PO-%d-%04d', $year, $nextNumber);
    }

    /**
     * Create a new purchase order
     */
    public function createPurchaseOrder(array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data) {
            // Create PO
            $po = PurchaseOrder::create([
                'po_number' => $this->generatePONumber(),
                'supplier_id' => $data['supplier_id'],
                'order_date' => $data['order_date'] ?? now(),
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'total_amount' => 0, // Will calculate after adding items
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Add items
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $this->addItemToPurchaseOrder($po, $item);
                }
            }

            // Recalculate total
            $this->recalculateTotal($po);

            return $po->fresh(['items.product', 'supplier']);
        });
    }

    /**
     * Add item to purchase order
     */
    public function addItemToPurchaseOrder(PurchaseOrder $po, array $itemData): PurchaseOrderItem
    {
        $product = Product::findOrFail($itemData['product_id']);
        $quantity = $itemData['quantity'];
        $unitCost = $itemData['unit_cost'] ?? $product->cost_price;

        $item = PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'product_id' => $product->id,
            'quantity_ordered' => $quantity,
            'quantity_received' => 0,
            'unit_cost' => $unitCost,
            'subtotal' => $quantity * $unitCost,
        ]);

        $this->recalculateTotal($po);

        return $item;
    }

    /**
     * Update PO item
     */
    public function updatePurchaseOrderItem(PurchaseOrderItem $item, array $data): PurchaseOrderItem
    {
        $quantity = $data['quantity'] ?? $item->quantity_ordered;
        $unitCost = $data['unit_cost'] ?? $item->unit_cost;

        $item->update([
            'quantity_ordered' => $quantity,
            'unit_cost' => $unitCost,
            'subtotal' => $quantity * $unitCost,
        ]);

        $this->recalculateTotal($item->purchaseOrder);

        return $item->fresh();
    }

    /**
     * Remove item from PO
     */
    public function removeItemFromPurchaseOrder(PurchaseOrderItem $item): void
    {
        $po = $item->purchaseOrder;
        $item->delete();
        $this->recalculateTotal($po);
    }

    /**
     * Recalculate PO total
     */
    protected function recalculateTotal(PurchaseOrder $po): void
    {
        $total = $po->items()->sum('subtotal');
        $po->update(['total_amount' => $total]);
    }

    /**
     * Update PO status
     */
    public function updateStatus(PurchaseOrder $po, string $status): PurchaseOrder
    {
        $po->update(['status' => $status]);

        // Send notification based on status change
        if ($status === 'sent') {
            $this->notificationService->notifyAdminsAndManagers(
                type: 'info',
                title: 'Purchase Order Sent',
                message: "{$po->po_number} has been sent to {$po->supplier->company_name}.",
                data: [
                    'purchase_order_id' => $po->id,
                    'po_number' => $po->po_number,
                    'supplier_name' => $po->supplier->company_name,
                    'link' => route('purchase_orders.show', $po),
                ]
            );
        }

        return $po->fresh();
    }

    /**
     * Receive purchase order (full)
     */
    public function receivePurchaseOrder(PurchaseOrder $po, ?string $notes = null): PurchaseOrder
    {
        return DB::transaction(function () use ($po, $notes) {
            // Mark all items as fully received
            foreach ($po->items as $item) {
                $this->receiveItem($item, $item->quantity_ordered);
            }

            // Update PO status
            $po->update([
                'status' => 'received',
                'received_by' => Auth::id(),
                'received_at' => now(),
                'notes' => $notes ? ($po->notes . "\n\nReceived: " . $notes) : $po->notes,
            ]);

            // Send notification
            $this->notificationService->notifyAdminsAndManagers(
                type: 'success',
                title: 'Purchase Order Received',
                message: "{$po->po_number} from {$po->supplier->company_name} has been marked as received. {$po->items->sum('quantity_received')} items added to inventory.",
                data: [
                    'purchase_order_id' => $po->id,
                    'po_number' => $po->po_number,
                    'supplier_name' => $po->supplier->company_name,
                    'total_items' => $po->items->sum('quantity_received'),
                    'link' => route('purchase_orders.show', $po),
                ]
            );

            return $po->fresh(['items.product', 'supplier']);
        });
    }

    /**
     * Receive individual item (partial receiving)
     */
    public function receiveItem(PurchaseOrderItem $item, int $quantity): PurchaseOrderItem
    {
        return DB::transaction(function () use ($item, $quantity) {
            // Validate quantity
            $remainingQty = $item->quantity_ordered - $item->quantity_received;
            if ($quantity > $remainingQty) {
                throw new \Exception("Cannot receive more than ordered. Remaining: {$remainingQty}");
            }

            // Update item
            $item->update([
                'quantity_received' => $item->quantity_received + $quantity,
            ]);

            // Update product stock
            $this->stockService->stockIn(
                product: $item->product,
                quantity: $quantity,
                reason: 'purchase',
                reference: $item->purchaseOrder->po_number,
                notes: "Received from PO {$item->purchaseOrder->po_number}"
            );

            // Check if PO is fully received
            $po = $item->purchaseOrder;
            $allReceived = $po->items->every(function ($item) {
                return $item->quantity_received >= $item->quantity_ordered;
            });

            if ($allReceived && $po->status !== 'received') {
                $this->receivePurchaseOrder($po);
            }

            return $item->fresh();
        });
    }

    /**
     * Cancel purchase order
     */
    public function cancelPurchaseOrder(PurchaseOrder $po, string $reason): PurchaseOrder
    {
        if ($po->status === 'received') {
            throw new \Exception('Cannot cancel a received purchase order.');
        }

        $po->update([
            'status' => 'cancelled',
            'notes' => ($po->notes ?? '') . "\n\nCancelled: {$reason}",
        ]);

        $this->notificationService->notifyAdminsAndManagers(
            type: 'warning',
            title: 'Purchase Order Cancelled',
            message: "{$po->po_number} has been cancelled. Reason: {$reason}",
            data: [
                'purchase_order_id' => $po->id,
                'po_number' => $po->po_number,
                'supplier_name' => $po->supplier->company_name,
                'link' => route('purchase_orders.show', $po),
            ]
        );

        return $po->fresh();
    }

    /**
     * Clone purchase order
     */
    public function clonePurchaseOrder(PurchaseOrder $originalPO): PurchaseOrder
    {
        return DB::transaction(function () use ($originalPO) {
            // Create new PO
            $newPO = PurchaseOrder::create([
                'po_number' => $this->generatePONumber(),
                'supplier_id' => $originalPO->supplier_id,
                'order_date' => now(),
                'expected_delivery_date' => now()->addDays(7),
                'status' => 'draft',
                'total_amount' => 0,
                'notes' => "Cloned from {$originalPO->po_number}",
                'created_by' => Auth::id(),
            ]);

            // Clone items
            foreach ($originalPO->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $newPO->id,
                    'product_id' => $item->product_id,
                    'quantity_ordered' => $item->quantity_ordered,
                    'quantity_received' => 0,
                    'unit_cost' => $item->unit_cost,
                    'subtotal' => $item->subtotal,
                ]);
            }

            $this->recalculateTotal($newPO);

            return $newPO->fresh(['items.product', 'supplier']);
        });
    }

    /**
     * Get PO statistics
     */
    public function getPurchaseOrderStats(?int $days = 30): array
    {
        $startDate = $days ? now()->subDays($days) : null;

        $query = PurchaseOrder::query();
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        $orders = $query->get();

        return [
            'total_orders' => $orders->count(),
            'draft' => $orders->where('status', 'draft')->count(),
            'sent' => $orders->where('status', 'sent')->count(),
            'received' => $orders->where('status', 'received')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
            'total_value' => $orders->sum('total_amount'),
            'average_order_value' => $orders->avg('total_amount'),
        ];
    }

    /**
     * Get pending orders
     */
    public function getPendingOrders(): Collection
    {
        return PurchaseOrder::whereIn('status', ['draft', 'sent'])
            ->with(['supplier:id,company_name', 'items.product'])
            ->orderBy('expected_delivery_date', 'asc')
            ->get();
    }

    /**
     * Get overdue orders
     */
    public function getOverdueOrders(): Collection
    {
        return PurchaseOrder::where('status', 'sent')
            ->where('expected_delivery_date', '<', now())
            ->with(['supplier:id,company_name', 'items.product'])
            ->orderBy('expected_delivery_date', 'asc')
            ->get();
    }

    /**
     * Get supplier purchase history
     */
    public function getSupplierPurchaseHistory(int $supplierId, ?int $months = 12): Collection
    {
        $startDate = $months ? now()->subMonths($months) : null;

        $query = PurchaseOrder::where('supplier_id', $supplierId)
            ->with(['items.product']);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        return $query->latest()->get();
    }

    /**
     * Calculate supplier performance metrics
     */
    public function calculateSupplierPerformance(int $supplierId): array
    {
        $orders = PurchaseOrder::where('supplier_id', $supplierId)
            ->where('status', 'received')
            ->get();

        $totalOrders = $orders->count();
        $onTimeDeliveries = 0;
        $totalDeliveryDays = 0;

        foreach ($orders as $order) {
            if ($order->received_at && $order->expected_delivery_date) {
                if ($order->received_at->lte($order->expected_delivery_date)) {
                    $onTimeDeliveries++;
                }
                $totalDeliveryDays += $order->order_date->diffInDays($order->received_at);
            }
        }

        return [
            'total_orders' => $totalOrders,
            'on_time_delivery_rate' => $totalOrders > 0 ? ($onTimeDeliveries / $totalOrders) * 100 : 0,
            'average_delivery_time_days' => $totalOrders > 0 ? $totalDeliveryDays / $totalOrders : 0,
            'total_spent' => $orders->sum('total_amount'),
        ];
    }
}

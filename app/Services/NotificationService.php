<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Create a notification for a user
     */
    public function create(
        User $user,
        string $type,
        string $title,
        string $message,
        ?array $data = null
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Create a low stock alert notification
     */
    public function createLowStockAlert(User $user, $product): Notification
    {
        return $this->create(
            user: $user,
            type: 'warning',
            title: 'Low Stock Alert',
            message: "{$product->name} has fallen below minimum stock level ({$product->current_stock}/{$product->minimum_stock} units).",
            data: [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'current_stock' => $product->current_stock,
                'minimum_stock' => $product->minimum_stock,
                'link' => route('products.show', $product),
            ]
        );
    }

    /**
     * Create an out of stock alert notification
     */
    public function createOutOfStockAlert(User $user, $product): Notification
    {
        return $this->create(
            user: $user,
            type: 'danger',
            title: 'Critical: Out of Stock',
            message: "{$product->name} is now out of stock. Immediate reorder recommended.",
            data: [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'link' => route('products.show', $product),
            ]
        );
    }

    /**
     * Create a purchase order received notification
     */
    public function createPurchaseOrderReceived(User $user, $purchaseOrder): Notification
    {
        return $this->create(
            user: $user,
            type: 'success',
            title: 'Purchase Order Received',
            message: "{$purchaseOrder->po_number} from {$purchaseOrder->supplier->company_name} has been marked as received. {$purchaseOrder->items->sum('quantity_received')} items added to inventory.",
            data: [
                'purchase_order_id' => $purchaseOrder->id,
                'po_number' => $purchaseOrder->po_number,
                'supplier_name' => $purchaseOrder->supplier->company_name,
                'total_items' => $purchaseOrder->items->sum('quantity_received'),
                'link' => route('purchase_orders.show', $purchaseOrder),
            ]
        );
    }

    /**
     * Create a stock adjustment notification
     */
    public function createStockAdjustment(User $user, $stockAdjustment): Notification
    {
        $product = $stockAdjustment->product;

        return $this->create(
            user: $user,
            type: 'info',
            title: 'Stock Adjustment Completed',
            message: "Stock adjusted for {$product->name}. {$stockAdjustment->adjustment_type}: {$stockAdjustment->quantity} units.",
            data: [
                'stock_adjustment_id' => $stockAdjustment->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'adjustment_type' => $stockAdjustment->adjustment_type,
                'quantity' => $stockAdjustment->quantity,
                'link' => route('products.show', $product),
            ]
        );
    }

    /**
     * Create a new supplier added notification
     */
    public function createNewSupplier(User $user, $supplier): Notification
    {
        return $this->create(
            user: $user,
            type: 'info',
            title: 'New Supplier Added',
            message: "{$supplier->company_name} has been added as a new supplier. Review their profile for details.",
            data: [
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->company_name,
                'link' => route('suppliers.show', $supplier),
            ]
        );
    }

    /**
     * Create a price update notification
     */
    public function createPriceUpdate(User $user, $product, $oldPrice, $newPrice): Notification
    {
        return $this->create(
            user: $user,
            type: 'warning',
            title: 'Price Update Required',
            message: "{$product->name} cost price has changed. Review and update selling price.",
            data: [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'old_price' => $oldPrice,
                'new_price' => $newPrice,
                'link' => route('products.show', $product),
            ]
        );
    }

    /**
     * Notify all admins and managers
     */
    public function notifyAdminsAndManagers(
        string $type,
        string $title,
        string $message,
        ?array $data = null
    ): Collection {
        $users = User::role(['Admin', 'Manager'])->get();
        $notifications = collect();

        foreach ($users as $user) {
            $notifications->push(
                $this->create($user, $type, $title, $message, $data)
            );
        }

        return $notifications;
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Delete old read notifications (cleanup job)
     */
    public function deleteOldNotifications(int $days = 30): int
    {
        return Notification::where('is_read', true)
            ->where('read_at', '<', now()->subDays($days))
            ->delete();
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->unread()
            ->count();
    }
}

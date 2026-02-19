<?php

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;

class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_purchase_orders');
    }

    public function view(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->hasPermissionTo('view_purchase_orders');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_purchase_orders');
    }

    public function update(User $user, PurchaseOrder $purchaseOrder): bool
    {
        if (in_array($purchaseOrder->status, ['received', 'cancelled'])) {
            return false;
        }

        return $user->hasPermissionTo('edit_purchase_orders');
    }

    public function delete(User $user, PurchaseOrder $purchaseOrder): bool
    {
        // Only deletable when still a draft
        if ($purchaseOrder->status !== 'draft') {
            return false;
        }

        return $user->hasPermissionTo('delete_purchase_orders');
    }

    public function receive(User $user, PurchaseOrder $purchaseOrder): bool
    {
        if ($purchaseOrder->status !== 'sent') {
            return false;
        }

        return $user->hasPermissionTo('receive_purchase_orders');
    }

    public function cancel(User $user, PurchaseOrder $purchaseOrder): bool
    {
        if (!in_array($purchaseOrder->status, ['draft', 'sent'])) {
            return false;
        }

        return $user->hasPermissionTo('edit_purchase_orders');
    }
}
<?php

namespace App\Livewire\PurchaseOrders;

use App\Models\PurchaseOrder;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class Show extends Component
{
    public PurchaseOrder $purchaseOrder;
    public $showEditModal = false;
    public $showReceiveModal = false;
    public $purchaseOrderToDelete = null;
    public $activeTab = 'details'; // details, activity

    public function mount(PurchaseOrder $purchaseOrder)
    {
        // FIXED: Changed 'createdBy' to 'creator' and 'receivedBy' to 'receiver'
        $this->purchaseOrder = $purchaseOrder->load(['supplier', 'items.product', 'creator', 'receiver']);
    }

    public function openEditModal()
    {
        $this->showEditModal = true;
        $this->dispatch('open-modal', 'edit-purchase-order-' . $this->purchaseOrder->id);
    }

    public function openReceiveModal()
    {
        if ($this->purchaseOrder->status !== 'sent') {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Only purchase orders with "Sent" status can be received.'
            ]);
            return;
        }

        $this->showReceiveModal = true;
        $this->dispatch('open-modal', 'receive-purchase-order-' . $this->purchaseOrder->id);
    }

    #[On('purchase-order-updated')]
    public function refreshPurchaseOrder()
    {
        $this->purchaseOrder->refresh();
        $this->showEditModal = false;
    }

    #[On('purchase-order-received')]
    public function handlePurchaseOrderReceived()
    {
        $this->purchaseOrder->refresh();
        $this->showReceiveModal = false;

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Purchase order received successfully! Stock has been updated.'
        ]);
    }

    public function changeStatus($status)
    {
        if (!in_array($status, ['draft', 'sent', 'cancelled'])) {
            return;
        }

        // Validate status transitions
        if ($this->purchaseOrder->status === 'received' && $status !== 'received') {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Cannot change status of a received purchase order.'
            ]);
            return;
        }

        $this->purchaseOrder->update(['status' => $status]);
        $this->purchaseOrder->refresh();

        $statusLabel = ucfirst($status);
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Purchase order status changed to {$statusLabel}"
        ]);
    }

    public function confirmDelete()
    {
        $this->purchaseOrderToDelete = $this->purchaseOrder->id;

        $this->dispatch('showConfirmModal', [
            'title' => 'Delete Purchase Order',
            'message' => "Are you sure you want to delete PO '{$this->purchaseOrder->po_number}'? This action cannot be undone.",
            'confirmText' => 'Delete',
            'cancelText' => 'Cancel',
            'confirmColor' => 'red',
            'icon' => 'danger',
        ]);
    }

    #[On('confirmed')]
    public function handleConfirmed()
    {
        if ($this->purchaseOrderToDelete) {
            $this->purchaseOrder->delete();

            return redirect()->route('purchase-orders.index')->with('toast', [
                'type' => 'success',
                'message' => 'Purchase order deleted successfully!'
            ]);
        }
    }

    #[On('cancelled')]
    public function handleCancelled()
    {
        $this->purchaseOrderToDelete = null;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function downloadPdf()
    {
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'PDF download feature coming soon!'
        ]);
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'draft' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-600',
            'sent' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 border border-blue-200 dark:border-blue-800',
            'received' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 border border-green-200 dark:border-green-800',
            'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 border border-red-200 dark:border-red-800',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
        };
    }

    public function getProgressPercentage()
    {
        $totalOrdered = $this->purchaseOrder->items->sum('quantity_ordered');
        $totalReceived = $this->purchaseOrder->items->sum('quantity_received');

        if ($totalOrdered == 0) return 0;

        return round(($totalReceived / $totalOrdered) * 100);
    }

    public function render()
    {
        return view('livewire.purchase-orders.show', [
            'progressPercentage' => $this->getProgressPercentage(),
        ]);
    }
}

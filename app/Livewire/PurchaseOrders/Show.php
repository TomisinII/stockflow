<?php

namespace App\Livewire\PurchaseOrders;

use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\Attributes\On;

class Show extends Component
{
    public PurchaseOrder $purchaseOrder;
    public $showEditModal = false;
    public $showReceiveModal = false;
    public $purchaseOrderToDelete = null;

    public function mount(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder->load([
            'supplier',
            'items.product.category',
            'creator',
            'receiver'
        ]);
    }

    public function openEditModal()
    {
        if (!$this->purchaseOrder->canBeEdited()) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Only draft purchase orders can be edited.'
            ]);
            return;
        }

        $this->showEditModal = true;
        $this->dispatch('open-modal', 'edit-purchase-order-' . $this->purchaseOrder->id);
    }

    public function openReceiveModal()
    {
        if (!$this->purchaseOrder->canBeReceived()) {
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

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Purchase order updated successfully!'
        ]);
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

    public function sendOrder()
    {
        if (!$this->purchaseOrder->canBeSent()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Cannot send this purchase order. Please ensure it has items.'
            ]);
            return;
        }

        $this->purchaseOrder->send();
        $this->purchaseOrder->refresh();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Purchase order marked as sent!'
        ]);
    }

    public function cancelOrder()
    {
        if (!$this->purchaseOrder->canBeCancelled()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'This purchase order cannot be cancelled.'
            ]);
            return;
        }

        $this->dispatch('showConfirmModal', [
            'title' => 'Cancel Purchase Order',
            'message' => "Are you sure you want to cancel PO '{$this->purchaseOrder->po_number}'? This action cannot be undone.",
            'confirmText' => 'Yes, Cancel Order',
            'cancelText' => 'Keep Order',
            'confirmColor' => 'red',
            'icon' => 'warning',
        ]);

        $this->purchaseOrderToDelete = 'cancel';
    }

    public function duplicateOrder()
    {
        $newPO = $this->purchaseOrder->duplicate();

        return redirect()->route('purchase_orders.show', $newPO)->with('toast', [
            'type' => 'success',
            'message' => 'Purchase order duplicated successfully!'
        ]);
    }

    public function confirmDelete()
    {
        $this->purchaseOrderToDelete = 'delete';

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
        if ($this->purchaseOrderToDelete === 'delete') {
            $this->purchaseOrder->delete();

            return redirect()->route('purchase_orders.index')->with('toast', [
                'type' => 'success',
                'message' => 'Purchase order deleted successfully!'
            ]);
        } elseif ($this->purchaseOrderToDelete === 'cancel') {
            $this->purchaseOrder->cancel();
            $this->purchaseOrder->refresh();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Purchase order cancelled successfully!'
            ]);
        }

        $this->purchaseOrderToDelete = null;
    }

    #[On('cancelled')]
    public function handleCancelled()
    {
        $this->purchaseOrderToDelete = null;
    }

    public function downloadPdf($purchaseOrderId)
    {
        $purchaseOrder = PurchaseOrder::with(['supplier', 'items.product', 'creator'])
            ->findOrFail($purchaseOrderId);

        $pdf = Pdf::loadView('pdf.purchase-order', [
            'purchaseOrder' => $purchaseOrder
        ]);

        $fileName = $purchaseOrder->po_number . '-' . now()->format('Y-m-d') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function render()
    {
        return view('livewire.purchase-orders.show');
    }
}

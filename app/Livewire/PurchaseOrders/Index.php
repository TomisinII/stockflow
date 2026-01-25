<?php

namespace App\Livewire\PurchaseOrders;

use App\Models\PurchaseOrder;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $selectedPurchaseOrderId = null;
    public $purchaseOrderToDelete = null;

    // Status counts for tabs
    public $draftCount = 0;
    public $sentCount = 0;
    public $receivedCount = 0;
    public $totalCount = 0;

    public function mount()
    {
        if (request()->query('action') === 'create-purchase-order') {
            $this->dispatch('open-modal', 'create-purchase-order');
        }
        $this->updateStatusCounts();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function setStatusFilter($status)
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function updateStatusCounts()
    {
        $this->totalCount = PurchaseOrder::count();
        $this->draftCount = PurchaseOrder::where('status', 'draft')->count();
        $this->sentCount = PurchaseOrder::where('status', 'sent')->count();
        $this->receivedCount = PurchaseOrder::where('status', 'received')->count();
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->dispatch('open-modal', 'create-purchase-order');
    }

    public function openEditModal($purchaseOrderId)
    {
        $this->selectedPurchaseOrderId = $purchaseOrderId;
        $this->showEditModal = true;
        $this->dispatch('open-modal', 'edit-purchase-order-' . $purchaseOrderId);
    }

    public function confirmDelete($purchaseOrderId)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($purchaseOrderId);
        $this->purchaseOrderToDelete = $purchaseOrderId;

        $this->dispatch('showConfirmModal', [
            'title' => 'Delete Purchase Order',
            'message' => "Are you sure you want to delete PO '{$purchaseOrder->po_number}'? This action cannot be undone.",
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
            $purchaseOrder = PurchaseOrder::findOrFail($this->purchaseOrderToDelete);
            $purchaseOrder->delete();

            $this->purchaseOrderToDelete = null;
            $this->updateStatusCounts();

            $this->dispatch('toast', [
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

    #[On('purchase-order-created')]
    public function handlePurchaseOrderCreated()
    {
        $this->showCreateModal = false;
        $this->updateStatusCounts();
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Purchase order created successfully!'
        ]);
    }

    #[On('purchase-order-updated')]
    public function handlePurchaseOrderUpdated()
    {
        $this->showEditModal = false;
        $this->selectedPurchaseOrderId = null;
        $this->updateStatusCounts();
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Purchase order updated successfully!'
        ]);
    }

    public function exportPurchaseOrders()
    {
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Export feature coming soon!'
        ]);
    }

    public function downloadPdf($purchaseOrderId)
    {
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'PDF download feature coming soon!'
        ]);
    }

    public function getPurchaseOrders()
    {
        return PurchaseOrder::query()
            ->with(['supplier', 'items.product', 'creator'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('po_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('supplier', function ($sq) {
                            $sq->where('company_name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(10);
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

    public function getDaysUntilDelivery($expectedDeliveryDate)
    {
        if (!$expectedDeliveryDate) {
            return null;
        }

        $days = now()->diffInDays($expectedDeliveryDate, false);

        if ($days < 0) {
            return 'Overdue';
        } elseif ($days == 0) {
            return 'Today';
        } else {
            return "Due in {$days} " . ($days == 1 ? 'day' : 'days');
        }
    }

    public function render()
    {
        return view('livewire.purchase-orders.index', [
            'purchaseOrders' => $this->getPurchaseOrders(),
        ]);
    }
}

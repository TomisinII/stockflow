<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public Supplier $supplier;
    public $showEditModal = false;
    public $selectedSupplierId = null;
    public $supplierToDelete = null;
    public $activeTab = 'overview'; // overview, products, orders

    protected NotificationService $notificationService;

    protected $listeners = [
        'confirmed' => 'handleConfirmed',
        'cancelled' => 'handleCancelled',
    ];

    public function boot(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function mount(Supplier $supplier)
    {
        $this->supplier = $supplier;
    }

    public function openEditModal($supplierId)
    {
        $this->selectedSupplierId = $supplierId;
        $this->showEditModal = true;
        $this->dispatch('open-modal', 'edit-supplier-' . $supplierId);
    }

    #[On('supplier-updated')]
    public function refreshSupplier()
    {
        $this->supplier->refresh();
    }

    public function confirmDelete($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $this->supplierToDelete = $supplierId;

        // Check if supplier has associated products
        $productsCount = $supplier->products()->count();
        $ordersCount = $supplier->purchaseOrders()->count();

        $message = "Are you sure you want to delete '{$supplier->company_name}'?";

        if ($productsCount > 0 || $ordersCount > 0) {
            $message .= " This supplier has {$productsCount} product(s) and {$ordersCount} purchase order(s) associated with it.";
        }

        $message .= " This action cannot be undone.";

        $this->dispatch('showConfirmModal', [
            'title' => 'Delete Supplier',
            'message' => $message,
            'confirmText' => 'Delete',
            'cancelText' => 'Cancel',
            'confirmColor' => 'red',
            'icon' => 'danger',
        ]);
    }

    public function handleConfirmed()
    {
        $supplierName = $this->supplier->company_name;
        $productsCount = $this->supplier->products()->count();
        $ordersCount = $this->supplier->purchaseOrders()->count();

        $this->supplier->delete();

        $this->supplierToDelete = null;

        // Notify admins and managers about supplier deletion
        $this->notificationService->notifyAdminsAndManagers(
            type: 'warning',
            title: 'Supplier Deleted',
            message: "Supplier '{$supplierName}' has been deleted by " . Auth::user()->name . ". {$productsCount} product(s) and {$ordersCount} order(s) were associated with this supplier.",
            data: [
                'deleted_by' => Auth::user()->name,
                'supplier_name' => $supplierName,
                'products_count' => $productsCount,
                'orders_count' => $ordersCount,
            ]
        );

        $this->dispatch('notification-created');

        return redirect()->route('suppliers.index')->with('toast', [
            'type' => 'success',
            'message' => 'Supplier deleted successfully!'
        ]);
    }

    public function handleCancelled()
    {
        $this->supplierToDelete = null;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function getProductsProperty()
    {
        return $this->supplier->products()
            ->withCount('stockAdjustments')
            ->paginate(10);
    }

    public function getPurchaseOrdersProperty()
    {
        return $this->supplier->purchaseOrders()
            ->latest()
            ->paginate(10);
    }

    public function getStatsProperty()
    {
        return [
            'total_products' => $this->supplier->products()->count(),
            'total_orders' => $this->supplier->purchaseOrders()->count(),
            'pending_orders' => $this->supplier->pendingOrdersCount,
            'total_spent' => $this->supplier->totalSpent,
            'active_products' => $this->supplier->activeProductsCount,
            'low_stock_products' => $this->supplier->lowStockProductsCount,
        ];
    }

    public function render()
    {
        return view('livewire.suppliers.show', [
            'stats' => $this->stats,
            'products' => $this->activeTab === 'products' ? $this->products : collect(),
            'purchaseOrders' => $this->activeTab === 'orders' ? $this->purchaseOrders : collect(),
        ]);
    }
}

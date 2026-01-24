<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
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
    public $activeTab = 'overview'; // overview, products, orders, notes

    protected $listeners = [
        'confirmed' => 'handleConfirmed',
        'cancelled' => 'handleCancelled',
    ];

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

        $this->dispatch('showConfirmModal', [
            'title' => 'Delete Supplier',
            'message' => "Are you sure you want to delete '{$supplier->company_name}'? This action cannot be undone.",
            'confirmText' => 'Delete',
            'cancelText' => 'Cancel',
            'confirmColor' => 'red',
            'icon' => 'danger',
        ]);
    }

    public function handleConfirmed()
    {
        $this->supplier->delete();

        $this->supplierToDelete = null;

        return redirect()->route('suppliers.index')->with('toast', ['type' => 'success', 'message' => 'Supplier deleted successfully!']);
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

    public function getInitialsProperty()
    {
        $words = explode(' ', $this->supplier->company_name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->supplier->company_name, 0, 2));
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
            'pending_orders' => $this->supplier->purchaseOrders()->where('status', 'sent')->count(),
            'total_spent' => $this->supplier->purchaseOrders()->where('status', 'received')->sum('total_amount'),
            'active_products' => $this->supplier->products()->where('status', 'active')->count(),
            'low_stock_products' => $this->supplier->products()->whereRaw('current_stock <= minimum_stock')->count(),
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

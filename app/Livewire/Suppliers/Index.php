<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
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
    public $selectedSupplierId = null;
    public $supplierToDelete = null;

    protected $listeners = [
        'confirmed' => 'handleConfirmed',
        'cancelled' => 'handleCancelled',
    ];


    public function mount(){
        if (request()->query('action') === 'create-supplier') {
            $this->dispatch('open-modal', 'create-supplier');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->dispatch('open-modal', 'create-supplier');
    }

    public function openEditModal($supplierId)
    {
        $this->selectedSupplierId = $supplierId;
        $this->showEditModal = true;
        $this->dispatch('open-modal', 'edit-supplier-' . $supplierId);
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
        if ($this->supplierToDelete) {
            $supplier = Supplier::findOrFail($this->supplierToDelete);
            $supplier->delete();

            $this->supplierToDelete = null;

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Supplier deleted successfully!'
            ]);
        }
    }

    #[On('cancelled')]
    public function handleCancelled()
    {
        $this->supplierToDelete = null;
    }

    #[On('supplier-created')]
    public function handleSupplierCreated()
    {
        $this->showCreateModal = false;
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Supplier created successfully!'
        ]);
    }

    #[On('supplier-updated')]
    public function handleSupplierUpdated()
    {
        $this->showEditModal = false;
        $this->selectedSupplierId = null;
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Supplier updated successfully!'
        ]);
    }

    public function exportSuppliers()
    {
        // Export functionality - you can implement CSV export here
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Export feature coming soon!'
        ]);
    }

    public function getSuppliers()
    {
        return Supplier::query()
            ->with(['products', 'purchaseOrders'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('company_name', 'like', '%' . $this->search . '%')
                        ->orWhere('contact_person', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(12);
    }

    public function getInitials($companyName)
    {
        $words = explode(' ', $companyName);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($companyName, 0, 2));
    }

    public function render()
    {
        return view('livewire.suppliers.index', [
            'suppliers' => $this->getSuppliers(),
        ]);
    }
}

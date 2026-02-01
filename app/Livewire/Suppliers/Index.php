<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $selectedSupplierId = null;
    public $supplierToDelete = null;

    protected NotificationService $notificationService;

    public function boot(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function mount()
    {
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

    #[On('confirmed')]
    public function handleConfirmed()
    {
        if ($this->supplierToDelete) {
            $supplier = Supplier::findOrFail($this->supplierToDelete);
            $supplierName = $supplier->company_name;
            $supplier->delete();

            $this->supplierToDelete = null;

            // Notify admins and managers about supplier deletion
            $this->notificationService->notifyAdminsAndManagers(
                type: 'info',
                title: 'Supplier Deleted',
                message: "Supplier '{$supplierName}' has been deleted by " . Auth::user()->name . ".",
                data: [
                    'deleted_by' => Auth::user()->name,
                    'supplier_name' => $supplierName,
                ]
            );

            $this->dispatch('notification-created');

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
        try {
            $filename = 'suppliers-export-' . now()->format('Y-m-d-His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            $callback = function() {
                $file = fopen('php://output', 'w');

                // Add UTF-8 BOM for Excel compatibility
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // Report Header
                fputcsv($file, ['StockFlow Suppliers Export']);
                fputcsv($file, ['Generated on: ' . now()->format('F d, Y H:i:s')]);
                fputcsv($file, ['Filters Applied: ' . $this->getAppliedFiltersText()]);
                fputcsv($file, []); // Empty row

                // Column Headers
                fputcsv($file, [
                    'Company Name',
                    'Contact Person',
                    'Email',
                    'Phone',
                    'Address',
                    'City',
                    'State',
                    'Zip Code',
                    'Country',
                    'Payment Terms',
                    'Status',
                    'Total Products',
                    'Total Orders',
                    'Pending Orders',
                    'Total Spent (₦)',
                    'Created Date',
                ]);

                // Get all suppliers matching current filters
                $suppliers = Supplier::query()
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
                    ->get();

                // Supplier Rows
                foreach ($suppliers as $supplier) {
                    fputcsv($file, [
                        $supplier->company_name,
                        $supplier->contact_person ?? 'N/A',
                        $supplier->email ?? 'N/A',
                        $supplier->phone ?? 'N/A',
                        $supplier->address ?? 'N/A',
                        $supplier->city ?? 'N/A',
                        $supplier->state ?? 'N/A',
                        $supplier->zip_code ?? 'N/A',
                        $supplier->country,
                        $supplier->payment_terms ?? 'N/A',
                        ucfirst($supplier->status),
                        $supplier->products->count(),
                        $supplier->purchaseOrders->count(),
                        $supplier->pendingOrdersCount,
                        number_format($supplier->totalSpent, 2),
                        $supplier->created_at->format('Y-m-d'),
                    ]);
                }

                // Summary Statistics
                fputcsv($file, []); // Empty row
                fputcsv($file, ['SUMMARY STATISTICS']);
                fputcsv($file, ['Total Suppliers Exported', $suppliers->count()]);
                fputcsv($file, ['Active Suppliers', $suppliers->where('status', 'active')->count()]);
                fputcsv($file, ['Inactive Suppliers', $suppliers->where('status', 'inactive')->count()]);
                fputcsv($file, ['Total Products Supplied', $suppliers->sum(fn($s) => $s->products->count())]);
                fputcsv($file, ['Total Purchase Orders', $suppliers->sum(fn($s) => $s->purchaseOrders->count())]);

                $totalSpentAll = $suppliers->sum(fn($supplier) => $supplier->totalSpent);
                fputcsv($file, ['Total Amount Spent (₦)', number_format($totalSpentAll, 2)]);

                // Suppliers by Country
                fputcsv($file, []); // Empty row
                fputcsv($file, ['SUPPLIERS BY COUNTRY']);
                fputcsv($file, ['Country', 'Count']);

                $suppliersByCountry = $suppliers->groupBy('country');
                foreach ($suppliersByCountry as $country => $countrySuppliers) {
                    fputcsv($file, [$country, $countrySuppliers->count()]);
                }

                // Suppliers by Payment Terms
                $paymentTermsGroups = $suppliers->whereNotNull('payment_terms')->groupBy('payment_terms');
                if ($paymentTermsGroups->count() > 0) {
                    fputcsv($file, []); // Empty row
                    fputcsv($file, ['SUPPLIERS BY PAYMENT TERMS']);
                    fputcsv($file, ['Payment Terms', 'Count']);

                    foreach ($paymentTermsGroups as $terms => $termSuppliers) {
                        fputcsv($file, [$terms, $termSuppliers->count()]);
                    }
                }

                // Top Suppliers by Order Value
                fputcsv($file, []); // Empty row
                fputcsv($file, ['TOP 10 SUPPLIERS BY ORDER VALUE']);
                fputcsv($file, ['Company Name', 'Total Orders', 'Total Spent (₦)']);

                $topSuppliers = $suppliers->map(function($supplier) {
                    return [
                        'supplier' => $supplier,
                        'total' => $supplier->totalSpent
                    ];
                })->sortByDesc('total')->take(10);

                foreach ($topSuppliers as $item) {
                    fputcsv($file, [
                        $item['supplier']->company_name,
                        $item['supplier']->purchaseOrders->count(),
                        number_format($item['total'], 2),
                    ]);
                }

                fclose($file);
            };

            // Create notification about export
            $notificationService = app(NotificationService::class);
            $notificationService->create(
                Auth::user(),
                'info',
                'Suppliers Export Completed',
                "You exported suppliers data to CSV at " . now()->format('g:i A'),
                [
                    'action' => 'export_suppliers',
                    'filename' => $filename,
                    'filters' => $this->getAppliedFiltersText(),
                ]
            );

            $this->dispatch('notification-created');

            return new StreamedResponse($callback, 200, $headers);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Failed to export suppliers: ' . $e->getMessage()
            ]);
        }
    }

    private function getAppliedFiltersText()
    {
        $filters = [];

        if ($this->search) {
            $filters[] = 'Search: "' . $this->search . '"';
        }

        if ($this->statusFilter !== 'all') {
            $filters[] = 'Status: ' . ucfirst($this->statusFilter);
        }

        return $filters ? implode(', ', $filters) : 'None';
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

    public function render()
    {
        return view('livewire.suppliers.index', [
            'suppliers' => $this->getSuppliers(),
        ]);
    }
}

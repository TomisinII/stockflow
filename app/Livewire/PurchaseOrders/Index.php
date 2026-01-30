<?php

namespace App\Livewire\PurchaseOrders;

use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
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

    /**
     * Export purchase orders to CSV
     */
    public function exportPurchaseOrders()
    {
        try {
            $filename = 'purchase-orders-export-' . now()->format('Y-m-d-His') . '.csv';

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
                fputcsv($file, ['StockFlow Purchase Orders Export']);
                fputcsv($file, ['Generated on: ' . now()->format('F d, Y H:i:s')]);
                fputcsv($file, ['Filters Applied: ' . $this->getPurchaseOrderFiltersText()]);
                fputcsv($file, []); // Empty row

                // Column Headers
                fputcsv($file, [
                    'PO Number',
                    'Supplier',
                    'Supplier Contact',
                    'Order Date',
                    'Expected Delivery',
                    'Status',
                    'Items Count',
                    'Total Quantity',
                    'Total Amount (₦)',
                    'Amount Paid (₦)',
                    'Balance Due (₦)',
                    'Created By',
                    'Created At',
                    'Last Updated',
                    'Notes'
                ]);

                // Build the query based on available properties
                $query = PurchaseOrder::with(['supplier', 'items.product', 'creator'])
                    ->when(isset($this->search) && $this->search, function ($query) {
                        $query->where(function ($q) {
                            $q->where('po_number', 'like', '%' . $this->search . '%')
                                ->orWhereHas('supplier', function ($sq) {
                                    $sq->where('company_name', 'like', '%' . $this->search . '%')
                                    ->orWhere('contact_person', 'like', '%' . $this->search . '%')
                                    ->orWhere('phone', 'like', '%' . $this->search . '%');
                                });
                        });
                    });

                // Check if statusFilter exists and is not 'all'
                if (isset($this->statusFilter) && $this->statusFilter !== 'all') {
                    $query->where('status', $this->statusFilter);
                }

                // Check if supplierFilter exists (use with isset to avoid property not found error)
                if (isset($this->supplierFilter) && $this->supplierFilter) {
                    $query->where('supplier_id', $this->supplierFilter);
                }

                // Check for date filters if they exist
                if (isset($this->dateFrom) && $this->dateFrom) {
                    $query->whereDate('order_date', '>=', $this->dateFrom);
                }

                if (isset($this->dateTo) && $this->dateTo) {
                    $query->whereDate('order_date', '<=', $this->dateTo);
                }

                // Get the purchase orders
                $purchaseOrders = $query->latest()->get();

                // Purchase Order Rows
                foreach ($purchaseOrders as $order) {
                    $totalQuantity = $order->items->sum('quantity');
                    $balanceDue = $order->total_amount - $order->amount_paid;

                    fputcsv($file, [
                        $order->po_number,
                        $order->supplier->company_name ?? 'N/A',
                        $order->supplier->contact_person ?? 'N/A',
                        $order->order_date->format('Y-m-d'),
                        $order->expected_delivery_date ? $order->expected_delivery_date->format('Y-m-d') : 'Not Set',
                        ucfirst($order->status),
                        $order->items->count(),
                        $totalQuantity,
                        number_format($order->total_amount, 2),
                        number_format($order->amount_paid, 2),
                        number_format($balanceDue, 2),
                        $order->creator->name ?? 'System',
                        $order->created_at->format('Y-m-d H:i:s'),
                        $order->updated_at->format('Y-m-d H:i:s'),
                        $order->notes ?? 'N/A',
                    ]);
                }

                // Summary Statistics
                fputcsv($file, []); // Empty row
                fputcsv($file, ['SUMMARY STATISTICS']);
                fputcsv($file, ['Total Purchase Orders Exported', $purchaseOrders->count()]);
                fputcsv($file, ['Total Amount (₦)', number_format($purchaseOrders->sum('total_amount'), 2)]);
                fputcsv($file, ['Total Amount Paid (₦)', number_format($purchaseOrders->sum('amount_paid'), 2)]);
                fputcsv($file, ['Total Balance Due (₦)', number_format($purchaseOrders->sum(fn($o) => $o->total_amount - $o->amount_paid), 2)]);
                fputcsv($file, ['Total Items Ordered', number_format($purchaseOrders->sum(fn($o) => $o->items->sum('quantity')))]);

                // Status Breakdown
                $statusCounts = $purchaseOrders->groupBy('status')->map->count();
                foreach ($statusCounts as $status => $count) {
                    fputcsv($file, [ucfirst($status) . ' Orders', $count]);
                }

                // Top Suppliers
                fputcsv($file, []); // Empty row
                fputcsv($file, ['TOP SUPPLIERS BY ORDER COUNT']);
                $supplierOrders = $purchaseOrders->groupBy('supplier.company_name')->map->count()->sortDesc()->take(5);
                foreach ($supplierOrders as $supplier => $count) {
                    fputcsv($file, [$supplier ?: 'Unknown Supplier', $count]);
                }

                fclose($file);
            };

            return new StreamedResponse($callback, 200, $headers);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Failed to export purchase orders: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get the applied filters text for purchase orders
     */
    private function getPurchaseOrderFiltersText(): string
    {
        $filters = [];

        if (isset($this->search) && $this->search) {
            $filters[] = "Search: {$this->search}";
        }

        if (isset($this->statusFilter) && $this->statusFilter !== 'all') {
            $filters[] = "Status: " . ucfirst($this->statusFilter);
        }

        // Only include supplierFilter if the property exists
        if (property_exists($this, 'supplierFilter') && $this->supplierFilter) {
            // You might want to fetch the supplier name here if needed
            $filters[] = "Supplier: " . $this->getSupplierName($this->supplierFilter);
        }

        if (isset($this->dateFrom) && $this->dateFrom) {
            $filters[] = "From: {$this->dateFrom}";
        }

        if (isset($this->dateTo) && $this->dateTo) {
            $filters[] = "To: {$this->dateTo}";
        }

        return $filters ? implode(' | ', $filters) : 'None';
    }

    /**
     * Get supplier name for filter text
     */
    private function getSupplierName($supplierId): string
    {
        try {
            $supplier = \App\Models\Supplier::find($supplierId);
            return $supplier ? $supplier->company_name : "ID: {$supplierId}";
        } catch (\Exception $e) {
            return "ID: {$supplierId}";
        }
    }

    /**
     * Download individual PO as PDF
     */
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

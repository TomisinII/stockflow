<?php

namespace App\Livewire\StockAdjustments;

use App\Models\StockAdjustment;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = 'all'; // all, in, out, correction
    public $startDate = '';
    public $endDate = '';
    public $showCreateModal = false;

    // Stats
    public $totalAdjustments = 0;
    public $stockInCount = 0;
    public $stockOutCount = 0;
    public $correctionsCount = 0;

    public function mount()
    {
        if (request()->query('action') === 'create-stock-adjustment') {
            $this->dispatch('open-modal', 'create-adjustment');
        }

        // Set default date range (last 30 days)
        $this->endDate = now()->format('Y-m-d');
        $this->startDate = now()->subDays(30)->format('Y-m-d');

        $this->updateStats();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
        $this->updateStats();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
        $this->updateStats();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
        $this->updateStats();
    }

    public function updateStats()
    {
        $query = $this->getBaseQuery();

        $this->totalAdjustments = $query->count();
        $this->stockInCount = (clone $query)->where('adjustment_type', 'in')->count();
        $this->stockOutCount = (clone $query)->where('adjustment_type', 'out')->count();
        $this->correctionsCount = (clone $query)->where('adjustment_type', 'correction')->count();
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->dispatch('open-modal', 'create-adjustment');
    }

    #[On('adjustment-created')]
    public function handleAdjustmentCreated()
    {
        $this->showCreateModal = false;
        $this->updateStats();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Stock adjustment created successfully!'
        ]);
    }

    public function exportAdjustments()
    {
        try {
            $filename = 'stock-adjustments-' . now()->format('Y-m-d-His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            $callback = function() {
                $file = fopen('php://output', 'w');

                // UTF-8 BOM
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // Headers
                fputcsv($file, ['StockFlow Stock Adjustments Export']);
                fputcsv($file, ['Generated: ' . now()->format('F d, Y H:i:s')]);
                fputcsv($file, ['Date Range: ' . $this->startDate . ' to ' . $this->endDate]);
                fputcsv($file, []);

                fputcsv($file, [
                    'Date & Time',
                    'Product',
                    'SKU',
                    'Type',
                    'Quantity',
                    'Before',
                    'After',
                    'Reason',
                    'Reference',
                    'Adjusted By',
                ]);

                $adjustments = $this->getBaseQuery()->get();

                foreach ($adjustments as $adjustment) {
                    fputcsv($file, [
                        $adjustment->adjustment_date->format('M d, h:i A'),
                        $adjustment->product->name,
                        $adjustment->product->sku,
                        $adjustment->formatted_type,
                        $adjustment->formatted_quantity,
                        $adjustment->stock_before,
                        $adjustment->stock_after,
                        $adjustment->reason_display,
                        $adjustment->reference ?? '—',
                        $adjustment->adjuster->name,
                    ]);
                }

                // Summary
                fputcsv($file, []);
                fputcsv($file, ['SUMMARY']);
                fputcsv($file, ['Total Adjustments', $this->totalAdjustments]);
                fputcsv($file, ['Stock In', $this->stockInCount]);
                fputcsv($file, ['Stock Out', $this->stockOutCount]);
                fputcsv($file, ['Corrections', $this->correctionsCount]);

                fclose($file);
            };

            return new StreamedResponse($callback, 200, $headers);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Export failed: ' . $e->getMessage()
            ]);
        }
    }

    private function getBaseQuery()
    {
        return StockAdjustment::query()
            ->with(['product', 'adjuster'])
            ->when($this->search, function ($query) {
                $query->search($this->search);
            })
            ->when($this->typeFilter !== 'all', function ($query) {
                $query->where('adjustment_type', $this->typeFilter);
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('adjustment_date', [
                    $this->startDate . ' 00:00:00',
                    $this->endDate . ' 23:59:59'
                ]);
            })
            ->latest('adjustment_date')
            ->latest('id');
    }

    public function render()
    {
        return view('livewire.stock-adjustments.index', [
            'adjustments' => $this->getBaseQuery()->paginate(10),
        ]);
    }
}

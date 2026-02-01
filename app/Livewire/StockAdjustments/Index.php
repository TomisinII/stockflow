<?php

namespace App\Livewire\StockAdjustments;

use App\Models\StockAdjustment;
use App\Models\Product;
use App\Models\User;
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
    public $productFilter = '';
    public $typeFilter = 'all';
    public $reasonFilter = 'all';
    public $startDate = '';
    public $endDate = '';
    public $showCreateModal = false;
    public $preselectedProductId = null;

    public function mount()
    {
        // Check if we should open the create modal with a preselected product
        if (request()->query('action') === 'create-adjustment') {
            $this->preselectedProductId = request()->query('product');
            $this->dispatch('open-modal', 'create-adjustment');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingProductFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingReasonFilter()
    {
        $this->resetPage();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
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
        $this->resetPage();
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Stock adjustment created successfully!'
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['search', 'productFilter', 'typeFilter', 'reasonFilter', 'startDate', 'endDate']);
        $this->resetPage();
    }

    public function exportAdjustments()
    {
        try {
            $filename = 'stock-adjustments-export-' . now()->format('Y-m-d-His') . '.csv';

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
                fputcsv($file, ['StockFlow Stock Adjustments Export']);
                fputcsv($file, ['Generated on: ' . now()->format('F d, Y H:i:s')]);
                fputcsv($file, ['Filters Applied: ' . $this->getAppliedFiltersText()]);
                fputcsv($file, []); // Empty row

                // Column Headers
                fputcsv($file, [
                    'Date',
                    'Product Name',
                    'SKU',
                    'Adjustment Type',
                    'Quantity',
                    'Unit',
                    'Reason',
                    'Reference',
                    'Adjusted By',
                    'Notes',
                    'Stock Before',
                    'Stock After',
                ]);

                // Get all adjustments matching current filters
                $adjustments = StockAdjustment::query()
                    ->with(['product', 'adjuster'])
                    ->when($this->search, function ($query) {
                        $query->whereHas('product', function ($q) {
                            $q->where('name', 'like', '%' . $this->search . '%')
                              ->orWhere('sku', 'like', '%' . $this->search . '%');
                        });
                    })
                    ->when($this->productFilter, function ($query) {
                        $query->where('product_id', $this->productFilter);
                    })
                    ->when($this->typeFilter !== 'all', function ($query) {
                        $query->where('adjustment_type', $this->typeFilter);
                    })
                    ->when($this->reasonFilter !== 'all', function ($query) {
                        $query->where('reason', $this->reasonFilter);
                    })
                    ->when($this->startDate, function ($query) {
                        $query->whereDate('adjustment_date', '>=', $this->startDate);
                    })
                    ->when($this->endDate, function ($query) {
                        $query->whereDate('adjustment_date', '<=', $this->endDate);
                    })
                    ->latest('adjustment_date')
                    ->get();

                // Adjustment Rows
                foreach ($adjustments as $adjustment) {
                    $stockBefore = $adjustment->product->current_stock - $adjustment->quantity;
                    $stockAfter = $adjustment->product->current_stock;

                    fputcsv($file, [
                        $adjustment->adjustment_date->format('Y-m-d H:i:s'),
                        $adjustment->product->name,
                        $adjustment->product->sku,
                        ucfirst($adjustment->adjustment_type),
                        abs($adjustment->quantity),
                        $adjustment->product->unit_of_measure,
                        ucfirst(str_replace('_', ' ', $adjustment->reason)),
                        $adjustment->reference ?? 'N/A',
                        $adjustment->adjuster->name,
                        $adjustment->notes ?? 'N/A',
                        $stockBefore,
                        $stockAfter,
                    ]);
                }

                // Summary Statistics
                fputcsv($file, []); // Empty row
                fputcsv($file, ['SUMMARY STATISTICS']);
                fputcsv($file, ['Total Adjustments', $adjustments->count()]);
                fputcsv($file, ['Stock In Adjustments', $adjustments->where('adjustment_type', 'in')->count()]);
                fputcsv($file, ['Stock Out Adjustments', $adjustments->where('adjustment_type', 'out')->count()]);
                fputcsv($file, ['Correction Adjustments', $adjustments->where('adjustment_type', 'correction')->count()]);

                // Adjustments by Reason
                fputcsv($file, []); // Empty row
                fputcsv($file, ['ADJUSTMENTS BY REASON']);
                fputcsv($file, ['Reason', 'Count']);

                $adjustmentsByReason = $adjustments->groupBy('reason');
                foreach ($adjustmentsByReason as $reason => $reasonAdjustments) {
                    fputcsv($file, [
                        ucfirst(str_replace('_', ' ', $reason)),
                        $reasonAdjustments->count()
                    ]);
                }

                // Top Products by Adjustments
                fputcsv($file, []); // Empty row
                fputcsv($file, ['TOP 10 PRODUCTS BY ADJUSTMENT COUNT']);
                fputcsv($file, ['Product Name', 'SKU', 'Adjustment Count']);

                $topProducts = $adjustments->groupBy('product_id')->map(function($group) {
                    return [
                        'product' => $group->first()->product,
                        'count' => $group->count()
                    ];
                })->sortByDesc('count')->take(10);

                foreach ($topProducts as $item) {
                    fputcsv($file, [
                        $item['product']->name,
                        $item['product']->sku,
                        $item['count'],
                    ]);
                }

                fclose($file);
            };

            // Create notification about export
            $notificationService = app(NotificationService::class);
            $notificationService->create(
                Auth::user(),
                'info',
                'Stock Adjustments Export Completed',
                "You exported stock adjustments data to CSV at " . now()->format('g:i A'),
                [
                    'action' => 'export_adjustments',
                    'filename' => $filename,
                    'filters' => $this->getAppliedFiltersText(),
                ]
            );
            $this->dispatch('notification-created');

            return new StreamedResponse($callback, 200, $headers);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Failed to export adjustments: ' . $e->getMessage()
            ]);
        }
    }

    private function getAppliedFiltersText()
    {
        $filters = [];

        if ($this->search) {
            $filters[] = 'Search: "' . $this->search . '"';
        }

        if ($this->productFilter) {
            $product = Product::find($this->productFilter);
            $filters[] = 'Product: ' . $product->name;
        }

        if ($this->typeFilter !== 'all') {
            $filters[] = 'Type: ' . ucfirst($this->typeFilter);
        }

        if ($this->reasonFilter !== 'all') {
            $filters[] = 'Reason: ' . ucfirst(str_replace('_', ' ', $this->reasonFilter));
        }

        if ($this->startDate) {
            $filters[] = 'From: ' . \Carbon\Carbon::parse($this->startDate)->format('M d, Y');
        }

        if ($this->endDate) {
            $filters[] = 'To: ' . \Carbon\Carbon::parse($this->endDate)->format('M d, Y');
        }

        return $filters ? implode(', ', $filters) : 'None';
    }

    public function getAdjustments()
    {
        return StockAdjustment::query()
            ->with(['product', 'adjuster'])
            ->when($this->search, function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->productFilter, function ($query) {
                $query->where('product_id', $this->productFilter);
            })
            ->when($this->typeFilter !== 'all', function ($query) {
                $query->where('adjustment_type', $this->typeFilter);
            })
            ->when($this->reasonFilter !== 'all', function ($query) {
                $query->where('reason', $this->reasonFilter);
            })
            ->when($this->startDate, function ($query) {
                $query->whereDate('adjustment_date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('adjustment_date', '<=', $this->endDate);
            })
            ->latest('adjustment_date')
            ->paginate(15);
    }

    public function getProducts()
    {
        return Product::query()
            ->select('id', 'name', 'sku')
            ->orderBy('name')
            ->get();
    }

    public function getTotalAdjustmentsProperty()
    {
        return StockAdjustment::query()
            ->when($this->search, function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->startDate, function ($query) {
                $query->whereDate('adjustment_date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('adjustment_date', '<=', $this->endDate);
            })
            ->count();
    }

    public function getStockInCountProperty()
    {
        return StockAdjustment::query()
            ->where('adjustment_type', 'in')
            ->when($this->search, function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->startDate, function ($query) {
                $query->whereDate('adjustment_date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('adjustment_date', '<=', $this->endDate);
            })
            ->count();
    }

    public function getStockOutCountProperty()
    {
        return StockAdjustment::query()
            ->where('adjustment_type', 'out')
            ->when($this->search, function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->startDate, function ($query) {
                $query->whereDate('adjustment_date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('adjustment_date', '<=', $this->endDate);
            })
            ->count();
    }

    public function getCorrectionsCountProperty()
    {
        return StockAdjustment::query()
            ->where('adjustment_type', 'correction')
            ->when($this->search, function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->startDate, function ($query) {
                $query->whereDate('adjustment_date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('adjustment_date', '<=', $this->endDate);
            })
            ->count();
    }

    public function render()
    {
        return view('livewire.stock-adjustments.index', [
            'adjustments' => $this->getAdjustments(),
            'products' => $this->getProducts(),
            'totalAdjustments' => $this->totalAdjustments,
            'stockInCount' => $this->stockInCount,
            'stockOutCount' => $this->stockOutCount,
            'correctionsCount' => $this->correctionsCount,
        ]);
    }
}

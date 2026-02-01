<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class StockService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Adjust stock for a product
     */
    public function adjustStock(
        Product $product,
        int $quantity,
        string $type, // 'in', 'out', 'correction'
        string $reason,
        ?string $reference = null,
        ?string $notes = null
    ): StockAdjustment {
        return DB::transaction(function () use ($product, $quantity, $type, $reason, $reference, $notes) {
            $oldStock = $product->current_stock;

            // Calculate new stock based on type
            $newStock = match ($type) {
                'in' => $oldStock + $quantity,
                'out' => max(0, $oldStock - $quantity), // Prevent negative stock
                'correction' => $quantity, // Direct set
                default => $oldStock,
            };

            // Update product stock
            $product->update(['current_stock' => $newStock]);

            // Create stock adjustment record
            $adjustment = StockAdjustment::create([
                'product_id' => $product->id,
                'adjustment_type' => $type,
                'quantity' => $quantity,
                'reason' => $reason,
                'reference' => $reference,
                'notes' => $notes,
                'adjusted_by' => Auth::id(),
                'adjustment_date' => now(),
            ]);

            // Check stock levels and send notifications
            $this->checkStockLevels($product, $oldStock, $newStock);

            return $adjustment;
        });
    }

    /**
     * Check stock levels and trigger notifications
     */
    protected function checkStockLevels(Product $product, int $oldStock, int $newStock): void
    {
        // Out of stock alert
        if ($newStock === 0 && $oldStock > 0) {
            $this->notificationService->notifyAdminsAndManagers(
                type: 'danger',
                title: 'Critical: Out of Stock',
                message: "{$product->name} is now out of stock. Immediate reorder recommended.",
                data: [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'link' => route('products.show', $product),
                ]
            );
        }
        // Low stock alert (only when crossing the threshold downward)
        elseif ($newStock <= $product->minimum_stock && $oldStock > $product->minimum_stock) {
            $this->notificationService->notifyAdminsAndManagers(
                type: 'warning',
                title: 'Low Stock Alert',
                message: "{$product->name} has fallen below minimum stock level ({$newStock}/{$product->minimum_stock} units).",
                data: [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'current_stock' => $newStock,
                    'minimum_stock' => $product->minimum_stock,
                    'link' => route('products.show', $product),
                ]
            );
        }
        // Stock restored notification (crossing minimum upward)
        elseif ($newStock > $product->minimum_stock && $oldStock <= $product->minimum_stock && $oldStock > 0) {
            $this->notificationService->notifyAdminsAndManagers(
                type: 'success',
                title: 'Stock Level Restored',
                message: "{$product->name} stock level is now above minimum ({$newStock}/{$product->minimum_stock} units).",
                data: [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'current_stock' => $newStock,
                    'minimum_stock' => $product->minimum_stock,
                    'link' => route('products.show', $product),
                ]
            );
        }
    }

    /**
     * Stock in (purchase, return from customer)
     */
    public function stockIn(
        Product $product,
        int $quantity,
        string $reason = 'purchase',
        ?string $reference = null,
        ?string $notes = null
    ): StockAdjustment {
        return $this->adjustStock($product, $quantity, 'in', $reason, $reference, $notes);
    }

    /**
     * Stock out (sale, damaged, expired, theft)
     */
    public function stockOut(
        Product $product,
        int $quantity,
        string $reason = 'sale',
        ?string $reference = null,
        ?string $notes = null
    ): StockAdjustment {
        return $this->adjustStock($product, $quantity, 'out', $reason, $reference, $notes);
    }

    /**
     * Stock correction (manual count adjustment)
     */
    public function stockCorrection(
        Product $product,
        int $newQuantity,
        string $reason = 'stocktake',
        ?string $notes = null
    ): StockAdjustment {
        return $this->adjustStock($product, $newQuantity, 'correction', $reason, null, $notes);
    }

    /**
     * Get stock movement history for a product
     */
    public function getStockHistory(Product $product, ?int $days = null): Collection
    {
        $query = StockAdjustment::where('product_id', $product->id)
            ->with(['user:id,name', 'product:id,name'])
            ->latest('adjustment_date');

        if ($days) {
            $query->where('adjustment_date', '>=', now()->subDays($days));
        }

        return $query->get();
    }

    /**
     * Get low stock products
     */
    public function getLowStockProducts(): Collection
    {
        return Product::whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)
            ->where('status', 'active')
            ->with(['category:id,name', 'supplier:id,company_name'])
            ->orderBy('current_stock', 'asc')
            ->get();
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStockProducts(): Collection
    {
        return Product::where('current_stock', 0)
            ->where('status', 'active')
            ->with(['category:id,name', 'supplier:id,company_name'])
            ->latest()
            ->get();
    }

    /**
     * Calculate total stock value
     */
    public function calculateStockValue(?string $priceType = 'cost'): float
    {
        $column = $priceType === 'selling' ? 'selling_price' : 'cost_price';

        return Product::where('status', 'active')
            ->sum(DB::raw("current_stock * {$column}"));
    }

    /**
     * Get stock value by category
     */
    public function getStockValueByCategory(?string $priceType = 'cost'): Collection
    {
        $column = $priceType === 'selling' ? 'selling_price' : 'cost_price';

        return Product::selectRaw("
                category_id,
                SUM(current_stock * {$column}) as total_value,
                SUM(current_stock) as total_quantity,
                COUNT(*) as product_count
            ")
            ->where('status', 'active')
            ->with('category:id,name,color')
            ->groupBy('category_id')
            ->orderByDesc('total_value')
            ->get();
    }

    /**
     * Get stock movement statistics
     */
    public function getStockMovementStats(?int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $adjustments = StockAdjustment::where('adjustment_date', '>=', $startDate)
            ->selectRaw('
                adjustment_type,
                COUNT(*) as count,
                SUM(quantity) as total_quantity
            ')
            ->groupBy('adjustment_type')
            ->get()
            ->keyBy('adjustment_type');

        return [
            'stock_in' => [
                'count' => $adjustments->get('in')?->count ?? 0,
                'quantity' => $adjustments->get('in')?->total_quantity ?? 0,
            ],
            'stock_out' => [
                'count' => $adjustments->get('out')?->count ?? 0,
                'quantity' => $adjustments->get('out')?->total_quantity ?? 0,
            ],
            'corrections' => [
                'count' => $adjustments->get('correction')?->count ?? 0,
                'quantity' => $adjustments->get('correction')?->total_quantity ?? 0,
            ],
            'total_adjustments' => StockAdjustment::where('adjustment_date', '>=', $startDate)->count(),
        ];
    }

    /**
     * Get fast-moving products (most stock adjustments)
     */
    public function getFastMovingProducts(int $limit = 10, ?int $days = 30): Collection
    {
        $startDate = $days ? now()->subDays($days) : null;

        return Product::withCount([
                'stockAdjustments' => function ($query) use ($startDate) {
                    if ($startDate) {
                        $query->where('adjustment_date', '>=', $startDate);
                    }
                }
            ])
            ->having('stock_adjustments_count', '>', 0)
            ->orderByDesc('stock_adjustments_count')
            ->limit($limit)
            ->with(['category:id,name', 'supplier:id,company_name'])
            ->get();
    }

    /**
     * Get slow-moving products (least stock adjustments)
     */
    public function getSlowMovingProducts(int $limit = 10, ?int $days = 90): Collection
    {
        $startDate = now()->subDays($days);

        return Product::whereDoesntHave('stockAdjustments', function ($query) use ($startDate) {
                $query->where('adjustment_date', '>=', $startDate);
            })
            ->where('status', 'active')
            ->where('current_stock', '>', 0)
            ->limit($limit)
            ->with(['category:id,name', 'supplier:id,company_name'])
            ->get();
    }

    /**
     * Get dead stock (no movement in X days)
     */
    public function getDeadStock(int $days = 90): Collection
    {
        $cutoffDate = now()->subDays($days);

        return Product::where('status', 'active')
            ->where('current_stock', '>', 0)
            ->whereDoesntHave('stockAdjustments', function ($query) use ($cutoffDate) {
                $query->where('adjustment_date', '>=', $cutoffDate);
            })
            ->with(['category:id,name', 'supplier:id,company_name'])
            ->get();
    }

    /**
     * Validate stock adjustment
     */
    public function validateStockAdjustment(Product $product, int $quantity, string $type): array
    {
        $errors = [];

        if ($quantity <= 0) {
            $errors[] = 'Quantity must be greater than zero.';
        }

        if ($type === 'out' && $product->current_stock < $quantity) {
            $errors[] = "Insufficient stock. Available: {$product->current_stock}, Requested: {$quantity}";
        }

        if ($type === 'correction' && $quantity < 0) {
            $errors[] = 'Stock correction quantity cannot be negative.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Bulk stock update (for imports)
     */
    public function bulkStockUpdate(array $updates): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        DB::beginTransaction();

        try {
            foreach ($updates as $update) {
                $product = Product::find($update['product_id'] ?? null);

                if (!$product) {
                    $results['failed']++;
                    $results['errors'][] = "Product not found: {$update['product_id']}";
                    continue;
                }

                $this->adjustStock(
                    product: $product,
                    quantity: $update['quantity'],
                    type: $update['type'] ?? 'correction',
                    reason: $update['reason'] ?? 'bulk_import',
                    reference: $update['reference'] ?? null,
                    notes: $update['notes'] ?? null
                );

                $results['success']++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CompleteDataExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Products' => new ProductsExport(),
            'Suppliers' => new SuppliersExport(),
            'Categories' => new CategoriesExport(),
            'Purchase Orders' => new PurchaseOrdersExport(),
            'Stock Adjustments' => new StockAdjustmentsExport(),
            'Summary' => new SummaryExport(),
        ];
    }
}

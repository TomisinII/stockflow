<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ProductsExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return Product::with(['category', 'supplier'])
            ->get()
            ->map(function ($product) {
                return [
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'barcode' => $product->barcode ?? 'N/A',
                    'category' => $product->category->name ?? 'Uncategorized',
                    'supplier' => $product->supplier->company_name ?? 'N/A',
                    'unit_of_measure' => $product->unit_of_measure,
                    'cost_price' => $product->cost_price,
                    'selling_price' => $product->selling_price,
                    'current_stock' => $product->current_stock,
                    'minimum_stock' => $product->minimum_stock,
                    'maximum_stock' => $product->maximum_stock ?? 'N/A',
                    'stock_status' => $product->stockStatus['status'],
                    'stock_value' => $product->stockValue,
                    'status' => ucfirst($product->status),
                    'created_date' => $product->created_at->format('Y-m-d'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Product Name',
            'Barcode',
            'Category',
            'Supplier',
            'Unit of Measure',
            'Cost Price (₦)',
            'Selling Price (₦)',
            'Current Stock',
            'Minimum Stock',
            'Maximum Stock',
            'Stock Status',
            'Stock Value (₦)',
            'Status',
            'Created Date',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'], // Blue
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Products';
    }
}

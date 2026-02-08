<?php

namespace App\Exports;

use App\Models\StockAdjustment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class StockAdjustmentsExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return StockAdjustment::with(['product', 'adjustedBy'])
            ->latest()
            ->get()
            ->map(function ($adjustment) {
                return [
                    'product_sku' => $adjustment->product->sku ?? 'N/A',
                    'product_name' => $adjustment->product->name ?? 'N/A',
                    'adjustment_type' => ucfirst($adjustment->adjustment_type),
                    'quantity' => $adjustment->quantity,
                    'reason' => $adjustment->reason,
                    'reference' => $adjustment->reference ?? 'N/A',
                    'adjusted_by' => $adjustment->adjustedBy->name ?? 'N/A',
                    'adjustment_date' => $adjustment->adjustment_date->format('Y-m-d'),
                    'notes' => $adjustment->notes ?? 'N/A',
                    'created_at' => $adjustment->created_at->format('Y-m-d H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Product SKU',
            'Product Name',
            'Adjustment Type',
            'Quantity',
            'Reason',
            'Reference',
            'Adjusted By',
            'Adjustment Date',
            'Notes',
            'Created At',
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
                    'startColor' => ['rgb' => 'F59E0B'], // Amber
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
        return 'Stock Adjustments';
    }
}

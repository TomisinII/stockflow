<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PurchaseOrdersExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return PurchaseOrder::with(['supplier', 'creator'])
            ->withCount('items')
            ->get()
            ->map(function ($order) {
                return [
                    'po_number' => $order->po_number,
                    'supplier' => $order->supplier->company_name ?? 'N/A',
                    'order_date' => $order->order_date->format('Y-m-d'),
                    'expected_delivery' => $order->expected_delivery_date?->format('Y-m-d') ?? 'N/A',
                    'status' => ucfirst($order->status),
                    'total_amount' => $order->total_amount,
                    'items_count' => $order->items_count,
                    'created_by' => $order->creator->name ?? 'N/A',
                    'received_by' => $order->receiver->name ?? 'N/A',
                    'received_at' => $order->received_at?->format('Y-m-d H:i') ?? 'N/A',
                    'notes' => $order->notes ?? 'N/A',
                    'created_date' => $order->created_at->format('Y-m-d'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'PO Number',
            'Supplier',
            'Order Date',
            'Expected Delivery',
            'Status',
            'Total Amount (₦)',
            'Items Count',
            'Created By',
            'Received By',
            'Received At',
            'Notes',
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
                    'startColor' => ['rgb' => '10B981'], // Green
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
        return 'Purchase Orders';
    }
}

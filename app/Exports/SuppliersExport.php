<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SuppliersExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return Supplier::withCount(['products', 'purchaseOrders'])
            ->get()
            ->map(function ($supplier) {
                return [
                    'company_name' => $supplier->company_name,
                    'contact_person' => $supplier->contact_person ?? 'N/A',
                    'email' => $supplier->email ?? 'N/A',
                    'phone' => $supplier->phone ?? 'N/A',
                    'address' => $supplier->address ?? 'N/A',
                    'city' => $supplier->city ?? 'N/A',
                    'state' => $supplier->state ?? 'N/A',
                    'zip_code' => $supplier->zip_code ?? 'N/A',
                    'country' => $supplier->country,
                    'payment_terms' => $supplier->payment_terms ?? 'N/A',
                    'status' => ucfirst($supplier->status),
                    'total_products' => $supplier->products_count,
                    'total_orders' => $supplier->purchase_orders_count,
                    'total_spent' => $supplier->totalSpent,
                    'created_date' => $supplier->created_at->format('Y-m-d'),
                ];
            });
    }

    public function headings(): array
    {
        return [
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
            'Total Spent (₦)',
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
                    'startColor' => ['rgb' => '0EA5E9'], // Sky
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
        return 'Suppliers';
    }
}

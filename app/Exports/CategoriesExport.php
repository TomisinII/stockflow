<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class CategoriesExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return Category::withCount('products')
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'description' => $category->description ?? 'N/A',
                    'icon' => $category->icon,
                    'color' => $category->color,
                    'total_products' => $category->products_count,
                    'created_date' => $category->created_at->format('Y-m-d'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Category Name',
            'Description',
            'Icon',
            'Color',
            'Total Products',
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
                    'startColor' => ['rgb' => '9333EA'], // Purple
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
        return 'Categories';
    }
}

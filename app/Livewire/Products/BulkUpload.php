<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\BarcodeService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class BulkUpload extends Component
{
    use WithFileUploads;

    public $csvFile;
    public $updateExisting = false;
    public $importResults = null;
    public $isProcessing = false;

    // CSV column mappings (case-insensitive)
    protected $requiredColumns = [
        'name',
        'sku',
        'category',
        'unit_of_measure',
        'cost_price',
        'selling_price',
        'current_stock',
        'minimum_stock',
    ];

    protected $optionalColumns = [
        'description',
        'barcode',
        'supplier',
        'maximum_stock',
        'status',
    ];

    protected $rules = [
        'csvFile' => 'required|file|mimes:csv,txt|max:5120', // 5MB max
        'updateExisting' => 'boolean',
    ];

    protected $messages = [
        'csvFile.required' => 'Please select a CSV file to upload',
        'csvFile.mimes' => 'File must be a CSV file',
        'csvFile.max' => 'File size cannot exceed 5MB',
    ];

    public function downloadTemplate()
    {
        try {
            $filename = 'products-import-template-' . now()->format('Y-m-d') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            $callback = function () {
                $file = fopen('php://output', 'w');

                // Add UTF-8 BOM for Excel compatibility
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // Instructions
                fputcsv($file, ['StockFlow Products Bulk Import Template']);
                fputcsv($file, ['Generated on: ' . now()->format('F d, Y H:i:s')]);
                fputcsv($file, []);
                fputcsv($file, ['INSTRUCTIONS:']);
                fputcsv($file, ['1. Fill in all required columns marked with * (asterisk)']);
                fputcsv($file, ['2. Category must match an existing category name exactly']);
                fputcsv($file, ['3. Supplier must match an existing supplier company name exactly (or leave empty)']);
                fputcsv($file, ['4. SKU must be unique for each product']);
                fputcsv($file, ['5. Leave barcode empty to auto-generate']);
                fputcsv($file, ['6. Valid units: pieces, kg, liters, boxes, packs, meters']);
                fputcsv($file, ['7. Valid status: active or inactive (default: active)']);
                fputcsv($file, ['8. You can delete instruction rows or leave them - the system will auto-detect headers']);
                fputcsv($file, []);

                // Available Categories
                fputcsv($file, ['AVAILABLE CATEGORIES:']);
                $categories = Category::orderBy('name')->pluck('name')->toArray();
                fputcsv($file, $categories);
                fputcsv($file, []);

                // Available Suppliers
                fputcsv($file, ['AVAILABLE SUPPLIERS:']);
                $suppliers = Supplier::active()->orderBy('company_name')->pluck('company_name')->toArray();
                fputcsv($file, $suppliers);
                fputcsv($file, []);

                // Column Headers
                fputcsv($file, [
                    'name*',
                    'sku*',
                    'description',
                    'barcode',
                    'category*',
                    'supplier',
                    'unit_of_measure*',
                    'cost_price*',
                    'selling_price*',
                    'current_stock*',
                    'minimum_stock*',
                    'maximum_stock',
                    'status',
                ]);

                // Example rows
                fputcsv($file, [
                    'iPhone 15 Pro Max 256GB',
                    'APL-IP15-256',
                    'Latest iPhone model with A17 Pro chip',
                    '', // Auto-generate barcode
                    'Electronics',
                    'Apple Inc',
                    'pieces',
                    '950000',
                    '1250000',
                    '50',
                    '10',
                    '200',
                    'active',
                ]);

                fputcsv($file, [
                    'Samsung Galaxy S24 Ultra',
                    'SAM-S24U-512',
                    'Flagship Samsung phone with S Pen',
                    '9780123456789',
                    'Electronics',
                    'Samsung Electronics',
                    'pieces',
                    '850000',
                    '1150000',
                    '30',
                    '5',
                    '100',
                    'active',
                ]);

                fputcsv($file, [
                    'Office Chair Executive',
                    'FUR-OC-EXE',
                    'Ergonomic executive office chair',
                    '',
                    'Office Supplies',
                    '',
                    'pieces',
                    '45000',
                    '75000',
                    '20',
                    '5',
                    '',
                    'active',
                ]);

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Failed to download template: ' . $e->getMessage()
            ]);
        }
    }

    public function import()
    {
        $this->validate();

        $this->isProcessing = true;
        $this->importResults = [
            'total_rows' => 0,
            'successful' => 0,
            'skipped' => 0,
            'failed' => 0,
            'errors' => [],
            'imported_products' => [],
        ];

        try {
            // Read CSV file
            $path = $this->csvFile->getRealPath();
            $csv = array_map(function ($line) {
                return str_getcsv($line);
            }, file($path));

            // Remove BOM if present
            if (isset($csv[0][0])) {
                $csv[0][0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $csv[0][0]);
            }

            // Find the header row (the row that contains the actual column headers)
            $headerRowIndex = $this->findHeaderRow($csv);

            if ($headerRowIndex === false) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Could not find valid column headers in CSV file. Please ensure the header row contains at least: name, sku, category'
                ]);
                $this->isProcessing = false;
                return;
            }

            // Get headers from the detected header row
            $headers = array_map(function($header) {
                // Remove asterisks and trim
                return trim(strtolower(str_replace('*', '', $header)));
            }, $csv[$headerRowIndex]);

            // Validate required columns
            $missingColumns = [];
            foreach ($this->requiredColumns as $required) {
                if (!in_array(strtolower($required), $headers)) {
                    $missingColumns[] = $required;
                }
            }

            if (!empty($missingColumns)) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Missing required columns: ' . implode(', ', $missingColumns)
                ]);
                $this->isProcessing = false;
                return;
            }

            // Process each row after the header row
            $rowNumber = $headerRowIndex; // Excel-style row numbering
            for ($i = $headerRowIndex + 1; $i < count($csv); $i++) {
                $rowNumber++;
                $row = $csv[$i];

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $this->importResults['total_rows']++;

                // Map row data to associative array
                $data = [];
                foreach ($headers as $index => $header) {
                    $data[$header] = isset($row[$index]) ? trim($row[$index]) : '';
                }

                // Process this row
                $result = $this->processRow($data, $rowNumber);

                if ($result['success']) {
                    $this->importResults['successful']++;
                    $this->importResults['imported_products'][] = $result['product'];
                } elseif ($result['skipped']) {
                    $this->importResults['skipped']++;
                    $this->importResults['errors'][] = "Row {$rowNumber}: {$result['message']}";
                } else {
                    $this->importResults['failed']++;
                    $this->importResults['errors'][] = "Row {$rowNumber}: {$result['message']}";
                }
            }

            // Create notification
            if ($this->importResults['successful'] > 0) {
                $notificationService = app(NotificationService::class);
                $notificationService->create(
                    Auth::user(),
                    'success',
                    'Products Import Completed',
                    "Successfully imported {$this->importResults['successful']} products. " .
                    ($this->importResults['failed'] > 0 ? "{$this->importResults['failed']} failed. " : '') .
                    ($this->importResults['skipped'] > 0 ? "{$this->importResults['skipped']} skipped." : ''),
                    [
                        'action' => 'bulk_import_products',
                        'total' => $this->importResults['total_rows'],
                        'successful' => $this->importResults['successful'],
                        'failed' => $this->importResults['failed'],
                        'skipped' => $this->importResults['skipped'],
                    ]
                );

                $this->dispatch('notification-created');
                $this->dispatch('product-created'); // Refresh products list
            }

            $this->dispatch('toast', [
                'type' => $this->importResults['successful'] > 0 ? 'success' : 'warning',
                'message' => $this->getImportSummaryMessage()
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk import error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Import failed: ' . $e->getMessage()
            ]);
        } finally {
            $this->isProcessing = false;
        }
    }

    /**
     * Find the header row by looking for a row that contains required columns
     */
    protected function findHeaderRow(array $csv): int|false
    {
        foreach ($csv as $index => $row) {
            // Clean and normalize the row
            $cleanedRow = array_map(function($cell) {
                return trim(strtolower(str_replace('*', '', $cell)));
            }, $row);

            // Check if this row contains at least 3 required columns
            $foundColumns = 0;
            $columnsToCheck = ['name', 'sku', 'category']; // Core identifying columns
            
            foreach ($columnsToCheck as $column) {
                if (in_array($column, $cleanedRow)) {
                    $foundColumns++;
                }
            }

            // If we found at least 3 of our core columns, this is likely the header row
            if ($foundColumns >= 3) {
                return $index;
            }
        }

        return false;
    }

    protected function processRow(array $data, int $rowNumber): array
    {
        try {
            // Validate required fields
            foreach ($this->requiredColumns as $column) {
                if (empty($data[$column])) {
                    return [
                        'success' => false,
                        'skipped' => false,
                        'message' => "Missing required field: {$column}",
                    ];
                }
            }

            // Check if SKU already exists
            $existingProduct = Product::withTrashed()->where('sku', $data['sku'])->first();

            if ($existingProduct && !$this->updateExisting) {
                return [
                    'success' => false,
                    'skipped' => true,
                    'message' => "SKU '{$data['sku']}' already exists (skipped)",
                ];
            }

            // Find or validate category
            $category = Category::where('name', $data['category'])->first();
            if (!$category) {
                return [
                    'success' => false,
                    'skipped' => false,
                    'message' => "Category '{$data['category']}' not found",
                ];
            }

            // Find supplier (optional)
            $supplier = null;
            if (!empty($data['supplier'])) {
                $supplier = Supplier::where('company_name', $data['supplier'])->first();
                if (!$supplier) {
                    return [
                        'success' => false,
                        'skipped' => false,
                        'message' => "Supplier '{$data['supplier']}' not found",
                    ];
                }
            }

            // Validate numeric fields
            if (!is_numeric($data['cost_price']) || $data['cost_price'] < 0) {
                return [
                    'success' => false,
                    'skipped' => false,
                    'message' => "Invalid cost_price value",
                ];
            }

            if (!is_numeric($data['selling_price']) || $data['selling_price'] < 0) {
                return [
                    'success' => false,
                    'skipped' => false,
                    'message' => "Invalid selling_price value",
                ];
            }

            if (!is_numeric($data['current_stock']) || $data['current_stock'] < 0) {
                return [
                    'success' => false,
                    'skipped' => false,
                    'message' => "Invalid current_stock value",
                ];
            }

            if (!is_numeric($data['minimum_stock']) || $data['minimum_stock'] < 0) {
                return [
                    'success' => false,
                    'skipped' => false,
                    'message' => "Invalid minimum_stock value",
                ];
            }

            // Validate unit of measure
            $validUnits = ['pieces', 'kg', 'liters', 'boxes', 'packs', 'meters'];
            if (!in_array(strtolower($data['unit_of_measure']), $validUnits)) {
                return [
                    'success' => false,
                    'skipped' => false,
                    'message' => "Invalid unit_of_measure. Must be one of: " . implode(', ', $validUnits),
                ];
            }

            // Generate barcode if not provided
            $barcode = !empty($data['barcode']) ? $data['barcode'] : null;
            if (!$barcode) {
                $barcodeService = app(BarcodeService::class);
                $barcode = $barcodeService->generateUniqueBarcode();
            }

            // Prepare product data
            $productData = [
                'name' => $data['name'],
                'sku' => $data['sku'],
                'description' => $data['description'] ?? null,
                'barcode' => $barcode,
                'category_id' => $category->id,
                'supplier_id' => $supplier?->id,
                'unit_of_measure' => strtolower($data['unit_of_measure']),
                'cost_price' => $data['cost_price'],
                'selling_price' => $data['selling_price'],
                'current_stock' => $data['current_stock'],
                'minimum_stock' => $data['minimum_stock'],
                'maximum_stock' => !empty($data['maximum_stock']) && is_numeric($data['maximum_stock']) ? $data['maximum_stock'] : null,
                'status' => !empty($data['status']) && in_array(strtolower($data['status']), ['active', 'inactive']) ? strtolower($data['status']) : 'active',
            ];

            // Create or update product
            DB::beginTransaction();

            if ($existingProduct && $this->updateExisting) {
                $existingProduct->update($productData);
                $product = $existingProduct;
            } else {
                $product = Product::create($productData);
            }

            DB::commit();

            return [
                'success' => true,
                'skipped' => false,
                'product' => $product,
                'message' => 'Product imported successfully',
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Error processing row {$rowNumber}: " . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'skipped' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    protected function getImportSummaryMessage(): string
    {
        $parts = [];

        if ($this->importResults['successful'] > 0) {
            $parts[] = "{$this->importResults['successful']} imported successfully";
        }

        if ($this->importResults['skipped'] > 0) {
            $parts[] = "{$this->importResults['skipped']} skipped";
        }

        if ($this->importResults['failed'] > 0) {
            $parts[] = "{$this->importResults['failed']} failed";
        }

        return implode(', ', $parts);
    }

    public function resetImport()
    {
        $this->reset(['csvFile', 'importResults', 'updateExisting']);
    }

    public function closeModal()
    {
        $this->resetImport();
        $this->dispatch('close-modal', 'bulk-upload-products');
    }

    public function render()
    {
        return view('livewire.products.bulk-upload');
    }
}
<?php

namespace App\Services;

use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class BarcodeService
{
    protected $generatorPNG;
    protected $generatorSVG;
    protected $generatorHTML;

    public function __construct()
    {
        $this->generatorPNG = new BarcodeGeneratorPNG();
        $this->generatorSVG = new BarcodeGeneratorSVG();
        $this->generatorHTML = new BarcodeGeneratorHTML();
    }

    /**
     * Generate a unique barcode number
     */
    public function generateUniqueBarcode(): string
    {
        do {
            // Generate EAN-13 format: 12 digits + 1 check digit
            $barcode = $this->generateEAN13();
        } while (Product::where('barcode', $barcode)->exists());

        return $barcode;
    }

    /**
     * Generate EAN-13 barcode (13 digits)
     */
    protected function generateEAN13(): string
    {
        // First 12 digits (you can customize the prefix)
        $prefix = '978'; // Common book prefix, or use your company code
        $randomDigits = str_pad(mt_rand(0, 999999999), 9, '0', STR_PAD_LEFT);
        $code = $prefix . $randomDigits;

        // Calculate check digit
        $checkDigit = $this->calculateEAN13CheckDigit($code);

        return $code . $checkDigit;
    }

    /**
     * Calculate EAN-13 check digit
     */
    protected function calculateEAN13CheckDigit(string $code): int
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int) $code[$i];
            $sum += ($i % 2 === 0) ? $digit : $digit * 3;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;
        return $checkDigit;
    }

    /**
     * Validate EAN-13 barcode
     */
    public function validateEAN13(string $barcode): bool
    {
        if (strlen($barcode) !== 13 || !ctype_digit($barcode)) {
            return false;
        }

        $code = substr($barcode, 0, 12);
        $providedCheckDigit = (int) substr($barcode, 12, 1);
        $calculatedCheckDigit = $this->calculateEAN13CheckDigit($code);

        return $providedCheckDigit === $calculatedCheckDigit;
    }

    /**
     * Generate barcode image as PNG (base64)
     */
    public function generateBarcodePNG(string $code, int $widthFactor = 2, int $height = 50): string
    {
        $barcode = $this->generatorPNG->getBarcode(
            $code,
            BarcodeGeneratorPNG::TYPE_CODE_128,
            $widthFactor,
            $height
        );

        return 'data:image/png;base64,' . base64_encode($barcode);
    }

    /**
     * Generate barcode as SVG
     */
    public function generateBarcodeSVG(string $code, int $widthFactor = 2, int $height = 50): string
    {
        return $this->generatorSVG->getBarcode(
            $code,
            BarcodeGeneratorSVG::TYPE_CODE_128,
            $widthFactor,
            $height
        );
    }

    /**
     * Generate barcode as HTML
     */
    public function generateBarcodeHTML(string $code, int $widthFactor = 2, int $height = 50): string
    {
        return $this->generatorHTML->getBarcode(
            $code,
            BarcodeGeneratorHTML::TYPE_CODE_128,
            $widthFactor,
            $height
        );
    }

    /**
     * Save barcode image to storage
     */
    public function saveBarcodeToStorage(string $code, string $filename = null): string
    {
        $filename = $filename ?? $code . '.png';
        $barcode = $this->generatorPNG->getBarcode(
            $code,
            BarcodeGeneratorPNG::TYPE_CODE_128,
            2,
            50
        );

        $path = 'barcodes/' . $filename;
        Storage::disk('public')->put($path, $barcode);

        return $path;
    }

    /**
     * Generate barcode label HTML for printing
     */
    public function generateBarcodeLabel(Product $product, string $size = 'standard'): string
    {
        $barcodeSVG = $this->generateBarcodeSVG($product->sku ?? $product->barcode, 2, 40);

        $sizes = [
            'small' => ['width' => '2in', 'height' => '1in', 'font-size' => '8px'],
            'standard' => ['width' => '2.5in', 'height' => '1.25in', 'font-size' => '10px'],
            'large' => ['width' => '3in', 'height' => '1.5in', 'font-size' => '12px'],
        ];

        $sizeConfig = $sizes[$size] ?? $sizes['standard'];

        return view('components.barcode-label', [
            'product' => $product,
            'barcodeSVG' => $barcodeSVG,
            'sizeConfig' => $sizeConfig,
        ])->render();
    }

    /**
     * Generate PDF with multiple barcode labels
     */
    public function generateBarcodeLabelsPDF(array $products, int $copies = 1): string
    {
        $labels = [];

        foreach ($products as $product) {
            for ($i = 0; $i < $copies; $i++) {
                $labels[] = [
                    'product' => $product,
                    'barcode' => $this->generateBarcodeSVG($product->sku ?? $product->barcode, 2, 40),
                ];
            }
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdf.barcode-labels', compact('labels'));
        $pdf->setPaper([0, 0, 180, 90], 'portrait'); // 2.5" x 1.25" label

        return $pdf->output();
    }

    /**
     * Generate SKU (Stock Keeping Unit)
     */
    public function generateSKU(Product $product): string
    {
        // Format: CATEGORY-SUPPLIER-RANDOM
        // Example: ELEC-APPL-8H3K2

        $categoryPrefix = strtoupper(substr($product->category->name ?? 'GEN', 0, 4));
        $supplierPrefix = $product->supplier
            ? strtoupper(substr($product->supplier->company_name, 0, 4))
            : 'NONE';

        // Generate random alphanumeric
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));

        $sku = "{$categoryPrefix}-{$supplierPrefix}-{$random}";

        // Ensure uniqueness
        while (Product::where('sku', $sku)->exists()) {
            $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
            $sku = "{$categoryPrefix}-{$supplierPrefix}-{$random}";
        }

        return $sku;
    }

    /**
     * Generate QR Code for product (optional)
     */
    public function generateQRCode(string $data): string
    {
        // You can use SimpleSoftwareIO/simple-qrcode package
        // For now, return a placeholder
        return "QR Code for: {$data}";
    }

    /**
     * Batch generate barcodes for products without them
     */
    public function batchGenerateBarcodes(): int
    {
        $products = Product::whereNull('barcode')->get();
        $count = 0;

        foreach ($products as $product) {
            $product->update([
                'barcode' => $this->generateUniqueBarcode(),
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Get barcode type from string
     */
    public function getBarcodeType(string $barcode): string
    {
        $length = strlen($barcode);

        return match ($length) {
            8 => 'EAN-8',
            12 => 'UPC-A',
            13 => 'EAN-13',
            default => 'CODE-128',
        };
    }
}

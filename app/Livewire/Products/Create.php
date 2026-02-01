<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Services\BarcodeService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $name = '';
    public $sku = '';
    public $barcode = '';
    public $description = '';
    public $category_id = '';
    public $supplier_id = '';
    public $unit_of_measure = 'pieces';
    public $cost_price = '';
    public $selling_price = '';
    public $current_stock = 0;
    public $minimum_stock = 10;
    public $maximum_stock = '';
    public $image = null;
    public $status = 'active';

    public $autoGenerateSku = true;
    public $autoGenerateBarcode = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'sku' => 'required|string|max:100|unique:products,sku',
        'barcode' => 'nullable|string|max:100|unique:products,barcode',
        'description' => 'nullable|string',
        'category_id' => 'required|exists:categories,id',
        'supplier_id' => 'nullable|exists:suppliers,id',
        'unit_of_measure' => 'required|string|max:50',
        'cost_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'current_stock' => 'required|integer|min:0',
        'minimum_stock' => 'required|integer|min:0',
        'maximum_stock' => 'nullable|integer|min:0',
        'image' => 'nullable|image|max:2048',
        'status' => 'required|in:active,inactive',
    ];

    protected $validationAttributes = [
        'category_id' => 'category',
        'supplier_id' => 'supplier',
        'unit_of_measure' => 'unit of measure',
        'cost_price' => 'cost price',
        'selling_price' => 'selling price',
        'current_stock' => 'current stock',
        'minimum_stock' => 'minimum stock',
        'maximum_stock' => 'maximum stock',
    ];

    protected $messages = [
        'name.required' => 'Product name is required',
        'sku.required' => 'SKU is required',
        'sku.unique' => 'This SKU already exists',
        'barcode.unique' => 'This barcode already exists',
        'category_id.required' => 'Please select a category',
        'unit_of_measure.required' => 'Unit of measure is required',
        'cost_price.required' => 'Cost price is required',
        'selling_price.required' => 'Selling price is required',
    ];

    public function updatedName($value)
    {
        if ($this->autoGenerateSku && empty($this->sku)) {
            $this->generateSku();
        }
    }

    public function updatedCategoryId($value)
    {
        if ($this->autoGenerateSku && !empty($this->name)) {
            $this->generateSku();
        }
    }

    public function updatedSupplierId($value)
    {
        if ($this->autoGenerateSku && !empty($this->name)) {
            $this->generateSku();
        }
    }

    public function generateSku()
    {
        try {
            $category = Category::find($this->category_id);
            $supplier = $this->supplier_id ? Supplier::find($this->supplier_id) : null;

            $categoryPrefix = $category
                ? strtoupper(substr($category->name, 0, 4))
                : 'GEN';

            $supplierPrefix = $supplier
                ? strtoupper(substr($supplier->company_name, 0, 4))
                : 'NONE';

            // Generate random alphanumeric
            $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));

            $sku = "{$categoryPrefix}-{$supplierPrefix}-{$random}";

            // Ensure uniqueness
            while (Product::where('sku', $sku)->exists()) {
                $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
                $sku = "{$categoryPrefix}-{$supplierPrefix}-{$random}";
            }

            $this->sku = $sku;

            $this->dispatch('toast', [
                'message' => 'SKU generated successfully',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to generate SKU',
                'type' => 'error'
            ]);
        }
    }

    public function generateBarcode()
    {
        try {
            $barcodeService = app(BarcodeService::class);
            $this->barcode = $barcodeService->generateUniqueBarcode();

            $this->dispatch('toast', [
                'message' => 'Barcode generated successfully',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to generate barcode',
                'type' => 'error'
            ]);
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'name' => $this->name,
                'sku' => $this->sku,
                'barcode' => $this->barcode,
                'description' => $this->description,
                'category_id' => $this->category_id,
                'supplier_id' => $this->supplier_id,
                'unit_of_measure' => $this->unit_of_measure,
                'cost_price' => $this->cost_price,
                'selling_price' => $this->selling_price,
                'current_stock' => $this->current_stock,
                'minimum_stock' => $this->minimum_stock,
                'maximum_stock' => $this->maximum_stock,
                'status' => $this->status,
            ];

            if ($this->image) {
                $data['image_path'] = $this->image->store('products', 'public');
            }

            $product = Product::create($data);

            // Check if stock is below minimum and create notification if needed
            if ($product->current_stock < $product->minimum_stock) {
                $notificationService = app(NotificationService::class);

                if ($product->current_stock == 0) {
                    // Out of stock notification
                    $notificationService->notifyAdminsAndManagers(
                        'danger',
                        'Critical: Product Out of Stock',
                        "New product '{$product->name}' was added with zero stock. Immediate action required.",
                        [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'link' => route('products.show', $product),
                        ]
                    );

                    $this->dispatch('notification-created');
                } else {
                    // Low stock notification
                    $notificationService->notifyAdminsAndManagers(
                        'warning',
                        'Low Stock Alert',
                        "New product '{$product->name}' was added with low stock ({$product->current_stock}/{$product->minimum_stock} units).",
                        [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'current_stock' => $product->current_stock,
                            'minimum_stock' => $product->minimum_stock,
                            'link' => route('products.show', $product),
                        ]
                    );

                    $this->dispatch('notification-created');
                }
            }

            // Notify admins and managers about new product
            $notificationService = app(NotificationService::class);
            $notificationService->notifyAdminsAndManagers(
                'info',
                'New Product Added',
                "'{$product->name}' has been added to inventory by " . Auth::user()->name,
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'created_by' => Auth::user()->name,
                    'link' => route('products.show', $product),
                ]
            );

            $this->dispatch('notification-created');
            $this->dispatch('close-modal', 'create-product');
            $this->dispatch('product-created');
            $this->reset();

            $this->dispatch('toast', [
                'message' => 'Product created successfully.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to create product: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function closeModal()
    {
        $this->reset();
        $this->dispatch('close-modal', 'create-product');
    }

    public function render()
    {
        return view('livewire.products.create', [
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::where('status', 'active')->orderBy('company_name')->get(),
        ]);
    }
}

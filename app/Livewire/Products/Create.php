<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class Create extends Component
{
    use WithFileUploads;

    public $name = '';
    public $sku = '';
    public $barcode = '';
    public $description = '';
    public $category_id = '';
    public $supplier_id = '';
    public $unit_of_measure = '';
    public $cost_price = '';
    public $selling_price = '';
    public $current_stock = 0;
    public $minimum_stock = 10;
    public $maximum_stock = '';
    public $image = null;
    public $status = 'active';

    public $autoGenerateSku = true;

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

    public function updatedName($value)
    {
        if ($this->autoGenerateSku && empty($this->sku)) {
            $this->generateSku();
        }
    }

    public function generateSku()
    {
        $prefix = 'APL';
        $random = strtoupper(Str::random(8));
        $this->sku = $prefix . '-' . $random;
    }

    public function generateBarcode()
    {
        // Generate a simple numeric barcode (EAN-13 format)
        $this->barcode = '9' . str_pad(rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
    }

    public function save()
    {
        $this->validate();

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

        Product::create($data);

        $this->dispatch('close-modal', 'create-product');
        $this->dispatch('product-created');
        $this->reset();

        session()->flash('message', 'Product created successfully.');
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

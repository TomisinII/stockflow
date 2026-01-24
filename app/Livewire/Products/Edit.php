<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;

class Edit extends Component
{
    use WithFileUploads;

    public Product $product;

    public $name;
    public $sku;
    public $barcode;
    public $description;
    public $category_id;
    public $supplier_id;
    public $unit_of_measure;
    public $cost_price;
    public $selling_price;
    public $current_stock;
    public $minimum_stock;
    public $maximum_stock;
    public $image;
    public $status;

    public $existingImage;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->barcode = $product->barcode;
        $this->description = $product->description;
        $this->category_id = $product->category_id;
        $this->supplier_id = $product->supplier_id;
        $this->unit_of_measure = $product->unit_of_measure;
        $this->cost_price = $product->cost_price;
        $this->selling_price = $product->selling_price;
        $this->current_stock = $product->current_stock;
        $this->minimum_stock = $product->minimum_stock;
        $this->maximum_stock = $product->maximum_stock;
        $this->status = $product->status;
        $this->existingImage = $product->image_path;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($this->product->id)],
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('products', 'barcode')->ignore($this->product->id)],
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
    }

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

    public function update()
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
            // Delete old image if exists
            if ($this->product->image_path) {
                Storage::disk('public')->delete($this->product->image_path);
            }
            $data['image_path'] = $this->image->store('products', 'public');
        }

        $this->product->update($data);

        $this->dispatch('close-modal', 'edit-product-' . $this->product->id);
        $this->dispatch('product-updated');

        $this->dispatch('toast', [
            'message' => 'Product updated successfully.',
            'type' => 'success'
        ]);
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', 'edit-product-' . $this->product->id);
    }

    public function render()
    {
        return view('livewire.products.edit', [
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::where('status', 'active')->orderBy('company_name')->get(),
        ]);
    }
}

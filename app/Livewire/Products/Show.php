<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Show extends Component
{
    public Product $product;

    public $showStockHistory = true;

    public function mount(Product $product)
    {
        $this->product = $product->load(['category', 'supplier', 'stockAdjustments.user']);
    }

    public function deleteProduct()
    {
        $this->product->delete();

        session()->flash('message', 'Product deleted successfully.');
        return redirect()->route('products.index');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.products.show', [
            'recentAdjustments' => $this->product->stockAdjustments()
                ->with('user')
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }
}

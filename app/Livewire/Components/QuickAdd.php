<?php

namespace App\Livewire;

use Livewire\Component;

class QuickAdd extends Component
{
    /**
     * Navigate to products page and open create modal
     */
    public function addProduct()
    {
        return redirect()->route('products.index', ['action' => 'create-product']);
    }

    /**
     * Navigate to categories page and open create modal
     */
    public function addCategory()
    {
        return redirect()->route('categories.index', ['action' => 'create-category']);
    }

    /**
     * Navigate to suppliers page and open create modal
     */
    public function addSupplier()
    {
        return redirect()->route('suppliers.index', ['action' => 'create-supplier']);
    }

    /**
     * Navigate to stock adjustments page and open create modal
     */
    public function addStockAdjustment()
    {
        return redirect()->route('stock_adjustments.index', ['action' => 'create-stock-adjustment']);
    }

    /**
     * Navigate to purchase orders page and open create modal
     */
    public function addPurchaseOrder()
    {
        return redirect()->route('purchase_orders.index', ['action' => 'create-purchase-order']);
    }

    public function render()
    {
        return view('livewire.components.quick-add');
    }
}

<?php

use App\Livewire\Dashboard;
use App\Livewire\Landing;
use App\Livewire\Products;
use App\Models\Product;
use App\Services\BarcodeService;
use App\Livewire\Categories;
use App\Livewire\Suppliers;
use App\Livewire\PurchaseOrders;
use App\Livewire\StockAdjustments;
use App\Livewire\Reports;
use App\Livewire\Notifications;
use App\Livewire\Users;
use App\Livewire\Settings;
use Illuminate\Support\Facades\Route;

Route::get('/', Landing::class)
    ->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)
        ->name('dashboard');

    Route::get('/products', Products\Index::class)->name('products.index');
    Route::get('/products/{product}', Products\Show::class)->name('products.show');

    Route::get('/products/{product}/barcode', function (Product $product) {
        $barcodeService = app(BarcodeService::class);
        $html = $barcodeService->generateBarcodeLabel($product, 'standard');
        return response($html)->header('Content-Type', 'text/html');
    })->name('barcode.print');

    Route::get('/categories', Categories\Index::class)->name('categories.index');

    Route::get('/suppliers', Suppliers\Index::class)->name('suppliers.index');
    Route::get('/suppliers/{supplier}', Suppliers\Show::class)->name('suppliers.show');

    Route::get('/purchase-orders', PurchaseOrders\Index::class)->name('purchase_orders.index');
    Route::get('/purchase-orders/{purchaseOrder}', PurchaseOrders\Show::class)->name('purchase_orders.show');

    Route::get('/stock-adjustments', StockAdjustments\Index::class)->name('stock_adjustments.index');

    Route::get('/reports', Reports\Index::class)->name('reports.index');

    Route::get('/notifications', Notifications\Index::class)->name('notifications.index');

    Route::get('/settings/profile', Settings\Profile::class)->name('settings.profile');

    Route::get('/users', Users\Index::class)
        ->middleware('can:view_users')
        ->name('users.index');

    Route::get('/settings', Settings\Index::class)
        ->middleware('can:view_settings')
        ->name('settings.index');

});

require __DIR__.'/auth.php';

<?php

namespace App\Providers;

use App\Services\BarcodeService;
use App\Services\NotificationService;
use App\Services\PurchaseOrderService;
use App\Services\ReportService;
use App\Services\StockService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(BarcodeService::class);
        $this->app->singleton(StockService::class);
        $this->app->singleton(PurchaseOrderService::class);
        $this->app->singleton(ReportService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Providers;


use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Category;
use App\Policies\ProductPolicy;
use App\Policies\PurchaseOrderPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\UserPolicy;
use App\Policies\CategoryPolicy;
use Illuminate\Support\Facades\Gate;
use App\Services\BarcodeService;
use App\Services\NotificationService;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(PurchaseOrder::class, PurchaseOrderPolicy::class);
        Gate::policy(User::class, UserPolicy::class);


        Gate::define('viewAny-reports', [ReportPolicy::class, 'viewAny']);
        Gate::define('export-reports', [ReportPolicy::class, 'export']);


        // Give admins unrestricted access to everything
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('Admin')) {
                return true;
            }
        });
    }
}

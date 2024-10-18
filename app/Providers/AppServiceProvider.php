<?php

namespace App\Providers;

use App\Models\Workspace;
use Laravel\Cashier\Cashier;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Cashier::useCustomerModel(Workspace::class);
        Vite::prefetch(concurrency: 3);
    }
}

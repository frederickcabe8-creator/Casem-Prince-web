<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\CartService::class);
        $this->app->singleton(\App\Services\PaymentService::class);
        $this->app->singleton(\App\Services\OrderService::class);
        $this->app->singleton(\App\Services\PayPalService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

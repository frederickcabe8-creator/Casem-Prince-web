<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// Public storefront
Route::get('/', fn () => view('welcome'))->name('home');
Route::resource('products', ProductController::class)->only(['index', 'show']);
Route::post('/currency/switch', [CurrencyController::class, 'switch'])->name('currency.switch');

// Stripe webhook (no CSRF)
Route::post('/webhooks/stripe', [WebhookController::class, 'stripe'])
     ->name('webhooks.stripe');

// Authenticated customer routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
    Route::resource('cart', CartController::class)
         ->only(['index', 'store', 'update', 'destroy']);
    Route::get('checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::resource('orders', OrderController::class)
         ->only(['index', 'show']);

    // Wishlist
    Route::get('wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('wishlist/{product}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
});

// Admin panel
Route::middleware(['auth', 'role:admin,super-admin'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {
         Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
         Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
         Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);
         Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
     });

require __DIR__.'/auth.php';
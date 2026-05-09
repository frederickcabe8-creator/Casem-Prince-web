<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

// Public storefront
Route::get('/', fn () => view('welcome'))->name('home');
Route::resource('products', ProductController::class)->only(['index', 'show']);

// Stripe webhook (no CSRF)
Route::post('/webhooks/stripe', [WebhookController::class, 'stripe'])
     ->name('webhooks.stripe');

// Authenticated customer routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
    Route::resource('cart', CartController::class)
         ->only(['index', 'store', 'update', 'destroy']);
    Route::get('checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::resource('orders', OrderController::class)
         ->only(['index', 'show']);
});

// Admin panel
Route::middleware(['auth', 'role:admin,super-admin'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {
         Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
         Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
         Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);
     });

// TEMPORARY - remove after use
Route::get('/setup-admin', function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $user = \App\Models\User::where('email', 'admin@fujii.com')->first();
    if ($user) {
        $user->syncRoles(['admin']);
        return 'Success! Admin role assigned to ' . $user->email;
    }
    return 'User not found!';
});

require __DIR__.'/auth.php';
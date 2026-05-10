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
Route::middleware(['auth'])->group(function () {
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
         Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
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

// TEMPORARY - remove after use
Route::get('/setup-customer', function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
    \App\Models\User::whereDoesntHave('roles')->each(function ($user) {
        $user->assignRole('customer');
    });
    return 'Customer role created and assigned to all users without roles!';
});

// TEMPORARY - remove after fixing
Route::get('/fix-sessions', function () {
    try {
        $exists = \Illuminate\Support\Facades\Schema::hasTable('sessions');
        if (!$exists) {
            \Illuminate\Support\Facades\Artisan::call('session:table');
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            return 'Sessions table CREATED successfully!';
        }
        return 'Sessions table already EXISTS - safe to use database driver';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

// TEMPORARY - remove after fixing
Route::get('/verify-all-users', function () {
    \App\Models\User::whereNull('email_verified_at')->update([
        'email_verified_at' => now()
    ]);
    return 'All users verified!';
});

// TEMPORARY DEBUG - remove after fixing
Route::get('/debug-login/{email}', function ($email) {
    $user = \App\Models\User::where('email', $email)->first();
    if (!$user) return 'User not found: ' . $email;
    return response()->json([
        'id' => $user->id,
        'email' => $user->email,
        'email_verified_at' => $user->email_verified_at,
        'roles' => $user->roles->pluck('name'),
        'has_password' => !empty($user->password),
    ]);
});

require __DIR__.'/auth.php';
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
{
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    
    $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    
    $user = User::where('email', 'casem@fujii.com')->first();
    if ($user) {
        $user->syncRoles(['admin']);
    }
}
}
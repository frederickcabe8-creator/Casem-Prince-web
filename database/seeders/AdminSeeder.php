<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        try {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

            $user = User::where('email', 'casem@fujii.com')->first();
            
            if ($user) {
                $user->syncRoles(['admin']);
                $this->command->info('Admin role assigned successfully!');
            } else {
                $this->command->error('User not found!');
            }
        } catch (\Exception $e) {
            $this->command->error('Error: ' . $e->getMessage());
        }
    }
}
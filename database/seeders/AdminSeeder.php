<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::where('email', 'casem@fujii.com')
            ->update(['role' => 'admin']);
    }
}
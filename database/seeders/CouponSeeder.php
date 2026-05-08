<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::create([
            'code'             => 'WELCOME10',
            'type'             => 'percentage',
            'value'            => 10,
            'min_order_amount' => 50,
            'is_active'        => true,
            'expires_at'       => now()->addMonths(3),
        ]);

        Coupon::create([
            'code'             => 'SAVE20',
            'type'             => 'fixed',
            'value'            => 20,
            'min_order_amount' => 100,
            'is_active'        => true,
            'expires_at'       => now()->addMonths(1),
        ]);
    }
}
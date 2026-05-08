<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->restrictOnDelete();
            $table->foreignId('coupon_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();
            $table->string('order_number')->unique();
            $table->enum('status', [
                'pending', 'confirmed', 'processing',
                'shipped', 'delivered', 'cancelled', 'refunded',
            ])->default('pending');
            $table->enum('payment_status', [
                'pending', 'paid', 'failed', 'refunded', 'partially_refunded',
            ])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->json('shipping_address');
            $table->json('billing_address');
            $table->text('notes')->nullable();
            $table->timestamp('placed_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'payment_status']);
            $table->index('order_number');
            $table->index('payment_intent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
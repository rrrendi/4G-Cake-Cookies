<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name', 150);
            $table->string('customer_phone', 20);
            $table->string('customer_email', 191)->nullable();
            $table->date('pickup_delivery_date');
            $table->string('pickup_delivery_slot', 20);
            $table->enum('delivery_method', ['pickup', 'jnt']);
            $table->text('delivery_address')->nullable();
            $table->string('delivery_city', 60)->nullable();
            $table->string('delivery_district', 60)->nullable();
            $table->string('delivery_postal_code', 10)->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('packaging_cost', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->enum('status', ['menunggu_pembayaran', 'diproses', 'dikemas', 'dikirim', 'selesai', 'dibatalkan'])->default('menunggu_pembayaran');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'pickup_delivery_date', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

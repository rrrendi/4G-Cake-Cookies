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
        Schema::create('shippings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('recipient_name', 150);
            $table->string('phone', 20);
            $table->text('address');
            $table->string('city', 60);
            $table->enum('courier', ['J&T Express', 'J&T Cargo', 'Kurir internal'])->default('J&T Express');
            $table->string('tracking_number', 30)->nullable()->unique();
            $table->enum('status', ['pickup', 'kurir', 'jalan', 'sampai'])->default('pickup');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shippings');
    }
};

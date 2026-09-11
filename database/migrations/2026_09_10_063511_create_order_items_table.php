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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name_snapshot', 150);
            $table->string('variant_snapshot', 100)->nullable();
            $table->decimal('price_snapshot', 12, 2);
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('subtotal', 12, 2);
            $table->enum('production_status', ['belum_mulai', 'diproses', 'selesai'])->default('belum_mulai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('slug')->unique();
            $table->text('description');
            
            // Info Ekstra Produk
            $table->text('ingredients')->nullable();
            $table->string('weight_label', 60)->nullable();
            $table->string('storage_note')->nullable();
            $table->string('delivery_info')->nullable()->default('J&T / ambil sendiri');
            $table->string('condition_info')->nullable()->default('Dibuat setelah dipesan');
            
            // Harga & Varian Dinamis
            $table->integer('price')->default(0); // Harga dasar (paling murah)
            $table->integer('compare_at_price')->nullable();
            $table->json('variant_options')->nullable(); // Simpan dalam bentuk JSON array
            
            // Stok & Pre-Order
            $table->boolean('is_preorder')->default(false);
            $table->integer('min_preorder_days')->default(0);
            $table->enum('stock_status', ['tersedia', 'habis'])->default('tersedia');
            $table->boolean('is_best_seller')->default(false);
            
            // Dukungan Banyak Foto
            $table->string('photo_main')->nullable();
            $table->json('photos')->nullable(); // Array path gambar
            
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->integer('sold_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

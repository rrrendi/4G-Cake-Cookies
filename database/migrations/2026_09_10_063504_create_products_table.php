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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained();
            $table->string('name', 150);
            $table->string('slug', 160)->unique();
            $table->text('description');
            $table->text('ingredients')->nullable();
            $table->text('storage_note')->nullable();
            $table->string('weight_label', 60)->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('compare_at_price', 12, 2)->nullable();
            $table->boolean('is_preorder')->default(true);
            $table->unsignedSmallInteger('min_preorder_days')->default(0);
            $table->enum('stock_status', ['tersedia', 'habis'])->default('tersedia');
            $table->boolean('is_best_seller')->default(false);
            $table->json('variant_options')->nullable();
            $table->string('photo_main', 255)->nullable();
            $table->decimal('rating_avg', 2, 1)->default(0);
            $table->integer('rating_count')->default(0);
            $table->integer('sold_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['category_id', 'stock_status', 'is_best_seller']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

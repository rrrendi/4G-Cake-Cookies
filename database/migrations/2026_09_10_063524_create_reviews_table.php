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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->tinyInteger('rating_overall');
            $table->tinyInteger('rating_rasa');
            $table->tinyInteger('rating_kualitas');
            $table->tinyInteger('rating_packaging');
            $table->tinyInteger('rating_pelayanan');
            $table->text('comment');
            $table->string('photo', 255)->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->text('admin_reply')->nullable();
            $table->foreignId('replied_by')->nullable()->constrained('users');
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

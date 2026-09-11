<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'ingredients', 
        'storage_note', 'weight_label', 'price', 'compare_at_price', 
        'is_preorder', 'min_preorder_days', 'stock_status', 
        'is_best_seller', 'variant_options', 'photo_main', 
        'rating_avg', 'rating_count', 'sold_count'
    ];

    protected $casts = [
        'is_preorder' => 'boolean',
        'is_best_seller' => 'boolean',
        'variant_options' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
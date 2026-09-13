<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    // Mengizinkan semua kolom diisi tanpa diblokir (Aman untuk Admin)
    protected $guarded = [];

    // Mengubah JSON string menjadi Array otomatis di PHP
    protected $casts = [
        'variant_options' => 'array',
        'photos' => 'array',
        'is_preorder' => 'boolean',
        'is_best_seller' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
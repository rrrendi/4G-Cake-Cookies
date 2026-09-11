<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Ambil 4 produk Best Seller dari Database
        $bestSellers = Product::with('category')
                              ->where('is_best_seller', true)
                              ->take(4)
                              ->get();
        
        // 2. Format datanya agar persis seperti variabel PRODUK di data.js
        $mappedBestSellers = $bestSellers->map(function($p) {
            return [
                'id' => $p->id,
                'slug' => $p->slug,
                'nama' => $p->name,
                'kategori' => $p->category->name ?? '',
                'harga' => $p->price,
                'hargaCoret' => $p->compare_at_price,
                'rating' => (float)$p->rating_avg,
                'ulasan' => $p->rating_count,
                'stok' => $p->stock_status,
                'po' => $p->min_preorder_days,
                'terjual' => $p->sold_count,
                'bestSeller' => $p->is_best_seller,
                'berat' => $p->weight_label,
                'dibuat' => $p->created_at ? $p->created_at->format('Y-m-d') : date('Y-m-d'),
                'desc' => $p->description,
                'bahan' => $p->ingredients,
                'simpan' => $p->storage_note,
                'varian' => is_array($p->variant_options) && count($p->variant_options) > 0 ? $p->variant_options : []
            ];
        });

        // 3. Hitung jumlah item di keranjang (agar badge di Beranda sinkron)
        $cartCount = collect(session()->get('cart', []))->sum('qty');

        return view('home', compact('mappedBestSellers', 'cartCount'));
    }
}
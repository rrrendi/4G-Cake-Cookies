<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CatalogController extends Controller
{
    public function index()
    {
        // 1. Ambil produk dan kategori dari Database
        $products = Product::with('category')->get();
        
        // 2. Format datanya agar persis seperti variabel PRODUK di data.js
        $mappedProducts = $products->map(function($p) {
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
                'varian' => is_array($p->variant_options) ? $p->variant_options : []
            ];
        });

        return view('catalog', compact('mappedProducts'));
    }

    public function show($slug)
    {
        // 1. Ambil data produk utama
        $product = Product::with('category')->where('slug', $slug)->firstOrFail();
        
        // 2. Ambil produk terkait (kategori sama, kecuali produk ini sendiri)
        $relatedProducts = Product::where('category_id', $product->category_id)
                                  ->where('id', '!=', $product->id)
                                  ->where('stock_status', 'tersedia')
                                  ->inRandomOrder()
                                  ->take(4)
                                  ->get();

        // 3. Tanggal Estimasi Pre-Order
        $estimasiPO = Carbon::now()->addDays($product->min_preorder_days)->translatedFormat('d F Y');

        // 4. Ambil Ulasan (Sementara list kosong untuk MVP)
        $reviews = collect(); 

        return view('product-detail', compact('product', 'relatedProducts', 'estimasiPO', 'reviews'));
    }
}
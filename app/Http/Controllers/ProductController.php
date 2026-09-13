<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // KUNCI PERBAIKAN: Fungsi Index untuk Halaman Admin (Hitung Terjual Real-Time)
    public function index()
    {
        // 1. Ambil semua produk beserta kategorinya
        $products = Product::with('category')->get();

        // 2. Hitung jumlah barang terjual dari tabel order_items yang tergabung dengan pesanan berstatus "selesai"
        foreach ($products as $product) {
            $terjual = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('order_items.product_id', $product->id)
                ->where('orders.status', 'selesai')
                ->sum('order_items.quantity');
                
            $product->sold_count = (int) $terjual;
        }

        // 3. Kirim variabel ke halaman Manajemen Produk (Blade)
        return view('admin.produk', compact('products'));
    }

    public function show($slug)
    {
        // 1. Ambil detail produk utama
        $product = Product::with('category')->where('slug', $slug)->firstOrFail();
        
        // 2. Ambil 4 produk acak lainnya (sebagai related products / "sering dipesan bersamaan")
        $relatedProducts = Product::with('category')
            ->where('id', '!=', $product->id)
            ->where('stock_status', 'tersedia')
            ->inRandomOrder()
            ->take(4)
            ->get();
            
        // 3. Kirim kedua variabel ke file view
        return view('product-detail', compact('product', 'relatedProducts'));
    }
}
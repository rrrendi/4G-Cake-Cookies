<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        // Mengambil produk best seller untuk ditampilkan di halaman depan
        $bestSellers = Product::where('stock_status', 'tersedia')
                              ->where('is_best_seller', true)
                              ->take(4)
                              ->get();

        return view('home', compact('bestSellers'));
    }
}
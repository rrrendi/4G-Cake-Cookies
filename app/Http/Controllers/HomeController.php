<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Hanya menghitung jumlah isi keranjang untuk badge di sudut kanan atas
        $cartCount = collect(session()->get('cart', []))->sum('qty');

        // PERBAIKAN: angka "4.8 dari 620 ulasan" sebelumnya ditulis tetap di
        // halaman (tidak sesuai data asli). Sekarang dihitung langsung dari
        // ulasan yang benar-benar ada & tidak disembunyikan admin.
        $totalUlasan = Review::where('is_hidden', false)->count();
        $rataRating = $totalUlasan ? round(Review::where('is_hidden', false)->avg('rating_overall'), 1) : 0;

        return view('home', compact('cartCount', 'totalUlasan', 'rataRating'));
    }
}
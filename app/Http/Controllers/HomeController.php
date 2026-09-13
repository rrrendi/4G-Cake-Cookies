<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Hanya menghitung jumlah isi keranjang untuk badge di sudut kanan atas
        $cartCount = collect(session()->get('cart', []))->sum('qty');

        return view('home', compact('cartCount'));
    }
}
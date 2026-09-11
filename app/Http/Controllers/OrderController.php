<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return view('riwayat-pesanan');
    }

    // Tambahkan method ini:
    public function show($kode)
    {
        // Lempar kode (ex: 4G-2026-XYZ) ke Blade untuk dibaca oleh JavaScript
        return view('pesanan-detail', compact('kode'));
    }
}
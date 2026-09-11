<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function create($kode)
    {
        // Melempar kode pesanan ke halaman Blade
        return view('review', compact('kode'));
    }

    public function store(Request $request, $kode)
    {
        // Validasi input ulasan dari AJAX
        $request->validate([
            'rating_total' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|min:20',
        ]);

        // Di fase integrasi DB nanti, kode ini akan menyimpan data ke tabel `reviews`.
        // Untuk sekarang, kita simulasikan sukses.
        
        return response()->json([
            'status' => 'success',
            'message' => 'Ulasan berhasil disimpan.'
        ]);
    }
}
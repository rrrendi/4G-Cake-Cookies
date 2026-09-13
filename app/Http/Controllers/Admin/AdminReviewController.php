<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index()
    {
        return view('admin.review');
    }

    // Method update disiapkan untuk fase backend (menyembunyikan review)
    public function update(Request $request, $id)
    {
        // Logika update akan diisi nanti
    }

    public function toggleStatus($id)
    {
        $review = \Illuminate\Support\Facades\DB::table('reviews')->where('id', $id)->first();
        
        if (!$review) {
            return response()->json(['message' => 'Ulasan tidak ditemukan.'], 404);
        }

        // Membalikkan nilai boolean is_hidden (jika 0 jadi 1, jika 1 jadi 0)
        $newStatus = $review->is_hidden ? false : true;
        
        \Illuminate\Support\Facades\DB::table('reviews')
            ->where('id', $id)
            ->update(['is_hidden' => $newStatus]);

        return response()->json([
            'status' => 'success', 
            // Tetap kembalikan format teks agar JavaScript di halaman admin dapat merespons
            'new_status' => $newStatus ? 'hidden' : 'approved' 
        ]);
    }

    public function reply(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string'
        ]);

        $review = \Illuminate\Support\Facades\DB::table('reviews')->where('id', $id)->first();
        
        if (!$review) {
            return response()->json(['message' => 'Ulasan tidak ditemukan.'], 404);
        }

        // Menyimpan teks balasan ke kolom admin_reply beserta riwayat waktu dan admin
        \Illuminate\Support\Facades\DB::table('reviews')
            ->where('id', $id)
            ->update([
                'admin_reply' => $request->reply,
                'replied_at' => now(),
                'replied_by' => auth()->id() 
            ]);

        return response()->json(['status' => 'success']);
    }
}
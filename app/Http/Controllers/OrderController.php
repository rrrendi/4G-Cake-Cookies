<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        // PERBAIKAN: Hanya mengambil pesanan milik user yang sedang login
        // 'shipping' ditambahkan supaya nomor resi ambil dari data Manajemen Pengiriman,
        // bukan kolom yang tidak pernah ada di tabel orders.
        $orders = Order::where('user_id', Auth::id())
                       ->with(['items.product', 'shipping'])
                       ->latest()
                       ->get();
        
        return view('riwayat-pesanan', compact('orders'));
    }

    public function show($kode)
    {
        // PERBAIKAN: Pastikan pesanan yang dilihat benar-benar milik user yang sedang login
        // Jika user memaksa membuka URL pesanan orang lain, sistem akan memunculkan error 404 (Not Found)
        $order = Order::with(['items.product', 'payment', 'shipping'])
                      ->where('order_number', $kode)
                      ->where('user_id', Auth::id())
                      ->firstOrFail();
        
        return view('pesanan-detail', compact('order', 'kode'));
    }

    public function cancel(Order $order)
    {
        // Memastikan hanya pemilik asli yang bisa membatalkan pesanannya sendiri
        if ($order->user_id !== Auth::id()) {
            return back()->with('error_toast', 'Akses ditolak.');
        }

        // Pesanan hanya bisa dibatalkan jika admin belum memprosesnya
        if ($order->status === 'menunggu_pembayaran') {
            $order->update(['status' => 'dibatalkan']);
            return back()->with('success_toast', 'Pesanan berhasil dibatalkan.');
        }

        return back()->with('error_toast', 'Pesanan ini sudah diproses dan tidak bisa dibatalkan.');
    }
}

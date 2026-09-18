<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index()
    {
        // 'shipping' ditambahkan supaya nomor resi di halaman ini benar-benar
        // ikut data yang diisi lewat Manajemen Pengiriman (bukan kolom yang tidak ada).
        $orders = Order::with(['items.product', 'shipping'])->latest()->get();

        return view('admin.pesanan', compact('orders'));
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            $request->validate([
                'status' => 'required|string',
            ]);

            // Nomor resi TIDAK dibuat otomatis di sini — resi hanya diisi manual lewat
            // Manajemen Pengiriman, karena metode antar bisa saja bukan J&T (mis. Gojek/
            // kurir instan untuk lokasi dekat) sehingga tidak selalu butuh nomor resi.
            $order->ubahStatus($request->status, 'Diubah manual dari halaman Manajemen Pesanan.');

            return response()->json([
                'status' => 'success',
                'message' => 'Status pesanan berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}

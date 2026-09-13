<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product')->latest()->get();
        return view('admin.pesanan', compact('orders'));
    }

    // PERBAIKAN: Menggunakan $id secara langsung agar kebal terhadap error Route Binding
    public function updateStatus(Request $request, $id)
    {
        try {
            // Tarik paksa data pesanan berdasarkan ID
            $order = Order::findOrFail($id);

            // PERBAIKAN: Longgarkan validasi agar menerima status desain baru ('dikemas')
            $request->validate([
                'status' => 'required|string',
            ]);

            $data = ['status' => $request->status];

            // Auto-Generate Resi jika J&T dikirim
            $metode = strtolower($order->delivery_method ?? '');
            if ($request->status === 'dikirim' && (str_contains($metode, 'jnt') || str_contains($metode, 'j&t')) && empty($order->tracking_number)) {
                $data['tracking_number'] = 'JT88' . rand(10000000, 99999999);
            }

            // Simpan perubahan ke database
            $order->update($data);

            // PERBAIKAN: Jika pesanan Selesai, tambahkan angka "Terjual" (+1) pada tabel Produk
            if ($request->status === 'selesai' && $order->status !== 'selesai') {
                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        \App\Models\Product::where('id', $item->product_id)->increment('sold_count', $item->quantity);
                    }
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Status pesanan berhasil diperbarui.',
                'tracking_number' => $data['tracking_number'] ?? $order->tracking_number
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
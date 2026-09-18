<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipping;
use App\Models\Review;
use Carbon\Carbon;

class AdminNotificationController extends Controller
{
    /**
     * Notifikasi lonceng di semua halaman admin — memakai data yang sama dengan
     * kartu "Perlu perhatian" di Dashboard, supaya konsisten dan tidak perlu
     * tabel notifikasi baru.
     */
    public function index()
    {
        $now = Carbon::now();
        $items = [];

        $menunggu = Order::where('status', 'menunggu_pembayaran')->count();
        if ($menunggu > 0) {
            $items[] = [
                'ikon' => 'clock',
                'tipe' => 'warning',
                'pesan' => $menunggu . ' pesanan menunggu konfirmasi pembayaran.',
                'link' => route('admin.pesanan.index'),
            ];
        }

        $habis = Product::where('stock_status', 'habis')->count();
        if ($habis > 0) {
            $items[] = [
                'ikon' => 'package-x',
                'tipe' => 'warning',
                'pesan' => $habis . ' produk berstatus habis.',
                'link' => route('admin.produk.index'),
            ];
        }

        $tanpaResi = Shipping::whereNull('tracking_number')->count();
        if ($tanpaResi > 0) {
            $items[] = [
                'ikon' => 'truck',
                'tipe' => 'info',
                'pesan' => $tanpaResi . ' paket J&T belum diisi nomor resi.',
                'link' => route('admin.pengiriman.index'),
            ];
        }

        $ulasanBaru = Review::where('created_at', '>=', $now->copy()->subDays(7))->count();
        if ($ulasanBaru > 0) {
            $items[] = [
                'ikon' => 'star',
                'tipe' => 'info',
                'pesan' => $ulasanBaru . ' ulasan baru masuk minggu ini.',
                'link' => route('admin.review.index'),
            ];
        }

        return response()->json(['items' => $items, 'total' => count($items)]);
    }
}

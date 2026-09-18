<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        $hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // Semua pesanan yang belum dibatalkan dikelompokkan per tanggal ambil/kirim,
        // supaya owner tetap bisa merencanakan produksi walau pembayaran belum diverifikasi.
        $orders = Order::where('status', '!=', 'dibatalkan')
            ->whereNotNull('pickup_delivery_date')
            ->with('items')
            ->orderBy('pickup_delivery_date')
            ->get();

        $jadwal = $orders->groupBy(fn ($o) => $o->pickup_delivery_date->format('Y-m-d'))
            ->map(function ($ordersHariItu, $tanggal) use ($hariIndo) {
                $items = [];
                foreach ($ordersHariItu as $o) {
                    foreach ($o->items as $it) {
                        $items[] = [
                            'id' => $it->id,
                            'nama' => $it->product_name_snapshot,
                            'varian' => $it->variant_snapshot,
                            'qty' => (int) $it->quantity,
                            'kode' => $o->order_number,
                            'status' => $it->production_status,
                        ];
                    }
                }
                $tgl = Carbon::parse($tanggal);

                return [
                    'tanggal' => $tanggal,
                    'hari' => $hariIndo[$tgl->dayOfWeek],
                    'pesanan' => $ordersHariItu->count(),
                    'items' => $items,
                ];
            })
            ->values();

        $now = Carbon::now();
        $tahun = $now->year;
        $bulan = $now->month - 1; // index array Alpine dimulai dari 0
        $tgl_iso = $now->format('Y-m-d');

        return view('admin.jadwal', compact('jadwal', 'tahun', 'bulan', 'tgl_iso'));
    }

    public function updateItemStatus(Request $request, OrderItem $orderItem)
    {
        $request->validate([
            'status' => 'required|in:belum_mulai,diproses,selesai',
        ]);

        $orderItem->update(['production_status' => $request->status]);

        // Keterhubungan Jadwal Produksi -> Manajemen Pesanan: begitu SEMUA item pada
        // sebuah pesanan yang sedang "Diproses" selesai dibuat, pesanan otomatis
        // dimajukan ke "Dikemas" — owner tidak perlu mengubahnya manual dua kali.
        $order = $orderItem->order;
        $cascade = null;

        if ($request->status === 'selesai' && $order && $order->status === 'diproses') {
            $masihAda = $order->items()->where('production_status', '!=', 'selesai')->exists();

            if (!$masihAda) {
                $order->ubahStatus('dikemas', 'Otomatis: seluruh item produksi pesanan ini sudah selesai dibuat.');
                $cascade = 'Semua item selesai — pesanan ' . $order->order_number . ' otomatis ditandai Dikemas.';
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Status produksi diperbarui.',
            'production_status' => $orderItem->production_status,
            'order_cascade' => $cascade,
        ]);
    }
}

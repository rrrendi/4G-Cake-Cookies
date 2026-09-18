<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminPengirimanController extends Controller
{
    public function index()
    {
        $shippings = Shipping::with('order')->latest()->get()->map(function ($s) {
            return $this->format($s);
        })->values();

        return view('admin.pengiriman', compact('shippings'));
    }

    public function updateResi(Request $request, $id)
    {
        $shipping = Shipping::with('order')->findOrFail($id);

        $request->validate([
            'kurir' => 'required|in:J&T Express,J&T Cargo,Kurir internal',
            'status' => 'required|in:pickup,kurir,jalan,sampai',
            'resi' => 'nullable|string|max:30',
            'tanggal_kirim' => 'nullable|date',
        ]);

        // Resi hanya wajib untuk pengiriman J&T. Kurir internal (termasuk ojek online/
        // pengiriman instan untuk lokasi dekat) biasanya tidak punya nomor resi sama sekali.
        if ($request->status !== 'pickup' && $request->kurir !== 'Kurir internal' && empty($request->resi)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Status ini membutuhkan nomor resi J&T terlebih dahulu.',
            ], 422);
        }

        if (!empty($request->resi)) {
            $dipakai = Shipping::where('tracking_number', $request->resi)->where('id', '!=', $shipping->id)->exists();
            if ($dipakai) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nomor resi ini sudah dipakai pengiriman lain.',
                ], 422);
            }
        }

        $data = [
            'courier' => $request->kurir,
            'status' => $request->status,
            'tracking_number' => $request->resi ?: null,
        ];

        $data['shipped_at'] = $request->status === 'pickup'
            ? null
            : ($request->tanggal_kirim ? Carbon::parse($request->tanggal_kirim) : ($shipping->shipped_at ?? Carbon::now()));

        $data['delivered_at'] = $request->status === 'sampai' ? ($shipping->delivered_at ?? Carbon::now()) : null;

        $shipping->update($data);
        $cascade = $this->cascadeKeOrder($shipping, $request->status);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengiriman ' . ($shipping->order->order_number ?? '') . ' diperbarui.',
            'data' => $this->format($shipping->fresh('order')),
            'order_cascade' => $cascade,
        ]);
    }

    public function majukan($id)
    {
        $shipping = Shipping::with('order')->findOrFail($id);
        $urutan = ['pickup', 'kurir', 'jalan', 'sampai'];
        $index = array_search($shipping->status, $urutan, true);

        if ($index === false || $index === count($urutan) - 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengiriman ini sudah sampai tujuan.',
            ], 422);
        }

        $statusBaru = $urutan[$index + 1];
        $data = ['status' => $statusBaru];

        // CATATAN: nomor resi TIDAK dibuat otomatis di sini. Sebagian pengiriman
        // (Gojek/kurir instan untuk lokasi dekat, atau "Kurir internal" lainnya) memang
        // tidak punya nomor resi sama sekali — admin yang isi manual lewat "Input resi"
        // kalau memang ada nomornya.
        if ($statusBaru === 'kurir' && empty($shipping->shipped_at)) {
            $data['shipped_at'] = Carbon::now();
        }

        if ($statusBaru === 'sampai') {
            $data['delivered_at'] = Carbon::now();
        }

        $shipping->update($data);
        $cascade = $this->cascadeKeOrder($shipping, $statusBaru);

        return response()->json([
            'status' => 'success',
            'message' => 'Status pengiriman diperbarui.',
            'data' => $this->format($shipping->fresh('order')),
            'order_cascade' => $cascade,
        ]);
    }

    /**
     * Menghubungkan status pengiriman ke status pesanan supaya Manajemen Pesanan,
     * Jadwal Produksi, dan Manajemen Pengiriman benar-benar satu alur, bukan tiga
     * modul terpisah:
     * - Paket mulai dibawa kurir/dalam perjalanan -> pesanan otomatis jadi "Dikirim".
     * - Paket sampai tujuan -> pesanan otomatis jadi "Selesai" (produk terjual ikut
     *   terhitung lewat Order::ubahStatus()).
     * Tidak menyentuh pesanan yang sudah "selesai" atau "dibatalkan".
     */
    private function cascadeKeOrder(Shipping $shipping, string $statusBaru): ?string
    {
        $order = $shipping->order;

        if (!$order || in_array($order->status, ['selesai', 'dibatalkan'], true)) {
            return null;
        }

        if ($statusBaru === 'sampai') {
            $order->ubahStatus('selesai', 'Otomatis: paket pengiriman sudah sampai ke pelanggan.');

            return 'Pesanan ' . $order->order_number . ' otomatis ditandai Selesai.';
        }

        if (in_array($statusBaru, ['kurir', 'jalan'], true) && in_array($order->status, ['diproses', 'dikemas'], true)) {
            $order->ubahStatus('dikirim', 'Otomatis: paket sudah diambil/dalam perjalanan kurir.');

            return 'Pesanan ' . $order->order_number . ' otomatis ditandai Dikirim.';
        }

        return null;
    }

    private function format(Shipping $s): array
    {
        return [
            'id' => $s->id,
            'kode' => $s->order->order_number ?? '-',
            'pelanggan' => $s->recipient_name,
            'kota' => $s->city,
            'resi' => $s->tracking_number ?? '-',
            'status' => $s->status,
            'kurir' => $s->courier,
            'ongkir' => (float) ($s->order->shipping_cost ?? 0),
            'tanggalKirim' => $s->shipped_at ? $s->shipped_at->format('Y-m-d') : '-',
            'update' => $s->updated_at ? $s->updated_at->format('Y-m-d H:i') : '-',
        ];
    }
}

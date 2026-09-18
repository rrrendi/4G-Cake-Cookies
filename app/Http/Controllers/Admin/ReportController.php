<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\FinancialTransaction;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // Melempar tanggal hari ini ke Blade untuk kop surat laporan
        $tanggalCetak = Carbon::now()->locale('id')->isoFormat('D MMMM Y');

        $orders = Order::with('items.product.category')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($o) {
                return [
                    'kode' => $o->order_number,
                    'tanggal' => $o->created_at->format('Y-m-d'),
                    'pelanggan' => $o->customer_name,
                    'metode' => $o->delivery_method === 'jnt' ? 'J&T' : 'Ambil di Tempat',
                    'status' => $o->status,
                    'total' => (float) $o->total,
                    'items' => $o->items->map(function ($it) {
                        return [
                            'nama' => $it->product_name_snapshot . ($it->variant_snapshot ? ' (' . $it->variant_snapshot . ')' : ''),
                            'kategori' => optional(optional($it->product)->category)->name ?? 'Tanpa kategori',
                            'harga' => (float) $it->price_snapshot,
                            'qty' => (int) $it->quantity,
                        ];
                    })->values(),
                ];
            })
            ->values();

        $transaksi = FinancialTransaction::orderByDesc('transaction_date')
            ->get()
            ->map(function ($t) {
                return [
                    'tgl' => $t->transaction_date->format('Y-m-d'),
                    'jenis' => $t->type,
                    'kategori' => $t->category,
                    'ket' => $t->description,
                    'nominal' => (float) $t->amount,
                ];
            })
            ->values();

        $dariDefault = Carbon::now()->startOfMonth()->format('Y-m-d');
        $sampaiDefault = Carbon::now()->format('Y-m-d');

        return view('admin.laporan', compact('tanggalCetak', 'orders', 'transaksi', 'dariDefault', 'sampaiDefault'));
    }
}

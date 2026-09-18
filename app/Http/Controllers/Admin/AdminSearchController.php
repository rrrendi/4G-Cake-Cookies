<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminSearchController extends Controller
{
    /**
     * Pencarian global panel admin: produk, pesanan (kode/nama pelanggan), dan
     * pengguna (khusus untuk yang login sebagai owner, karena halaman Pengguna
     * & Role sendiri memang dibatasi untuk owner).
     */
    public function search(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['produk' => [], 'pesanan' => [], 'pengguna' => []]);
        }

        $produk = Product::where('name', 'like', "%{$q}%")
            ->take(5)
            ->get(['id', 'name'])
            ->map(fn ($p) => ['label' => $p->name, 'link' => route('admin.produk.index')])
            ->values();

        $pesanan = Order::where('order_number', 'like', "%{$q}%")
            ->orWhere('customer_name', 'like', "%{$q}%")
            ->take(5)
            ->get(['id', 'order_number', 'customer_name'])
            ->map(fn ($o) => ['label' => $o->order_number . ' — ' . $o->customer_name, 'link' => route('admin.pesanan.index')])
            ->values();

        $pengguna = collect();
        if (auth()->user() && auth()->user()->role === 'owner') {
            $pengguna = User::where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->take(5)
                ->get(['id', 'name', 'email'])
                ->map(fn ($u) => ['label' => $u->name . ' — ' . $u->email, 'link' => route('admin.pengguna.index')])
                ->values();
        }

        return response()->json([
            'produk' => $produk,
            'pesanan' => $pesanan,
            'pengguna' => $pengguna,
        ]);
    }
}

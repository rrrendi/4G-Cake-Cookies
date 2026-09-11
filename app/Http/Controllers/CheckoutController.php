<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        
        // Jika keranjang kosong, paksa kembali ke katalog
        if (empty($cart)) {
            return redirect()->route('catalog')->with('error_toast', 'Keranjang belanja Anda masih kosong.');
        }

        $cartItems = [];
        foreach($cart as $key => $item) {
            $cartItems[] = [
                'cart_key' => $key,
                'produk' => [
                    'slug' => $item['slug'],
                    'nama' => $item['name'],
                    'harga' => $item['price'],
                    'po' => $item['po_days'],
                ],
                'varian' => $item['variant'],
                'qty' => $item['qty'],
                'subtotal' => $item['qty'] * $item['price']
            ];
        }
        
        return view('checkout', compact('cartItems'));
    }

    public function proses(Request $request)
    {
        // Di fase berikutnya, di sinilah kita menyimpan data ke Tabel Orders di Database.
        // Untuk sekarang, kita terima datanya, simpan fotonya, dan kembalikan nomor resi sukses.

        $request->validate([
            'nama' => 'required|string|max:255',
            'hp' => 'required|string|max:20',
            'tanggal' => 'required|date',
            'metode' => 'required|in:jnt,ambil'
        ]);

        // Simulasi Upload File Bukti Pembayaran
        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/bukti_transfer'), $filename);
            $buktiPath = 'uploads/bukti_transfer/' . $filename;
        }

        // Generate Nomor Pesanan Unik (Format: 4G-TAHUNBULAN-RANDOM)
        $orderId = '4G-' . date('Ym') . '-' . strtoupper(Str::random(5));

        // Hapus keranjang karena pesanan sudah sukses
        session()->forget('cart');

        return response()->json([
            'status' => 'success',
            'order_id' => $orderId,
            'message' => 'Pesanan berhasil dibuat.'
        ]);
    }
}
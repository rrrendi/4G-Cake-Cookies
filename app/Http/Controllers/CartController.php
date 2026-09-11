<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    // Menampilkan isi keranjang (Fase selanjutnya)
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('keranjang', compact('cart'));
    }

    // Memasukkan produk ke dalam session keranjang
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
            'variant' => 'nullable|string',
            'action' => 'required|string|in:cart,checkout'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        if ($product->stock_status === 'habis') {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $product->name . ' sedang kehabisan stok.']);
            }
            return redirect()->back()->with('error_toast', $product->name . ' sedang kehabisan stok.');
        }

        $cart = session()->get('cart', []);
        $cartKey = $product->id . '-' . ($request->variant ?: 'default');

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['qty'] += $request->qty;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'qty' => $request->qty,
                'price' => $product->price,
                'variant' => $request->variant,
                'photo' => $product->photo_main,
                'weight' => $product->weight_label,
                'po_days' => $product->min_preorder_days
            ];
        }

        session()->put('cart', $cart);
        
        // Hitung total kuantitas item di keranjang
        $cartCount = collect($cart)->sum('qty');

        // Jika request berasal dari JavaScript (AJAX), balas tanpa refresh
        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success', 
                'message' => $request->qty . 'x ' . $product->name . ' berhasil ditambahkan.',
                'cart_count' => $cartCount
            ]);
        }

        if ($request->action === 'checkout') {
            return redirect()->route('cart.index')->with('info_toast', 'Menuju halaman checkout...');
        }

        return redirect()->back()->with('success_toast', $request->qty . 'x ' . $product->name . ' masuk ke keranjang.');
    }

    // Menghapus satu item dari keranjang
    public function destroy(Request $request)
    {
        $cartKey = $request->cart_key;
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $name = $cart[$cartKey]['name'];
            unset($cart[$cartKey]);
            session()->put('cart', $cart);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $name . ' dihapus dari keranjang.',
                    'cart_count' => collect($cart)->sum('qty')
                ]);
            }
            return redirect()->back()->with('success_toast', $name . ' dihapus dari keranjang.');
        }

        return redirect()->back()->with('error_toast', 'Produk tidak ditemukan di keranjang.');
    }

    // Mengubah jumlah (qty) item di keranjang
    public function update(Request $request)
    {
        $cartKey = $request->cart_key;
        $qty = (int) $request->qty;
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            if ($qty <= 0) {
                // Jika qty di-set 0, hapus item
                unset($cart[$cartKey]);
            } else {
                $cart[$cartKey]['qty'] = $qty;
            }
            session()->put('cart', $cart);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'cart_count' => collect($cart)->sum('qty')
                ]);
            }
            return redirect()->back();
        }

        return response()->json(['status' => 'error'], 404);
    }
}
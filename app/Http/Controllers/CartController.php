<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return view('keranjang');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'nullable|integer|min:1',
            'variant' => 'nullable|string'
        ]);

        $product = Product::with('category')->findOrFail($request->product_id);
        $qty = (int) ($request->qty ?? 1);
        $variant = $request->variant ?? 'Original';
        $cartKey = $product->id . '_' . $variant;

        $hargaFinal = (int) $product->price;
        $varianProduk = is_string($product->variant_options) ? json_decode($product->variant_options, true) : $product->variant_options;

        if (is_array($varianProduk)) {
            foreach ($varianProduk as $v) {
                if (is_array($v) && isset($v['name']) && isset($v['price'])) {
                    if (strtolower(trim($v['name'])) === strtolower(trim($variant))) {
                        $hargaFinal = (int) $v['price'];
                        break;
                    }
                }
            }
        }
        
        if ($hargaFinal === (int) $product->price && $request->has('price')) {
             $hargaFinal = (int) $request->price;
        }

        $itemData = [
            'product_id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $hargaFinal, 
            'qty' => $qty,
            'quantity' => $qty,
            'po_days' => (int) ($product->min_preorder_days ?? 2),
            'min_po' => (int) ($product->min_preorder_days ?? 2),
            'variant' => $variant,
            'photo' => $product->photo_main,
            'kategori' => $product->category ? $product->category->name : 'Produk',
        ];

        // ===============================================
        // KUNCI PERBAIKAN: PISAHKAN JALUR BUY NOW DAN CART
        // ===============================================
        if ($request->action === 'buy_now') {
            // Hapus sesi buy_now lama (jika ada), lalu buat yang baru
            session()->forget('buy_now');
            session()->put('buy_now', [$cartKey => $itemData]);
            
            return response()->json([
                'status' => 'success',
                'redirect' => route('checkout.index')
            ]);
        }

        // JIKA ACTION: CART (Masuk ke keranjang asli)
        $cart = session()->get('cart', []);
        
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['qty'] += $qty;
            $cart[$cartKey]['quantity'] = $cart[$cartKey]['qty'];
            $cart[$cartKey]['price'] = $hargaFinal;
        } else {
            $cart[$cartKey] = $itemData;
        }

        session()->put('cart', $cart);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $qty . 'x ' . $product->name . ' berhasil ditambahkan.',
                'cart_count' => collect($cart)->sum('qty')
            ]);
        }

        return back()->with('success_toast', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function update(Request $request)
    {
        $cartKey = $request->input('cart_key') ?? $request->json('cart_key');
        $qty = (int) ($request->input('qty') ?? $request->json('qty'));
        $cart = session()->get('cart', []);

        if ($cartKey && isset($cart[$cartKey])) {
            $cart[$cartKey]['qty'] = max(1, $qty);
            $cart[$cartKey]['quantity'] = max(1, $qty);
            session()->put('cart', $cart);
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Item tidak ditemukan'], 400);
    }

    public function destroy(Request $request)
    {
        $cartKey = $request->input('cart_key') ?? $request->json('cart_key');
        $cart = session()->get('cart', []);

        if ($cartKey && isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
            return response()->json(['status' => 'success', 'message' => 'Item dihapus']);
        } elseif (!$cartKey) {
            session()->forget('cart');
            return response()->json(['status' => 'success', 'message' => 'Keranjang dikosongkan']);
        }

        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus item'], 400);
    }
}
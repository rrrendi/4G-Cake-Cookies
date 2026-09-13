<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CheckoutController extends Controller
{
    protected $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function index(Request $request)
    {
        // 1. Cek apakah ini mode "Beli Sekarang" dari URL
        $isBuyNow = $request->query('mode') === 'buy_now';
        
        if ($isBuyNow && session()->has('buy_now')) {
            // Gunakan wadah 'buy_now' khusus
            session()->put('checkout_active_session', 'buy_now');
            $cart = session()->get('buy_now', []);
        } else {
            // Jika masuk dari halaman Keranjang biasa, amankan wadah 'cart'
            session()->put('checkout_active_session', 'cart');
            
            // Hapus sisa memori buy_now (jika ada) agar tidak menjadi bug/hantu
            session()->forget('buy_now');
            $cart = session()->get('cart', []);
            $isBuyNow = false; 
        }

        if (empty($cart)) {
            return redirect()->route('catalog')->with('error_toast', 'Keranjang Anda masih kosong.');
        }

        // Kita lempar variabel $cartItems ke view agar Blade tidak memaksa membaca session('cart')
        return view('checkout', [
            'cartItems' => $cart, 
            'is_buy_now' => $isBuyNow
        ]);
    }

    public function proses(Request $request)
    {
        // 2. Ambil data dari wadah sesi yang sedang aktif
        $activeSession = session()->get('checkout_active_session', 'cart');
        $cart = session()->get($activeSession, []);
        
        if (empty($cart)) {
            return response()->json(['status' => 'error', 'message' => 'Data pesanan kosong'], 400);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'hp' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'tanggal' => 'required|date',
            'jam' => 'required|string',
            'metode' => 'required|in:jnt,ambil',
            'catatan' => 'nullable|string',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string',
            'bayar' => 'required|string',
            'subtotal' => 'required|numeric',
            'ongkir' => 'required|numeric',
            'kemasan' => 'required|numeric',
            'diskon' => 'nullable|numeric',
            'kupon' => 'nullable|string|max:50',
            'total' => 'required|numeric',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048', 
        ]);

        if ($validated['bayar'] !== 'tunai' && !$request->hasFile('bukti')) {
            return response()->json([
                'status' => 'error',
                'message' => 'File gambar gagal ditangkap server. Pastikan ukuran di bawah 2 MB.'
            ], 400);
        }

        $checkoutData = $validated;
        $pathBukti = null;

        if ($request->hasFile('bukti')) {
            $pathBukti = $request->file('bukti')->store('payments', 'public');
            $checkoutData['bukti'] = $pathBukti;
            $checkoutData['payment_proof'] = $pathBukti;
            $checkoutData['bukti_pembayaran'] = $pathBukti;
        } else {
            $checkoutData['bukti'] = null;
        }

        $checkoutData['metode_pengiriman'] = $validated['metode'] === 'ambil' ? 'pickup' : 'jnt';

        $bayar = strtolower($validated['bayar']);
        if (in_array($bayar, ['bca', 'mandiri', 'bsi'])) {
            $checkoutData['metode_bayar'] = 'transfer_' . $bayar;
        } else {
            $checkoutData['metode_bayar'] = 'tunai';
        }

        $catatanAsli = $validated['catatan'] ?? '';
        if ($checkoutData['metode_bayar'] !== 'tunai') {
            $namaBank = strtoupper($bayar);
            $checkoutData['catatan'] = $catatanAsli ? "{$catatanAsli} (Transfer via {$namaBank})" : "Transfer via {$namaBank}";
        } else {
            $checkoutData['catatan'] = $catatanAsli;
        }

        $checkoutData['biaya_kemasan'] = $validated['kemasan'];
        $checkoutData['total_bayar'] = $validated['total'];

        $cartItems = array_map(function($item) {
            return [
                'product_id' => $item['product_id'],
                'product_name' => $item['name'] ?? $item['nama'] ?? 'Produk', 
                'variant' => $item['variant'] ?? 'Original',
                'price' => $item['price'] ?? 0,
                'quantity' => $item['qty'] ?? $item['quantity'] ?? 1,
            ];
        }, array_values($cart));

        try {
            $order = $this->checkoutService->processCheckout($checkoutData, $cartItems);
            
            if ($order) {
                $orderId = is_object($order) ? ($order->id ?? null) : (is_array($order) ? ($order['id'] ?? null) : null);

                if ($orderId) {
                    $columns = Schema::getColumnListing('orders');
                    $updateData = [];

                    if (!in_array('diskon', $columns) && !in_array('discount', $columns)) {
                        Schema::table('orders', function (Blueprint $table) {
                            $table->integer('diskon')->default(0);
                            $table->string('kode_promo')->nullable();
                        });
                        $columns[] = 'diskon';
                        $columns[] = 'kode_promo';
                    }

                    $diskonCol = in_array('diskon', $columns) ? 'diskon' : 'discount';
                    $promoCol = in_array('kode_promo', $columns) ? 'kode_promo' : (in_array('promo_code', $columns) ? 'promo_code' : null);

                    $updateData[$diskonCol] = $validated['diskon'] ?? 0;
                    if ($promoCol && !empty($validated['kupon'])) {
                        $updateData[$promoCol] = $validated['kupon'];
                    }

                    if ($pathBukti) {
                        $targetCol = null;
                        $candidates = ['payment_proof', 'bukti_pembayaran', 'bukti_bayar', 'receipt', 'payment_receipt', 'bukti_tf', 'bukti', 'file_bukti', 'struk', 'photo', 'image'];
                        foreach($candidates as $col) {
                            if (in_array($col, $columns)) { $targetCol = $col; break; }
                        }
                        
                        if (!$targetCol) {
                            Schema::table('orders', function (Blueprint $table) {
                                $table->string('bukti_pembayaran')->nullable();
                            });
                            $targetCol = 'bukti_pembayaran';
                        }
                        $updateData[$targetCol] = $pathBukti;
                    }

                    if (!empty($updateData)) {
                        DB::table('orders')->where('id', $orderId)->update($updateData);
                    }
                }
            }

            // 3. KUNCI: Hapus HANYA sesi yang baru saja diselesaikan checkout-nya
            session()->forget($activeSession);
            session()->forget('checkout_active_session');

            $orderNumber = is_object($order) ? ($order->order_number ?? 'Pesanan') : (is_array($order) ? ($order['order_number'] ?? 'Pesanan') : 'Pesanan');

            return response()->json([
                'status' => 'success',
                'order_id' => $orderNumber,
                'message' => 'Pesanan berhasil dibuat.'
            ]);
            
        } catch (\Throwable $e) { 
            return response()->json([
                'status' => 'error', 
                'message' => 'Kesalahan Sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}
<?php

namespace App\Services;

use App\Models\{Order, OrderItem, Payment, Shipping, OrderStatusHistory};
use Illuminate\Support\Facades\DB;
use Exception;

class CheckoutService
{
    public function processCheckout(array $validatedData, array $cartItems): Order
    {
        return DB::transaction(function () use ($validatedData, $cartItems) {
            
            // 1. Generate Nomor Pesanan (Format: 4G-YYMMDD-NNN)
            $orderNumber = $this->generateOrderNumber();
            
            // 2. Buat Data Utama Order
            $order = Order::create([
                'order_number'         => $orderNumber,
                'user_id'              => auth()->id(), // Akan null jika customer guest
                'customer_name'        => $validatedData['nama'],
                'customer_phone'       => $validatedData['hp'],
                'customer_email'       => $validatedData['email'] ?? null,
                'pickup_delivery_date' => $validatedData['tanggal'],
                'pickup_delivery_slot' => $validatedData['jam'],
                'delivery_method'      => $validatedData['metode_pengiriman'],
                'delivery_address'     => $validatedData['alamat'] ?? null,
                'delivery_city'        => $validatedData['kota'] ?? null,
                'subtotal'             => $validatedData['subtotal'],
                'shipping_cost'        => $validatedData['ongkir'],
                'packaging_cost'       => $validatedData['biaya_kemasan'],
                'total'                => $validatedData['total_bayar'],
                'notes'                => $validatedData['catatan'] ?? null,
                'status'               => 'menunggu_pembayaran'
            ]);

            // 3. Buat Detail Order Items (Snapshot Data agar tidak berubah jika produk dihapus/diedit admin)
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'              => $order->id,
                    'product_id'            => $item['product_id'],
                    'product_name_snapshot' => $item['product_name'],
                    'variant_snapshot'      => $item['variant'] ?? null,
                    'price_snapshot'        => $item['price'],
                    'quantity'              => $item['quantity'],
                    'subtotal'              => $item['price'] * $item['quantity'],
                ]);
            }

            // 4. Buat Data Pembayaran Awal
            Payment::create([
                'order_id'   => $order->id,
                'method'     => $validatedData['metode_bayar'],
                'amount'     => $validatedData['total_bayar'],
                'proof_file' => $validatedData['path_bukti'] ?? null,
                'status'     => 'pending'
            ]);

            // 5. Buat Data Pengiriman J&T (Hanya jika metode adalah JNT)
            if ($validatedData['metode_pengiriman'] === 'jnt') {
                Shipping::create([
                    'order_id'       => $order->id,
                    'recipient_name' => $validatedData['nama'],
                    'phone'          => $validatedData['hp'],
                    'address'        => $validatedData['alamat'],
                    'city'           => $validatedData['kota'],
                    'status'         => 'pickup'
                ]);
            }

            // 6. Log Status Awal ke Histori
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status'   => 'menunggu_pembayaran',
                'note'     => 'Pesanan baru dibuat via web'
            ]);

            return $order;
        });
    }
    
    private function generateOrderNumber(): string
    {
        $datePrefix = '4G-' . date('ymd') . '-';
        
        // Cari urutan pesanan terakhir di hari yang sama
        $lastOrder = Order::where('order_number', 'like', $datePrefix . '%')
                          ->orderBy('order_number', 'desc')
                          ->first();
                          
        // Tambahkan +1 dari pesanan terakhir, atau mulai dari 1
        $sequence = $lastOrder ? (int) substr($lastOrder->order_number, -3) + 1 : 1;
        
        // Gabungkan kembali dengan format 3 digit (contoh: 4G-260910-001)
        return $datePrefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}
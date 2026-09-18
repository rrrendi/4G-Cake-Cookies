<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'customer_name', 'customer_phone', 
        'customer_email', 'pickup_delivery_date', 'pickup_delivery_slot', 
        'delivery_method', 'delivery_address', 'delivery_city', 
        'delivery_district', 'delivery_postal_code', 'subtotal', 
        'shipping_cost', 'packaging_cost', 'total', 'status', 'notes'
    ];

    protected $casts = [
        'pickup_delivery_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function shipping()
    {
        return $this->hasOne(Shipping::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function financialTransactions()
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    /**
     * Titik tunggal untuk mengubah status pesanan, dipakai bersama oleh
     * Manajemen Pesanan, Jadwal Produksi (saat semua item selesai dibuat),
     * dan Manajemen Pengiriman (saat status kirim berubah) — supaya ketiga
     * modul itu benar-benar terhubung dan tidak masing-masing punya logika sendiri.
     *
     * Begitu pesanan pertama kali menjadi "selesai" (dijaga dari klik dobel /
     * cascade berulang lewat pengecekan status lama):
     * - "sold_count" tiap produk pada pesanan ini bertambah.
     * - Nilai penjualan (order->total) OTOMATIS dicatat sebagai satu baris
     *   "pemasukan" di Modul Keuangan (tertaut lewat order_id) — supaya owner
     *   tidak perlu mencatat ulang penjualan yang sama secara manual. Modul
     *   Keuangan jadi hanya untuk mencatat hal yang TIDAK punya sumber data lain
     *   (pengeluaran, atau pemasukan di luar sistem pesanan seperti jualan tunai).
     * - Perpindahan status dicatat ke order_status_histories supaya ada jejak
     *   jelas kenapa status berubah (manual atau otomatis).
     */
    public function ubahStatus(string $statusBaru, ?string $catatan = null): void
    {
        $statusLama = $this->status;

        if ($statusLama === $statusBaru) {
            return;
        }

        $this->update(['status' => $statusBaru]);

        if ($statusBaru === 'selesai' && $statusLama !== 'selesai') {
            foreach ($this->items as $item) {
                if ($item->product_id) {
                    \App\Models\Product::where('id', $item->product_id)->increment('sold_count', $item->quantity);
                }
            }

            // Dijaga dengan firstOrCreate berdasarkan order_id supaya tidak pernah
            // dobel walau ubahStatus('selesai') entah bagaimana terpanggil lagi.
            \App\Models\FinancialTransaction::firstOrCreate(
                ['order_id' => $this->id, 'type' => 'pemasukan'],
                [
                    'category' => 'Penjualan Online',
                    'description' => 'Penjualan pesanan ' . $this->order_number,
                    'amount' => $this->total,
                    'transaction_date' => now()->toDateString(),
                    'created_by' => auth()->id(),
                ]
            );
        }

        \App\Models\OrderStatusHistory::create([
            'order_id' => $this->id,
            'status' => $statusBaru,
            'note' => $catatan,
            'changed_by' => auth()->id(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\FinancialTransaction;
use App\Models\Shipping;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /** Nama bulan singkat berbahasa Indonesia, dipakai untuk label grafik. */
    private const BULAN_SINGKAT = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
    private const BULAN_PANJANG = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    public function index()
    {
        $now = Carbon::now();

        $awalBulanIni = $now->copy()->startOfMonth();
        $akhirBulanIni = $now->copy()->endOfMonth();
        $awalBulanLalu = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $akhirBulanLalu = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $ringkasan = $this->hitungRingkasan($awalBulanIni, $akhirBulanIni, $awalBulanLalu, $akhirBulanLalu);
        $ringkasan['periode'] = [
            'ini' => self::BULAN_PANJANG[$now->month - 1] . ' ' . $now->year,
            'lalu' => self::BULAN_PANJANG[$awalBulanLalu->month - 1] . ' ' . $awalBulanLalu->year,
        ];

        $chartData = $this->hitungTrenGrafik($now);
        $chartData['kategoriPenjualan'] = $this->hitungPenjualanPerKategori($awalBulanIni, $akhirBulanIni);
        $chartData['terlaris'] = $this->hitungProdukTerlaris();

        $pesananTerbaru = Order::latest()->take(5)->get()->map(function ($o) {
            return [
                'kode' => $o->order_number,
                'pelanggan' => $o->customer_name,
                'ambil' => optional($o->pickup_delivery_date)->format('Y-m-d'),
                'status' => $o->status,
                'total' => (float) $o->total,
            ];
        })->values();

        $antrean = $this->hitungAntreanProduksi($now);
        $perhatian = $this->hitungPerluPerhatian($now);

        return view('admin.dashboard', compact('ringkasan', 'chartData', 'pesananTerbaru', 'antrean', 'perhatian'));
    }

    private function hitungRingkasan($awalIni, $akhirIni, $awalLalu, $akhirLalu): array
    {
        $delta = function ($skrg, $lalu) {
            if ((float) $lalu == 0.0) {
                return $skrg > 0 ? 100.0 : 0.0;
            }
            return round((($skrg - $lalu) / $lalu) * 100, 1);
        };

        $pesananIni = Order::whereBetween('created_at', [$awalIni, $akhirIni])->count();
        $pesananLalu = Order::whereBetween('created_at', [$awalLalu, $akhirLalu])->count();

        $penjualanIni = (float) Order::whereBetween('created_at', [$awalIni, $akhirIni])->where('status', '!=', 'dibatalkan')->sum('total');
        $penjualanLalu = (float) Order::whereBetween('created_at', [$awalLalu, $akhirLalu])->where('status', '!=', 'dibatalkan')->sum('total');

        $pengeluaranIni = (float) FinancialTransaction::whereBetween('transaction_date', [$awalIni, $akhirIni])->where('type', 'pengeluaran')->sum('amount');
        $pengeluaranLalu = (float) FinancialTransaction::whereBetween('transaction_date', [$awalLalu, $akhirLalu])->where('type', 'pengeluaran')->sum('amount');

        $labaIni = $penjualanIni - $pengeluaranIni;
        $labaLalu = $penjualanLalu - $pengeluaranLalu;

        return [
            'totalPesanan' => ['nilai' => $pesananIni, 'delta' => $delta($pesananIni, $pesananLalu), 'sebelum' => $pesananLalu],
            'totalPenjualan' => ['nilai' => $penjualanIni, 'delta' => $delta($penjualanIni, $penjualanLalu), 'sebelum' => $penjualanLalu],
            'totalPengeluaran' => ['nilai' => $pengeluaranIni, 'delta' => $delta($pengeluaranIni, $pengeluaranLalu), 'sebelum' => $pengeluaranLalu],
            'labaBersih' => ['nilai' => $labaIni, 'delta' => $delta($labaIni, $labaLalu), 'sebelum' => $labaLalu],
        ];
    }

    private function hitungTrenGrafik(Carbon $now): array
    {
        $bulan12 = [];
        $penjualan12 = [];
        $pengeluaran12 = [];

        for ($i = 11; $i >= 0; $i--) {
            $bln = $now->copy()->subMonthsNoOverflow($i);
            $bulan12[] = self::BULAN_SINGKAT[$bln->month - 1];

            $penjualan12[] = (float) Order::whereYear('created_at', $bln->year)
                ->whereMonth('created_at', $bln->month)
                ->where('status', '!=', 'dibatalkan')
                ->sum('total');

            $pengeluaran12[] = (float) FinancialTransaction::whereYear('transaction_date', $bln->year)
                ->whereMonth('transaction_date', $bln->month)
                ->where('type', 'pengeluaran')
                ->sum('amount');
        }

        return [
            'bulan' => array_slice($bulan12, 6),
            'penjualan' => array_slice($penjualan12, 6),
            'pengeluaran' => array_slice($pengeluaran12, 6),
            'bulan12' => $bulan12,
            'penjualan12' => $penjualan12,
            'pengeluaran12' => $pengeluaran12,
        ];
    }

    /**
     * Pengganti "Pengeluaran per kategori": pengeluaran manual sering belum konsisten
     * dicatat, sehingga donatnya kosong. Data yang selalu tersedia dan lebih berguna
     * bagi owner untuk perencanaan produksi & stok adalah kontribusi pendapatan tiap
     * kategori produk (Cake, Cookies, Brownies, dst) pada bulan berjalan.
     */
    private function hitungPenjualanPerKategori($awalBulan, $akhirBulan): array
    {
        $query = fn ($withPeriode) => OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->where('orders.status', '!=', 'dibatalkan')
            ->when($withPeriode, fn ($q) => $q->whereBetween('orders.created_at', [$awalBulan, $akhirBulan]))
            ->select(
                DB::raw("COALESCE(categories.name, 'Tanpa kategori') as kategori"),
                DB::raw('SUM(order_items.subtotal) as total')
            )
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get();

        $hasil = $query(true);
        // Jika bulan berjalan belum ada transaksi, tampilkan agregat sepanjang waktu agar chart tidak kosong.
        if ($hasil->isEmpty()) {
            $hasil = $query(false);
        }

        return [
            'label' => $hasil->pluck('kategori')->values()->all(),
            'nilai' => $hasil->pluck('total')->map(fn ($v) => (float) $v)->values()->all(),
        ];
    }

    /**
     * PERBAIKAN: sebelumnya mengandalkan kolom cache "sold_count" di tabel produk,
     * yang hanya bertambah lewat Order::ubahStatus() dan gampang basi jika ada
     * pesanan lama yang belum pernah tersinkron. Sekarang dihitung langsung dari
     * data pesanan yang sudah "selesai" setiap kali dashboard dibuka — tidak perlu
     * sinkronisasi manual apa pun, selalu akurat.
     */
    private function hitungProdukTerlaris(): array
    {
        $produk = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'selesai')
            ->select('order_items.product_name_snapshot as nama', DB::raw('SUM(order_items.quantity) as total'))
            ->groupBy('nama')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return [
            'label' => $produk->pluck('nama')->values()->all(),
            'nilai' => $produk->pluck('total')->map(fn ($v) => (int) $v)->values()->all(),
        ];
    }

    private function hitungAntreanProduksi(Carbon $now): array
    {
        $hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $orders = Order::where('status', '!=', 'dibatalkan')
            ->whereDate('pickup_delivery_date', '>=', $now->toDateString())
            ->with('items')
            ->orderBy('pickup_delivery_date')
            ->get();

        $grup = $orders->groupBy(fn ($o) => optional($o->pickup_delivery_date)->format('Y-m-d'))
            ->filter(fn ($v, $k) => $k !== '' && $k !== null)
            ->take(3);

        $antrean = [];
        foreach ($grup as $tanggal => $ordersHariItu) {
            $items = [];
            foreach ($ordersHariItu as $o) {
                foreach ($o->items as $it) {
                    $items[] = [
                        'nama' => $it->product_name_snapshot . ($it->variant_snapshot ? ' — ' . $it->variant_snapshot : ''),
                        'qty' => (int) $it->quantity,
                    ];
                }
            }
            $tgl = Carbon::parse($tanggal);
            $antrean[] = [
                'tanggal' => $tanggal,
                'hari' => $hariIndo[$tgl->dayOfWeek],
                'pesanan' => $ordersHariItu->count(),
                'items' => $items,
            ];
        }

        return $antrean;
    }

    private function hitungPerluPerhatian(Carbon $now): array
    {
        $menunggu = Order::where('status', 'menunggu_pembayaran')->count();
        $habis = Product::where('stock_status', 'habis')->pluck('name')->values()->all();
        $tanpaResi = Shipping::whereNull('tracking_number')->count();

        $ulasanMingguIni = Review::where('created_at', '>=', $now->copy()->subDays(7))->get();

        return [
            'menunggu' => $menunggu,
            'produkHabis' => $habis,
            'tanpaResi' => $tanpaResi,
            'ulasanBaru' => $ulasanMingguIni->count(),
            'ulasanRata' => $ulasanMingguIni->count() ? round($ulasanMingguIni->avg('rating_overall'), 1) : 0,
        ];
    }
}

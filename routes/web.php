<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    LandingController,
    CatalogController,
    CartController,
    CheckoutController,
    OrderController,
    ReviewController,
    HomeController,
    AuthController,
    ProductController
};
use App\Http\Controllers\Admin\{
    AdminDashboardController,
    AdminProductController,
    AdminOrderController,
    ScheduleController,
    AdminPengirimanController,
    AdminReviewController,
    FinancialTransactionController,
    ReportController,
    UserController,
    AdminSearchController,
    AdminNotificationController
};

use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// ----------------------------------------------------
// PUBLIC & CUSTOMER AREA (BISA DIAKSES GUEST)
// ----------------------------------------------------
Route::get('/', [HomeController::class, 'index'])->name('home');

// Daftarkan DUA nama rute untuk URL yang sama agar tidak error jika ada view yang terlanjur pakai 'catalog' atau 'katalog'
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/katalog-toko', [CatalogController::class, 'index'])->name('katalog');

Route::get('/produk/{slug}', [ProductController::class, 'show'])->name('product.detail');

// Rute Guest (Hanya untuk yang belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

// ----------------------------------------------------
// AUTHENTICATED CUSTOMER AREA (WAJIB LOGIN)
// ----------------------------------------------------
Route::middleware(['auth'])->group(function () {

    // Keranjang Belanja
    Route::prefix('keranjang')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/tambah', [CartController::class, 'store'])->name('store');
        Route::post('/update', [CartController::class, 'update'])->name('update');
        Route::delete('/hapus', [CartController::class, 'destroy'])->name('destroy');
    });

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/proses', [CheckoutController::class, 'proses'])->name('checkout.proses');

    // Pesanan Saya (Order History & Detail)
    Route::get('/pesanan-saya', [OrderController::class, 'index'])->name('order.history');
    Route::get('/pesanan', [OrderController::class, 'index'])->name('order.index');
    Route::get('/pesanan/{kode}', [OrderController::class, 'show'])->name('order.show'); // Menggunakan 'show' jika di controller anda namanya show
    Route::post('/pesanan/{order}/batal', [OrderController::class, 'cancel'])->name('order.cancel');

    // Review / Ulasan
    Route::get('/review/{kode}', [ReviewController::class, 'create'])->name('review.create');
    Route::post('/review/{kode}', [ReviewController::class, 'store'])->name('review.store');
});


// ----------------------------------------------------
// ADMIN & OWNER AREA
// ----------------------------------------------------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,owner'])->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Pencarian global & notifikasi (dipakai di topbar semua halaman admin)
    Route::get('/search', [AdminSearchController::class, 'search'])->name('search');
    Route::get('/notifikasi', [AdminNotificationController::class, 'index'])->name('notifikasi');

    // Manajemen Produk & Pesanan
    Route::resource('produk', AdminProductController::class)->except(['create', 'show', 'edit']);
    Route::patch('/produk/{product}/stok', [AdminProductController::class, 'toggleStock'])->name('produk.stok');

    Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{order}', [AdminOrderController::class, 'show'])->name('pesanan.show');
    Route::put('/pesanan/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('pesanan.status');
    Route::post('/pesanan/{order}/verifikasi-bayar', [AdminOrderController::class, 'approvePayment'])->name('pesanan.approve');

    // Operasional Harian
    Route::get('/jadwal', [ScheduleController::class, 'index'])->name('jadwal.index');
    Route::patch('/jadwal/item/{orderItem}/status', [ScheduleController::class, 'updateItemStatus'])->name('jadwal.item.status');

    // Pengiriman
    Route::get('/pengiriman', [AdminPengirimanController::class, 'index'])->name('pengiriman.index');
    Route::patch('/pengiriman/{id}/resi', [AdminPengirimanController::class, 'updateResi'])->name('pengiriman.resi');
    Route::patch('/pengiriman/{id}/majukan', [AdminPengirimanController::class, 'majukan'])->name('pengiriman.majukan');

    Route::resource('review', AdminReviewController::class)->only(['index', 'update']);

    // Owner Only Area (Laporan, Keuangan & Pengguna)
    // Catatan: dipindahkan ke sini agar konsisten dengan halaman "Pengguna & Role" yang
    // menyatakan Admin tidak boleh melihat modul keuangan — sebelumnya keuangan & laporan
    // masih bisa diakses role admin walau UI-nya sudah menjanjikan sebaliknya.
    Route::middleware(['role:owner'])->group(function () {
        Route::resource('keuangan', FinancialTransactionController::class)->except(['create', 'show', 'edit']);
        Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
        Route::get('/pengguna', [UserController::class, 'index'])->name('pengguna.index');
        Route::get('/pengguna/{user}/detail', [UserController::class, 'detail'])->name('pengguna.detail');
        Route::patch('/pengguna/{user}/role', [UserController::class, 'updateRole'])->name('pengguna.role');
        Route::patch('/pengguna/{user}/status', [UserController::class, 'updateStatus'])->name('pengguna.status');
        Route::patch('/pengguna/{user}/reset-password', [UserController::class, 'resetPassword'])->name('pengguna.reset-password');
    });
});

Route::put('/admin/review/{id}/toggle', [App\Http\Controllers\ReviewController::class, 'toggleStatus']);
Route::put('/admin/review/{id}/reply', [App\Http\Controllers\ReviewController::class, 'reply']);

Route::get('/sync-katalog', function () {
    $products = \App\Models\Product::all();
    $hasStatus = \Illuminate\Support\Facades\Schema::hasColumn('reviews', 'status');

    foreach ($products as $prod) {
        $terjual = \Illuminate\Support\Facades\DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.product_id', $prod->id)
            ->where('orders.status', 'selesai')
            ->sum('order_items.quantity');

        $ulasanQuery = \Illuminate\Support\Facades\DB::table('reviews')
            ->join('order_items', 'reviews.order_item_id', '=', 'order_items.id')
            ->where('order_items.product_id', $prod->id);

        if ($hasStatus) {
            $ulasanQuery->where('reviews.status', 'approved');
        }

        $ulasanCount = $ulasanQuery->count();
        $ulasanAvg = $ulasanCount > 0 ? $ulasanQuery->avg('rating_overall') : 0;

        $prod->update([
            'sold_count' => $terjual,
            'rating_count' => $ulasanCount,
            'rating_avg' => $ulasanAvg
        ]);
    }
    return "<h1>SINKRONISASI BERHASIL!</h1><p>Silakan kembali ke halaman Katalog atau Beranda, angka Terjual dan Ulasan kini sudah akurat.</p>";
});

// Menambal pesanan "Selesai" dari SEBELUM fitur pencatatan otomatis Keuangan
// dipasang, supaya nilainya ikut masuk sebagai pemasukan di Modul Keuangan &
// Laporan Laba Rugi. Aman dijalankan berkali-kali (dicek per order_id lewat
// firstOrCreate, tidak pernah dobel).
Route::get('/sync-keuangan', function () {
    $pesananSelesai = \App\Models\Order::where('status', 'selesai')->get();
    $dibuat = 0;

    foreach ($pesananSelesai as $order) {
        $trx = \App\Models\FinancialTransaction::firstOrCreate(
            ['order_id' => $order->id, 'type' => 'pemasukan'],
            [
                'category' => 'Penjualan Online',
                'description' => 'Penjualan pesanan ' . $order->order_number,
                'amount' => $order->total,
                'transaction_date' => $order->updated_at->toDateString(),
                'created_by' => null,
            ]
        );
        if ($trx->wasRecentlyCreated) {
            $dibuat++;
        }
    }

    return "<h1>SINKRONISASI KEUANGAN BERHASIL!</h1><p>{$dibuat} pesanan lama berstatus Selesai baru saja ditambahkan sebagai pemasukan di Modul Keuangan. Silakan cek halaman Keuangan atau Laporan.</p>";
});

// Mem-bypass Controller: Route langsung untuk Sembunyikan Ulasan
Route::put('/admin/review/{id}/toggle', function($id) {
    $review = DB::table('reviews')->where('id', $id)->first();
    if (!$review) return response()->json(['message' => 'Ulasan tidak ditemukan.'], 404);
    
    $newStatus = $review->is_hidden ? false : true;
    DB::table('reviews')->where('id', $id)->update(['is_hidden' => $newStatus]);
    
    return response()->json(['status' => 'success', 'new_status' => $newStatus ? 'hidden' : 'approved']);
});

// Mem-bypass Controller: Route langsung untuk Balas Ulasan
Route::put('/admin/review/{id}/reply', function(Request $request, $id) {
    $review = DB::table('reviews')->where('id', $id)->first();
    if (!$review) return response()->json(['message' => 'Ulasan tidak ditemukan.'], 404);

    DB::table('reviews')->where('id', $id)->update([
        'admin_reply' => $request->reply,
        'replied_at' => now(),
        'replied_by' => auth()->id() 
    ]);

    return response()->json(['status' => 'success']);
});

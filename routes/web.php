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
    AuthController
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
    UserController
};

// ----------------------------------------------------
// PUBLIC & CUSTOMER AREA
// ----------------------------------------------------
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/produk/{product:slug}', [CatalogController::class, 'show'])->name('product.detail');

// Keranjang (Telah dibersihkan dari deklarasi ganda)
Route::prefix('keranjang')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/tambah', [CartController::class, 'store'])->name('store');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::delete('/hapus', [CartController::class, 'destroy'])->name('destroy');
});

// Checkout (Telah dibersihkan dari deklarasi ganda)
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/proses', [CheckoutController::class, 'proses'])->name('checkout.proses');

// Authenticated Customer Area
Route::middleware(['auth'])->group(function () {
    Route::get('/pesanan-saya', [OrderController::class, 'index'])->name('order.history');
    Route::get('/pesanan', [OrderController::class, 'index'])->name('order.index');
    Route::get('/pesanan/{kode}', [OrderController::class, 'show'])->name('order.show');
    Route::post('/pesanan/{order}/batal', [OrderController::class, 'cancel'])->name('order.cancel');

    Route::get('/review/{kode}', [ReviewController::class, 'create'])->name('review.create');
    Route::post('/review/{kode}', [ReviewController::class, 'store'])->name('review.store');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ----------------------------------------------------
// ADMIN & OWNER AREA
// ----------------------------------------------------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,owner'])->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

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

    // --- INI PERBAIKANNYA (Menghapus prefix "admin." karena sudah diwarisi dari group) ---
    Route::get('/pengiriman', [AdminPengirimanController::class, 'index'])->name('pengiriman.index');

    Route::resource('review', AdminReviewController::class)->only(['index', 'update']);
    Route::resource('keuangan', FinancialTransactionController::class)->except(['create', 'show', 'edit']);
    Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');

    // Owner Only Area (Laporan, Keuangan & Pengguna)
    Route::middleware(['role:owner'])->group(function () {

        Route::get('/pengguna', [UserController::class, 'index'])->name('pengguna.index');
        Route::patch('/pengguna/{user}/role', [UserController::class, 'updateRole'])->name('pengguna.role');
        Route::patch('/pengguna/{user}/status', [UserController::class, 'updateStatus'])->name('pengguna.status');
    });
});
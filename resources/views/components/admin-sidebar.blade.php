@php
    // LOGIKA PERHITUNGAN BADGE OTOMATIS
    // Menghitung total pesanan yang butuh tindakan (Menunggu Pembayaran + Diproses)
    $jmlPesanan = \Illuminate\Support\Facades\DB::table('orders')
        ->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), [
            'menunggu pembayaran', 
            'menunggu_pembayaran', 
            'diproses'
        ])
        ->count();

    // Menghitung pesanan yang sedang dalam tahap pengiriman (Dikemas + Dikirim)
    $jmlPengiriman = \Illuminate\Support\Facades\DB::table('orders')
        ->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), [
            'menunggu pickup', 
            'menunggu_pickup', 
            'diambil kurir', 
            'diambil_kurir', 
            'dalam perjalanan', 
            'dalam_perjalanan',
            'dikemas',
            'dikirim'
        ])
        ->count();
@endphp

<div x-show="sidebar" x-cloak @click="sidebar=false" class="fixed inset-0 z-40 bg-cocoa-700/40 backdrop-blur-sm lg:hidden"></div>
<aside id="admin-sidebar" :class="sidebar ? 'translate-x-0' : '-translate-x-full'"
       class="fixed lg:sticky top-0 z-50 h-screen w-[268px] shrink-0 bg-white border-r border-cream-200 flex flex-col transition-transform duration-300 lg:translate-x-0">
  
  <!-- LOGO ADMIN SIDEBAR -->
  <div class="flex items-center gap-3 px-5 h-[68px] border-b border-cream-200 shrink-0">
    <img src="{{ asset('assets/img/logo_4g.png') }}" alt="Logo 4G" class="h-10 w-auto object-contain shrink-0">
    <div class="leading-tight min-w-0">
      <p class="font-display font-bold text-[14px] text-cocoa-700 truncate">4G Cake &amp; Cookies</p>
      <p class="text-[11px] text-cocoa-300">Panel Admin</p>
    </div>
    <button @click="sidebar=false" class="lg:hidden ml-auto icon-btn tap" aria-label="Tutup menu navigasi"><i data-lucide="x" class="w-5 h-5"></i></button>
  </div>

  <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
    <p class="px-3 pb-2 text-[10px] font-bold tracking-[.12em] text-cocoa-300 uppercase">Menu Utama</p>
    
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-rose-500 text-white shadow-[0_10px_22px_-12px_rgba(197,104,104,.95)]' : 'text-cocoa-500 hover:bg-cream-100 hover:text-cocoa-700' }}">
      <i data-lucide="layout-dashboard" class="w-[18px] h-[18px] shrink-0"></i><span>Dashboard</span>
    </a>
    <a href="{{ route('admin.produk.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.produk.*') ? 'bg-rose-500 text-white shadow-[0_10px_22px_-12px_rgba(197,104,104,.95)]' : 'text-cocoa-500 hover:bg-cream-100 hover:text-cocoa-700' }}">
      <i data-lucide="cake-slice" class="w-[18px] h-[18px] shrink-0"></i><span>Manajemen Produk</span>
    </a>
    <a href="{{ route('admin.pesanan.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.pesanan.*') ? 'bg-rose-500 text-white shadow-[0_10px_22px_-12px_rgba(197,104,104,.95)]' : 'text-cocoa-500 hover:bg-cream-100 hover:text-cocoa-700' }}">
      <i data-lucide="receipt-text" class="w-[18px] h-[18px] shrink-0"></i><span>Manajemen Pesanan</span>
      @if($jmlPesanan > 0)
        <span class="ml-auto badge {{ request()->routeIs('admin.pesanan.*') ? 'bg-white/20 text-white' : 'badge-rose' }} !px-2 !py-0.5">{{ $jmlPesanan }}</span>
      @endif
    </a>
    <a href="{{ route('admin.jadwal.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.jadwal.*') ? 'bg-rose-500 text-white shadow-[0_10px_22px_-12px_rgba(197,104,104,.95)]' : 'text-cocoa-500 hover:bg-cream-100 hover:text-cocoa-700' }}">
      <i data-lucide="calendar-days" class="w-[18px] h-[18px] shrink-0"></i><span>Jadwal Produksi</span>
    </a>
    <a href="{{ route('admin.pengiriman.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.pengiriman.*') ? 'bg-rose-500 text-white shadow-[0_10px_22px_-12px_rgba(197,104,104,.95)]' : 'text-cocoa-500 hover:bg-cream-100 hover:text-cocoa-700' }}">
      <i data-lucide="truck" class="w-[18px] h-[18px] shrink-0"></i><span>Pengiriman</span>
      @if($jmlPengiriman > 0)
        <span class="ml-auto badge {{ request()->routeIs('admin.pengiriman.*') ? 'bg-white/20 text-white' : 'badge-rose' }} !px-2 !py-0.5">{{ $jmlPengiriman }}</span>
      @endif
    </a>
    @if((Auth::user()->role ?? null) === 'owner')
    <a href="{{ route('admin.keuangan.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.keuangan.*') ? 'bg-rose-500 text-white shadow-[0_10px_22px_-12px_rgba(197,104,104,.95)]' : 'text-cocoa-500 hover:bg-cream-100 hover:text-cocoa-700' }}">
      <i data-lucide="wallet" class="w-[18px] h-[18px] shrink-0"></i><span>Keuangan</span>
    </a>
    <a href="{{ route('admin.laporan.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.laporan.*') ? 'bg-rose-500 text-white shadow-[0_10px_22px_-12px_rgba(197,104,104,.95)]' : 'text-cocoa-500 hover:bg-cream-100 hover:text-cocoa-700' }}">
      <i data-lucide="file-bar-chart" class="w-[18px] h-[18px] shrink-0"></i><span>Laporan</span>
    </a>
    @endif
    <a href="{{ route('admin.review.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.review.*') ? 'bg-rose-500 text-white shadow-[0_10px_22px_-12px_rgba(197,104,104,.95)]' : 'text-cocoa-500 hover:bg-cream-100 hover:text-cocoa-700' }}">
      <i data-lucide="star" class="w-[18px] h-[18px] shrink-0"></i><span>Manajemen Review</span>
    </a>
    @if((Auth::user()->role ?? null) === 'owner')
    <a href="{{ route('admin.pengguna.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.pengguna.*') ? 'bg-rose-500 text-white shadow-[0_10px_22px_-12px_rgba(197,104,104,.95)]' : 'text-cocoa-500 hover:bg-cream-100 hover:text-cocoa-700' }}">
      <i data-lucide="users" class="w-[18px] h-[18px] shrink-0"></i><span>Pengguna & Role</span>
    </a>
    @endif
    
    <div class="my-3 h-px bg-cream-200"></div>
    
    <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-cocoa-500 hover:bg-cream-100 transition">
      <i data-lucide="store" class="w-[18px] h-[18px]"></i><span>Lihat Toko</span>
    </a>
    
    <form action="{{ route('logout') }}" method="POST" class="block w-full">
      @csrf
      <button type="submit" class="w-full flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-rose-600 hover:bg-blush-50 transition">
        <i data-lucide="log-out" class="w-[18px] h-[18px]"></i><span>Keluar</span>
      </button>
    </form>
  </nav>

  <div class="p-3 border-t border-cream-200">
    <div class="flex items-center gap-3 rounded-2xl bg-cream-100 p-3">
      <span class="grid place-items-center w-9 h-9 rounded-full bg-gold-400 text-white font-semibold text-xs shrink-0 uppercase">{{ substr(Auth::user()->name ?? 'A', 0, 2) }}</span>
      <div class="min-w-0">
        <p class="text-[13px] font-semibold text-cocoa-700 truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
        <p class="text-[11px] text-cocoa-300 capitalize">{{ Auth::user()->role ?? 'Administrator' }}</p>
      </div>
    </div>
  </div>
</aside>

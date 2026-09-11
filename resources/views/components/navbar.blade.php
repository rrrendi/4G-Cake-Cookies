<header x-data="{ open:false, akun:false }" class="sticky top-0 z-50">
  <!-- BANNER PROMO -->
  <div x-data="{
        showPromo: sessionStorage.getItem('promoBannerClosed') !== 'true',

        tutupPromo() {
            this.showPromo = false;
            sessionStorage.setItem('promoBannerClosed', 'true');
        }
    }" x-show="showPromo" x-cloak x-transition class="bg-cocoa-500 text-cream-100 text-xs">
    <div class="relative mx-auto max-w-7xl px-10 sm:px-12 lg:px-14 py-2 flex items-center justify-center text-center">

      <div class="flex items-center justify-center gap-2">
        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-gold-400"></i>

        <span>
          Pre-order H-1 sampai H-5 tergantung produk
          <span class="hidden sm:inline">
            &middot; Gratis kartu ucapan untuk pembelian di atas Rp250.000
          </span>
        </span>
      </div>

      <button type="button" @click="tutupPromo()" class="absolute right-3 sm:right-5 top-1/2 -translate-y-1/2
                   inline-flex items-center justify-center
                   w-6 h-6 rounded-full
                   text-cream-100/80 hover:text-white hover:bg-white/10
                   transition" aria-label="Tutup informasi">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>

    </div>
  </div>

  <nav class="bg-cream-50/90 backdrop-blur border-b border-cream-200">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-[68px] items-center justify-between gap-4">

        <!-- LOGO -->
        <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0 group">
          <span
            class="grid place-items-center w-10 h-10 rounded-2xl bg-rose-500 text-white font-display font-bold text-sm shadow-[0_8px_18px_-8px_rgba(197,104,104,.9)] group-hover:scale-105 transition">4G</span>
          <span class="leading-tight">
            <span class="block font-display font-bold text-[15px] text-cocoa-700">4G Cake &amp; Cookies</span>
            <span class="block text-[11px] text-cocoa-300 tracking-wide">Homemade sejak 2019</span>
          </span>
        </a>

        <!-- MENU DESKTOP -->
        <div class="hidden md:flex items-center gap-8">
          <a href="{{ route('home') }}"
            class="link-underline text-sm font-medium text-cocoa-500 hover:text-rose-600 transition">Beranda</a>
          <a href="{{ route('catalog') }}"
            class="link-underline text-sm font-medium text-cocoa-500 hover:text-rose-600 transition">Katalog</a>
          <a href="{{ route('order.history') }}"
            class="link-underline text-sm font-medium text-cocoa-500 hover:text-rose-600 transition">Pesanan Saya</a>
          <a href="{{ route('home') }}#tentang"
            class="link-underline text-sm font-medium text-cocoa-500 hover:text-rose-600 transition">Tentang</a>
        </div>

        <!-- TOMBOL KANAN -->
        <div class="flex items-center gap-2">

          <!-- ICON KERANJANG -->
          <a href="{{ route('cart.index') }}"
            class="relative grid place-items-center w-11 h-11 rounded-full hover:bg-cream-100 transition tap"
            aria-label="Buka keranjang belanja">
            <i data-lucide="shopping-bag" class="w-5 h-5 text-cocoa-500"></i>
            <span data-cart-badge
              class="hidden absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 grid place-items-center rounded-full bg-rose-500 text-white text-[10px] font-bold">0</span>
          </a>

          <!-- DROPDOWN AKUN DESKTOP -->
          <div class="relative hidden md:block" @click.outside="akun=false" @keydown.escape.window="akun=false">
            <button @click="akun=!akun" class="btn btn-outline btn-sm" :aria-expanded="akun" aria-haspopup="true">
              <i data-lucide="user-round" class="w-4 h-4"></i>
              <!-- Tampilkan nama depan jika login, jika tidak tampilkan 'Akun' -->
              @auth
                {{ explode(' ', Auth::user()->name)[0] }}
              @else
                Akun
              @endauth
              <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
            </button>

            <div x-show="akun" x-cloak x-transition.opacity.duration.150ms
              class="absolute right-0 mt-2 w-52 card p-1.5">
              @guest
                <a href="{{ route('login') }}"
                  class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm text-cocoa-500 hover:bg-cream-100 transition"><i
                    data-lucide="log-in" class="w-4 h-4"></i> Masuk</a>
                <a href="{{ route('register') }}"
                  class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm text-cocoa-500 hover:bg-cream-100 transition"><i
                    data-lucide="user-plus" class="w-4 h-4"></i> Daftar</a>
              @endguest

              @auth
                <div class="px-3 py-2 text-xs text-cocoa-400 border-b border-cream-200 mb-1 truncate">
                  Hai, {{ Auth::user()->name }}
                </div>
                <a href="{{ route('order.history') }}"
                  class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm text-cocoa-500 hover:bg-cream-100 transition"><i
                    data-lucide="receipt-text" class="w-4 h-4"></i> Riwayat Pesanan</a>

                {{-- Tombol Rahasia khusus Admin & Owner --}}
                @if(in_array(Auth::user()->role, ['admin', 'owner']))
                  <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm text-cocoa-600 font-medium hover:bg-cream-100 transition"><i
                      data-lucide="layout-dashboard" class="w-4 h-4 text-rose-500"></i> Panel Admin</a>
                @endif

                <div class="my-1 h-px bg-cream-200"></div>
                <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button type="submit"
                    class="w-full flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm text-rose-600 hover:bg-blush-50 transition"><i
                      data-lucide="log-out" class="w-4 h-4"></i> Keluar</button>
                </form>
              @endauth
            </div>
          </div>

          <a href="{{ route('catalog') }}" class="hidden lg:inline-flex btn btn-primary btn-sm">
            <i data-lucide="cake" class="w-4 h-4"></i> Pesan Sekarang
          </a>

          <!-- TOMBOL HAMBURGER MOBILE -->
          <button @click="open=!open"
            class="md:hidden grid place-items-center w-11 h-11 rounded-full hover:bg-cream-100 transition tap"
            :aria-expanded="open" aria-controls="menu-mobile" aria-label="Buka menu navigasi">
            <i data-lucide="menu" x-show="!open" class="w-5 h-5 text-cocoa-500"></i>
            <i data-lucide="x" x-show="open" x-cloak class="w-5 h-5 text-cocoa-500"></i>
          </button>
        </div>
      </div>

      <!-- DROPDOWN MENU MOBILE -->
      <div id="menu-mobile" x-show="open" x-cloak x-transition class="md:hidden pb-4 space-y-1">
        <a href="{{ route('home') }}"
          class="flex items-center justify-between rounded-xl px-4 py-3 text-cocoa-500 hover:bg-cream-100 hover:text-rose-600 transition">
          <span class="font-medium">Beranda</span><i data-lucide="chevron-right" class="w-4 h-4 text-cocoa-300"></i>
        </a>
        <a href="{{ route('catalog') }}"
          class="flex items-center justify-between rounded-xl px-4 py-3 text-cocoa-500 hover:bg-cream-100 hover:text-rose-600 transition">
          <span class="font-medium">Katalog</span><i data-lucide="chevron-right" class="w-4 h-4 text-cocoa-300"></i>
        </a>
        <a href="{{ route('order.history') }}"
          class="flex items-center justify-between rounded-xl px-4 py-3 text-cocoa-500 hover:bg-cream-100 hover:text-rose-600 transition">
          <span class="font-medium">Pesanan Saya</span><i data-lucide="chevron-right"
            class="w-4 h-4 text-cocoa-300"></i>
        </a>
        <a href="{{ route('home') }}#tentang"
          class="flex items-center justify-between rounded-xl px-4 py-3 text-cocoa-500 hover:bg-cream-100 hover:text-rose-600 transition">
          <span class="font-medium">Tentang</span><i data-lucide="chevron-right" class="w-4 h-4 text-cocoa-300"></i>
        </a>

        <div class="pt-4 border-t border-cream-200 mt-2 grid grid-cols-2 gap-2">
          @guest
            <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Masuk</a>
            <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
          @endguest
          @auth
            <form action="{{ route('logout') }}" method="POST" class="col-span-2">
              @csrf
              <button type="submit" class="btn btn-outline btn-sm w-full">Keluar</button>
            </form>
          @endauth
        </div>

        {{-- Link Admin di Mobile --}}
        @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'owner']))
          <a href="{{ route('admin.dashboard') }}"
            class="block text-center text-sm font-medium text-rose-600 mt-4 bg-blush-50 py-2 rounded-xl">Masuk ke Panel
            Admin</a>
        @endif
      </div>
    </div>
  </nav>
</header>
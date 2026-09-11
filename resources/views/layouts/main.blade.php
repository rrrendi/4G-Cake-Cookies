<!DOCTYPE html>
<html lang="id" class="scroll-pt-24">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="4G Cake & Cookies — cake, cookies, brownies, dan dessert box homemade di Lhokseumawe. Pesan online, pilih tanggal ambil atau kirim.">
  <title>@yield('title', 'Kue rumahan Lhokseumawe &middot; 4G Cake & Cookies')</title>
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='9' fill='%23D97E7E'/%3E%3Ctext x='16' y='22' font-family='Verdana' font-size='14' font-weight='bold' text-anchor='middle' fill='white'%3E4G%3C/text%3E%3C/svg%3E">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
  
  <style>
    [x-cloak] {
      display: none !important;
    }
  </style>
  
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: {
          display: ['Quicksand', 'Inter', 'sans-serif'],
          sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif']
        },
        colors: {
          cream: { 50:'#FFFCF8', 100:'#FDF6EE', 200:'#F7EADB', 300:'#EFDCC5' },
          blush: { 50:'#FDF1F1', 100:'#F9E2E2', 200:'#F2CCCC', 300:'#E9AFAF' },
          rose:  { 300:'#EDB4B4', 400:'#E39898', 500:'#D97E7E', 600:'#C56868', 700:'#A64F4F' },
          cocoa: { 200:'#D8CABF', 300:'#B49B8C', 400:'#8B6B58', 500:'#6B4C3C', 600:'#523A2E', 700:'#3C2A21' },
          gold:  { 300:'#E4CC7E', 400:'#D9B44A', 500:'#C9A227', 600:'#A8871F' }
        },
        borderRadius: { '2xl':'1.1rem', '3xl':'1.6rem' }
      }
    }
  }
  </script>
  <script src="https://unpkg.com/lucide@0.462.0/dist/umd/lucide.min.js"></script>
  @stack('styles')
</head>

<body data-base="{{ asset('/') }}" class="min-h-screen bg-cream-50 text-cocoa-700 antialiased">

<!-- NAVBAR START -->
@include('components.navbar')
<!-- NAVBAR END -->

<main id="konten">
    @yield('content')
</main>

<!-- FOOTER START -->
@include('components.footer')
<!-- FOOTER END -->

<!-- TOAST START -->
<div id="toast-root" x-data role="region" aria-live="polite" aria-label="Notifikasi sistem">
  <template x-for="t in $store.toast.items" :key="t.id">
    <div class="toast" :class="'toast-' + t.tipe" x-transition.opacity.duration.200ms role="status">
      <span class="shrink-0 mt-[1px]" x-html="ikonToast(t.tipe)"></span>
      <div class="flex-1 min-w-0">
        <p class="font-semibold text-sm text-cocoa-700 mb-[2px]" x-show="t.judul" x-text="t.judul"></p>
        <p class="text-sm text-cocoa-500 leading-snug" x-html="t.pesan"></p>
      </div>
      <button type="button" @click="$store.toast.tutup(t.id)"
              class="shrink-0 text-cocoa-300 hover:text-cocoa-600 transition" aria-label="Tutup notifikasi">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
      </button>
    </div>
  </template>
</div>
<!-- TOAST END -->

<!-- MODAL KONFIRMASI GLOBAL START -->
<div x-data x-show="$store.konfirmasi.buka" x-cloak class="modal-root grid place-items-center p-4"
     role="dialog" aria-modal="true" aria-labelledby="konfirmasi-judul"
     @keydown.escape.window="$store.konfirmasi.batal()">
  <div class="modal-overlay" @click="$store.konfirmasi.batal()"></div>
  <div x-show="$store.konfirmasi.buka" x-transition class="modal-panel card max-w-sm p-7 text-center">
    <span class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-[#FDECEA] text-rose-700 mb-5">
      <i :data-lucide="$store.konfirmasi.ikon" class="w-6 h-6"></i>
    </span>
    <h2 id="konfirmasi-judul" class="font-display font-bold text-lg text-cocoa-700 mb-2" x-text="$store.konfirmasi.judul"></h2>
    <p class="text-sm text-cocoa-400 mb-6" x-html="$store.konfirmasi.pesan"></p>
    <div class="flex gap-3">
      <button type="button" @click="$store.konfirmasi.batal()" class="btn btn-outline flex-1">Batal</button>
      <button type="button" @click="$store.konfirmasi.ya()" class="btn btn-primary flex-1" x-text="$store.konfirmasi.label"></button>
    </div>
  </div>
</div>
<!-- MODAL KONFIRMASI GLOBAL END -->

<!-- SCRIPTS -->
<script src="{{ asset('assets/js/data.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>

<!-- === SINKRONISASI KERANJANG GLOBAL LARAVEL === -->
<script>
    // 1. Ambil total item langsung dari memori Session Server
    window.LARAVEL_CART_COUNT = {{ collect(session('cart', []))->sum('qty') }};

    // 2. TIMPA fungsi bawaan app.js agar tidak membaca localStorage di semua halaman
    window.badgeKeranjang = function() {
        document.querySelectorAll('[data-cart-badge]').forEach(el => {
            el.textContent = window.LARAVEL_CART_COUNT;
            el.classList.toggle('hidden', window.LARAVEL_CART_COUNT === 0);
        });
    };
</script>

<script defer src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js"></script>

<!-- PENANGKAP NOTIFIKASI BACKEND LARAVEL -->
@if(session('success_toast'))
<script>document.addEventListener('DOMContentLoaded', () => setTimeout(() => toast("{{ session('success_toast') }}", 'success', 'Berhasil'), 300));</script>
@endif

@if(session('error_toast'))
<script>document.addEventListener('DOMContentLoaded', () => setTimeout(() => toast("{{ session('error_toast') }}", 'error', 'Gagal'), 300));</script>
@endif

@if(session('info_toast'))
<script>document.addEventListener('DOMContentLoaded', () => setTimeout(() => toast("{{ session('info_toast') }}", 'info'), 300));</script>
@endif

@stack('scripts')
</body>
</html>
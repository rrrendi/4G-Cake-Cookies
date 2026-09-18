<!DOCTYPE html>
<html lang="id" class="scroll-pt-24">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel Admin 4G Cake & Cookies">
    <title>@yield('title', 'Dashboard Admin · 4G Cake & Cookies')</title>
    <!-- Favicon sudah diganti ke gambar asli -->
    <link rel="icon" href="{{ asset('assets/img/logo_4g.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { display: ['Quicksand', 'Inter', 'sans-serif'], sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        cream: { 50: '#FFFCF8', 100: '#FDF6EE', 200: '#F7EADB', 300: '#EFDCC5' },
                        blush: { 50: '#FDF1F1', 100: '#F9E2E2', 200: '#F2CCCC', 300: '#E9AFAF' },
                        rose: { 300: '#EDB4B4', 400: '#E39898', 500: '#D97E7E', 600: '#C56868', 700: '#A64F4F' },
                        cocoa: { 200: '#D8CABF', 300: '#B49B8C', 400: '#8B6B58', 500: '#6B4C3C', 600: '#523A2E', 700: '#3C2A21' },
                        gold: { 300: '#E4CC7E', 400: '#D9B44A', 500: '#C9A227', 600: '#A8871F' }
                    },
                    borderRadius: { '2xl': '1.1rem', '3xl': '1.6rem' }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@0.462.0/dist/umd/lucide.min.js"></script>
    @stack('styles')
</head>

<body data-base="{{ asset('/') }}" class="min-h-screen bg-cream-50 text-cocoa-700 antialiased">
    <a href="#konten" class="skip-link">Lompat ke konten utama</a>

    <div x-data="{ sidebar:false }" class="min-h-screen lg:flex">

        @include('components.admin-sidebar')

        <div class="flex-1 min-w-0 flex flex-col">
            <!-- ADMIN TOPBAR START -->
            <header id="admin-topbar" class="sticky top-0 z-30 bg-cream-50/90 backdrop-blur border-b border-cream-200">
                <div class="flex h-[68px] items-center gap-3 px-4 sm:px-6">
                    <button @click="sidebar=true"
                        class="lg:hidden grid place-items-center w-11 h-11 rounded-full hover:bg-cream-100 tap"
                        aria-label="Buka menu navigasi admin">
                        <i data-lucide="menu" class="w-5 h-5 text-cocoa-500"></i>
                    </button>
                    <div class="min-w-0">
                        <h1 class="font-display font-bold text-[17px] sm:text-xl text-cocoa-700 truncate">
                            @yield('header_title', 'Panel Admin')</h1>
                        <p class="hidden sm:block text-xs text-cocoa-300 truncate">
                            @yield('header_subtitle', 'Sistem Manajemen 4G Cake & Cookies')</p>
                    </div>
                    <div class="ml-auto flex items-center gap-2">
                        <div x-data="pencarianGlobalAdmin()" class="relative hidden xl:block" @keydown.escape.window="buka=false" @click.outside="buka=false">
                            <div class="flex items-center gap-2 rounded-full bg-white border border-cream-200 px-3.5 py-2 w-64 focus-within:border-rose-300">
                                <i data-lucide="search" class="w-4 h-4 text-cocoa-300 shrink-0"></i>
                                <input type="search" aria-label="Pencarian global panel admin" x-model="q" @input.debounce.350ms="cari()"
                                    @focus="if (hasil) buka = true"
                                    placeholder="Cari apa saja&hellip;"
                                    class="bg-transparent text-sm outline-none w-full placeholder:text-cocoa-300">
                            </div>
                            <div x-show="buka" x-cloak x-transition
                                class="absolute right-0 mt-2 w-80 max-h-96 overflow-y-auto rounded-2xl bg-white border border-cream-200 shadow-xl p-2 z-40">
                                <template x-if="mencari"><p class="text-xs text-cocoa-300 text-center py-4">Mencari&hellip;</p></template>
                                <template x-if="!mencari && hasil && totalHasil === 0"><p class="text-xs text-cocoa-300 text-center py-4">Tidak ada hasil untuk "<span x-text="q"></span>".</p></template>
                                <template x-if="!mencari && hasil">
                                    <div class="space-y-3">
                                        <template x-for="grup in [{k:'produk',label:'Produk',ikon:'cake'},{k:'pesanan',label:'Pesanan',ikon:'receipt-text'},{k:'pengguna',label:'Pengguna',ikon:'users'}]" :key="grup.k">
                                            <div x-show="hasil[grup.k] && hasil[grup.k].length">
                                                <p class="text-[10px] font-semibold text-cocoa-300 uppercase tracking-wide px-2 mb-1" x-text="grup.label"></p>
                                                <template x-for="(item, i) in hasil[grup.k]" :key="grup.k + i">
                                                    <a :href="item.link" class="flex items-center gap-2.5 rounded-xl px-2 py-2 hover:bg-cream-100 transition">
                                                        <i :data-lucide="grup.ikon" class="w-4 h-4 text-cocoa-300 shrink-0"></i>
                                                        <span class="text-sm text-cocoa-600 truncate" x-text="item.label"></span>
                                                    </a>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div x-data="notifikasiAdmin()" x-init="init()" class="relative" @keydown.escape.window="buka=false" @click.outside="buka=false">
                            <button @click="buka = !buka; if (buka) muat()"
                                class="relative grid place-items-center w-11 h-11 rounded-full bg-white border border-cream-200 hover:bg-cream-100 transition tap"
                                aria-label="Notifikasi admin">
                                <i data-lucide="bell" class="w-[18px] h-[18px] text-cocoa-500"></i>
                                <span x-show="total > 0" class="absolute top-1.5 right-2 w-2 h-2 rounded-full bg-rose-500"></span>
                            </button>
                            <div x-show="buka" x-cloak x-transition class="absolute right-0 mt-2 w-80 rounded-2xl bg-white border border-cream-200 shadow-xl p-2 z-40">
                                <p class="text-[10px] font-semibold text-cocoa-300 uppercase tracking-wide px-2 py-1.5">Notifikasi</p>
                                <template x-if="memuat"><p class="text-xs text-cocoa-300 text-center py-6">Memuat&hellip;</p></template>
                                <template x-if="!memuat && items.length === 0"><p class="text-xs text-cocoa-300 text-center py-6">Tidak ada yang perlu diperhatikan saat ini.</p></template>
                                <template x-for="(n, i) in items" :key="i">
                                    <a :href="n.link" class="flex items-start gap-2.5 rounded-xl px-2 py-2.5 hover:bg-cream-100 transition">
                                        <span class="grid place-items-center w-8 h-8 rounded-lg shrink-0" :class="n.tipe === 'warning' ? 'bg-[#FEF4E2] text-[#96650B]' : 'bg-cream-100 text-cocoa-500'">
                                            <i :data-lucide="n.ikon" class="w-4 h-4"></i>
                                        </span>
                                        <span class="text-sm text-cocoa-600 leading-snug" x-text="n.pesan"></span>
                                    </a>
                                </template>
                            </div>
                        </div>
                        <span
                            class="hidden sm:grid place-items-center w-10 h-10 rounded-full bg-gold-400 text-white font-semibold text-xs uppercase">{{ substr(Auth::user()->name ?? 'A', 0, 2) }}</span>
                    </div>
                </div>
            </header>
            <!-- ADMIN TOPBAR END -->

            <main id="konten" class="flex-1 w-full max-w-[1600px] mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
                @yield('content')
            </main>

            <footer id="admin-footer" class="px-4 sm:px-6 lg:px-8 py-6 text-xs text-cocoa-300 border-t border-cream-200 mt-auto flex items-center gap-3">
                <img src="{{ asset('assets/img/logo_4g.png') }}" alt="Logo" class="h-6 w-auto opacity-50 grayscale">
                <span>&copy; {{ date('Y') }} 4G Cake &amp; Cookies &middot; Panel admin prototype</span>
            </footer>
        </div>
    </div>

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
                    class="shrink-0 text-cocoa-300 hover:text-cocoa-600 transition">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round">
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    </div>

    <!-- MODAL KONFIRMASI GLOBAL START -->
    <div x-data x-show="$store.konfirmasi.buka" x-cloak class="modal-root grid place-items-center p-4"
        @keydown.escape.window="$store.konfirmasi.batal()">
        <div class="modal-overlay" @click="$store.konfirmasi.batal()"></div>
        <div x-show="$store.konfirmasi.buka" x-transition class="modal-panel card max-w-sm p-7 text-center">
            <span class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-[#FDECEA] text-rose-700 mb-5">
                <i :data-lucide="$store.konfirmasi.ikon" class="w-6 h-6"></i>
            </span>
            <h2 id="konfirmasi-judul" class="font-display font-bold text-lg text-cocoa-700 mb-2"
                x-text="$store.konfirmasi.judul"></h2>
            <p class="text-sm text-cocoa-400 mb-6" x-html="$store.konfirmasi.pesan"></p>
            <div class="flex gap-3">
                <button type="button" @click="$store.konfirmasi.batal()" class="btn btn-outline flex-1">Batal</button>
                <button type="button" @click="$store.konfirmasi.ya()" class="btn btn-primary flex-1"
                    x-text="$store.konfirmasi.label"></button>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/data.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script defer src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <script>
      function pencarianGlobalAdmin() {
        return {
          q: '', buka: false, mencari: false, hasil: null,
          get totalHasil() {
            if (!this.hasil) return 0;
            return (this.hasil.produk?.length || 0) + (this.hasil.pesanan?.length || 0) + (this.hasil.pengguna?.length || 0);
          },
          cari() {
            const q = this.q.trim();
            if (q.length < 2) { this.hasil = null; this.buka = false; return; }
            this.mencari = true; this.buka = true;
            fetch(`{{ url('admin/search') }}?q=${encodeURIComponent(q)}`, { headers: { 'Accept': 'application/json' } })
              .then(res => res.json())
              .then(data => { this.hasil = data; })
              .catch(() => { this.hasil = { produk: [], pesanan: [], pengguna: [] }; })
              .finally(() => { this.mencari = false; this.$nextTick(() => icons()); });
          }
        };
      }

      function notifikasiAdmin() {
        return {
          buka: false, memuat: false, items: [], total: 0, sudahMuat: false,
          init() { this.muat(); },
          muat() {
            if (this.memuat) return;
            this.memuat = true;
            fetch(`{{ url('admin/notifikasi') }}`, { headers: { 'Accept': 'application/json' } })
              .then(res => res.json())
              .then(data => { this.items = data.items || []; this.total = data.total || 0; })
              .catch(() => { this.items = []; this.total = 0; })
              .finally(() => { this.memuat = false; this.sudahMuat = true; this.$nextTick(() => icons()); });
          }
        };
      }
    </script>

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

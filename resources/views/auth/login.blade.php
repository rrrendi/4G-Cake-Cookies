<!DOCTYPE html>
<html lang="id" class="scroll-pt-24">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Masuk ke akun pelanggan 4G Cake & Cookies.">
  <title>Masuk &middot; 4G Cake &amp; Cookies</title>
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='9' fill='%23D97E7E'/%3E%3Ctext x='16' y='22' font-family='Verdana' font-size='14' font-weight='bold' text-anchor='middle' fill='white'%3E4G%3C/text%3E%3C/svg%3E">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
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
</head>
<body data-base="{{ asset('/') }}" class="min-h-screen bg-cream-50 text-cocoa-700 antialiased">
<a href="#konten" class="skip-link">Lompat ke konten utama</a>

<main id="konten">

<div class="min-h-screen lg:grid lg:grid-cols-2">

  <div class="hidden lg:flex flex-col justify-between bg-cocoa-700 text-cream-100 p-12 xl:p-16 relative overflow-hidden">
    <div class="absolute -top-20 -left-20 w-80 h-80 rounded-full bg-rose-500/20 blur-3xl"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 rounded-full bg-gold-500/10 blur-3xl"></div>
    <a href="{{ route('home') }}" class="relative flex items-center gap-3 w-fit">
      <span class="grid place-items-center w-10 h-10 rounded-2xl bg-rose-500 text-white font-display font-bold text-sm">4G</span>
      <span class="font-display font-bold text-white">4G Cake &amp; Cookies</span>
    </a>
    <div class="relative max-w-md">
      <img src="{{ asset('assets/img/hero/hero-cake.svg') }}" alt="" class="w-56 mb-8 opacity-95">
      <h2 class="font-display font-bold text-3xl leading-tight text-white mb-4">Pesanan Anda, tercatat rapi.</h2>
      <p class="text-cream-200/70 leading-relaxed">Tidak perlu lagi menggulir chat WhatsApp untuk mencari pesanan mana yang sudah dibayar. Semua ada di satu halaman.</p>
      <div class="mt-8 flex items-center gap-4">
        <div class="flex -space-x-2">
          <span class="grid place-items-center w-9 h-9 rounded-full bg-rose-500 border-2 border-cocoa-700 text-white text-[11px] font-semibold">NS</span>
          <span class="grid place-items-center w-9 h-9 rounded-full bg-gold-500 border-2 border-cocoa-700 text-white text-[11px] font-semibold">RA</span>
          <span class="grid place-items-center w-9 h-9 rounded-full bg-cocoa-400 border-2 border-cocoa-700 text-white text-[11px] font-semibold">PM</span>
        </div>
        <p class="text-sm text-cream-200/60">Bergabung dengan 620+ pelanggan tetap kami</p>
      </div>
    </div>
    <p class="relative text-xs text-cream-200/40">&copy; 2026 4G Cake &amp; Cookies &middot; Lhokseumawe, Aceh</p>
  </div>

  <div class="flex flex-col justify-center px-5 sm:px-10 lg:px-16 py-10 lg:py-16">
    <a href="{{ route('home') }}" class="lg:hidden flex items-center gap-3 mb-10">
      <span class="grid place-items-center w-10 h-10 rounded-2xl bg-rose-500 text-white font-display font-bold text-sm">4G</span>
      <span class="font-display font-bold text-cocoa-700">4G Cake &amp; Cookies</span>
    </a>

    <div class="w-full max-w-md mx-auto">
      <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs text-cocoa-300 hover:text-rose-600 transition mb-6">
        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke beranda
      </a>
      <h1 class="font-display font-bold text-3xl text-cocoa-700 mb-2">Selamat datang kembali</h1>
      <p class="text-cocoa-400 text-sm mb-8">Masuk untuk melihat riwayat pesanan dan melacak status kue Anda.</p>

      <form @submit.prevent="masuk($event)" class="space-y-4"
            x-data="{ email: '', pass: '', ingat: true, lihat: false,
                      masuk(ev) {
                        const btn = ev.target.querySelector('button[type=submit]');
                        const selesai = tombolMuat(btn, 'Memeriksa&hellip;');
                        
                        fetch('{{ route('login.post') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                            body: JSON.stringify({ email: this.email, password: this.pass, remember: this.ingat })
                        })
                        .then(async res => {
                            const data = await res.json();
                            selesai();
                            if(res.ok && data.status === 'success') {
                                localStorage.setItem('4g_login_bypass', 'true');
                                toast(data.message, 'success', 'Berhasil Masuk');
                                setTimeout(() => location.href = data.redirect, 1000);
                            } else {
                                let msg = data.message || 'Kredensial tidak cocok.';
                                if(data.errors) msg = Object.values(data.errors)[0][0];
                                toast(msg, 'error', 'Gagal Masuk');
                            }
                        }).catch(() => { selesai(); toast('Gangguan jaringan.', 'error'); });
                      }
                    }">
        <div>
          <label class="label" for="l-email">Email</label>
          <div class="relative">
            <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-cocoa-300 pointer-events-none"></i>
            <input id="l-email" x-model="email" type="email" required class="input !pl-11" placeholder="nama@email.com">
          </div>
        </div>
        <div>
          <label class="label" for="l-pass">Kata sandi</label>
          <div class="relative">
            <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-cocoa-300 pointer-events-none"></i>
            <input id="l-pass" x-model="pass" :type="lihat ? 'text' : 'password'" required class="input !pl-11 !pr-11" placeholder="Minimal 8 karakter">
            <button type="button" @click="lihat=!lihat" class="absolute right-2 top-1/2 -translate-y-1/2 icon-btn !w-9 !h-9 tap" :aria-label="lihat ? 'Sembunyikan sandi' : 'Tampilkan sandi'">
              <i data-lucide="eye" x-show="!lihat" class="w-[18px] h-[18px]"></i>
              <i data-lucide="eye-off" x-show="lihat" x-cloak class="w-[18px] h-[18px]"></i>
            </button>
          </div>
        </div>
        <div class="flex items-center justify-between gap-4 pt-1">
          <label class="flex items-center gap-2 text-sm text-cocoa-400 cursor-pointer">
            <input type="checkbox" x-model="ingat" class="w-4 h-4 rounded border-cream-300 text-rose-500 focus:ring-rose-400"> Ingat saya
          </label>
          <button type="button" onclick="toast('Fitur lupa sandi menyusul.','info')" class="text-sm text-rose-600 hover:underline py-2 px-1">Lupa sandi?</button>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-lg !mt-6">Masuk</button>
      </form>

      <div class="flex items-center gap-4 my-7">
        <span class="flex-1 h-px bg-cream-200"></span>
        <span class="text-xs text-cocoa-300">atau</span>
        <span class="flex-1 h-px bg-cream-200"></span>
      </div>

      <button type="button" onclick="toast('Login Google baru tersedia setelah konfigurasi OAuth selesai.','info')" class="btn btn-outline btn-block">
        <i data-lucide="chrome" class="w-[18px] h-[18px]"></i> Masuk dengan Google
      </button>

      <p class="text-center text-sm text-cocoa-400 mt-8">
        Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-rose-600 hover:underline">Daftar sekarang</a>
      </p>
    </div>
  </div>
</div>

</main>

<!-- TOAST START -->
<div id="toast-root" x-data role="region" aria-live="polite" aria-label="Notifikasi sistem">
  <template x-for="t in $store.toast.items" :key="t.id">
    <div class="toast" :class="'toast-' + t.tipe" x-transition.opacity.duration.200ms role="status">
      <span class="shrink-0 mt-[1px]" x-html="ikonToast(t.tipe)"></span>
      <div class="flex-1 min-w-0">
        <p class="font-semibold text-sm text-cocoa-700 mb-[2px]" x-show="t.judul" x-text="t.judul"></p>
        <p class="text-sm text-cocoa-500 leading-snug" x-html="t.pesan"></p>
      </div>
      <button type="button" @click="$store.toast.tutup(t.id)" class="shrink-0 text-cocoa-300 hover:text-cocoa-600 transition" aria-label="Tutup notifikasi">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
      </button>
    </div>
  </template>
</div>
<!-- TOAST END -->

<script src="{{ asset('assets/js/app.js') }}"></script>
<script defer src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js"></script>
<script>document.addEventListener('DOMContentLoaded', () => icons());</script>
</body>
</html>
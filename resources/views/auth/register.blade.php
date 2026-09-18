<!DOCTYPE html>
<html lang="id" class="scroll-pt-24">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Buat akun pelanggan 4G Cake & Cookies.">
  <title>Daftar &middot; 4G Cake &amp; Cookies</title>
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
      <h2 class="font-display font-bold text-3xl leading-tight text-white mb-4">Satu akun untuk semua pesanan.</h2>
      <p class="text-cream-200/70 leading-relaxed">Riwayat pesanan, status produksi, nomor resi, sampai kolom ulasan — semuanya tersimpan di akun ini.</p>
      <div class="mt-8 flex items-center gap-4">
        <div class="flex -space-x-2">
          <span class="grid place-items-center w-9 h-9 rounded-full bg-rose-500 border-2 border-cocoa-700 text-white text-[11px] font-semibold">NS</span>
          <span class="grid place-items-center w-9 h-9 rounded-full bg-gold-500 border-2 border-cocoa-700 text-white text-[11px] font-semibold">RA</span>
          <span class="grid place-items-center w-9 h-9 rounded-full bg-cocoa-400 border-2 border-cocoa-700 text-white text-[11px] font-semibold">PM</span>
        </div>
        <p class="text-sm text-cream-200/60">Bergabung dengan 620+ pelanggan tetap kami</p>
      </div>
    </div>
    <p class="relative text-xs text-cream-200/40">&copy; 2026 4G Cake &amp; Cookies &middot; Bandung, Jawa Barat</p>
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
      <h1 class="font-display font-bold text-3xl text-cocoa-700 mb-2">Buat akun baru</h1>
      <p class="text-cocoa-400 text-sm mb-8">Cukup sekali daftar, seterusnya pesan tinggal beberapa klik.</p>

      <form class="space-y-4" @submit.prevent="daftar($event)"
            x-data="{ nama: '', email: '', hp: '', pass: '', pass2: '', tos: false,
                      get kuat() { return this.pass.length >= 12 ? 3 : this.pass.length >= 8 ? 2 : this.pass.length > 0 ? 1 : 0 },
                      get cocok() { return this.pass.length > 0 && this.pass === this.pass2 },
                      daftar(ev) {
                        if (!this.tos) { toast('Setujui ketentuan terlebih dahulu.', 'warning'); return; }
                        if (!this.cocok) { toast('Konfirmasi kata sandi belum sama.', 'error'); return; }
                        
                        const btn = ev.target.querySelector('button[type=submit]');
                        const selesai = tombolMuat(btn, 'Mendaftarkan&hellip;');
                        
                        fetch('{{ route('register.post') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                            // Perhatikan kita mengirim data tambahan: hp
                            body: JSON.stringify({ nama: this.nama, email: this.email, hp: this.hp, password: this.pass, password_confirmation: this.pass2 })
                        })
                        .then(async res => {
                            const data = await res.json();
                            selesai();
                            if(res.ok && data.status === 'success') {
                                localStorage.setItem('4g_login_bypass', 'true');
                                toast(data.message, 'success', 'Pendaftaran Berhasil');
                                setTimeout(() => location.href = '{{ route('order.history') }}', 1000);
                            } else {
                                let msg = data.message || 'Gagal memproses data.';
                                if(data.errors) msg = Object.values(data.errors)[0][0];
                                toast(msg, 'error', 'Pendaftaran Gagal');
                            }
                        }).catch(() => { selesai(); toast('Gangguan jaringan.', 'error'); });
                      } }">
        <div>
          <label class="label" for="r-nama">Nama lengkap</label>
          <div class="relative">
            <i data-lucide="user-round" class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-cocoa-300 pointer-events-none"></i>
            <input id="r-nama" x-model="nama" type="text" required class="input !pl-11" placeholder="Nama sesuai panggilan sehari-hari">
          </div>
        </div>
        <div>
          <label class="label" for="r-email">Email</label>
          <div class="relative">
            <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-cocoa-300 pointer-events-none"></i>
            <input id="r-email" x-model="email" type="email" required class="input !pl-11" placeholder="nama@email.com">
          </div>
        </div>
        <div>
          <label class="label" for="r-hp">Nomor WhatsApp</label>
          <div class="relative">
            <i data-lucide="phone" class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-cocoa-300 pointer-events-none"></i>
            <input id="r-hp" x-model="hp" type="tel" required class="input !pl-11" placeholder="0812-3344-5566">
          </div>
          <p class="hint">Dipakai admin untuk konfirmasi pesanan.</p>
        </div>
        <div>
          <label class="label" for="r-pass">Kata sandi</label>
          <div class="relative">
            <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-cocoa-300 pointer-events-none"></i>
            <input id="r-pass" x-model="pass" type="password" required minlength="8" class="input !pl-11" placeholder="Minimal 8 karakter">
          </div>
          <div class="flex gap-1.5 mt-2">
            <span class="h-1.5 flex-1 rounded-full transition-colors" :class="kuat >= 1 ? 'bg-rose-400' : 'bg-cream-200'"></span>
            <span class="h-1.5 flex-1 rounded-full transition-colors" :class="kuat >= 2 ? 'bg-gold-400' : 'bg-cream-200'"></span>
            <span class="h-1.5 flex-1 rounded-full transition-colors" :class="kuat >= 3 ? 'bg-green-500' : 'bg-cream-200'"></span>
          </div>
          <p class="hint" x-text="kuat === 0 ? 'Belum diisi' : kuat === 1 ? 'Terlalu pendek' : kuat === 2 ? 'Cukup aman' : 'Kuat'"></p>
        </div>
        <div>
          <label class="label" for="r-pass2">Konfirmasi kata sandi</label>
          <div class="relative">
            <i data-lucide="lock-keyhole" class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-cocoa-300 pointer-events-none"></i>
            <input id="r-pass2" x-model="pass2" type="password" required class="input !pl-11" :class="pass2 && !cocok && 'is-error'" placeholder="Ulangi kata sandi">
          </div>
          <p x-show="pass2 && !cocok" x-cloak class="error-text"><i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> Konfirmasi kata sandi belum sama.</p>
          <p x-show="pass2 && cocok" x-cloak class="hint text-green-700">Kata sandi sudah cocok.</p>
        </div>
        <div class="flex items-start gap-3 pt-1">
          <input id="r-tos" type="checkbox" x-model="tos" required class="mt-1 w-4 h-4 rounded border-cream-300 text-rose-500 focus:ring-rose-400 cursor-pointer">
          <label for="r-tos" class="text-sm text-cocoa-400 leading-relaxed cursor-pointer">
            Saya setuju dengan ketentuan pemesanan dan kebijakan pre-order 4G Cake &amp; Cookies.
          </label>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-lg !mt-6">Daftar sekarang</button>
      </form>

      <p class="text-center text-sm text-cocoa-400 mt-8">
        Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-rose-600 hover:underline">Masuk di sini</a>
      </p>
    </div>
  </div>
</div>
</main>

<div id="toast-root" x-data role="region" aria-live="polite">
  <template x-for="t in $store.toast.items" :key="t.id">
    <div class="toast" :class="'toast-' + t.tipe" x-transition.opacity.duration.200ms role="status">
      <span class="shrink-0 mt-[1px]" x-html="ikonToast(t.tipe)"></span>
      <div class="flex-1 min-w-0">
        <p class="font-semibold text-sm text-cocoa-700 mb-[2px]" x-show="t.judul" x-text="t.judul"></p>
        <p class="text-sm text-cocoa-500 leading-snug" x-html="t.pesan"></p>
      </div>
    </div>
  </template>
</div>
<script src="{{ asset('assets/js/app.js') }}"></script>
<script defer src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js"></script>
<script>document.addEventListener('DOMContentLoaded', () => icons());</script>
</body>
</html>

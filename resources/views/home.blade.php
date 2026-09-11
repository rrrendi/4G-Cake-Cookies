@extends('layouts.main')

@section('content')
<!-- HERO -->
<section class="relative overflow-hidden">
  <div class="absolute inset-0 -z-10 bg-gradient-to-b from-blush-50 via-cream-50 to-cream-50"></div>
  <div class="absolute -top-24 -right-24 -z-10 w-96 h-96 rounded-full bg-blush-100/60 blur-3xl"></div>
  <div class="absolute top-40 -left-32 -z-10 w-80 h-80 rounded-full bg-cream-200/70 blur-3xl"></div>

  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 sm:py-20 lg:py-24">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
      <div class="fade-up">
        <span class="badge badge-rose mb-5">
          <i data-lucide="heart" class="w-3.5 h-3.5"></i> Dibuat fresh setiap hari di Lhokseumawe
        </span>
        <h1 class="font-display font-bold text-cocoa-700 text-4xl sm:text-5xl lg:text-[3.4rem] leading-[1.1] mb-5">
          Kue rumahan yang<br class="hidden sm:block">
          <span class="text-rose-500">layak jadi</span> pusat meja.
        </h1>
        <p class="font-display font-semibold text-rose-600 text-lg mb-3">Dibuat dengan rasa, dikemas dengan cinta.</p>
        <p class="text-cocoa-400 text-base sm:text-lg leading-relaxed max-w-lg mb-8">
          Cake, cookies, brownies, dan dessert box yang dipanggang dalam batch kecil.
          Pesan online, pilih tanggal ambil atau kirim, dan pantau statusnya sampai sampai di tangan.
        </p>
        <div class="flex flex-wrap gap-3 mb-10">
          <a href="{{ route('catalog') }}" class="btn btn-primary btn-lg">
            <i data-lucide="cake" class="w-[18px] h-[18px]"></i> Lihat Menu
          </a>
          <a href="{{ route('catalog', ['kategori' => 'Best Seller']) }}" class="btn btn-outline btn-lg">
            <i data-lucide="shopping-bag" class="w-[18px] h-[18px]"></i> Pesan Sekarang
          </a>
        </div>
        <div class="flex flex-wrap items-center gap-x-8 gap-y-4">
          <div>
            <p class="font-display font-bold text-2xl text-cocoa-700">1.240+</p>
            <p class="text-xs text-cocoa-300">Pesanan diselesaikan</p>
          </div>
          <span class="hidden sm:block w-px h-10 bg-cream-200"></span>
          <div>
            <p class="font-display font-bold text-2xl text-cocoa-700 flex items-center gap-1.5">4.8
              <i data-lucide="star" class="w-4 h-4 star fill-current"></i></p>
            <p class="text-xs text-cocoa-300">Dari 620 ulasan pelanggan</p>
          </div>
          <span class="hidden sm:block w-px h-10 bg-cream-200"></span>
          <div>
            <p class="font-display font-bold text-2xl text-cocoa-700">7 tahun</p>
            <p class="text-xs text-cocoa-300">Melayani sejak 2019</p>
          </div>
        </div>
      </div>

      <div class="relative fade-up d-2">
        <div class="relative mx-auto max-w-[520px]">
          <img src="{{ asset('assets/img/hero/hero-cake.svg') }}" alt="Ilustrasi cake dan cookies 4G Cake &amp; Cookies" class="w-full drop-shadow-[0_30px_60px_rgba(60,42,33,.18)]">
          <div class="absolute top-6 -left-2 sm:left-0 card px-4 py-3 flex items-center gap-3 animate-[fade-up_.6s_ease_both]">
            <span class="grid place-items-center w-9 h-9 rounded-xl bg-blush-100 text-rose-600"><i data-lucide="clock" class="w-4 h-4"></i></span>
            <div class="leading-tight">
              <p class="text-[11px] text-cocoa-300">Pre-order</p>
              <p class="text-sm font-semibold text-cocoa-700">Mulai H-1</p>
            </div>
          </div>
          <div class="absolute bottom-10 -right-2 sm:right-0 card px-4 py-3 flex items-center gap-3">
            <span class="grid place-items-center w-9 h-9 rounded-xl bg-cream-100 text-gold-600"><i data-lucide="truck" class="w-4 h-4"></i></span>
            <div class="leading-tight">
              <p class="text-[11px] text-cocoa-300">Pengiriman</p>
              <p class="text-sm font-semibold text-cocoa-700">J&amp;T &amp; ambil di tempat</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- KATEGORI -->
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
  <div class="flex items-end justify-between gap-4 mb-6">
    <div>
      <h2 class="font-display font-bold text-2xl sm:text-3xl text-cocoa-700">Pilih sesuai suasananya</h2>
      <p class="text-sm text-cocoa-400 mt-1.5">Lima kategori, dari camilan sore sampai kue perayaan.</p>
    </div>
    <a href="{{ route('catalog') }}" class="hidden sm:inline-flex btn btn-ghost btn-sm">Semua produk <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
    <a href="{{ route('catalog', ['kategori' => 'Cake']) }}" class="card card-hover p-5 text-center">
      <span class="grid place-items-center w-12 h-12 mx-auto rounded-2xl bg-blush-100 text-rose-600 mb-3"><i data-lucide="cake" class="w-5 h-5"></i></span>
      <p class="font-display font-semibold text-cocoa-700 text-sm">Cake</p>
      <p class="text-[11px] text-cocoa-300 mt-1">Ulang tahun &amp; perayaan</p>
    </a>
    <a href="{{ route('catalog', ['kategori' => 'Cookies']) }}" class="card card-hover p-5 text-center">
      <span class="grid place-items-center w-12 h-12 mx-auto rounded-2xl bg-cream-200 text-gold-600 mb-3"><i data-lucide="cookie" class="w-5 h-5"></i></span>
      <p class="font-display font-semibold text-cocoa-700 text-sm">Cookies</p>
      <p class="text-[11px] text-cocoa-300 mt-1">Toples &amp; hantaran</p>
    </a>
    <a href="{{ route('catalog', ['kategori' => 'Brownies']) }}" class="card card-hover p-5 text-center">
      <span class="grid place-items-center w-12 h-12 mx-auto rounded-2xl bg-cocoa-200/60 text-cocoa-600 mb-3"><i data-lucide="square-stack" class="w-5 h-5"></i></span>
      <p class="font-display font-semibold text-cocoa-700 text-sm">Brownies</p>
      <p class="text-[11px] text-cocoa-300 mt-1">Kukus &amp; panggang</p>
    </a>
    <a href="{{ route('catalog', ['kategori' => 'Dessert']) }}" class="card card-hover p-5 text-center">
      <span class="grid place-items-center w-12 h-12 mx-auto rounded-2xl bg-blush-50 text-rose-500 mb-3"><i data-lucide="ice-cream-bowl" class="w-5 h-5"></i></span>
      <p class="font-display font-semibold text-cocoa-700 text-sm">Dessert</p>
      <p class="text-[11px] text-cocoa-300 mt-1">Box siap santap</p>
    </a>
    <a href="{{ route('catalog', ['kategori' => 'Hampers']) }}" class="card card-hover p-5 text-center col-span-2 md:col-span-1">
      <span class="grid place-items-center w-12 h-12 mx-auto rounded-2xl bg-cream-100 text-gold-500 mb-3"><i data-lucide="gift" class="w-5 h-5"></i></span>
      <p class="font-display font-semibold text-cocoa-700 text-sm">Hampers</p>
      <p class="text-[11px] text-cocoa-300 mt-1">Lebaran &amp; korporat</p>
    </a>
  </div>
</section>

<!-- BEST SELLER -->
<section id="best" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
  <div class="flex items-end justify-between gap-4 mb-6">
    <div>
      <span class="badge badge-gold mb-2"><i data-lucide="flame" class="w-3.5 h-3.5"></i> Paling dicari</span>
      <h2 class="font-display font-bold text-2xl sm:text-3xl text-cocoa-700">Best seller bulan ini</h2>
    </div>
    <a href="{{ route('catalog') }}" class="btn btn-ghost btn-sm">Lihat semua <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
  </div>
  <!-- GRID PRODUK DARI DATABASE -->
  <div id="grid-best" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6"></div>
</section>

<!-- ALASAN -->
<section id="tentang" class="bg-cream-100/70 border-y border-cream-200 mt-8">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 sm:py-20">
    <div class="grid lg:grid-cols-[0.9fr_1.1fr] gap-12 items-start">
      <div>
        <span class="badge badge-neutral mb-4">Tentang 4G Cake &amp; Cookies</span>
        <h2 class="font-display font-bold text-2xl sm:text-3xl text-cocoa-700 leading-snug mb-4">
          Dapur rumahan kecil yang menolak jadi pabrik
        </h2>
        <p class="text-cocoa-400 leading-relaxed mb-4">
          4G Cake &amp; Cookies berdiri tahun 2019 di Lhokseumawe, berawal dari pesanan tetangga
          yang kebetulan mencicipi brownies buatan Ibu Gustina. Sampai hari ini semua adonan
          masih dikerjakan tangan, dalam jumlah terbatas per hari.
        </p>
        <p class="text-cocoa-400 leading-relaxed mb-6">
          Itu sebabnya kami memakai sistem pre-order: kue baru dibuat setelah pesanan masuk,
          jadi yang sampai ke Anda bukan stok yang menginap di etalase.
        </p>
        <a href="{{ route('catalog') }}" class="btn btn-cocoa">Mulai pesan <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <div class="card p-6">
          <span class="grid place-items-center w-11 h-11 rounded-2xl bg-blush-100 text-rose-600 mb-4"><i data-lucide="wheat" class="w-5 h-5"></i></span>
          <h3 class="font-display font-semibold text-cocoa-700 mb-1.5">Homemade</h3>
          <p class="text-sm text-cocoa-400 leading-relaxed">Semua adonan dikerjakan tangan di dapur rumah, bukan produksi pabrik. Bahan dibeli mingguan dalam jumlah kecil.</p>
        </div>
        <div class="card p-6">
          <span class="grid place-items-center w-11 h-11 rounded-2xl bg-cream-200 text-gold-600 mb-4"><i data-lucide="calendar-check" class="w-5 h-5"></i></span>
          <h3 class="font-display font-semibold text-cocoa-700 mb-1.5">Freshly Made</h3>
          <p class="text-sm text-cocoa-400 leading-relaxed">Kue baru dibuat setelah pesanan masuk, jadi yang Anda terima bukan stok yang menginap di etalase.</p>
        </div>
        <div class="card p-6">
          <span class="grid place-items-center w-11 h-11 rounded-2xl bg-cocoa-200/60 text-cocoa-600 mb-4"><i data-lucide="package-check" class="w-5 h-5"></i></span>
          <h3 class="font-display font-semibold text-cocoa-700 mb-1.5">Packaging Aman</h3>
          <p class="text-sm text-cocoa-400 leading-relaxed">Box tebal, ice gel untuk dessert, dan bubble wrap untuk pengiriman luar kota.</p>
        </div>
        <div class="card p-6">
          <span class="grid place-items-center w-11 h-11 rounded-2xl bg-blush-50 text-rose-500 mb-4"><i data-lucide="calendar-clock" class="w-5 h-5"></i></span>
          <h3 class="font-display font-semibold text-cocoa-700 mb-1.5">Bisa Pre-Order</h3>
          <p class="text-sm text-cocoa-400 leading-relaxed">Pilih sendiri tanggal ambil atau kirim saat checkout, mulai H-1 sampai H-5 tergantung produknya.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CARA PESAN -->
<section id="cara-pesan" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 sm:py-20">
  <div class="text-center max-w-2xl mx-auto mb-10">
    <h2 class="font-display font-bold text-2xl sm:text-3xl text-cocoa-700 mb-3">Empat langkah, selesai</h2>
    <p class="text-cocoa-400">Tidak perlu chat bolak-balik. Semua pesanan tercatat rapi dan bisa dilacak sendiri.</p>
  </div>
  <div class="relative grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
    <div class="card p-6 relative">
      <span class="absolute -top-3 left-6 grid place-items-center w-8 h-8 rounded-full bg-rose-500 text-white text-xs font-bold">1</span>
      <i data-lucide="search" class="w-6 h-6 text-rose-500 mt-3 mb-3"></i>
      <h3 class="font-display font-semibold text-cocoa-700 mb-1.5">Pilih kue</h3>
      <p class="text-sm text-cocoa-400 leading-relaxed">Telusuri katalog, cek rating dan ketentuan pre-order tiap produk.</p>
    </div>
    <div class="card p-6 relative">
      <span class="absolute -top-3 left-6 grid place-items-center w-8 h-8 rounded-full bg-rose-500 text-white text-xs font-bold">2</span>
      <i data-lucide="calendar-days" class="w-6 h-6 text-rose-500 mt-3 mb-3"></i>
      <h3 class="font-display font-semibold text-cocoa-700 mb-1.5">Tentukan tanggal</h3>
      <p class="text-sm text-cocoa-400 leading-relaxed">Pilih tanggal ambil atau kirim, minimal H-1 sampai H-5 tergantung produknya.</p>
    </div>
    <div class="card p-6 relative">
      <span class="absolute -top-3 left-6 grid place-items-center w-8 h-8 rounded-full bg-rose-500 text-white text-xs font-bold">3</span>
      <i data-lucide="wallet" class="w-6 h-6 text-rose-500 mt-3 mb-3"></i>
      <h3 class="font-display font-semibold text-cocoa-700 mb-1.5">Bayar &amp; unggah bukti</h3>
      <p class="text-sm text-cocoa-400 leading-relaxed">Transfer bank atau bayar tunai saat ambil. Bukti transfer diunggah langsung di web.</p>
    </div>
    <div class="card p-6 relative">
      <span class="absolute -top-3 left-6 grid place-items-center w-8 h-8 rounded-full bg-rose-500 text-white text-xs font-bold">4</span>
      <i data-lucide="package-search" class="w-6 h-6 text-rose-500 mt-3 mb-3"></i>
      <h3 class="font-display font-semibold text-cocoa-700 mb-1.5">Pantau statusnya</h3>
      <p class="text-sm text-cocoa-400 leading-relaxed">Dari diproses sampai dikirim, lengkap dengan nomor resi J&amp;T.</p>
    </div>
  </div>
</section>

<!-- TESTIMONI -->
<section class="bg-cocoa-700 text-cream-100">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 sm:py-20">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
      <div>
        <h2 class="font-display font-bold text-2xl sm:text-3xl text-white mb-2">Kata mereka yang sudah pesan</h2>
        <p class="text-cream-200/60 text-sm">Ulasan diambil dari pesanan yang sudah selesai.</p>
      </div>
      <button onclick="toast('Halaman ulasan menyusul.', 'info')" class="btn btn-gold btn-sm self-start">Tulis ulasan Anda</button>
    </div>
    <div id="grid-testimoni" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6"></div>
  </div>
</section>

<!-- CTA -->
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 sm:py-20">
  <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blush-100 via-cream-100 to-cream-200 border border-cream-200 p-8 sm:p-12 lg:p-16 text-center">
    <div class="absolute -top-16 -right-10 w-56 h-56 rounded-full bg-white/50 blur-2xl"></div>
    <div class="relative">
      <span class="badge badge-rose mb-4"><i data-lucide="calendar-heart" class="w-3.5 h-3.5"></i> Slot minggu ini masih tersedia</span>
      <h2 class="font-display font-bold text-2xl sm:text-4xl text-cocoa-700 mb-4 leading-tight">
        Ada acara minggu depan?<br>Amankan tanggalnya sekarang.
      </h2>
      <p class="text-cocoa-400 max-w-xl mx-auto mb-7">
        Slot produksi harian kami terbatas. Semakin cepat memesan, semakin leluasa memilih tanggal dan desain.
      </p>
      <div class="flex flex-wrap justify-center gap-3">
        <a href="{{ route('catalog') }}" class="btn btn-primary btn-lg">Pesan sekarang</a>
        <button onclick="toast('Halaman Riwayat Pesanan menyusul.', 'info')" class="btn btn-outline btn-lg bg-white/60">Lacak pesanan saya</button>
      </div>
    </div>
  </div>
</section>

<!-- FORM ADD TO CART TERSEMBUNYI (PENGHUBUNG KE LARAVEL) -->
<form id="form-cart-laravel" action="{{ route('cart.store') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="product_id" id="cart-product-id">
    <input type="hidden" name="qty" id="cart-qty" value="1">
    <input type="hidden" name="variant" id="cart-variant">
    <input type="hidden" name="action" id="cart-action" value="cart">
</form>

@endsection

@push('scripts')
<script>
  // Mengambil data Best Seller dari Laravel
  const BEST_SELLER = {!! json_encode($mappedBestSellers) !!};
  const JUMLAH_KERANJANG = {{ $cartCount }};

  // Fungsi Kartu Produk Asli
  window.kartuProduk = function(p) {
    const habis = p.stok === 'habis';
    const diskon = p.hargaCoret ? Math.round((1 - p.harga / p.hargaCoret) * 100) : 0;
    const urlDetail = `{{ url('/produk') }}/${p.slug}`;
    
    return `
    <article class="card card-hover overflow-hidden flex flex-col group h-full">
      <a href="${urlDetail}" class="relative block aspect-[4/3] overflow-hidden bg-cream-100"
         aria-label="Lihat detail ${p.nama}">
        <img src="${imgProduk(p.slug)}" alt="${p.nama}" loading="lazy"
             class="w-full h-full object-cover transition duration-500 group-hover:scale-[1.06] ${habis ? 'grayscale opacity-70' : ''}">
        <span class="absolute top-3 left-3 flex flex-col gap-1.5 items-start">
          ${p.bestSeller ? '<span class="badge badge-gold shadow-sm"><i data-lucide="flame" class="w-3 h-3"></i> Best Seller</span>' : ''}
          ${diskon ? `<span class="badge badge-rose shadow-sm">Hemat ${diskon}%</span>` : ''}
        </span>
        ${habis ? '<span class="absolute inset-x-0 bottom-0 bg-cocoa-700/85 text-white text-xs font-semibold text-center py-2">Stok Habis &mdash; buka PO minggu depan</span>' : ''}
      </a>
      <div class="p-4 sm:p-5 flex flex-col flex-1">
        <div class="flex items-center gap-2 mb-2">
          <span class="badge badge-neutral">${p.kategori}</span>
          <span class="flex items-center gap-1 text-xs text-cocoa-400">
            <i data-lucide="star" class="w-3.5 h-3.5 star fill-current"></i>${p.rating.toFixed(1)}
            <span class="text-cocoa-300">(${p.ulasan})</span>
          </span>
        </div>
        <h3 class="font-display font-semibold text-cocoa-700 leading-snug mb-1">
          <a href="${urlDetail}" class="hover:text-rose-600 transition">${p.nama}</a>
        </h3>
        <p class="text-xs text-cocoa-300 mb-3">${p.berat} &middot; PO min. H-${p.po}</p>
        <div class="mt-auto">
          <div class="flex items-baseline gap-2 mb-3">
            <span class="font-display font-bold text-lg text-rose-600">${rp(p.harga)}</span>
            ${p.hargaCoret ? `<span class="text-xs text-cocoa-300 line-through">${rp(p.hargaCoret)}</span>` : ''}
          </div>
          <div class="flex gap-2">
            <a href="${urlDetail}" class="btn btn-outline btn-sm flex-1">Detail</a>
            ${habis
              ? `<button type="button" class="btn btn-primary btn-sm flex-1 is-disabled" disabled aria-disabled="true">Habis</button>`
              : `<button type="button" class="btn btn-primary btn-sm flex-1" onclick="tambahKeranjang('${p.id}', 1, '${p.varian[0] || ''}')" aria-label="Tambah ${p.nama} ke keranjang">
                   <i data-lucide="plus" class="w-4 h-4"></i> Keranjang</button>`}
          </div>
        </div>
      </div>
    </article>`;
  }

  // Fungsi Tambah Keranjang AJAX
  window.tambahKeranjang = function(id, qty = 1, varian = '') {
      const form = document.getElementById('form-cart-laravel');
      const csrfToken = form.querySelector('input[name="_token"]').value;
      const url = form.getAttribute('action'); 

      fetch(url, {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json'
          },
          body: JSON.stringify({
              product_id: id, qty: qty, variant: varian, action: 'cart'
          })
      })
      .then(response => response.json())
      .then(data => {
          if (data.status === 'success') {
              toast(data.message, 'success', 'Masuk keranjang');
              document.querySelectorAll('[data-cart-badge]').forEach(el => {
                  el.textContent = data.cart_count;
                  el.classList.remove('hidden');
              });
          } else {
              toast(data.message, 'error', 'Gagal');
          }
      })
      .catch(() => toast('Terjadi kesalahan jaringan.', 'error'));
  };

  /* Render best seller + testimoni dari data dummy (sinkronisasi) */
  document.addEventListener('DOMContentLoaded', () => {
    // 1. Render Best Seller dengan data dari Laravel (Disinkronkan dengan rating Dummy)
    if (typeof PRODUK !== 'undefined') {
        BEST_SELLER.forEach(p => {
            const dataAsli = PRODUK.find(x => x.slug === p.slug);
            if (dataAsli) { 
                p.rating = dataAsli.rating; 
                p.ulasan = dataAsli.ulasan; 
                p.terjual = dataAsli.terjual; // Tambahkan ini agar angka terjual juga konsisten
            }
        });
    }

    document.getElementById('grid-best').innerHTML =
      BEST_SELLER.map((p, i) => `<div class="fade-up d-${i+1}">${kartuProduk(p)}</div>`).join('');

    // 2. Render Testimoni Dummy
    document.getElementById('grid-testimoni').innerHTML = REVIEWS.filter(r => r.bintang === 5).slice(0, 3).map(r => `
      <figure class="rounded-2xl bg-white/5 border border-white/10 p-6 backdrop-blur-sm">
        <div class="flex items-center gap-1 mb-3">${starsHTML(r.bintang, 'w-4 h-4')}</div>
        <blockquote class="text-sm leading-relaxed text-cream-200/85 mb-5">&ldquo;${r.teks}&rdquo;</blockquote>
        <figcaption class="flex items-center gap-3">
          <img src="${imgAvatar(r.avatar)}" alt="Ilustrasi ${r.nama}" loading="lazy"
               class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 ring-white/20">
          <span class="min-w-0">
            <span class="block text-sm font-semibold text-white truncate">${r.nama}</span>
            <span class="block text-[11px] text-cream-200/50 truncate">${r.produk}</span>
          </span>
        </figcaption>
      </figure>`).join('');
      
    // 3. Sinkronisasi Badge Keranjang Pertama Kali Buka Beranda
    if (JUMLAH_KERANJANG > 0) {
        document.querySelectorAll('[data-cart-badge]').forEach(el => {
            el.textContent = JUMLAH_KERANJANG;
            el.classList.remove('hidden');
        });
    }

    icons();
  });
</script>
@endpush
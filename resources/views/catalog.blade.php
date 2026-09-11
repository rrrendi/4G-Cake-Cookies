@extends('layouts.main')

@section('title', 'Katalog produk &middot; 4G Cake & Cookies')

@section('content')
<!-- HEADER KATALOG -->
<section class="bg-gradient-to-b from-blush-50 to-cream-50 border-b border-cream-200">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <nav class="flex items-center gap-2 text-xs text-cocoa-300 mb-4" aria-label="Breadcrumb">
      <a href="{{ route('home') }}" class="hover:text-rose-600 transition">Beranda</a>
      <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
      <span class="text-cocoa-500 font-medium">Katalog</span>
    </nav>
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
      <div class="max-w-xl">
        <h1 class="font-display font-bold text-3xl sm:text-4xl text-cocoa-700 mb-3">Katalog produk</h1>
        <p class="text-cocoa-400 leading-relaxed">
          Semua kue dibuat setelah pesanan masuk. Perhatikan ketentuan pre-order pada tiap produk
          sebelum menentukan tanggal ambil atau kirim.
        </p>
      </div>
      <div class="flex flex-wrap gap-3">
        <div class="card px-4 py-3 flex items-center gap-3">
          <span class="grid place-items-center w-9 h-9 rounded-xl bg-blush-100 text-rose-600"><i data-lucide="cake" class="w-4 h-4"></i></span>
          <div class="leading-tight"><p class="font-display font-bold text-cocoa-700" id="stat-total">0</p><p class="text-[11px] text-cocoa-300">Produk aktif</p></div>
        </div>
        <div class="card px-4 py-3 flex items-center gap-3">
          <span class="grid place-items-center w-9 h-9 rounded-xl bg-cream-200 text-gold-600"><i data-lucide="flame" class="w-4 h-4"></i></span>
          <div class="leading-tight"><p class="font-display font-bold text-cocoa-700" id="stat-best">0</p><p class="text-[11px] text-cocoa-300">Best seller</p></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FILTER BAR START -->
<div class="sticky top-[68px] z-40 bg-cream-50/95 backdrop-blur border-b border-cream-200">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 space-y-3">
    <div class="flex flex-col sm:flex-row gap-3">
      <div class="relative flex-1">
        <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-cocoa-300 pointer-events-none"></i>
        <input id="cari" type="search" placeholder="Cari nama kue, misal: brownies, nastar, tiramisu&hellip;"
               class="input !rounded-full !pl-11 !pr-11" autocomplete="off" aria-label="Cari produk">
        <button id="hapus-cari" type="button" data-js-handler class="hidden absolute right-3.5 top-1/2 -translate-y-1/2 text-cocoa-300 hover:text-cocoa-500" aria-label="Hapus pencarian">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>
      <select id="urut" class="select !rounded-full sm:w-56" aria-label="Urutkan produk">
        <option value="populer">Terlaris</option>
        <option value="terbaru">Terbaru</option>
        <option value="rating">Rating tertinggi</option>
        <option value="murah">Harga terendah</option>
        <option value="mahal">Harga tertinggi</option>
        <option value="nama">Nama A &ndash; Z</option>
      </select>
    </div>

    <div class="flex items-center gap-3">
      <div id="chips" role="group" aria-label="Filter kategori produk" class="flex-1 flex gap-2 overflow-x-auto no-scrollbar py-0.5"></div>
      <button id="reset" type="button" data-js-handler class="hidden sm:inline-flex btn btn-ghost btn-sm shrink-0">
        <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reset
      </button>
    </div>
  </div>
</div>
<!-- FILTER BAR END -->

<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
  <p id="ringkas" class="text-sm text-cocoa-400 mb-5"></p>

  <!-- GRID PRODUK (Template Asli Anda) -->
  <div id="grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6"></div>

  <!-- EMPTY STATE -->
  <div id="kosong" class="hidden card py-16 px-6 text-center">
    <span class="grid place-items-center w-16 h-16 mx-auto rounded-3xl bg-cream-100 text-cocoa-300 mb-5">
      <i data-lucide="search-x" class="w-7 h-7"></i>
    </span>
    <h2 class="font-display font-semibold text-lg text-cocoa-700 mb-2">Belum ada yang cocok</h2>
    <p class="text-sm text-cocoa-400 max-w-sm mx-auto mb-6">
      Kata kunci atau kategori yang dipilih belum menemukan produk. Coba kata yang lebih umum,
      misalnya &ldquo;cake&rdquo; atau &ldquo;cookies&rdquo;.
    </p>
    <button type="button" onclick="resetFilter()" class="btn btn-primary btn-sm mx-auto">Tampilkan semua produk</button>
  </div>
</section>

<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pb-8">
  <div class="card p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center gap-5">
    <span class="grid place-items-center w-12 h-12 rounded-2xl bg-blush-100 text-rose-600 shrink-0"><i data-lucide="messages-square" class="w-5 h-5"></i></span>
    <div class="flex-1">
      <h2 class="font-display font-semibold text-cocoa-700 mb-1">Tidak menemukan yang dicari?</h2>
      <p class="text-sm text-cocoa-400">Kami menerima pesanan custom: rasa, ukuran, sampai desain kue ulang tahun.</p>
    </div>
    <button type="button" onclick="toast('Form pesanan custom akan tersedia di fase berikutnya.','info')" class="btn btn-outline btn-sm shrink-0">Ajukan pesanan custom</button>
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
  // Mengambil data dari Laravel dengan aman
  const DB_PRODUK = {!! json_encode($mappedProducts) !!};
  
  // --- SINKRONISASI RATING DUMMY GLOBAL ---
  if (typeof PRODUK !== 'undefined') {
      DB_PRODUK.forEach(p => {
          const dataAsli = PRODUK.find(x => x.slug === p.slug);
          if (dataAsli) {
              p.rating = dataAsli.rating; 
              p.ulasan = dataAsli.ulasan; 
              p.terjual = dataAsli.terjual;
          }
      });
  }

  // --- KODE AJAX UNTUK TAMBAH KERANJANG TANPA REFRESH ---
  window.tambahKeranjang = function(id, qty = 1, varian = '') {
      const form = document.getElementById('form-cart-laravel');
      const csrfToken = form.querySelector('input[name="_token"]').value;
      
      // PERBAIKAN: Gunakan getAttribute agar tidak bentrok dengan input name="action"
      const url = form.getAttribute('action'); 

      fetch(url, {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json'
          },
          body: JSON.stringify({
              product_id: id,
              qty: qty,
              variant: varian,
              action: 'cart'
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
      .catch(error => {
          toast('Terjadi kesalahan jaringan.', 'error');
      });
  };

  // Kartu Produk asli milik Anda dengan sedikit penyesuaian href (dinamis)
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

  // SCRIPT FILTER & PENCARIAN 100% ASLI ANDA TANPA UBAH
  const FILTER = ['Semua', 'Best Seller', ...KATEGORI];
  let state = { q:'', kategori:'Semua', urut:'populer' };

  function renderChips() {
    document.getElementById('chips').innerHTML = FILTER.map(k => {
      const on = state.kategori === k;
      return `<button type="button" data-k="${k}" data-js-handler aria-pressed="${on}"
        class="shrink-0 rounded-full border px-4 py-2 text-sm font-medium transition ${on
          ? 'bg-cocoa-500 border-cocoa-500 text-white shadow-[0_8px_18px_-12px_rgba(60,42,33,.9)]'
          : 'bg-white border-cream-200 text-cocoa-500 hover:border-rose-300 hover:text-rose-600'}">${k}</button>`;
    }).join('');
    document.querySelectorAll('#chips button').forEach(b =>
      b.addEventListener('click', () => { state.kategori = b.dataset.k; render(); }));
  }

  function terfilter() {
    let out = DB_PRODUK.filter(p => { 
      const cocokKat = state.kategori === 'Semua'
        || (state.kategori === 'Best Seller' ? p.bestSeller : p.kategori === state.kategori);
      const q = state.q.trim().toLowerCase();
      const cocokQ = !q || p.nama.toLowerCase().includes(q) || p.kategori.toLowerCase().includes(q)
        || p.desc.toLowerCase().includes(q);
      return cocokKat && cocokQ;
    });
    const urutan = {
      populer: (a,b) => b.terjual - a.terjual,
      terbaru: (a,b) => b.dibuat.localeCompare(a.dibuat),
      rating:  (a,b) => b.rating - a.rating,
      murah:   (a,b) => a.harga - b.harga,
      mahal:   (a,b) => b.harga - a.harga,
      nama:    (a,b) => a.nama.localeCompare(b.nama)
    };
    return out.sort(urutan[state.urut]);
  }

  function render() {
    renderChips();
    const hasil = terfilter();
    const grid = document.getElementById('grid');
    const kosong = document.getElementById('kosong');
    grid.innerHTML = hasil.map((p,i) => `<div class="fade-up d-${(i%6)+1}">${kartuProduk(p)}</div>`).join('');
    grid.classList.toggle('hidden', hasil.length === 0);
    kosong.classList.toggle('hidden', hasil.length !== 0);

    const bagian = state.kategori === 'Semua' ? 'semua kategori' : `kategori <span class="font-semibold text-cocoa-500">${state.kategori}</span>`;
    document.getElementById('ringkas').innerHTML = hasil.length
      ? `Menampilkan <span class="font-semibold text-cocoa-500">${hasil.length}</span> produk dari ${bagian}${state.q ? ` untuk kata kunci &ldquo;${state.q}&rdquo;` : ''}.`
      : '';
    document.getElementById('hapus-cari').classList.toggle('hidden', !state.q);
    
    // Update Header Statistik Dinamis
    document.getElementById('stat-total').textContent = DB_PRODUK.filter(p => p.stok === 'tersedia').length;
    document.getElementById('stat-best').textContent = DB_PRODUK.filter(p => p.bestSeller).length;
    
    icons();
  }

  function resetFilter() {
    state = { q:'', kategori:'Semua', urut:'populer' };
    document.getElementById('cari').value = '';
    document.getElementById('urut').value = 'populer';
    render();
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('grid').innerHTML = skeletonKartu(8);

    const kat = new URLSearchParams(location.search).get('kategori');
    if (kat && [...KATEGORI, 'Best Seller'].includes(kat)) state.kategori = kat;
    if (location.hash === '#best') state.kategori = 'Best Seller';

    document.getElementById('cari').addEventListener('input', e => { state.q = e.target.value; render(); });
    document.getElementById('hapus-cari').addEventListener('click', () => { state.q=''; document.getElementById('cari').value=''; render(); });
    document.getElementById('urut').addEventListener('change', e => { state.urut = e.target.value; render(); });
    document.getElementById('reset').addEventListener('click', resetFilter);
    setTimeout(render, 450);
  });
</script>
@endpush
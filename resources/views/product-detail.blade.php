@extends('layouts.main')

@section('title', $product->name . ' &middot; 4G Cake & Cookies')

@section('content')
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
  <nav class="flex items-center gap-2 text-xs text-cocoa-300 mb-6 flex-wrap" aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="hover:text-rose-600 transition">Beranda</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <a href="{{ route('catalog') }}" class="hover:text-rose-600 transition">Katalog</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <span id="bc-kategori" class="hover:text-rose-600"></span>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <span id="bc-nama" class="text-cocoa-500 font-medium"></span>
  </nav>

  <div class="grid lg:grid-cols-2 gap-8 lg:gap-14">
    <!-- GALERI START -->
    <div x-data="{ aktif: 0 }" class="lg:sticky lg:top-28 lg:self-start">
      <div class="card overflow-hidden aspect-[4/3] bg-cream-100">
        <img id="galeri-utama" src="{{ asset('assets/img/products/placeholder.svg') }}" alt="" class="w-full h-full object-cover transition duration-300">
      </div>
      <div id="galeri-thumb" class="grid grid-cols-4 gap-3 mt-3"></div>
      <div class="hidden sm:flex items-center gap-2 mt-4 text-xs text-cocoa-300">
        <i data-lucide="camera" class="w-4 h-4"></i>
        Foto ilustrasi. Tampilan asli menyesuaikan ukuran dan varian yang dipilih.
      </div>
    </div>
    <!-- GALERI END -->

    <!-- INFO PRODUK START -->
    <div x-data="detailProduk()" x-init="init()">
      <div class="flex flex-wrap items-center gap-2 mb-3">
        <span id="p-kategori" class="badge badge-neutral"></span>
        <span id="p-best" class="badge badge-gold hidden"><i data-lucide="flame" class="w-3 h-3"></i> Best Seller</span>
        <span id="p-stok" class="badge"></span>
      </div>

      <h1 id="p-nama" class="font-display font-bold text-3xl sm:text-4xl text-cocoa-700 leading-tight mb-3"></h1>

      <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mb-5 text-sm">
        <span id="p-rating" class="flex items-center gap-2"></span>
        <span class="divider-dot"></span>
        <span id="p-terjual" class="text-cocoa-400"></span>
      </div>

      <div class="flex flex-wrap items-baseline gap-x-3 gap-y-2 mb-5">
        <span id="p-harga" class="font-display font-bold text-3xl text-rose-600"></span>
        <span id="p-coret" class="text-sm text-cocoa-300 line-through"></span>
        <span id="p-hemat" class="badge badge-rose"></span>
      </div>

      <p id="p-desc" class="text-cocoa-400 leading-relaxed mb-6"></p>

      <!-- INFO PRE-ORDER -->
      <div class="rounded-2xl border border-gold-300/60 bg-[#FDF8EA] p-4 sm:p-5 mb-6 flex gap-3.5">
        <span class="grid place-items-center w-10 h-10 rounded-xl bg-gold-400/20 text-gold-600 shrink-0">
          <i data-lucide="calendar-clock" class="w-5 h-5"></i>
        </span>
        <div>
          <p class="font-semibold text-sm text-cocoa-700 mb-1">Produk pre-order &mdash; <span id="p-po-h"></span></p>
          <p class="text-sm text-cocoa-400 leading-relaxed">
            Kue baru mulai dibuat setelah pembayaran dikonfirmasi. Tanggal ambil atau kirim paling cepat
            <span id="p-po-tgl" class="font-semibold text-cocoa-500"></span>. Untuk tanggal mendesak, hubungi kami dulu lewat WhatsApp.
          </p>
        </div>
      </div>

      <!-- VARIAN -->
      <div class="mb-6">
        <p class="label">Pilih varian / ukuran</p>
        <div id="p-varian" class="flex flex-wrap gap-2"></div>
      </div>

      <!-- QTY + AKSI -->
      <div class="flex flex-wrap items-center gap-3 mb-4">
        <div class="inline-flex items-center rounded-full border border-cream-200 bg-white">
          <button type="button" @click="qty = Math.max(1, qty-1)" class="grid place-items-center w-11 h-11 rounded-full text-cocoa-500 hover:bg-cream-100 transition" aria-label="Kurangi jumlah">
            <i data-lucide="minus" class="w-4 h-4"></i>
          </button>
          <input x-model.number="qty" type="number" min="1" max="99" class="w-12 text-center bg-transparent font-semibold text-cocoa-700 outline-none" aria-label="Jumlah">
          <button type="button" @click="qty = Math.min(99, qty+1)" class="grid place-items-center w-11 h-11 rounded-full text-cocoa-500 hover:bg-cream-100 transition" aria-label="Tambah jumlah">
            <i data-lucide="plus" class="w-4 h-4"></i>
          </button>
        </div>
        <p class="text-sm text-cocoa-400">Total <span class="font-display font-bold text-cocoa-700" x-text="totalRp"></span></p>
      </div>

      <div class="grid sm:grid-cols-2 gap-3 mb-6">
        <button type="button" @click="tambah()" :class="habis && 'is-disabled'" class="btn btn-outline btn-lg">
          <i data-lucide="shopping-bag" class="w-[18px] h-[18px]"></i> Tambah ke Keranjang
        </button>
        <button type="button" @click="beli()" :class="habis && 'is-disabled'" class="btn btn-primary btn-lg">
          <i data-lucide="zap" class="w-[18px] h-[18px]"></i> Beli Sekarang
        </button>
      </div>

      <div class="grid sm:grid-cols-2 gap-x-6 gap-y-3 py-5 border-y border-cream-200 text-sm">
        <div class="flex items-center gap-2.5"><i data-lucide="scale" class="w-4 h-4 text-cocoa-300"></i><span class="text-cocoa-400">Ukuran</span><span id="p-berat" class="ml-auto font-medium text-cocoa-600"></span></div>
        <div class="flex items-center gap-2.5"><i data-lucide="thermometer-snowflake" class="w-4 h-4 text-cocoa-300"></i><span class="text-cocoa-400">Simpan</span><span id="p-simpan" class="ml-auto font-medium text-cocoa-600 text-right"></span></div>
        <div class="flex items-center gap-2.5"><i data-lucide="truck" class="w-4 h-4 text-cocoa-300"></i><span class="text-cocoa-400">Pengiriman</span><span class="ml-auto font-medium text-cocoa-600">J&amp;T / ambil sendiri</span></div>
        <div class="flex items-center gap-2.5"><i data-lucide="badge-check" class="w-4 h-4 text-cocoa-300"></i><span class="text-cocoa-400">Kondisi</span><span class="ml-auto font-medium text-cocoa-600">Dibuat setelah dipesan</span></div>
      </div>

      <div class="flex items-center gap-3 pt-5">
        <span class="text-sm text-cocoa-400">Bagikan:</span>
        
        <!-- 1. Salin Tautan (Langsung Aktif) -->
        <button type="button" onclick="navigator.clipboard.writeText(window.location.href).then(() => toast('Tautan produk berhasil disalin.', 'success')).catch(() => toast('Gagal menyalin tautan.', 'error'))" class="grid place-items-center w-9 h-9 rounded-full border border-cream-200 text-cocoa-400 hover:text-rose-600 hover:border-rose-300 transition" aria-label="Salin tautan">
            <i data-lucide="link" class="w-4 h-4"></i>
        </button>
        
        <!-- 2. Bagikan ke WhatsApp (Langsung Aktif membuka WA) -->
        <a href="https://wa.me/?text={{ urlencode('Cek kue ini di 4G Cake & Cookies: ' . $product->name . ' - ' . route('product.detail', $product->slug)) }}" target="_blank" class="grid place-items-center w-9 h-9 rounded-full border border-cream-200 text-cocoa-400 hover:text-rose-600 hover:border-rose-300 transition" aria-label="Bagikan ke WhatsApp">
            <i data-lucide="message-circle" class="w-4 h-4"></i>
        </a>
        
        <!-- 3. Simpan Favorit (Menunggu Fase Login) -->
        <button type="button" onclick="toast('Fitur Favorit akan aktif setelah sistem Login pelanggan selesai dibangun.','info')" class="grid place-items-center w-9 h-9 rounded-full border border-cream-200 text-cocoa-400 hover:text-rose-600 hover:border-rose-300 transition" aria-label="Simpan favorit">
            <i data-lucide="heart" class="w-4 h-4"></i>
        </button>
      </div>
    </div>
    <!-- INFO PRODUK END -->
  </div>

  <!-- TAB DETAIL -->
  <div x-data="{ tab:'deskripsi' }" class="card mt-12 sm:mt-16 overflow-hidden">
    <div class="flex gap-1 p-2 border-b border-cream-200 overflow-x-auto no-scrollbar" role="tablist" aria-label="Informasi produk">
      <button role="tab" :aria-selected="tab==='deskripsi'" @click="tab='deskripsi'" :class="tab==='deskripsi' ? 'bg-cream-100 text-cocoa-700' : 'text-cocoa-400 hover:text-cocoa-600'" class="shrink-0 rounded-xl px-4 py-2.5 text-sm font-medium transition">Deskripsi &amp; Bahan</button>
      <button role="tab" :aria-selected="tab==='po'" @click="tab='po'" :class="tab==='po' ? 'bg-cream-100 text-cocoa-700' : 'text-cocoa-400 hover:text-cocoa-600'" class="shrink-0 rounded-xl px-4 py-2.5 text-sm font-medium transition">Pre-Order &amp; Penyimpanan</button>
      <button role="tab" :aria-selected="tab==='ulasan'" @click="tab='ulasan'" :class="tab==='ulasan' ? 'bg-cream-100 text-cocoa-700' : 'text-cocoa-400 hover:text-cocoa-600'" class="shrink-0 rounded-xl px-4 py-2.5 text-sm font-medium transition">Ulasan <span id="tab-ulasan-n"></span></button>
    </div>

    <div class="p-6 sm:p-8">
      <div x-show="tab==='deskripsi'" role="tabpanel" class="max-w-3xl space-y-4 text-cocoa-400 leading-relaxed text-sm sm:text-base">
        <p id="tab-desc"></p>
        <div class="rounded-2xl bg-cream-100 p-5 !mt-5">
          <h3 class="font-display font-semibold text-cocoa-700 mb-2 flex items-center gap-2">
            <i data-lucide="list" class="w-4 h-4"></i> Bahan yang dipakai
          </h3>
          <p id="tab-bahan" class="text-sm"></p>
        </div>
        <p>Setiap pesanan dikerjakan dalam batch kecil oleh tim dapur kami. Bahan utama seperti mentega,
          cokelat, dan keju dibeli mingguan sehingga tidak ada stok lama yang disimpan berbulan-bulan.</p>
        <ul class="space-y-2 pt-2">
          <li class="flex gap-2.5"><i data-lucide="check" class="w-4 h-4 text-rose-500 shrink-0 mt-0.5"></i> Tanpa pengawet tambahan</li>
          <li class="flex gap-2.5"><i data-lucide="check" class="w-4 h-4 text-rose-500 shrink-0 mt-0.5"></i> Tingkat kemanisan bisa diturunkan atas permintaan</li>
          <li class="flex gap-2.5"><i data-lucide="check" class="w-4 h-4 text-rose-500 shrink-0 mt-0.5"></i> Gratis kartu ucapan untuk pembelian di atas Rp250.000</li>
        </ul>
      </div>

      <div x-show="tab==='po'" x-cloak role="tabpanel" class="max-w-3xl space-y-5 text-sm sm:text-base text-cocoa-400 leading-relaxed">
        <div class="rounded-2xl bg-cream-100 p-5">
          <h3 class="font-display font-semibold text-cocoa-700 mb-2">Ketentuan pre-order</h3>
          <p id="tab-po" class="mb-3"></p>
          <p>Pesanan yang masuk setelah pukul 18.00 dihitung sebagai pesanan hari berikutnya.
              Pembatalan masih bisa dilakukan selama status pesanan belum berubah menjadi &ldquo;Diproses&rdquo;.</p>
        </div>
        <div>
          <h3 class="font-display font-semibold text-cocoa-700 mb-2">Cara menyimpan</h3>
          <ul class="space-y-2">
            <li class="flex gap-2.5"><i data-lucide="refrigerator" class="w-4 h-4 text-cocoa-300 shrink-0 mt-0.5"></i> <span id="tab-simpan"></span></li>
            <li class="flex gap-2.5"><i data-lucide="sun" class="w-4 h-4 text-cocoa-300 shrink-0 mt-0.5"></i> Keluarkan 15 menit sebelum disajikan supaya teksturnya kembali lembut.</li>
            <li class="flex gap-2.5"><i data-lucide="package" class="w-4 h-4 text-cocoa-300 shrink-0 mt-0.5"></i> Kue kering dalam toples tertutup tahan sampai 3 minggu di suhu ruang.</li>
          </ul>
        </div>
      </div>

      <div x-show="tab==='ulasan'" x-cloak role="tabpanel">
        <div id="ulasan-ringkas" class="grid sm:grid-cols-[auto_1fr] gap-8 items-center mb-8 pb-8 border-b border-cream-200"></div>
        <div id="ulasan-list" class="space-y-5"></div>
        <div id="ulasan-kosong" class="hidden text-center py-10">
          <span class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-cream-100 text-cocoa-300 mb-4"><i data-lucide="message-square-dashed" class="w-6 h-6"></i></span>
          <p class="font-display font-semibold text-cocoa-700 mb-1">Belum ada ulasan untuk produk ini</p>
          <p class="text-sm text-cocoa-400 mb-5">Jadilah yang pertama memberi ulasan setelah pesanan Anda selesai.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- STICKY CTA MOBILE START -->
  <div class="sticky-cta -mx-4 sm:-mx-6 mt-10 flex items-center gap-3" x-data="{}">
    <div class="min-w-0 flex-1">
      <p class="text-[11px] text-cocoa-300 truncate" id="sticky-nama"></p>
      <p class="font-display font-bold text-rose-600" id="sticky-harga"></p>
    </div>
    <button type="button" id="sticky-tambah" data-js-handler class="btn btn-outline btn-sm shrink-0 tap">
      <i data-lucide="shopping-bag" class="w-4 h-4"></i> <span class="hidden min-[400px]:inline">Keranjang</span>
    </button>
    <button type="button" id="sticky-beli" data-js-handler class="btn btn-primary btn-sm shrink-0 tap">Beli</button>
  </div>
  <!-- STICKY CTA MOBILE END -->

  <!-- PRODUK TERKAIT -->
  <div class="mt-12 sm:mt-16">
    <div class="flex items-end justify-between gap-4 mb-6">
      <h2 class="font-display font-bold text-2xl text-cocoa-700">Sering dipesan bersamaan</h2>
      <a href="{{ route('catalog') }}" class="btn btn-ghost btn-sm">Lihat katalog <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
    </div>
    <div id="grid-terkait" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6"></div>
  </div>
</section>

<!-- FORM ADD TO CART TERSEMBUNYI (PENGHUBUNG KE LARAVEL) -->
<form id="form-cart-laravel" action="{{ route('cart.store') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="product_id" id="cart-product-id">
    <input type="hidden" name="qty" id="cart-qty">
    <input type="hidden" name="variant" id="cart-variant">
    <input type="hidden" name="action" id="cart-action" value="cart">
</form>

@endsection

@push('scripts')
@php
    // Mapping produk tunggal dari Database ke format yang dikenali script JS bawaan Anda
    $p = $product;
    $mappedP = [
        'id' => $p->id, 'slug' => $p->slug, 'nama' => $p->name, 'kategori' => $p->category->name ?? '',
        'harga' => $p->price, 'hargaCoret' => $p->compare_at_price, 'rating' => (float)$p->rating_avg,
        'ulasan' => $p->rating_count, 'stok' => $p->stock_status, 'po' => $p->min_preorder_days,
        'terjual' => $p->sold_count, 'bestSeller' => $p->is_best_seller, 'berat' => $p->weight_label,
        'dibuat' => $p->created_at ? $p->created_at->format('Y-m-d') : date('Y-m-d'),
        'desc' => $p->description, 'bahan' => $p->ingredients, 'simpan' => $p->storage_note,
        'varian' => is_array($p->variant_options) && count($p->variant_options) > 0 ? $p->variant_options : ['Original']
    ];

    // Mapping produk terkait dari Database
    $mappedRelated = $relatedProducts->map(function($rp) {
        return [
            'id' => $rp->id, 'slug' => $rp->slug, 'nama' => $rp->name, 'kategori' => $rp->category->name ?? '',
            'harga' => $rp->price, 'hargaCoret' => $rp->compare_at_price, 'rating' => (float)$rp->rating_avg,
            'ulasan' => $rp->rating_count, 'stok' => $rp->stock_status, 'po' => $rp->min_preorder_days,
            'terjual' => $rp->sold_count, 'bestSeller' => $rp->is_best_seller, 'berat' => $rp->weight_label,
            'dibuat' => $rp->created_at ? $rp->created_at->format('Y-m-d') : date('Y-m-d'),
            'desc' => $rp->description, 'bahan' => $rp->ingredients, 'simpan' => $rp->storage_note,
            'varian' => is_array($rp->variant_options) && count($rp->variant_options) > 0 ? $rp->variant_options : ['Original']
        ];
    });
@endphp

<script>
  // 1. Data dari Laravel MySQL disuntikkan ke variabel JS
  const P = {!! json_encode($mappedP) !!};
  const TERKAIT = {!! json_encode($mappedRelated) !!};

  // --- TAMBAHAN SINKRONISASI DATA DUMMY ---
  if (typeof PRODUK !== 'undefined') {
      const dataAsli = PRODUK.find(x => x.slug === P.slug);
      if (dataAsli) {
          P.rating = dataAsli.rating; P.ulasan = dataAsli.ulasan; P.terjual = dataAsli.terjual;
      }
      // Sinkronisasi untuk produk terkait agar tidak 0.0
      TERKAIT.forEach(rp => {
          const rpAsli = PRODUK.find(x => x.slug === rp.slug);
          if (rpAsli) {
              rp.rating = rpAsli.rating; rp.ulasan = rpAsli.ulasan; rp.terjual = rpAsli.terjual;
          }
      });
  }
  // ----------------------------------------

  let VARIAN = P.varian[0];

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

  // 2. Fungsi Kartu Produk Lokal (Versi FULL 100% Asli)
  window.kartuProduk = function(prod) {
    const habis = prod.stok === 'habis';
    const diskon = prod.hargaCoret ? Math.round((1 - prod.harga / prod.hargaCoret) * 100) : 0;
    const urlDetail = `{{ url('/produk') }}/${prod.slug}`;
    
    return `
    <article class="card card-hover overflow-hidden flex flex-col group h-full">
      <a href="${urlDetail}" class="relative block aspect-[4/3] overflow-hidden bg-cream-100"
         aria-label="Lihat detail ${prod.nama}">
        <img src="${imgProduk(prod.slug)}" alt="${prod.nama}" loading="lazy"
             class="w-full h-full object-cover transition duration-500 group-hover:scale-[1.06] ${habis ? 'grayscale opacity-70' : ''}">
        <span class="absolute top-3 left-3 flex flex-col gap-1.5 items-start">
          ${prod.bestSeller ? '<span class="badge badge-gold shadow-sm"><i data-lucide="flame" class="w-3 h-3"></i> Best Seller</span>' : ''}
          ${diskon ? `<span class="badge badge-rose shadow-sm">Hemat ${diskon}%</span>` : ''}
        </span>
        ${habis ? '<span class="absolute inset-x-0 bottom-0 bg-cocoa-700/85 text-white text-xs font-semibold text-center py-2">Stok Habis &mdash; buka PO minggu depan</span>' : ''}
      </a>
      <div class="p-4 sm:p-5 flex flex-col flex-1">
        <div class="flex items-center gap-2 mb-2">
          <span class="badge badge-neutral">${prod.kategori}</span>
          <span class="flex items-center gap-1 text-xs text-cocoa-400">
            <i data-lucide="star" class="w-3.5 h-3.5 star fill-current"></i>${prod.rating.toFixed(1)}
            <span class="text-cocoa-300">(${prod.ulasan})</span>
          </span>
        </div>
        <h3 class="font-display font-semibold text-cocoa-700 leading-snug mb-1">
          <a href="${urlDetail}" class="hover:text-rose-600 transition">${prod.nama}</a>
        </h3>
        <p class="text-xs text-cocoa-300 mb-3">${prod.berat} &middot; PO min. H-${prod.po}</p>
        <div class="mt-auto">
          <div class="flex items-baseline gap-2 mb-3">
            <span class="font-display font-bold text-lg text-rose-600">${rp(prod.harga)}</span>
            ${prod.hargaCoret ? `<span class="text-xs text-cocoa-300 line-through">${rp(prod.hargaCoret)}</span>` : ''}
          </div>
          <div class="flex gap-2">
            <a href="${urlDetail}" class="btn btn-outline btn-sm flex-1">Detail</a>
            ${habis
              ? `<button type="button" class="btn btn-primary btn-sm flex-1 is-disabled" disabled aria-disabled="true">Habis</button>`
              : `<button type="button" class="btn btn-primary btn-sm flex-1" onclick="tambahKeranjang('${prod.id}', 1, '${prod.varian[0] || ''}')" aria-label="Tambah ${prod.nama} ke keranjang">
                   <i data-lucide="plus" class="w-4 h-4"></i> Keranjang</button>`}
          </div>
        </div>
      </div>
    </article>`;
  }

  // 3. Modifikasi fungsi utama Alpine
  function detailProduk() {
    return {
      qty: 1, habis: false,
      init() {
        VARIAN = P.varian[0];
        this.habis = P.stok === 'habis';
        this.$watch('qty', v => { if (!v || v < 1) this.qty = 1; });
      },
      get totalRp() { return rp(P.harga * (this.qty || 1)); },
      
      tambah() {
        if (this.habis) { toast(P.nama + ' sedang habis.', 'error'); return; }
        // Panggil fungsi AJAX secara langsung (Tanpa Refresh)
        window.tambahKeranjang(P.id, this.qty, VARIAN);
      },
      beli() {
        if (this.habis) { toast(P.nama + ' sedang habis.', 'error'); return; }
        // Tombol Beli Langsung tetap pakai .submit() agar langsung pindah halaman
        document.getElementById('cart-product-id').value = P.id;
        document.getElementById('cart-qty').value = this.qty;
        document.getElementById('cart-variant').value = VARIAN;
        document.getElementById('cart-action').value = 'checkout';
        document.getElementById('form-cart-laravel').submit();
      }
    };
  }

  // 4. Kodingan Vanilla JS Asli Anda
  function tanggalPO(h) {
    const d = new Date(); d.setDate(d.getDate() + h);
    return tglID(d.toISOString().slice(0,10), true);
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.title = P.nama + ' · 4G Cake & Cookies';
    document.getElementById('bc-kategori').textContent = P.kategori;
    document.getElementById('bc-nama').textContent = P.nama;

    /* Galeri */
    const foto = [imgProduk(P.slug), imgProduk(P.slug + '-2'), imgProduk(P.slug + '-3'), imgProduk(P.slug + '-4')];
    const utama = document.getElementById('galeri-utama');
    utama.src = foto[0]; utama.alt = P.nama;
    document.getElementById('galeri-thumb').innerHTML = foto.map((f,i) => `
      <button type="button" data-i="${i}" data-js-handler class="thumb card overflow-hidden aspect-square bg-cream-100 ${i===0 ? 'ring-2 ring-rose-400' : ''}">
        <img src="${f}" alt="${P.nama} tampilan ${i+1}" class="w-full h-full object-cover"></button>`).join('');
    document.querySelectorAll('.thumb').forEach(b => b.addEventListener('click', () => {
      utama.src = foto[b.dataset.i];
      document.querySelectorAll('.thumb').forEach(x => x.classList.remove('ring-2','ring-rose-400'));
      b.classList.add('ring-2','ring-rose-400');
    }));

    /* Info */
    document.getElementById('p-kategori').textContent = P.kategori;
    document.getElementById('p-best').classList.toggle('hidden', !P.bestSeller);
    const stok = document.getElementById('p-stok');
    stok.className = 'badge ' + (P.stok === 'habis' ? 'badge-cancel' : 'badge-done');
    stok.innerHTML = `<span class="badge-dot"></span> ${P.stok === 'habis' ? 'Stok habis' : 'Siap dipesan'}`;
    document.getElementById('p-nama').textContent = P.nama;
    document.getElementById('p-rating').innerHTML =
      `${starsHTML(P.rating)} <span class="font-semibold text-cocoa-700">${P.rating.toFixed(1)}</span>
       <span class="text-cocoa-300">(${P.ulasan} ulasan)</span>`;
    document.getElementById('p-terjual').textContent = P.terjual + ' terjual';
    document.getElementById('p-harga').textContent = rp(P.harga);
    const coret = document.getElementById('p-coret'), hemat = document.getElementById('p-hemat');
    if (P.hargaCoret) {
      coret.textContent = rp(P.hargaCoret);
      hemat.textContent = 'Hemat ' + Math.round((1 - P.harga / P.hargaCoret) * 100) + '%';
    } else { coret.classList.add('hidden'); hemat.classList.add('hidden'); }
    document.getElementById('p-desc').textContent = P.desc;
    document.getElementById('p-berat').textContent = P.berat;
    document.getElementById('p-simpan').textContent = P.simpan.split('.')[0];
    document.getElementById('p-po-h').textContent = 'pesan minimal H-' + P.po;
    document.getElementById('p-po-tgl').textContent = tanggalPO(P.po);

    document.getElementById('p-varian').innerHTML = P.varian.map((v,i) => `
      <button type="button" data-v="${v}" data-js-handler
        class="varian shrink-0 rounded-full border px-4 py-2 text-sm font-medium transition ${i===0
          ? 'bg-cocoa-500 border-cocoa-500 text-white' : 'bg-white border-cream-200 text-cocoa-500 hover:border-rose-300'}">${v}</button>`).join('');
    document.querySelectorAll('.varian').forEach(b => b.addEventListener('click', () => {
      document.querySelectorAll('.varian').forEach(x => {
        x.className = 'varian shrink-0 rounded-full border px-4 py-2 text-sm font-medium transition bg-white border-cream-200 text-cocoa-500 hover:border-rose-300';
      });
      b.className = 'varian shrink-0 rounded-full border px-4 py-2 text-sm font-medium transition bg-cocoa-500 border-cocoa-500 text-white';
      VARIAN = b.dataset.v;
    }));

    /* Tab deskripsi & PO */
    document.getElementById('tab-desc').textContent = P.desc;
    document.getElementById('tab-bahan').textContent = P.bahan;
    document.getElementById('tab-simpan').textContent = P.simpan;
    document.getElementById('tab-po').textContent =
      `${P.nama} membutuhkan waktu persiapan minimal ${P.po} hari (H-${P.po}) sebelum tanggal ambil atau kirim yang Anda pilih.`;

    /* Ulasan */
    const ul = REVIEWS.filter(r => r.slug === P.slug);
    
    // 1. Gunakan P.ulasan (angka 52) sebagai patokan agar SINKRON dengan Katalog & Beranda
    document.getElementById('tab-ulasan-n').textContent = '(' + P.ulasan + ')';
    
    const list = document.getElementById('ulasan-list');
    const ringkas = document.getElementById('ulasan-ringkas');
    
    if (P.ulasan === 0) {
      document.getElementById('ulasan-kosong').classList.remove('hidden');
      ringkas.classList.add('hidden');
    } else {
      // 2. Buat simulasi distribusi bar bintang yang logis berdasarkan angka P.ulasan
      let d5 = 0, d4 = 0, d3 = 0, d2 = 0, d1 = 0;
      if (P.rating >= 4.8) { d5 = Math.round(P.ulasan * 0.85); d4 = P.ulasan - d5; }
      else if (P.rating >= 4.5) { d5 = Math.round(P.ulasan * 0.7); d4 = Math.round(P.ulasan * 0.2); d3 = P.ulasan - d5 - d4; }
      else { d5 = Math.round(P.ulasan * 0.5); d4 = Math.round(P.ulasan * 0.3); d3 = P.ulasan - d5 - d4; }
      
      const dist = [
          { b: 5, n: d5 }, { b: 4, n: d4 }, { b: 3, n: d3 }, { b: 2, n: d2 }, { b: 1, n: d1 }
      ];

      // 3. Render Ringkasan Bintang
      ringkas.innerHTML = `
        <div class="text-center sm:pr-8 sm:border-r border-cream-200">
          <p class="font-display font-bold text-5xl text-cocoa-700">${P.rating.toFixed(1)}</p>
          <div class="flex justify-center my-2">${starsHTML(P.rating)}</div>
          <p class="text-xs text-cocoa-300">${P.ulasan} penilaian</p>
        </div>
        <div class="space-y-1.5">
          ${dist.map(d => `
            <div class="flex items-center gap-3 text-xs">
              <span class="w-8 text-cocoa-400">${d.b} <i data-lucide="star" class="inline w-3 h-3 star fill-current"></i></span>
              <span class="flex-1 h-2 rounded-full bg-cream-200 overflow-hidden">
                <span class="block h-full rounded-full bg-gold-400" style="width:${P.ulasan ? (d.n/P.ulasan*100) : 0}%"></span>
              </span>
              <span class="w-6 text-right text-cocoa-300">${d.n}</span>
            </div>`).join('')}
        </div>`;
      
      // 4. Render komentar teks yang benar-benar ada
      list.innerHTML = ul.map(r => `
        <article class="rounded-2xl bg-cream-50 border border-cream-200 p-5">
          <div class="flex items-start gap-3 mb-3">
            <span class="grid place-items-center w-10 h-10 rounded-full bg-rose-500 text-white text-xs font-semibold shrink-0">${r.nama.split(' ').map(w=>w[0]).slice(0,2).join('')}</span>
            <div class="min-w-0 flex-1">
              <p class="font-semibold text-sm text-cocoa-700">${r.nama}</p>
              <p class="text-[11px] text-cocoa-300">${tglID(r.tgl)}</p>
            </div>
            <div>${starsHTML(r.bintang, 'w-3.5 h-3.5')}</div>
          </div>
          <p class="text-sm text-cocoa-400 leading-relaxed mb-3">${r.teks}</p>
          <div class="flex flex-wrap gap-2 text-[11px]">
            <span class="badge badge-neutral">Rasa ${r.aspek.rasa}/5</span>
            <span class="badge badge-neutral">Kualitas ${r.aspek.kualitas}/5</span>
            <span class="badge badge-neutral">Packaging ${r.aspek.packaging}/5</span>
            <span class="badge badge-neutral">Pelayanan ${r.aspek.pelayanan}/5</span>
          </div>
        </article>`).join('');
        
      // 5. Tambahkan teks penjelasan ala e-commerce asli
      if (ul.length < P.ulasan && ul.length > 0) {
          list.innerHTML += `<p class="text-center text-xs text-cocoa-400 mt-6 pt-6 border-t border-cream-200">Menampilkan ${ul.length} ulasan dengan komentar dari total ${P.ulasan} penilaian pembeli.</p>`;
      }
    }

    /* Terkait (Menggunakan fungsi kartuProduk orisinal 100%) */
    document.getElementById('grid-terkait').innerHTML = TERKAIT.map(p => kartuProduk(p)).join('');

    /* Sticky CTA mobile */
    document.getElementById('sticky-nama').textContent = P.nama;
    document.getElementById('sticky-harga').textContent = rp(P.harga);
    const habis = P.stok === 'habis';
    
    document.getElementById('sticky-tambah').addEventListener('click', () => {
        if (habis) { toast(P.nama + ' sedang habis.', 'error'); return; }
        
        window.tambahKeranjang(P.id, 1, VARIAN);
    });
    
    document.getElementById('sticky-beli').addEventListener('click', () => {
      if (habis) { toast(P.nama + ' sedang habis.', 'error'); return; }
        document.getElementById('cart-product-id').value = P.id;
        document.getElementById('cart-qty').value = 1;
        document.getElementById('cart-variant').value = VARIAN;
        document.getElementById('cart-action').value = 'checkout';
        document.getElementById('form-cart-laravel').submit();
    });

    icons();
  });
</script>
@endpush
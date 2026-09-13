@extends('layouts.main')

@section('title', $product->name . ' &middot; 4G Cake & Cookies')

@section('content')
  @php
    $p = $product;
    $photosDb = $p->photos;
    if (is_string($photosDb))
      $photosDb = json_decode($photosDb, true);
    if (!is_array($photosDb) || empty($photosDb))
      $photosDb = $p->photo_main ? [$p->photo_main] : [];
    $fotoPreviews = array_map(function ($path) {
      return asset('storage/' . $path);
    }, $photosDb);
    if (empty($fotoPreviews))
      $fotoPreviews = [asset('assets/img/products/placeholder.svg')];

    $varian = $p->variant_options;
    if (is_string($varian))
      $varian = json_decode($varian, true);
    if (!is_array($varian) || empty($varian)) {
      $varian = [['name' => 'Original', 'price' => $p->price]];
    } else {
      if (isset($varian[0]) && !is_array($varian[0])) {
        $newVar = [];
        foreach ($varian as $vStr)
          $newVar[] = ['name' => $vStr, 'price' => $p->price];
        $varian = $newVar;
      }
    }

    $opsiVarianProduk = array_column($varian, 'name');
    $defaultVarian = count($opsiVarianProduk) > 0 ? $opsiVarianProduk[0] : 'Original';

    $terjualAsli = \Illuminate\Support\Facades\DB::table('order_items')
      ->join('orders', 'order_items.order_id', '=', 'orders.id')
      ->where('order_items.product_id', $p->id)
      ->where('orders.status', 'selesai')
      ->sum('order_items.quantity');

    $mappedReviews = [];
    $ulasanCount = 0;
    $ulasanAvg = 0;

    if (\Illuminate\Support\Facades\Schema::hasTable('reviews')) {
      $reviewsQuery = \Illuminate\Support\Facades\DB::table('reviews')
        ->join('order_items', 'reviews.order_item_id', '=', 'order_items.id')
        ->leftJoin('users', 'reviews.user_id', '=', 'users.id')
        ->where('order_items.product_id', $p->id)
        ->where('reviews.is_hidden', false) // MENYEMBUNYIKAN ULASAN YANG DI-HIDE ADMIN
        ->select('reviews.*', 'users.name as user_name')
        ->orderBy('reviews.created_at', 'desc');

      $orderItemCols = \Illuminate\Support\Facades\Schema::hasTable('order_items') ? \Illuminate\Support\Facades\Schema::getColumnListing('order_items') : [];
      foreach ($orderItemCols as $col) {
        $reviewsQuery->addSelect('order_items.' . $col . ' as oi_' . $col);
      }

      $allReviews = $reviewsQuery->get();
      $ulasanCount = $allReviews->count();
      $ulasanAvg = $ulasanCount > 0 ? $allReviews->avg('rating_overall') : 0;

      $mappedReviews = $allReviews->map(function ($r) use ($opsiVarianProduk, $defaultVarian) {
        $namaPelanggan = $r->user_name ?? 'Pelanggan';
        $varianBeli = null;
        $opsiLower = array_map('strtolower', array_map('trim', $opsiVarianProduk));

        foreach ((array) $r as $key => $val) {
          if (!empty($val) && is_string($val)) {
            $valLower = strtolower(trim($val));
            $index = array_search($valLower, $opsiLower);
            if ($index !== false) {
              $varianBeli = $opsiVarianProduk[$index];
              break;
            }
            if ((str_starts_with(trim($val), '{') && str_ends_with(trim($val), '}')) || (str_starts_with(trim($val), '[') && str_ends_with(trim($val), ']'))) {
              $decoded = json_decode($val, true);
              if (is_array($decoded)) {
                $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($decoded));
                foreach ($iterator as $jVal) {
                  if (is_string($jVal)) {
                    $idx = array_search(strtolower(trim($jVal)), $opsiLower);
                    if ($idx !== false) {
                      $varianBeli = $opsiVarianProduk[$idx];
                      break 2;
                    }
                  }
                }
              }
            }
          }
        }

        if (!$varianBeli) {
          $varianBeli = $r->oi_variant ?? ($r->oi_varian ?? ($r->variant ?? ($r->varian ?? null)));
        }
        if (empty(trim($varianBeli)) || strtolower(trim($varianBeli)) === 'null') {
          $varianBeli = $defaultVarian;
        }
        if (strtolower(trim($varianBeli)) === 'original' && !in_array('original', $opsiLower)) {
          $varianBeli = $defaultVarian;
        }

        $paletWarna = [
          'from-rose-400 to-rose-600',
          'from-blue-400 to-blue-600',
          'from-emerald-400 to-emerald-600',
          'from-amber-400 to-amber-600',
          'from-purple-400 to-purple-600',
          'from-teal-400 to-teal-600',
          'from-indigo-400 to-indigo-600',
          'from-fuchsia-400 to-fuchsia-600',
        ];

        $indeksWarna = abs(crc32($namaPelanggan)) % count($paletWarna);

        return [
          'nama' => $namaPelanggan,
          'bintang' => (int) ($r->rating_overall ?? 5),
          'teks' => $r->comment ?? '',
          'balasan' => $r->admin_reply ?? null, // MENARIK TEKS BALASAN DARI ADMIN
          'tgl' => isset($r->created_at) ? \Carbon\Carbon::parse($r->created_at)->format('Y-m-d') : date('Y-m-d'),
          'varian' => $varianBeli,
          'avatar' => strtoupper(substr($namaPelanggan, 0, 2)),
          'warna' => $paletWarna[$indeksWarna]
        ];
      })->values()->all();
    }

    $mappedP = [
      'id' => $p->id,
      'slug' => $p->slug,
      'nama' => $p->name,
      'kategori' => $p->category->name ?? '',
      'harga' => $p->price,
      'hargaCoret' => $p->compare_at_price,
      'rating' => (float) $ulasanAvg,
      'ulasan' => (int) $ulasanCount,
      'terjual' => (int) $terjualAsli,
      'stok' => $p->stock_status,
      'po' => $p->min_preorder_days,
      'bestSeller' => $p->is_best_seller,
      'berat' => $p->weight_label ?? '500 gram',
      'desc' => $p->description,
      'bahan' => $p->ingredients ?? '',
      'simpan' => $p->storage_note ?? '',
      'pengiriman' => $p->delivery_info ?? 'J&T / ambil sendiri',
      'kondisi' => $p->condition_info ?? 'Dibuat setelah dipesan',
      'fotoPreviews' => $fotoPreviews,
      'varian' => $varian,
      'daftarUlasan' => $mappedReviews
    ];

    $mappedRelated = $relatedProducts->map(function ($rp) {
      $r_terjual = \Illuminate\Support\Facades\DB::table('order_items')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->where('order_items.product_id', $rp->id)
        ->where('orders.status', 'selesai')
        ->sum('order_items.quantity');

      $r_ulasan_count = 0;
      $r_ulasan_avg = 0;

      if (\Illuminate\Support\Facades\Schema::hasTable('reviews')) {
        $r_ulasan = \Illuminate\Support\Facades\DB::table('reviews')
          ->join('order_items', 'reviews.order_item_id', '=', 'order_items.id')
          ->where('order_items.product_id', $rp->id)
          ->where('reviews.is_hidden', false);

        $r_ulasan_count = $r_ulasan->count();
        $r_ulasan_avg = $r_ulasan_count > 0 ? $r_ulasan->avg('rating_overall') : 0;
      }

      $rpPhotos = $rp->photos;
      if (is_string($rpPhotos))
        $rpPhotos = json_decode($rpPhotos, true);
      if (!is_array($rpPhotos) || empty($rpPhotos))
        $rpPhotos = $rp->photo_main ? [$rp->photo_main] : [];
      $rpFoto = !empty($rpPhotos) ? asset('storage/' . $rpPhotos[0]) : asset('assets/img/products/placeholder.svg');

      return [
        'id' => $rp->id,
        'slug' => $rp->slug,
        'nama' => $rp->name,
        'kategori' => $rp->category->name ?? '',
        'harga' => $rp->price,
        'hargaCoret' => $rp->compare_at_price,
        'rating' => (float) $r_ulasan_avg,
        'ulasan' => (int) $r_ulasan_count,
        'terjual' => (int) $r_terjual,
        'stok' => $rp->stock_status,
        'po' => $rp->min_preorder_days,
        'bestSeller' => $rp->is_best_seller,
        'berat' => $rp->weight_label,
        'foto' => $rpFoto,
      ];
    });
  @endphp

  <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6 sm:py-10" x-data="detailProduk()" x-init="init()">
    <nav class="flex items-center gap-2 text-xs text-cocoa-300 mb-6 flex-wrap" aria-label="Breadcrumb">
      <a href="{{ route('home') }}" class="hover:text-rose-600 transition">Beranda</a>
      <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
      <a href="{{ route('catalog') }}" class="hover:text-rose-600 transition">Katalog</a>
      <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
      <span class="hover:text-rose-600" x-text="P.kategori"></span>
      <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
      <span class="text-cocoa-500 font-medium" x-text="P.nama"></span>
    </nav>

    <div class="grid lg:grid-cols-2 gap-8 lg:gap-14">
      <div class="lg:sticky lg:top-28 lg:self-start">
        <div class="card overflow-hidden aspect-[4/3] bg-cream-100 border border-cream-200">
          <img :src="fotoAktif"
            onerror="this.onerror=null; this.src='{{ asset('assets/img/products/placeholder.svg') }}';" alt="Foto Produk"
            class="w-full h-full object-cover transition duration-300">
        </div>
        <div class="grid grid-cols-4 gap-3 mt-3">
          <template x-for="(f, i) in P.fotoPreviews" :key="i">
            <button type="button" @click="fotoAktif = f"
              class="thumb card overflow-hidden aspect-square bg-cream-100 transition border border-cream-200"
              :class="fotoAktif === f ? 'ring-2 ring-rose-400' : ''">
              <img :src="f" onerror="this.onerror=null; this.src='{{ asset('assets/img/products/placeholder.svg') }}';"
                class="w-full h-full object-cover">
            </button>
          </template>
        </div>
        <div class="hidden sm:flex items-center gap-2 mt-4 text-xs text-cocoa-300">
          <i data-lucide="camera" class="w-4 h-4"></i> Foto ilustrasi. Tampilan asli menyesuaikan pesanan.
        </div>
      </div>

      <div>
        <div class="flex flex-wrap items-center gap-2 mb-3">
          <span class="badge badge-neutral" x-text="P.kategori"></span>
          <span class="badge badge-gold" x-show="P.bestSeller"><i data-lucide="flame" class="w-3 h-3"></i> Best
            Seller</span>
          <span class="badge" :class="P.stok === 'habis' ? 'badge-cancel' : 'badge-done'">
            <span class="badge-dot" :class="P.stok === 'habis' ? 'bg-rose-500' : 'bg-green-500'"></span> <span
              x-text="P.stok === 'habis' ? 'Stok habis' : 'Siap dipesan'"></span>
          </span>
        </div>

        <h1 class="font-display font-bold text-3xl sm:text-4xl text-cocoa-700 leading-tight mb-3" x-text="P.nama"></h1>

        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mb-5 text-sm">
          <span class="flex items-center gap-2">
            <span x-html="typeof starsHTML === 'function' ? starsHTML(P.rating) : ''"></span>
            <span class="font-semibold text-cocoa-700" x-text="P.rating.toFixed(1)"></span>
            <span class="text-cocoa-300">(<span x-text="P.ulasan"></span> ulasan)</span>
          </span>
          <span class="divider-dot"></span>
          <span class="text-cocoa-400" x-text="P.terjual + ' terjual'"></span>
        </div>

        <div class="flex flex-wrap items-baseline gap-x-3 gap-y-2 mb-5">
          <span class="font-display font-bold text-3xl text-rose-600" x-text="formatRupiah(hargaAktif)"></span>
          <span class="text-sm text-cocoa-300 line-through" x-show="P.hargaCoret"
            x-text="P.hargaCoret ? formatRupiah(P.hargaCoret) : ''"></span>
          <span class="badge badge-rose" x-show="P.hargaCoret"
            x-text="P.hargaCoret ? 'Hemat ' + Math.round((1 - hargaAktif / P.hargaCoret) * 100) + '%' : ''"></span>
        </div>

        <p class="text-cocoa-400 leading-relaxed mb-6" x-text="P.desc"></p>

        <div class="rounded-2xl border border-gold-300/60 bg-[#FDF8EA] p-4 sm:p-5 mb-6 flex gap-3.5">
          <span class="grid place-items-center w-10 h-10 rounded-xl bg-gold-400/20 text-gold-600 shrink-0"><i
              data-lucide="calendar-clock" class="w-5 h-5"></i></span>
          <div>
            <p class="font-semibold text-sm text-cocoa-700 mb-1">Produk pre-order &mdash; pesan minimal H-<span
                x-text="P.po"></span></p>
            <p class="text-sm text-cocoa-400 leading-relaxed">Tanggal ambil atau kirim paling cepat <span
                class="font-semibold text-cocoa-500" x-text="tanggalPO(P.po)"></span>.</p>
          </div>
        </div>

        <div class="mb-6"
          x-show="P.varian.length > 1 || (P.varian.length === 1 && P.varian[0].name.toLowerCase() !== 'original')">
          <p class="label">Pilih varian / ukuran</p>
          <div class="flex flex-wrap gap-2">
            <template x-for="(v, index) in P.varian" :key="index">
              <button type="button" @click="varianAktif = v"
                class="shrink-0 rounded-full border px-4 py-2 text-sm font-medium transition"
                :class="varianAktif.name === v.name ? 'bg-cocoa-500 border-cocoa-500 text-white' : 'bg-white border-cream-200 text-cocoa-500 hover:border-rose-300'"
                x-text="v.name"></button>
            </template>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 mb-4">
          <div class="inline-flex items-center rounded-full border border-cream-200 bg-white">
            <button type="button" @click="qty = Math.max(1, qty-1)"
              class="grid place-items-center w-11 h-11 rounded-full text-cocoa-500 hover:bg-cream-100 transition"><i
                data-lucide="minus" class="w-4 h-4"></i></button>
            <input x-model.number="qty" type="number" min="1" max="99"
              class="w-12 text-center bg-transparent font-semibold text-cocoa-700 outline-none">
            <button type="button" @click="qty = Math.min(99, qty+1)"
              class="grid place-items-center w-11 h-11 rounded-full text-cocoa-500 hover:bg-cream-100 transition"><i
                data-lucide="plus" class="w-4 h-4"></i></button>
          </div>
          <p class="text-sm text-cocoa-400">Total <span class="font-display font-bold text-cocoa-700"
              x-text="formatRupiah(hargaAktif * qty)"></span></p>
        </div>

        <div class="grid sm:grid-cols-2 gap-3 mb-6">
          <button type="button" @click="tambah()" :class="habis && 'is-disabled'" class="btn btn-outline btn-lg"><i
              data-lucide="shopping-bag" class="w-[18px] h-[18px]"></i> Tambah Keranjang</button>
          <button type="button" @click="beli()" :class="habis && 'is-disabled'" class="btn btn-primary btn-lg"><i
              data-lucide="zap" class="w-[18px] h-[18px]"></i> Beli Sekarang</button>
        </div>

        <div class="grid sm:grid-cols-2 gap-x-6 gap-y-3 py-5 border-y border-cream-200 text-sm">
          <div class="flex items-center gap-2.5"><i data-lucide="scale" class="w-4 h-4 text-cocoa-300"></i><span
              class="text-cocoa-400">Ukuran</span><span class="ml-auto font-medium text-cocoa-600"
              x-text="P.berat"></span></div>
          <div class="flex items-center gap-2.5"><i data-lucide="thermometer-snowflake"
              class="w-4 h-4 text-cocoa-300"></i><span class="text-cocoa-400">Simpan</span><span
              class="ml-auto font-medium text-cocoa-600 text-right"
              x-text="P.simpan ? P.simpan.split('.')[0] : 'Suhu Ruang'"></span></div>
          <div class="flex items-center gap-2.5"><i data-lucide="truck" class="w-4 h-4 text-cocoa-300"></i><span
              class="text-cocoa-400">Pengiriman</span><span class="ml-auto font-medium text-cocoa-600"
              x-text="P.pengiriman"></span></div>
          <div class="flex items-center gap-2.5"><i data-lucide="badge-check" class="w-4 h-4 text-cocoa-300"></i><span
              class="text-cocoa-400">Kondisi</span><span class="ml-auto font-medium text-cocoa-600"
              x-text="P.kondisi"></span></div>
        </div>
      </div>
    </div>

    <div id="ulasan" x-data="{ tab: window.location.hash.includes('ulasan') ? 'ulasan' : 'deskripsi' }"
      class="card mt-12 sm:mt-16 overflow-hidden scroll-mt-24">
      <div class="flex gap-1 p-2 border-b border-cream-200 overflow-x-auto no-scrollbar" role="tablist">
        <button role="tab" @click="tab='deskripsi'"
          :class="tab==='deskripsi' ? 'bg-cream-100 text-cocoa-700' : 'text-cocoa-400 hover:text-cocoa-600'"
          class="shrink-0 rounded-xl px-4 py-2.5 text-sm font-medium transition">Deskripsi &amp; Bahan</button>
        <button role="tab" @click="tab='po'"
          :class="tab==='po' ? 'bg-cream-100 text-cocoa-700' : 'text-cocoa-400 hover:text-cocoa-600'"
          class="shrink-0 rounded-xl px-4 py-2.5 text-sm font-medium transition">Pre-Order &amp; Penyimpanan</button>
        <button role="tab" @click="tab='ulasan'"
          :class="tab==='ulasan' ? 'bg-cream-100 text-cocoa-700' : 'text-cocoa-400 hover:text-cocoa-600'"
          class="shrink-0 rounded-xl px-4 py-2.5 text-sm font-medium transition">Ulasan (<span
            x-text="P.ulasan"></span>)</button>
      </div>

      <div class="p-6 sm:p-8">
        <div x-show="tab==='deskripsi'" class="max-w-3xl space-y-4 text-cocoa-400 leading-relaxed text-sm sm:text-base">
          <p x-text="P.desc"></p>
          <div class="rounded-2xl bg-cream-100 p-5 !mt-5">
            <h3 class="font-display font-semibold text-cocoa-700 mb-2 flex items-center gap-2"><i data-lucide="list"
                class="w-4 h-4"></i> Bahan yang dipakai</h3>
            <p class="text-sm" x-text="P.bahan ? P.bahan : 'Bahan segar berkualitas tinggi.'"></p>
          </div>
        </div>

        <div x-show="tab==='po'" x-cloak class="max-w-3xl space-y-5 text-sm sm:text-base text-cocoa-400 leading-relaxed">
          <div class="rounded-2xl bg-cream-100 p-5">
            <h3 class="font-display font-semibold text-cocoa-700 mb-2">Ketentuan pre-order</h3>
            <p class="mb-3" x-text="P.nama + ' membutuhkan waktu persiapan minimal H-' + P.po"></p>
          </div>
          <div>
            <h3 class="font-display font-semibold text-cocoa-700 mb-2">Cara menyimpan</h3>
            <ul class="space-y-2">
              <li class="flex gap-2.5"><i data-lucide="refrigerator" class="w-4 h-4 text-cocoa-300 shrink-0 mt-0.5"></i>
                <span x-text="P.simpan ? P.simpan : 'Simpan di wadah tertutup.'"></span>
              </li>
            </ul>
          </div>
        </div>

        <div x-show="tab==='ulasan'" x-cloak role="tabpanel">
          <div id="ulasan-ringkas"
            class="grid sm:grid-cols-[auto_1fr] gap-8 items-center mb-8 pb-8 border-b border-cream-200"></div>
          <div id="ulasan-list" class="space-y-5"></div>
          <div id="ulasan-kosong" class="hidden text-center py-10">
            <span class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-cream-100 text-cocoa-300 mb-4"><i
                data-lucide="message-square-dashed" class="w-6 h-6"></i></span>
            <p class="font-display font-bold text-cocoa-700 mb-1">Belum ada ulasan</p>
            <p class="text-sm text-cocoa-400 mb-5">Jadilah yang pertama memberi ulasan setelah pesanan pelanggan berstatus
              Selesai.</p>
            <a href="{{ route('order.history') }}" class="btn btn-outline btn-sm">Ke pesanan</a>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-12 sm:mt-16">
      <div class="flex items-end justify-between gap-4 mb-6">
        <h2 class="font-display font-bold text-2xl text-cocoa-700">Mungkin Anda suka</h2>
        <a href="{{ route('catalog') }}" class="btn btn-ghost btn-sm">Lihat katalog <i data-lucide="arrow-right"
            class="w-4 h-4"></i></a>
      </div>
      <div id="grid-terkait" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6"></div>
    </div>
  </section>

  <form id="form-cart-laravel" action="{{ route('cart.store') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="product_id" id="cart-product-id">
    <input type="hidden" name="qty" id="cart-qty">
    <input type="hidden" name="variant" id="cart-variant">
    <input type="hidden" name="action" id="cart-action" value="cart">
  </form>
@endsection

@push('scripts')
  <script>
    const IS_LOGGED_IN = @json(auth()->check());
    const P = {!! json_encode($mappedP) !!};
    const TERKAIT = {!! json_encode($mappedRelated) !!};

    function formatRupiah(n) { return 'Rp' + Number(n || 0).toLocaleString('id-ID'); }

    window.tambahKeranjangCepat = function (id, nama, habis, hargaAsli) {
      if (!IS_LOGGED_IN) { window.location.href = '{{ route('login') }}'; return; }
      if (habis) {
        if (typeof toast === 'function') toast(nama + ' sedang habis.', 'error');
        return;
      }

      const url = document.getElementById('form-cart-laravel').getAttribute('action');
      fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({
          product_id: id, qty: 1, variant: 'Original', price: hargaAsli, harga: hargaAsli, action: 'cart'
        })
      })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            if (typeof toast === 'function') toast(nama + ' ditambahkan ke keranjang.', 'success');
            document.querySelectorAll('[data-cart-badge]').forEach(el => {
              el.textContent = data.cart_count; el.classList.remove('hidden');
            });
          }
        }).catch(err => {
          if (typeof toast === 'function') toast('Terjadi kesalahan jaringan.', 'error');
        });
    };

    function detailProduk() {
      return {
        qty: 1, habis: false, varianAktif: null, fotoAktif: P.fotoPreviews[0],
        init() {
          this.varianAktif = P.varian[0];
          this.habis = P.stok === 'habis';
          this.$watch('qty', v => { if (!v || v < 1) this.qty = 1; });
          this.$nextTick(() => { if (typeof icons === 'function') icons() });
        },
        get hargaAktif() { return this.varianAktif ? this.varianAktif.price : P.harga; },

        tanggalPO(h) {
          const d = new Date(); d.setDate(d.getDate() + h);
          return typeof tglID === 'function' ? tglID(d.toISOString().slice(0, 10), true) : d.toISOString().slice(0, 10);
        },

        tambah() {
          if (!IS_LOGGED_IN) { window.location.href = '{{ route('login') }}'; return; }
          if (this.habis) { toast(P.nama + ' sedang habis.', 'error'); return; }
          const url = document.getElementById('form-cart-laravel').getAttribute('action');
          fetch(url, {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({
              product_id: P.id, qty: this.qty, variant: this.varianAktif.name, varian: this.varianAktif.name, price: this.hargaAktif, action: 'cart'
            })
          }).then(res => res.json()).then(data => {
            if (data.status === 'success') {
              if (typeof toast === 'function') toast(data.message || 'Berhasil ditambahkan', 'success');
              document.querySelectorAll('[data-cart-badge]').forEach(el => { el.textContent = data.cart_count; el.classList.remove('hidden'); });
            }
          });
        },

        beli() {
          if (!IS_LOGGED_IN) { window.location.href = '{{ route('login') }}'; return; }
          if (this.habis) return;

          const jalankanBeliSekarang = async () => {
            if (typeof toast === 'function') toast('Memproses pesanan...', 'info');
            try {
              const url = document.getElementById('form-cart-laravel').getAttribute('action');
              let res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({
                  product_id: P.id, qty: this.qty, variant: this.varianAktif.name, varian: this.varianAktif.name, price: this.hargaAktif,
                  action: 'buy_now'
                })
              });
              let data = await res.json();
              if (data.status === 'success' || data.redirect) {
                // PERBAIKAN: Menambahkan bendera penanda khusus buy_now
                window.location.href = "{{ route('checkout.index') }}?mode=buy_now";
              }
            } catch (err) {
              if (typeof toast === 'function') toast('Terjadi kesalahan koneksi.', 'error');
            }
          };

          jalankanBeliSekarang();
        }
      };
    }

    // .. sisa script (kartuProduk, pagination, dsb) tetap sama seperti sebelumnya ..
    window.kartuProduk = function (prod) {
      const habis = prod.stok === 'habis';
      const diskon = prod.hargaCoret ? Math.round((1 - prod.harga / prod.hargaCoret) * 100) : 0;
      const urlDetail = `{{ url('/produk') }}/${prod.slug}`;
      return `
      <article class="card card-hover overflow-hidden flex flex-col group h-full">
        <a href="${urlDetail}" class="relative block aspect-[4/3] overflow-hidden bg-cream-100" aria-label="Lihat detail ${prod.nama}">
          <img src="${prod.foto}" alt="${prod.nama}" onerror="this.onerror=null; this.src='{{ asset('assets/img/products/placeholder.svg') }}';" loading="lazy" class="w-full h-full object-cover transition duration-500 group-hover:scale-[1.06] ${habis ? 'grayscale opacity-70' : ''}">
          <span class="absolute top-3 left-3 flex flex-col gap-1.5 items-start">
            ${prod.bestSeller ? '<span class="badge badge-gold shadow-sm"><i data-lucide="flame" class="w-3 h-3"></i> Best Seller</span>' : ''}
            ${diskon ? `<span class="badge badge-rose shadow-sm">Hemat ${diskon}%</span>` : ''}
          </span>
        </a>
        <div class="p-4 sm:p-5 flex flex-col flex-1">
          <div class="flex items-center gap-2 mb-2">
            <span class="badge badge-neutral">${prod.kategori}</span>
            <span class="flex items-center gap-1 text-xs text-cocoa-400"><i data-lucide="star" class="w-3.5 h-3.5 star fill-current"></i>${parseFloat(prod.rating).toFixed(1)}<span class="text-cocoa-300">(${prod.ulasan})</span></span>
          </div>
          <h3 class="font-display font-semibold text-cocoa-700 leading-snug mb-1"><a href="${urlDetail}">${prod.nama}</a></h3>
          <div class="mt-auto pt-3">
            <div class="flex items-baseline gap-2 mb-3"><span class="font-display font-bold text-lg text-rose-600">${formatRupiah(prod.harga)}</span>${prod.hargaCoret ? `<span class="text-xs text-cocoa-300 line-through">${formatRupiah(prod.hargaCoret)}</span>` : ''}</div>
            <div class="flex gap-2">
              <a href="${urlDetail}" class="btn btn-outline btn-sm flex-1">Detail</a>
              <button type="button" onclick="tambahKeranjangCepat(${prod.id}, '${prod.nama.replace(/'/g, "\\'")}', ${habis}, ${prod.harga})" class="btn btn-primary btn-sm flex-1 ${habis ? 'is-disabled' : ''}" ${habis ? 'disabled' : ''}><i data-lucide="shopping-bag" class="w-[14px] h-[14px]"></i> Tambah</button>
            </div>
          </div>
        </div>
      </article>`;
    }

    document.addEventListener('DOMContentLoaded', () => {
      const gridTerkait = document.getElementById('grid-terkait');
      if (gridTerkait && TERKAIT && TERKAIT.length > 0) gridTerkait.innerHTML = TERKAIT.map(p => window.kartuProduk(p)).join('');

      const ul = P.daftarUlasan || [];
      const list = document.getElementById('ulasan-list');
      const ringkas = document.getElementById('ulasan-ringkas');

      if (!ul.length) {
        document.getElementById('ulasan-kosong').classList.remove('hidden');
        if (ringkas) ringkas.classList.add('hidden');
      } else {
        const dist = [5, 4, 3, 2, 1].map(b => ({ b, n: ul.filter(r => r.bintang === b).length }));
        if (ringkas) {
          ringkas.innerHTML = `
            <div class="text-center sm:pr-8 sm:border-r border-cream-200">
              <p class="font-display font-bold text-5xl text-cocoa-700">${P.rating.toFixed(1)}</p>
              <div class="flex justify-center my-2">${typeof starsHTML === 'function' ? starsHTML(P.rating) : ''}</div>
              <p class="text-xs text-cocoa-300">${P.ulasan} ulasan</p>
            </div>
            <div class="space-y-1.5">
              ${dist.map(d => `
                <div class="flex items-center gap-3 text-xs">
                  <span class="w-8 text-cocoa-400">${d.b} <i data-lucide="star" class="inline w-3 h-3 star fill-current"></i></span>
                  <span class="flex-1 h-2 rounded-full bg-cream-200 overflow-hidden"><span class="block h-full rounded-full bg-gold-400" style="width:${ul.length ? (d.n / ul.length * 100) : 0}%"></span></span>
                  <span class="w-6 text-right text-cocoa-300">${d.n}</span>
                </div>`).join('')}
            </div>`;
        }

        let currentPage = 1; const perPage = 5; const totalPages = Math.ceil(ul.length / perPage) || 1;

        window.renderDaftarUlasan = function () {
          if (!list) return;
          const start = (currentPage - 1) * perPage;
          const end = start + perPage;
          const ulasanDipotong = ul.slice(start, end);

          list.innerHTML = ulasanDipotong.map(r => `
              <article class="rounded-2xl bg-cream-50 border border-cream-200 p-5">
                <div class="flex items-start gap-3 mb-3">
                  <span class="grid place-items-center w-10 h-10 rounded-full bg-gradient-to-br ${r.warna} shadow-inner text-white text-xs font-bold shrink-0 uppercase ring-2 ring-white border border-black/5">${r.avatar}</span>
                  <div class="min-w-0 flex-1"><p class="font-semibold text-sm text-cocoa-700">${r.nama}</p><p class="text-[11px] text-cocoa-300">${typeof tglID === 'function' ? tglID(r.tgl) : r.tgl}</p></div>
                  <div>${typeof starsHTML === 'function' ? starsHTML(r.bintang, 'w-3.5 h-3.5') : ''}</div>
                </div>
                <p class="text-sm text-cocoa-400 leading-relaxed mb-3">${r.teks}</p>
                ${r.balasan ? `<div class="mt-2 mb-4 bg-cream-100/70 p-3.5 rounded-xl border border-cream-200 relative"><p class="text-[11px] font-bold text-cocoa-700 mb-1 flex items-center gap-1.5"><i data-lucide="corner-down-right" class="w-3.5 h-3.5 text-cocoa-400"></i> Tanggapan Pemilik</p><p class="text-sm text-cocoa-600 leading-relaxed">${r.balasan}</p></div>` : ''}
                <div class="flex flex-wrap gap-2 mt-3"><span class="inline-flex items-center gap-1.5 rounded-lg bg-cream-200/50 px-2.5 py-1.5 text-[11px] font-semibold text-cocoa-600 border border-cream-200"><i data-lucide="tag" class="w-3.5 h-3.5 text-cocoa-400"></i> Varian: ${r.varian}</span></div>
              </article>`).join('');

          let wadahBtn = document.getElementById('wadah-btn-more-ulasan');
          if (!wadahBtn) {
            wadahBtn = document.createElement('div');
            wadahBtn.id = 'wadah-btn-more-ulasan';
            wadahBtn.className = 'flex flex-wrap items-center justify-between gap-4 mt-6 pt-5 border-t border-cream-200';
            list.parentNode.insertBefore(wadahBtn, list.nextSibling);
          }
          if (totalPages > 1) {
            wadahBtn.innerHTML = `
                    <p class="text-sm text-cocoa-400">Hal <span class="font-semibold text-cocoa-700">${currentPage}</span> dari <span class="font-semibold text-cocoa-700">${totalPages}</span></p>
                    <div class="flex gap-2">
                      <button type="button" onclick="gantiHalamanUlasan(${currentPage - 1})" class="btn btn-outline btn-sm !px-3 ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}" ${currentPage === 1 ? 'disabled' : ''}><i data-lucide="chevron-left" class="w-4 h-4"></i> Prev</button>
                      <button type="button" onclick="gantiHalamanUlasan(${currentPage + 1})" class="btn btn-outline btn-sm !px-3 ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}" ${currentPage === totalPages ? 'disabled' : ''}>Next <i data-lucide="chevron-right" class="w-4 h-4"></i></button>
                    </div>`;
          } else { wadahBtn.innerHTML = ''; }
          if (typeof icons === 'function') icons();
        };

        window.gantiHalamanUlasan = function (page) {
          if (page < 1 || page > totalPages) return;
          currentPage = page; window.renderDaftarUlasan();
          const ulasanSection = document.getElementById('ulasan-list');
          if (ulasanSection) window.scrollTo({ top: ulasanSection.getBoundingClientRect().top + window.scrollY - 100, behavior: 'smooth' });
        };

        window.renderDaftarUlasan();
      }
      if (typeof icons === 'function') icons();
    });
  </script>
@endpush
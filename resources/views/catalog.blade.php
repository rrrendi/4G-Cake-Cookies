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
            <span class="grid place-items-center w-9 h-9 rounded-xl bg-blush-100 text-rose-600"><i data-lucide="cake"
                class="w-4 h-4"></i></span>
            <div class="leading-tight">
              <p class="font-display font-bold text-cocoa-700" id="stat-total">0</p>
              <p class="text-[11px] text-cocoa-300">Produk aktif</p>
            </div>
          </div>
          <div class="card px-4 py-3 flex items-center gap-3">
            <span class="grid place-items-center w-9 h-9 rounded-xl bg-cream-200 text-gold-600"><i data-lucide="flame"
                class="w-4 h-4"></i></span>
            <div class="leading-tight">
              <p class="font-display font-bold text-cocoa-700" id="stat-best">0</p>
              <p class="text-[11px] text-cocoa-300">Best seller</p>
            </div>
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
          <i data-lucide="search"
            class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-cocoa-300 pointer-events-none"></i>
          <input id="cari" type="search" placeholder="Cari nama kue, misal: brownies, nastar, tiramisu&hellip;"
            class="input !rounded-full !pl-11 !pr-11" autocomplete="off" aria-label="Cari produk">
          <button id="hapus-cari" type="button" data-js-handler
            class="hidden absolute right-3.5 top-1/2 -translate-y-1/2 text-cocoa-300 hover:text-cocoa-500"
            aria-label="Hapus pencarian">
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
        <div id="chips" role="group" aria-label="Filter kategori produk"
          class="flex-1 flex gap-2 overflow-x-auto no-scrollbar py-0.5"></div>
        <button id="reset" type="button" data-js-handler class="hidden sm:inline-flex btn btn-ghost btn-sm shrink-0">
          <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reset
        </button>
      </div>
    </div>
  </div>
  <!-- FILTER BAR END -->

  <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
    <p id="ringkas" class="text-sm text-cocoa-400 mb-5"></p>

    <!-- GRID PRODUK -->
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
      <button type="button" onclick="window.resetFilter()" class="btn btn-primary btn-sm mx-auto">Tampilkan semua
        produk</button>
    </div>
  </section>

  <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pb-8">
    <div class="card p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center gap-5">
      <span class="grid place-items-center w-12 h-12 rounded-2xl bg-blush-100 text-rose-600 shrink-0"><i
          data-lucide="messages-square" class="w-5 h-5"></i></span>
      <div class="flex-1">
        <h2 class="font-display font-semibold text-cocoa-700 mb-1">Tidak menemukan yang dicari?</h2>
        <p class="text-sm text-cocoa-400">Kami menerima pesanan custom: rasa, ukuran, sampai desain kue ulang tahun.</p>
      </div>
      <button type="button" onclick="toast('Form pesanan custom akan tersedia di fase berikutnya.','info')"
        class="btn btn-outline btn-sm shrink-0"><a
          href="https://wa.me/6287714391814?text=Halo%20Admin%204G%20Cake,%20saya%20ingin%20mengajukan%20pesanan%20custom."
          target="_blank" class="...">
          Ajukan pesanan custom
        </a>
    </div>
  </section>

@endsection

@push('scripts')
  @php
    $cols = \Illuminate\Support\Facades\Schema::hasTable('reviews') ? \Illuminate\Support\Facades\Schema::getColumnListing('reviews') : [];
    $hasStatus = in_array('status', $cols);
    $ratingCol = in_array('rating_overall', $cols) ? 'rating_overall' : (in_array('rating', $cols) ? 'rating' : (in_array('bintang', $cols) ? 'bintang' : 'rating_overall'));

    $mappedCatalog = $products->map(function ($p) use ($hasStatus, $ratingCol) {
      $terjualAsli = \Illuminate\Support\Facades\DB::table('order_items')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->where('order_items.product_id', $p->id)
        ->where('orders.status', 'selesai')
        ->sum('order_items.quantity');

      $ulasanCount = 0;
      $ulasanAvg = 0;

      if (\Illuminate\Support\Facades\Schema::hasTable('reviews')) {
        $reviewsQuery = \Illuminate\Support\Facades\DB::table('reviews')
          ->join('order_items', 'reviews.order_item_id', '=', 'order_items.id')
          ->where('order_items.product_id', $p->id);

        if ($hasStatus) {
          try {
            $reviewsQuery->where('reviews.status', 'approved');
          } catch (\Exception $e) {
          }
        }

        $ulasanCount = $reviewsQuery->count();
        $ulasanAvg = $ulasanCount > 0 ? $reviewsQuery->avg($ratingCol) : 0;
      }

      $photosDb = $p->photos;
      if (is_string($photosDb))
        $photosDb = json_decode($photosDb, true);
      if (!is_array($photosDb) || empty($photosDb))
        $photosDb = $p->photo_main ? [$p->photo_main] : [];
      $foto = !empty($photosDb) ? asset('storage/' . $photosDb[0]) : asset('assets/img/products/placeholder.svg');

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

      return [
        'id' => $p->id,
        'slug' => $p->slug,
        'nama' => $p->name,
        'kategori' => $p->category ? $p->category->name : 'Uncategorized',
        'berat' => $p->weight_label ?? '500 gram',
        'harga' => (int) $varian[0]['price'],
        'hargaCoret' => $p->compare_at_price ? (int) $p->compare_at_price : null,
        'stok' => $p->stock_status ?? 'tersedia',
        'po' => (int) ($p->min_preorder_days ?? 2),
        'bestSeller' => (bool) $p->is_best_seller,
        'rating' => (float) $ulasanAvg,
        'ulasan' => (int) $ulasanCount,
        'terjual' => (int) $terjualAsli,
        'foto' => $foto,
        'varian' => $varian
      ];
    })->values()->all();

    $categoriesList = array_values(array_unique(array_filter(array_merge(
      $categories->pluck('name')->all(),
      $products->pluck('category.name')->filter()->all()
    ))));
  @endphp

  <script>
    (function () {
      const IS_LOGGED_IN = @json(auth()->check());
      const DATA_PRODUK = @json($mappedCatalog);
      const KATEGORI = @json($categoriesList);

      if (!KATEGORI.includes('Best Seller')) KATEGORI.unshift('Best Seller');

      let q = new URLSearchParams(window.location.search).get('q') || '';
      let fKat = new URLSearchParams(window.location.search).get('kategori') || '';
      let urutVal = 'populer';

      const grid = document.getElementById('grid');
      const kosong = document.getElementById('kosong');
      const statTotal = document.getElementById('stat-total');
      const statBest = document.getElementById('stat-best');
      const ringkas = document.getElementById('ringkas');
      const inputCari = document.getElementById('cari');
      const selectUrut = document.getElementById('urut');
      const chips = document.getElementById('chips');
      const btnReset = document.getElementById('reset');
      const btnHapusCari = document.getElementById('hapus-cari');

      function formatRupiah(n) { return 'Rp' + Number(n || 0).toLocaleString('id-ID'); }

      function renderChips() {
        if (!chips) return;
        const btnBase = "px-4 py-1.5 rounded-full text-sm font-medium transition-colors shrink-0 whitespace-nowrap border";
        const btnActive = "bg-cocoa-700 border-cocoa-700 text-white";
        const btnInactive = "bg-white border-cream-200 text-cocoa-500 hover:bg-cream-100";

        let html = `<button type="button" class="${btnBase} ${!fKat ? btnActive : btnInactive}" onclick="window.filterKategori('')">Semua</button>`;
        KATEGORI.forEach(k => {
          html += `<button type="button" class="${btnBase} ${fKat === k ? btnActive : btnInactive}" onclick="window.filterKategori('${k}')">${k}</button>`;
        });
        chips.innerHTML = html;
      }

      window.filterKategori = function (kat) {
        fKat = kat; renderChips(); render();
      };

      window.resetFilter = function () {
        fKat = ''; q = '';
        if (inputCari) inputCari.value = '';
        if (selectUrut) selectUrut.value = 'populer';
        urutVal = 'populer';
        if (btnHapusCari) btnHapusCari.classList.add('hidden');
        renderChips(); render();
        window.history.replaceState(null, '', window.location.pathname);
      };

      function renderCard(p) {
        const habis = p.stok === 'habis';
        const diskon = p.hargaCoret ? Math.round((1 - p.harga / p.hargaCoret) * 100) : 0;
        const urlDetail = `{{ url('produk') }}/${p.slug}`;
        const varName = p.varian && p.varian.length > 0 ? p.varian[0].name : 'Original';

        return `
          <article class="card card-hover overflow-hidden flex flex-col group h-full">
            <a href="${urlDetail}" class="relative block aspect-[4/3] overflow-hidden bg-cream-100" aria-label="Lihat detail ${p.nama}">
              <img src="${p.foto}" onerror="this.onerror=null; this.src='{{ asset('assets/img/products/placeholder.svg') }}';" alt="${p.nama}" loading="lazy" class="w-full h-full object-cover transition duration-500 group-hover:scale-[1.06] ${habis ? 'grayscale opacity-70' : ''}">
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
                  <i data-lucide="star" class="w-3.5 h-3.5 star fill-current"></i>${parseFloat(p.rating).toFixed(1)}
                  <span class="text-cocoa-300">(${p.ulasan})</span>
                </span>
              </div>
              <h3 class="font-display font-semibold text-cocoa-700 leading-snug mb-1">
                <a href="${urlDetail}" class="hover:text-rose-600 transition">${p.nama}</a>
              </h3>
              <p class="text-xs text-cocoa-300 mb-3">${p.berat} &middot; PO min. H-${p.po}</p>
              <div class="mt-auto">
                <div class="flex items-baseline gap-2 mb-3">
                  <span class="font-display font-bold text-lg text-rose-600">${formatRupiah(p.harga)}</span>
                  ${p.hargaCoret ? `<span class="text-xs text-cocoa-300 line-through">${formatRupiah(p.hargaCoret)}</span>` : ''}
                </div>
                <div class="flex gap-2">
                  <a href="${urlDetail}" class="btn btn-outline btn-sm flex-1">Detail</a>
                  ${habis
            ? `<button type="button" class="btn btn-primary btn-sm flex-1 is-disabled" disabled aria-disabled="true">Habis</button>`
            : `<button type="button" class="btn btn-primary btn-sm flex-1" onclick="window.tambahKeranjang('${p.id}', 1, '${varName}')" aria-label="Tambah ${p.nama} ke keranjang">
                         <i data-lucide="plus" class="w-4 h-4"></i> Keranjang</button>`}
                </div>
              </div>
            </div>
          </article>`;
      }

      function render() {
        let hasil = DATA_PRODUK.filter(p => {
          const matchCari = !q || p.nama.toLowerCase().includes(q) || p.kategori.toLowerCase().includes(q);
          const matchKat = !fKat || (fKat === 'Best Seller' ? p.bestSeller : p.kategori === fKat);
          return matchCari && matchKat;
        });

        if (urutVal === 'murah') hasil.sort((a, b) => a.harga - b.harga);
        else if (urutVal === 'mahal') hasil.sort((a, b) => b.harga - a.harga);
        else if (urutVal === 'populer') hasil.sort((a, b) => b.terjual - a.terjual);
        else if (urutVal === 'nama') hasil.sort((a, b) => a.nama.localeCompare(b.nama));
        else if (urutVal === 'rating') hasil.sort((a, b) => b.rating - a.rating);
        else if (urutVal === 'terbaru') hasil.sort((a, b) => b.id - a.id);

        if (statTotal) statTotal.textContent = DATA_PRODUK.filter(p => p.stok === 'tersedia').length;
        if (statBest) statBest.textContent = DATA_PRODUK.filter(p => p.bestSeller).length;
        if (ringkas) {
          ringkas.textContent = `Menampilkan ${hasil.length} produk${fKat ? ' kategori ' + fKat : ''}${q ? ' untuk "' + q + '"' : ''}.`;
        }

        if (hasil.length === 0) {
          if (grid) grid.innerHTML = '';
          if (kosong) kosong.classList.remove('hidden');
        } else {
          if (kosong) kosong.classList.add('hidden');
          if (grid) grid.innerHTML = hasil.map(p => renderCard(p)).join('');
        }

        if (btnReset) btnReset.classList.toggle('hidden', !fKat && !q);
        if (btnHapusCari) btnHapusCari.classList.toggle('hidden', !q);

        if (typeof icons === 'function') icons();
      }

      if (inputCari) {
        inputCari.value = q;
        inputCari.addEventListener('input', (e) => {
          q = e.target.value.trim().toLowerCase();
          if (btnHapusCari) btnHapusCari.classList.toggle('hidden', !q);
          render();
        });
      }

      if (selectUrut) {
        selectUrut.addEventListener('change', () => { urutVal = selectUrut.value; render(); });
      }

      if (btnHapusCari) {
        btnHapusCari.addEventListener('click', () => {
          q = ''; if (inputCari) inputCari.value = '';
          btnHapusCari.classList.add('hidden'); render();
        });
      }

      if (btnReset) btnReset.addEventListener('click', window.resetFilter);

      renderChips(); render();

      window.tambahKeranjang = function (id, qty = 1, varian = null) {
        if (!IS_LOGGED_IN) { window.location.href = '{{ route('login') }}'; return; }

        const btn = event.currentTarget || event.target;
        const isBtn = btn && btn.tagName === 'BUTTON';
        const originalText = isBtn ? btn.innerHTML : '';
        if (isBtn) btn.innerHTML = '<span class="spinner"></span>...';

        fetch('{{ route('cart.store') }}', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
          body: JSON.stringify({ product_id: id, qty: qty || 1, variant: varian || 'Original' })
        })
          .then(res => res.json())
          .then(data => {
            if (isBtn) btn.innerHTML = originalText;
            if (data.status === 'success') {
              window.LARAVEL_CART_COUNT = data.cart_count;
              if (typeof window.badgeKeranjang === 'function') window.badgeKeranjang();
              if (typeof toast === 'function') toast(data.message, 'success', 'Masuk Keranjang');
              document.querySelectorAll('[data-cart-badge]').forEach(el => { el.textContent = data.cart_count; el.classList.remove('hidden'); });
            }
          })
          .catch(err => {
            if (isBtn) btn.innerHTML = originalText;
            if (typeof toast === 'function') toast('Gagal menambahkan produk', 'error');
          });
      };
      window.submitKeranjang = window.tambahKeranjang;
    })();
  </script>
@endpush
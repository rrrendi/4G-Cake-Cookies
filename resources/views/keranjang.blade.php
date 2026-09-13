@extends('layouts.main')

@section('title', 'Keranjang · 4G Cake & Cookies')

@section('content')
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
  <nav class="flex items-center gap-2 text-xs text-cocoa-300 mb-5" aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="hover:text-rose-600 transition">Beranda</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <span class="text-cocoa-500 font-medium">Keranjang</span>
  </nav>

  <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
    <div>
      <h1 class="font-display font-bold text-3xl sm:text-4xl text-cocoa-700 mb-2">Keranjang belanja</h1>
      <p id="ringkas-jumlah" class="text-cocoa-400 text-sm"></p>
    </div>
    <a href="{{ route('catalog') }}" class="btn btn-outline btn-sm">
      <i data-lucide="arrow-left" class="w-4 h-4"></i> Lanjut belanja
    </a>
  </div>

  <div id="isi-keranjang" class="grid lg:grid-cols-[1.6fr_1fr] gap-6 lg:gap-8 items-start hidden">
    <div class="space-y-4">
      <!-- ITEM KERANJANG -->
      <div id="daftar-item" class="space-y-4"></div>

      <div class="card p-5 sm:p-6">
        <label for="catatan" class="label">Catatan untuk dapur <span class="font-normal text-cocoa-300">(opsional)</span></label>
        <textarea id="catatan" rows="3" class="textarea"
          placeholder="Contoh: tulisan di atas kue 'Selamat Ulang Tahun Aira', kurangi manis, tanpa kacang."></textarea>
        <p class="hint">Catatan akan dibaca tim dapur sebelum pesanan dibuat.</p>
      </div>

      <div class="rounded-2xl border border-gold-300/60 bg-[#FDF8EA] p-5 flex gap-3.5">
        <span class="grid place-items-center w-10 h-10 rounded-xl bg-gold-400/20 text-gold-600 shrink-0"><i data-lucide="calendar-clock" class="w-5 h-5"></i></span>
        <div>
          <p class="font-semibold text-sm text-cocoa-700 mb-1">Ingat ketentuan pre-order</p>
          <p id="po-terlama" class="text-sm text-cocoa-400 leading-relaxed"></p>
        </div>
      </div>
    </div>

    <!-- RINGKASAN START -->
    <aside class="card p-6 lg:sticky lg:top-28">
      <h2 class="font-display font-bold text-lg text-cocoa-700 mb-5">Ringkasan belanja</h2>
      <dl class="space-y-3 text-sm">
        <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Subtotal produk</dt><dd id="r-subtotal" class="font-medium text-cocoa-700"></dd></div>
        <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Estimasi ongkir</dt><dd class="font-medium text-cocoa-700">Dihitung saat checkout</dd></div>
        <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Diskon</dt><dd id="r-diskon" class="font-medium text-rose-600"></dd></div>
        <div class="pt-3 border-t border-cream-200 flex justify-between gap-4 items-baseline">
          <dt class="font-semibold text-cocoa-700">Total sementara</dt>
          <dd id="r-total" class="font-display font-bold text-2xl text-rose-600"></dd>
        </div>
      </dl>

      <div class="mt-5 flex gap-2">
        <input id="kupon" type="text" class="input" placeholder="Kode promo" aria-label="Kode promo">
        <button type="button" onclick="pakaiKupon()" class="btn btn-outline btn-sm shrink-0">Pakai</button>
      </div>
      <p class="hint">Coba kode <span class="font-semibold text-cocoa-500">MANIS10</span> untuk simulasi diskon 10%.</p>

      <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block btn-lg mt-6 flex justify-center items-center gap-2">
        Lanjut ke Checkout <i data-lucide="arrow-right" class="w-[18px] h-[18px]"></i>
      </a>
      <button type="button" onclick="kosongkan()" class="btn btn-ghost btn-sm btn-block mt-2 text-cocoa-300">Kosongkan keranjang</button>

      <div class="mt-6 pt-5 border-t border-cream-200 space-y-2.5 text-xs text-cocoa-400">
        <p class="flex gap-2"><i data-lucide="shield-check" class="w-4 h-4 text-cocoa-300 shrink-0"></i> Pembayaran dikonfirmasi manual oleh admin sebelum diproses.</p>
        <p class="flex gap-2"><i data-lucide="refresh-cw" class="w-4 h-4 text-cocoa-300 shrink-0"></i> Bisa dibatalkan selama status masih Menunggu Bayar.</p>
      </div>
    </aside>
    <!-- RINGKASAN END -->
  </div>

  <!-- EMPTY STATE START -->
  <div id="keranjang-kosong" class="hidden card py-16 sm:py-20 px-6 text-center">
    <span class="grid place-items-center w-20 h-20 mx-auto rounded-3xl bg-blush-50 text-rose-400 mb-6">
      <i data-lucide="shopping-bag" class="w-9 h-9"></i>
    </span>
    <h2 class="font-display font-bold text-xl text-cocoa-700 mb-2">Keranjang Anda masih kosong</h2>
    <p class="text-sm text-cocoa-400 max-w-sm mx-auto mb-7">
      Belum ada kue yang dipilih. Mulai dari best seller kami &mdash; brownies kukus dan nastar kastengel
      biasanya jadi pilihan pertama.
    </p>
    <div class="flex flex-wrap justify-center gap-3">
      <a href="{{ route('catalog') }}" class="btn btn-primary btn-lg">Mulai Belanja</a>
    </div>
  </div>
  <!-- EMPTY STATE END -->
</section>
@endsection

@push('scripts')
@php
    $cart = session('cart', []);
    $cartItems = [];
    foreach($cart as $key => $item) {
        $qty = $item['qty'] ?? ($item['quantity'] ?? 1);
        $price = $item['price'] ?? ($item['harga'] ?? 0);
        $poDays = $item['po_days'] ?? ($item['min_po'] ?? ($item['po'] ?? 2));
        $nama = $item['name'] ?? ($item['nama'] ?? 'Produk');
        $slug = $item['slug'] ?? '';
        $variant = $item['variant'] ?? ($item['varian'] ?? 'Original');

        // MENGAMBIL DATA PRODUK LANGSUNG DARI DATABASE UNTUK OPSI DROPDOWN VARIAN
        $productModel = \App\Models\Product::where('slug', $slug)->first();
        $productId = $item['product_id'] ?? ($item['id'] ?? ($productModel->id ?? null));
        $availableVariants = ['Original'];
        
        if ($productModel && $productModel->variant_options) {
            $vOpts = is_string($productModel->variant_options) ? json_decode($productModel->variant_options, true) : $productModel->variant_options;
            if (is_array($vOpts) && count($vOpts) > 0) {
                if (isset($vOpts[0]) && is_array($vOpts[0])) {
                    $availableVariants = array_column($vOpts, 'name');
                } else {
                    $availableVariants = $vOpts;
                }
            }
        }

        $cartItems[] = [
            'cart_key' => (string) $key,
            'product_id' => $productId,
            'produk' => [
                'slug' => $slug,
                'nama' => $nama,
                'kategori' => $item['kategori'] ?? 'Produk', 
                'harga' => (int) $price,
                'po' => (int) $poDays,
                'foto' => isset($item['photo']) && $item['photo'] ? asset('storage/' . $item['photo']) : null
            ],
            'varian' => $variant,
            'available_variants' => $availableVariants,
            'qty' => (int) $qty,
            'subtotal' => (int) ($qty * $price)
        ];
    }
    
    // PERBAIKAN: Membalikkan urutan array agar pesanan/item yang paling baru masuk keranjang berada paling atas
    $cartItems = array_values($cartItems);
    $cartItems = array_reverse($cartItems);
@endphp

<script>
  let ITEMS = @json($cartItems);
  
  let diskon = parseFloat(sessionStorage.getItem('diskon_val')) || 0;
  let kupon_aktif = sessionStorage.getItem('kupon_aktif') || '';

  function render() {
    if (typeof PRODUK !== 'undefined') {
        ITEMS.forEach(it => {
            const dataAsli = PRODUK.find(p => p.slug === it.produk.slug);
            if (dataAsli && it.produk.kategori === 'Produk') it.produk.kategori = dataAsli.kategori;
        });
    }

    const kosong = ITEMS.length === 0;
    document.getElementById('isi-keranjang').classList.toggle('hidden', kosong);
    document.getElementById('keranjang-kosong').classList.toggle('hidden', !kosong);

    const totalQty = ITEMS.reduce((sum, it) => sum + it.qty, 0);
    
    window.LARAVEL_CART_COUNT = totalQty;
    if (typeof window.badgeKeranjang === 'function') window.badgeKeranjang();

    document.getElementById('ringkas-jumlah').textContent = kosong
      ? 'Tidak ada produk di keranjang.'
      : `${ITEMS.length} jenis produk · ${totalQty} item siap dipesan.`;
      
    if (kosong) { icons(); return; }

    if (kupon_aktif && document.getElementById('kupon')) {
        document.getElementById('kupon').value = kupon_aktif;
    }

    document.getElementById('daftar-item').innerHTML = ITEMS.map((it, i) => {
      const imgSrc = it.produk.foto ? it.produk.foto : `{{ asset('assets/img/products') }}/${it.produk.slug}.svg`;
      
      let varianUI = '';
      if (it.available_variants && it.available_variants.length > 1) {
          let opts = it.available_variants.map(v => `<option value="${v}" ${v === it.varian ? 'selected' : ''}>${v}</option>`).join('');
          varianUI = `
            <div class="mt-1 flex items-center gap-1.5">
              <label class="text-[11px] font-medium text-cocoa-400">Varian:</label>
              <select onchange="ubahVarian(${i}, this.value, event)" class="text-[11px] font-semibold border border-cream-300 rounded-md bg-white text-cocoa-700 py-0.5 pl-2 pr-6 focus:ring-rose-400 focus:border-rose-400 cursor-pointer shadow-sm">
                ${opts}
              </select>
            </div>`;
      } else {
          varianUI = `<p class="text-xs font-semibold text-cocoa-500 mt-1">Varian: ${it.varian || 'Original'}</p>`;
      }

      return `
      <article class="card p-4 sm:p-5 flex gap-4">
        <a href="{{ url('/produk') }}/${it.produk.slug}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden bg-cream-100 shrink-0">
          <img src="${imgSrc}" alt="${it.produk.nama}" class="w-full h-full object-cover">
        </a>
        <div class="flex-1 min-w-0 flex flex-col">
          <div class="flex items-start gap-3">
            <div class="min-w-0 flex-1">
              <span class="badge badge-neutral mb-1.5">${it.produk.kategori}</span>
              <h3 class="font-display font-semibold text-cocoa-700 leading-snug truncate">
                <a href="{{ url('/produk') }}/${it.produk.slug}" class="hover:text-rose-600 transition">${it.produk.nama}</a>
              </h3>
              ${varianUI}
              <p class="text-[11px] text-cocoa-300 mt-1.5"><i data-lucide="clock" class="inline w-3 h-3 -mt-0.5"></i> PO min. H-${it.produk.po}</p>
            </div>
            <button type="button" onclick="hapus(${i})" class="icon-btn icon-btn-danger tap shrink-0" aria-label="Hapus ${it.produk.nama}">
              <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
          </div>
          <div class="mt-auto pt-3 flex flex-wrap items-center justify-between gap-3">
            <div class="inline-flex items-center rounded-full border border-cream-200 bg-white shadow-sm">
              <button type="button" onclick="ubah(${i}, ${it.qty - 1})" class="grid place-items-center w-10 h-10 rounded-full text-cocoa-500 hover:bg-cream-100 transition tap" aria-label="Kurangi"><i data-lucide="minus" class="w-3.5 h-3.5"></i></button>
              <span class="w-9 text-center text-sm font-semibold text-cocoa-700">${it.qty}</span>
              <button type="button" onclick="ubah(${i}, ${it.qty + 1})" class="grid place-items-center w-10 h-10 rounded-full text-cocoa-500 hover:bg-cream-100 transition tap" aria-label="Tambah"><i data-lucide="plus" class="w-3.5 h-3.5"></i></button>
            </div>
            <div class="text-right">
              <p class="text-[11px] text-cocoa-400 mb-0.5">${rp(it.produk.harga)} / pcs</p>
              <p class="font-display font-bold text-rose-600 text-lg">${rp(it.subtotal)}</p>
            </div>
          </div>
        </div>
      </article>`;
    }).join('');

    const sub = ITEMS.reduce((sum, it) => sum + it.subtotal, 0);
    const pot = Math.round(sub * diskon);
    document.getElementById('r-subtotal').textContent = rp(sub);
    document.getElementById('r-diskon').textContent = pot ? '- ' + rp(pot) : 'Rp0';
    document.getElementById('r-total').textContent = rp(sub - pot);

    const maxPo = Math.max(...ITEMS.map(i => i.produk.po));
    const d = new Date(); d.setDate(d.getDate() + maxPo);
    const tglText = d.toISOString().slice(0,10);
    document.getElementById('po-terlama').innerHTML =
      `Produk dengan waktu persiapan terlama di keranjang ini butuh <span class="font-semibold text-cocoa-600">H-${maxPo}</span>.
       Tanggal ambil/kirim paling cepat yang bisa dipilih adalah
       <span class="font-semibold text-cocoa-600">${tglID(tglText, true)}</span>.`;
    icons();
  }

  function ubah(i, qty) {
    if (qty < 1) { hapus(i); return; }
    
    fetch('{{ route('cart.update') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ cart_key: ITEMS[i].cart_key, qty: qty })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            ITEMS[i].qty = qty;
            ITEMS[i].subtotal = qty * ITEMS[i].produk.harga;
            render();
        }
    });
  }

  window.ubahVarian = async function(i, newVarian, event) {
    const it = ITEMS[i];
    const oldKey = it.cart_key;
    const selectEl = event.target;
    
    selectEl.disabled = true;
    if(typeof toast === 'function') toast('Memperbarui varian...', 'info');
    
    try {
        let resAdd = await fetch('{{ route('cart.store') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ product_id: it.product_id, qty: it.qty, variant: newVarian, action: 'cart' })
        });
        let dataAdd = await resAdd.json();

        if (dataAdd.status === 'success') {
            await fetch('{{ route('cart.destroy') }}', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ cart_key: oldKey })
            });

            let resHtml = await fetch(window.location.href);
            let htmlText = await resHtml.text();
            
            let match = htmlText.match(/let ITEMS = (\[.*?\]);\s*let diskon/s);
            if (match && match[1]) {
                let newItemsRaw = JSON.parse(match[1]);
                
                let urutanAsli = ITEMS.map(x => x.cart_key);
                
                let newItemData = newItemsRaw.find(x => x.product_id == it.product_id && x.varian === newVarian);
                
                if (newItemData) {
                    let idx = urutanAsli.indexOf(oldKey);
                    if (idx !== -1) urutanAsli[idx] = newItemData.cart_key;
                }
                
                newItemsRaw.sort((a, b) => {
                    let posA = urutanAsli.indexOf(a.cart_key);
                    let posB = urutanAsli.indexOf(b.cart_key);
                    if (posA === -1) posA = 999;
                    if (posB === -1) posB = 999;
                    return posA - posB;
                });
                
                ITEMS = newItemsRaw;
                render();
                if(typeof toast === 'function') toast('Varian berhasil diubah', 'success');
            } else {
                window.location.reload(); 
            }
        } else {
            selectEl.disabled = false;
            if(typeof toast === 'function') toast('Gagal mengubah varian.', 'error');
        }
    } catch (err) {
        selectEl.disabled = false;
        if(typeof toast === 'function') toast('Terjadi kesalahan jaringan.', 'error');
    }
  };

  function hapus(i) {
    const nama = ITEMS[i].produk.nama;
    konfirmasi({
      judul: 'Hapus dari keranjang?',
      pesan: `<span class="font-semibold text-cocoa-600">${nama}</span> akan dikeluarkan dari keranjang Anda.`,
      label: 'Ya, hapus',
      ikon: 'trash-2',
      aksi: () => {
        fetch('{{ route('cart.destroy') }}', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ cart_key: ITEMS[i].cart_key })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                ITEMS.splice(i, 1);
                render();
                toast(nama + ' dihapus dari keranjang.', 'info');
            }
        });
      }
    });
  }

  function kosongkan() {
    if (!ITEMS.length) { toast('Keranjang memang sudah kosong.', 'info'); return; }
    konfirmasi({
      judul: 'Kosongkan keranjang?',
      pesan: 'Semua produk yang sudah dipilih akan dikeluarkan dari keranjang.',
      label: 'Ya, kosongkan',
      ikon: 'shopping-bag',
      aksi: () => {
        fetch('{{ route('cart.destroy') }}', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                ITEMS = [];
                diskon = 0;
                sessionStorage.removeItem('kupon_aktif');
                sessionStorage.removeItem('diskon_val');
                render();
                toast('Semua item dikeluarkan dari keranjang.', 'warning', 'Keranjang dikosongkan');
            }
        });
      }
    });
  }

  function pakaiKupon() {
    const k = document.getElementById('kupon').value.trim().toUpperCase();
    if (!k) { toast('Masukkan kode promo dulu.', 'error'); return; }
    
    if (k === 'MANIS10') { 
        diskon = 0.10; 
        sessionStorage.setItem('kupon_aktif', k);
        sessionStorage.setItem('diskon_val', '0.10');
        render(); 
        toast('Kode MANIS10 dipakai, potongan 10% masuk.', 'success', 'Promo aktif'); 
    } else { 
        diskon = 0; 
        sessionStorage.removeItem('kupon_aktif');
        sessionStorage.removeItem('diskon_val');
        render(); 
        toast(`Kode "${k}" tidak dikenali.`, 'error'); 
    }
  }

  document.addEventListener('DOMContentLoaded', render);
</script>
@endpush
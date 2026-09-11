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
      <a href="{{ route('catalog', ['kategori' => 'Best Seller']) }}" class="btn btn-outline btn-lg">Lihat best seller</a>
    </div>
  </div>
  <!-- EMPTY STATE END -->
</section>
@endsection

@push('scripts')
@php
    // Membaca data keranjang dari Session Laravel
    $cart = session('cart', []);
    $cartItems = [];
    foreach($cart as $key => $item) {
        $cartItems[] = [
            'cart_key' => $key,
            'produk' => [
                'slug' => $item['slug'],
                'nama' => $item['name'],
                'kategori' => 'Produk', 
                'harga' => $item['price'],
                'po' => $item['po_days'],
            ],
            'varian' => $item['variant'],
            'qty' => $item['qty'],
            'subtotal' => $item['qty'] * $item['price']
        ];
    }
    $cartItems = array_values($cartItems);
@endphp

<script>
  // Mengirim data keranjang Laravel ke JavaScript
  let ITEMS = @json($cartItems);
  let diskon = 0;

  function render() {
    // Sinkronisasi kategori dari data dummy JS
    if (typeof PRODUK !== 'undefined') {
        ITEMS.forEach(it => {
            const dataAsli = PRODUK.find(p => p.slug === it.produk.slug);
            if (dataAsli) it.produk.kategori = dataAsli.kategori;
        });
    }

    const kosong = ITEMS.length === 0;
    document.getElementById('isi-keranjang').classList.toggle('hidden', kosong);
    document.getElementById('keranjang-kosong').classList.toggle('hidden', !kosong);

    const totalQty = ITEMS.reduce((sum, it) => sum + it.qty, 0);
    
    // Perbarui Global Badge Navbar secara otomatis
    window.LARAVEL_CART_COUNT = totalQty;
    if (typeof window.badgeKeranjang === 'function') window.badgeKeranjang();

    document.getElementById('ringkas-jumlah').textContent = kosong
      ? 'Tidak ada produk di keranjang.'
      : `${ITEMS.length} jenis produk · ${totalQty} item siap dipesan.`;
      
    if (kosong) { icons(); return; }

    document.getElementById('daftar-item').innerHTML = ITEMS.map((it, i) => `
      <article class="card p-4 sm:p-5 flex gap-4">
        <a href="{{ url('/produk') }}/${it.produk.slug}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden bg-cream-100 shrink-0">
          <img src="${imgProduk(it.produk.slug)}" alt="${it.produk.nama}" class="w-full h-full object-cover">
        </a>
        <div class="flex-1 min-w-0 flex flex-col">
          <div class="flex items-start gap-3">
            <div class="min-w-0 flex-1">
              <span class="badge badge-neutral mb-1.5">${it.produk.kategori}</span>
              <h3 class="font-display font-semibold text-cocoa-700 leading-snug truncate">
                <a href="{{ url('/produk') }}/${it.produk.slug}" class="hover:text-rose-600 transition">${it.produk.nama}</a>
              </h3>
              <p class="text-xs text-cocoa-300 mt-0.5">${it.varian || 'Original'} &middot; PO min. H-${it.produk.po}</p>
            </div>
            <button type="button" onclick="hapus(${i})" class="icon-btn icon-btn-danger tap shrink-0" aria-label="Hapus ${it.produk.nama}">
              <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
          </div>
          <div class="mt-auto pt-3 flex flex-wrap items-center justify-between gap-3">
            <div class="inline-flex items-center rounded-full border border-cream-200 bg-white">
              <button type="button" onclick="ubah(${i}, ${it.qty - 1})" class="grid place-items-center w-10 h-10 rounded-full text-cocoa-500 hover:bg-cream-100 transition tap" aria-label="Kurangi"><i data-lucide="minus" class="w-3.5 h-3.5"></i></button>
              <span class="w-9 text-center text-sm font-semibold text-cocoa-700">${it.qty}</span>
              <button type="button" onclick="ubah(${i}, ${it.qty + 1})" class="grid place-items-center w-10 h-10 rounded-full text-cocoa-500 hover:bg-cream-100 transition tap" aria-label="Tambah"><i data-lucide="plus" class="w-3.5 h-3.5"></i></button>
            </div>
            <div class="text-right">
              <p class="text-[11px] text-cocoa-300">${rp(it.produk.harga)} / pcs</p>
              <p class="font-display font-bold text-cocoa-700">${rp(it.subtotal)}</p>
            </div>
          </div>
        </div>
      </article>`).join('');

    const sub = ITEMS.reduce((sum, it) => sum + it.subtotal, 0);
    const pot = Math.round(sub * diskon);
    document.getElementById('r-subtotal').textContent = rp(sub);
    document.getElementById('r-diskon').textContent = pot ? '- ' + rp(pot) : 'Rp0';
    document.getElementById('r-total').textContent = rp(sub - pot);

    const maxPo = Math.max(...ITEMS.map(i => i.produk.po));
    const d = new Date(); d.setDate(d.getDate() + maxPo);
    document.getElementById('po-terlama').innerHTML =
      `Produk dengan waktu persiapan terlama di keranjang ini butuh <span class="font-semibold text-cocoa-600">H-${maxPo}</span>.
       Tanggal ambil/kirim paling cepat yang bisa dipilih adalah
       <span class="font-semibold text-cocoa-600">${tglID(d.toISOString().slice(0,10), true)}</span>.`;
    icons();
  }

  // Integrasi AJAX ke Backend Laravel
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
        // Eksekusi hapus massal tanpa refresh menggunakan Promise.all
        Promise.all(ITEMS.map(it => 
            fetch('{{ route('cart.destroy') }}', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ cart_key: it.cart_key })
            })
        )).then(() => {
            ITEMS = [];
            diskon = 0;
            render();
            toast('Semua item dikeluarkan dari keranjang.', 'warning', 'Keranjang dikosongkan');
        });
      }
    });
  }

  function pakaiKupon() {
    const k = document.getElementById('kupon').value.trim().toUpperCase();
    if (!k) { toast('Masukkan kode promo dulu.', 'error'); return; }
    if (k === 'MANIS10') { diskon = 0.10; render(); toast('Kode MANIS10 dipakai, potongan 10% masuk.', 'success', 'Promo aktif'); }
    else { diskon = 0; render(); toast(`Kode "${k}" tidak dikenali.`, 'error'); }
  }

  document.addEventListener('DOMContentLoaded', render);
</script>
@endpush
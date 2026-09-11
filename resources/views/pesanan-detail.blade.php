@extends('layouts.main')

@section('title', 'Detail Pesanan · 4G Cake & Cookies')

@section('content')
<section class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-8 sm:py-12" x-data="detailPesanan()" x-init="init()">
  <nav class="flex items-center gap-2 text-xs text-cocoa-300 mb-5 flex-wrap" aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="hover:text-rose-600 transition">Beranda</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <a href="{{ route('order.history') }}" class="hover:text-rose-600 transition">Pesanan Saya</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <span class="text-cocoa-500 font-medium" x-text="o.kode"></span>
  </nav>

  <div class="card overflow-hidden mb-6">
    <div class="flex flex-wrap items-center gap-4 px-6 py-5 bg-cocoa-700 text-cream-100">
      <div>
        <p class="text-[11px] text-cream-200/50 mb-0.5">Nomor pesanan</p>
        <p class="font-display font-bold text-xl text-white" x-text="o.kode"></p>
      </div>
      <span class="badge" :class="STATUS_PESANAN[o.status].cls"><span class="badge-dot"></span><span x-text="STATUS_PESANAN[o.status].label"></span></span>
      <div class="ml-auto text-right">
        <p class="text-[11px] text-cream-200/50 mb-0.5">Total dibayar</p>
        <p class="font-display font-bold text-xl text-white" x-text="rp(o.total)"></p>
      </div>
    </div>

    <div class="grid sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-cream-200">
      <div class="p-5">
        <p class="text-[11px] text-cocoa-300 mb-1.5">Waktu pemesanan</p>
        <p class="text-sm font-medium text-cocoa-700" x-text="tglID(o.tanggal, true)"></p>
        <p class="text-xs text-cocoa-400 mt-0.5" x-text="'Pukul ' + (o.jamPesan || '-') + ' WIB'"></p>
      </div>
      <div class="p-5">
        <p class="text-[11px] text-cocoa-300 mb-1.5">Tanggal ambil / kirim</p>
        <p class="text-sm font-medium text-cocoa-700" x-text="tglID(o.ambil, true)"></p>
        <p class="text-xs text-cocoa-400 mt-0.5" x-text="o.jam ? 'Jam ' + o.jam : ''"></p>
      </div>
      <div class="p-5">
        <p class="text-[11px] text-cocoa-300 mb-1.5">Metode</p>
        <p class="text-sm font-medium text-cocoa-700" x-text="o.metode === 'J&T' ? 'Dikirim via J&T Express' : 'Ambil di tempat'"></p>
      </div>
    </div>
  </div>

  <div class="grid lg:grid-cols-[1.4fr_1fr] gap-6 items-start">
    <div class="space-y-6">
      <!-- ITEM -->
      <div class="card p-6">
        <h2 class="font-display font-bold text-cocoa-700 mb-5">Rincian pesanan</h2>
        <div class="space-y-4">
          <template x-for="it in o.items" :key="it.nama">
            <div class="flex items-center gap-4">
              <a :href="'{{ url('/produk') }}/' + it.slug" class="w-16 h-16 rounded-2xl overflow-hidden bg-cream-100 shrink-0">
                <img :src="imgProduk(it.slug)" :alt="it.nama" class="w-full h-full object-cover">
              </a>
              <div class="min-w-0 flex-1">
                <p class="font-medium text-cocoa-700 truncate" x-text="it.nama"></p>
                <p class="text-xs text-cocoa-300" x-text="rp(it.harga) + ' × ' + it.qty"></p>
              </div>
              <p class="font-semibold text-cocoa-700 shrink-0" x-text="rp(it.harga * it.qty)"></p>
            </div>
          </template>
        </div>
        <dl class="space-y-2.5 text-sm mt-6 pt-5 border-t border-cream-200">
          <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Subtotal produk</dt><dd class="text-cocoa-700" x-text="rp(o.total - o.ongkir)"></dd></div>
          <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Ongkos kirim</dt><dd class="text-cocoa-700" x-text="o.ongkir ? rp(o.ongkir) : 'Gratis'"></dd></div>
          <div class="flex justify-between gap-4 pt-3 border-t border-cream-200 items-baseline">
            <dt class="font-semibold text-cocoa-700">Total</dt>
            <dd class="font-display font-bold text-2xl text-rose-600" x-text="rp(o.total)"></dd>
          </div>
        </dl>
      </div>

      <!-- CATATAN PELANGGAN -->
      <div class="card p-6" x-show="o.catatan">
        <h2 class="font-display font-bold text-cocoa-700 mb-3 flex items-center gap-2">
          <i data-lucide="sticky-note" class="w-4 h-4 text-cocoa-400"></i> Catatan untuk dapur
        </h2>
        <p class="text-sm text-cocoa-400 leading-relaxed" x-text="o.catatan"></p>
      </div>

      <!-- TIMELINE -->
      <div class="card p-6">
        <h2 class="font-display font-bold text-cocoa-700 mb-5">Perjalanan pesanan</h2>
        <ol class="space-y-5">
          <template x-for="(t, i) in timeline" :key="i">
            <li class="relative flex gap-4">
              <span x-show="i < timeline.length - 1" class="timeline-line timeline-line-lg"></span>
              <span class="relative grid place-items-center w-10 h-10 rounded-full shrink-0 z-10"
                    :class="t.aktif ? 'bg-rose-500 text-white' : 'bg-cream-100 text-cocoa-300'">
                <i :data-lucide="t.icon" class="w-[18px] h-[18px]"></i>
              </span>
              <div class="pb-1">
                <p class="font-medium" :class="t.aktif ? 'text-cocoa-700' : 'text-cocoa-300'" x-text="t.label"></p>
                <p class="text-xs mt-0.5" :class="t.aktif ? 'text-cocoa-400' : 'text-cocoa-200'" x-text="t.ket"></p>
              </div>
            </li>
          </template>
        </ol>
      </div>
    </div>

    <aside class="space-y-6 lg:sticky lg:top-28">
      <div class="card p-6">
        <h2 class="font-display font-bold text-cocoa-700 mb-4">Penerima</h2>
        <p class="font-medium text-cocoa-700 text-sm" x-text="o.pelanggan"></p>
        <p class="text-xs text-cocoa-400 mt-1" x-text="o.hp"></p>
        <p class="text-xs text-cocoa-400 mt-3 leading-relaxed" x-text="o.alamat"></p>
        <div x-show="o.metode === 'J&T'" class="mt-4 pt-4 border-t border-cream-200">
          <p class="text-[11px] text-cocoa-300 mb-1">Nomor resi</p>
          <p class="font-medium text-cocoa-700 text-sm flex items-center gap-2">
            <span x-text="o.resi !== '-' ? o.resi : 'Belum tersedia'"></span>
            <button type="button" x-show="o.resi !== '-'" @click="toast('Nomor resi disalin.','success')" class="icon-btn !w-9 !h-9 tap" aria-label="Salin nomor resi"><i data-lucide="copy" class="w-4 h-4"></i></button>
          </p>
        </div>
      </div>

      <div class="card p-6">
        <h2 class="font-display font-bold text-cocoa-700 mb-4">Pembayaran</h2>
        <p class="text-sm text-cocoa-500 mb-3" x-text="o.bayar"></p>
        <div class="rounded-2xl border border-cream-200 p-4 flex items-center gap-3">
          <span class="grid place-items-center w-11 h-11 rounded-xl bg-cream-100 text-cocoa-400 shrink-0"><i data-lucide="receipt" class="w-5 h-5"></i></span>
          <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-cocoa-700">bukti-transfer.jpg</p>
            <p class="text-[11px] text-cocoa-300" x-text="o.status === 'menunggu' ? 'Menunggu verifikasi admin' : 'Sudah diverifikasi'"></p>
          </div>
        </div>
      </div>

      <div class="card p-6 space-y-2">
        <a href="{{ route('order.history') }}" class="btn btn-outline btn-block"><i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar</a>
        <a x-show="o.status === 'selesai'" :href="'{{ url('/review') }}/' + o.kode" class="btn btn-gold btn-block">
          <i data-lucide="star" class="w-4 h-4"></i> Beri review
        </a>
        <button @click="toast('Struk pesanan disiapkan untuk diunduh (simulasi).','info')" class="btn btn-ghost btn-block">
          <i data-lucide="download" class="w-4 h-4"></i> Unduh struk
        </button>
      </div>
    </aside>
  </div>
</section>
@endsection

@push('scripts')
<script>
  const SEMUA = semuaPesanan();
  // Menyuntikkan kode dari Laravel langsung ke JavaScript
  const KODE = "{{ $kode }}";
  const ORDER = SEMUA.find(o => o.kode === KODE) || SEMUA[0];

  function detailPesanan() {
    return {
      o: ORDER, STATUS_PESANAN,
      init() { document.title = ORDER.kode + ' · 4G Cake & Cookies'; this.$nextTick(() => icons()); },
      get timeline() { return timelinePesanan(this.o, true); }
    };
  }
</script>
@endpush
@extends('layouts.main')

@section('title', 'Pesanan Saya · 4G Cake & Cookies')

@section('content')
<section class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-8 sm:py-12" x-data="riwayat()" x-init="init()">
  <nav class="flex items-center gap-2 text-xs text-cocoa-300 mb-5" aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="hover:text-rose-600 transition">Beranda</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <span class="text-cocoa-500 font-medium">Pesanan Saya</span>
  </nav>

  <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
    <div>
      <h1 class="font-display font-bold text-3xl sm:text-4xl text-cocoa-700 mb-2">Riwayat &amp; status pesanan</h1>
      <p class="text-cocoa-400 text-sm">Semua pesanan atas nama <span class="font-semibold text-cocoa-600">Nadia Safitri</span> beserta perkembangannya.</p>
    </div>
    <a href="{{ route('catalog') }}" class="btn btn-primary btn-sm"><i data-lucide="plus" class="w-4 h-4"></i> Pesan lagi</a>
  </div>

  <!-- FILTER STATUS -->
  <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1 mb-6">
    <template x-for="s in chip" :key="s.key">
      <button @click="f = s.key"
        class="shrink-0 rounded-full border px-4 py-2 text-sm font-medium transition flex items-center gap-2"
        :class="f === s.key ? 'bg-cocoa-500 border-cocoa-500 text-white' : 'bg-white border-cream-200 text-cocoa-500 hover:border-rose-300'">
        <span x-text="s.label"></span>
        <span class="rounded-full px-1.5 py-0.5 text-[10px] font-bold" :class="f === s.key ? 'bg-white/20' : 'bg-cream-100 text-cocoa-400'" x-text="jumlah(s.key)"></span>
      </button>
    </template>
  </div>

  <!-- DAFTAR PESANAN START -->
  <div class="space-y-5">
    <template x-for="o in hasil" :key="o.kode">
      <article class="card overflow-hidden">
        <div class="flex flex-wrap items-center gap-3 px-5 sm:px-6 py-4 bg-cream-100/70 border-b border-cream-200">
          <div>
            <p class="font-display font-bold text-cocoa-700" x-text="o.kode"></p>
            <p class="text-[11px] text-cocoa-300">Dipesan <span x-text="tglID(o.tanggal)"></span></p>
          </div>
          <span class="badge" :class="STATUS_PESANAN[o.status].cls"><span class="badge-dot"></span><span x-text="STATUS_PESANAN[o.status].label"></span></span>
          <div class="ml-auto text-right">
            <p class="text-[11px] text-cocoa-300">Total pembayaran</p>
            <p class="font-display font-bold text-cocoa-700" x-text="rp(o.total)"></p>
          </div>
        </div>

        <div class="p-5 sm:p-6 grid lg:grid-cols-[1.2fr_1fr] gap-6">
          <div>
            <div class="space-y-3 mb-5">
              <template x-for="it in o.items" :key="it.nama">
                <div class="flex items-center gap-3">
                  <img :src="imgProduk(it.slug)" :alt="it.nama" class="w-14 h-14 rounded-xl object-cover bg-cream-100 shrink-0">
                  <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-cocoa-700 truncate" x-text="it.nama"></p>
                    <p class="text-[11px] text-cocoa-300" x-text="rp(it.harga) + ' × ' + it.qty"></p>
                  </div>
                  <p class="text-sm font-semibold text-cocoa-700 shrink-0" x-text="rp(it.harga * it.qty)"></p>
                </div>
              </template>
            </div>

            <div class="grid sm:grid-cols-2 gap-3 text-xs">
              <div class="rounded-xl bg-cream-50 border border-cream-200 p-3">
                <p class="text-cocoa-300 mb-1">Tanggal ambil / kirim</p>
                <p class="font-medium text-cocoa-700" x-text="tglID(o.ambil, true)"></p>
              </div>
              <div class="rounded-xl bg-cream-50 border border-cream-200 p-3">
                <p class="text-cocoa-300 mb-1" x-text="o.metode === 'J&T' ? 'Nomor resi J&T' : 'Metode'"></p>
                <p class="font-medium text-cocoa-700 flex items-center gap-2">
                  <span x-text="o.metode === 'J&T' ? (o.resi !== '-' ? o.resi : 'Belum tersedia') : 'Ambil di tempat'"></span>
                  <button type="button" x-show="o.metode === 'J&T' && o.resi !== '-'" @click="toast('Nomor resi ' + o.resi + ' disalin.','success')" class="icon-btn !w-9 !h-9 tap" aria-label="Salin nomor resi">
                    <i data-lucide="copy" class="w-4 h-4"></i></button>
                </p>
              </div>
            </div>
          </div>

          <!-- TIMELINE STATUS -->
          <div class="lg:border-l lg:border-cream-200 lg:pl-6">
            <p class="label mb-4">Perkembangan pesanan</p>
            <ol class="space-y-4">
              <template x-for="(t, i) in timeline(o)" :key="i">
                <li class="relative flex gap-3">
                  <span x-show="i < timeline(o).length - 1" class="timeline-line"></span>
                  <span class="relative grid place-items-center w-8 h-8 rounded-full shrink-0 z-10"
                        :class="t.aktif ? 'bg-rose-500 text-white' : 'bg-cream-100 text-cocoa-300'">
                    <i :data-lucide="t.icon" class="w-4 h-4"></i>
                  </span>
                  <div class="pb-1">
                    <p class="text-sm font-medium" :class="t.aktif ? 'text-cocoa-700' : 'text-cocoa-300'" x-text="t.label"></p>
                    <p class="text-[11px]" :class="t.aktif ? 'text-cocoa-400' : 'text-cocoa-200'" x-text="t.ket"></p>
                  </div>
                </li>
              </template>
            </ol>
          </div>
        </div>

        <div class="flex flex-wrap gap-2 px-5 sm:px-6 py-4 border-t border-cream-200 bg-cream-50">
          <a :href="'{{ url('/pesanan') }}/' + o.kode" class="btn btn-outline btn-sm">
            <i data-lucide="file-text" class="w-4 h-4"></i> Lihat detail
          </a>
          <button x-show="o.status === 'menunggu'" @click="toast('Halaman pembayaran akan dibuka setelah backend siap.','info')" class="btn btn-primary btn-sm">
            <i data-lucide="wallet" class="w-4 h-4"></i> Bayar sekarang
          </button>
          <button x-show="o.metode === 'J&T' && (o.status === 'dikirim' || o.status === 'selesai')" @click="lihatResi(o)" class="btn btn-outline btn-sm">
            <i data-lucide="truck" class="w-4 h-4"></i> Lihat Resi
          </button>
          <a x-show="o.status === 'selesai'" :href="'{{ url('/review') }}/' + o.kode" class="btn btn-gold btn-sm">
            <i data-lucide="star" class="w-4 h-4"></i> Beri review
          </a>
          <button x-show="o.status === 'menunggu'" @click="batal(o)" class="btn btn-ghost btn-sm ml-auto text-cocoa-300">Batalkan pesanan</button>
          <button @click="toast('Tim kami akan menghubungi Anda lewat WhatsApp.','info')" class="btn btn-ghost btn-sm" :class="o.status !== 'menunggu' && 'ml-auto'">
            <i data-lucide="headphones" class="w-4 h-4"></i> Bantuan
          </button>
        </div>
      </article>
    </template>
  </div>
  <!-- DAFTAR PESANAN END -->

  <!-- EMPTY STATE -->
  <div x-show="hasil.length === 0" x-cloak class="card py-16 sm:py-20 px-6 text-center">
    <span class="grid place-items-center w-20 h-20 mx-auto rounded-3xl bg-blush-50 text-rose-400 mb-6">
      <i data-lucide="receipt-text" class="w-9 h-9"></i>
    </span>
    <h2 class="font-display font-bold text-xl text-cocoa-700 mb-2" x-text="f === 'semua' ? 'Belum ada pesanan' : 'Tidak ada pesanan dengan status ini'"></h2>
    <p class="text-sm text-cocoa-400 max-w-sm mx-auto mb-7">
      Setiap pesanan yang Anda buat akan muncul di sini lengkap dengan status produksinya.
    </p>
    <div class="flex flex-wrap justify-center gap-3">
      <a href="{{ route('catalog') }}" class="btn btn-primary">Mulai pesan</a>
      <button @click="f='semua'" class="btn btn-outline">Tampilkan semua status</button>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
  function riwayat() {
    return {
      list: JSON.parse(JSON.stringify(semuaPesanan())),
      STATUS_PESANAN, f: 'semua',
      chip: [{key:'semua',label:'Semua'}, ...Object.entries(STATUS_PESANAN).map(([k,v]) => ({key:k, label:v.label}))],

      init() { this.$nextTick(() => icons()); this.$watch('hasil', () => this.$nextTick(() => icons())); },
      jumlah(k) { return k === 'semua' ? this.list.length : this.list.filter(o => o.status === k).length; },
      get hasil() { return this.f === 'semua' ? this.list : this.list.filter(o => o.status === this.f); },

      timeline(o) { return timelinePesanan(o); },

      batal(o) {
        konfirmasi({
          judul: 'Batalkan pesanan ini?',
          pesan: `Pesanan <span class="font-semibold text-cocoa-600">${o.kode}</span> akan dibatalkan dan tidak diproses.`,
          label: 'Ya, batalkan',
          ikon: 'x-circle',
          aksi: () => {
            o.status = 'dibatalkan';
            // Simpan status batal ke localStorage agar permanen di sesi prototype
            let dataLokal = semuaPesanan();
            let index = dataLokal.findIndex(x => x.kode === o.kode);
            if(index > -1) {
                dataLokal[index].status = 'dibatalkan';
                localStorage.setItem('4g_orders', JSON.stringify(dataLokal));
            }
            toast(o.kode + ' dibatalkan. Hubungi kami bila ini tidak disengaja.', 'warning', 'Pesanan dibatalkan');
            this.$nextTick(() => icons());
          }
        });
      },

      lihatResi(o) {
        if (!o.resi || o.resi === '-') { toast('Nomor resi belum tersedia. Paket masih menunggu penjemputan kurir.', 'info'); return; }
        konfirmasi({
          judul: 'Nomor resi J&T',
          pesan: `<span class="font-mono font-semibold text-cocoa-700 text-base">${o.resi}</span><br><span class="text-xs">Tujuan: ${o.alamat}</span>`,
          label: 'Salin nomor resi',
          ikon: 'truck',
          aksi: () => {
              navigator.clipboard.writeText(o.resi);
              toast('Nomor resi ' + o.resi + ' disalin.', 'success');
          }
        });
      }
    };
  }
</script>
@endpush
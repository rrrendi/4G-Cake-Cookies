@extends('layouts.admin')

@section('title', 'Manajemen Pesanan · 4G Cake & Cookies')
@section('header_title', 'Manajemen Pesanan')
@section('header_subtitle', 'Pantau, filter, dan ubah status seluruh pesanan masuk')

@section('content')
<div x-data="manajemenPesanan()" x-init="init()">

  <!-- FILTER STATUS (chip) -->
  <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1 mb-5">
    <template x-for="s in chipStatus" :key="s.key">
      <button @click="fStatus = s.key"
        class="shrink-0 rounded-full border px-4 py-2 text-sm font-medium transition flex items-center gap-2"
        :class="fStatus === s.key ? 'bg-cocoa-500 border-cocoa-500 text-white' : 'bg-white border-cream-200 text-cocoa-500 hover:border-rose-300'">
        <span x-text="s.label"></span>
        <span class="rounded-full px-1.5 py-0.5 text-[10px] font-bold"
              :class="fStatus === s.key ? 'bg-white/20' : 'bg-cream-100 text-cocoa-400'" x-text="jumlah(s.key)"></span>
      </button>
    </template>
  </div>

  <!-- TOOLBAR -->
  <div class="card p-4 sm:p-5 mb-5">
    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
      <div class="relative xl:col-span-1">
        <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-cocoa-300 pointer-events-none"></i>
        <input x-model="q" type="search" class="input !pl-11" placeholder="Cari kode / nama pelanggan" aria-label="Cari pesanan">
      </div>
      <select x-model="fMetode" class="select" aria-label="Filter metode">
        <option value="">Semua metode</option>
        <option value="J&T">J&amp;T Express</option>
        <option value="Ambil di Tempat">Ambil di Tempat</option>
      </select>
      <input x-model="fTanggal" type="date" class="input" aria-label="Filter tanggal ambil">
      <div class="flex gap-2">
        <button @click="q=''; fMetode=''; fTanggal=''; fStatus='semua'" class="btn btn-outline flex-1">
          <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reset
        </button>
        <button @click="toast('File Excel akan dibuat setelah backend siap.','info','Ekspor')" class="btn btn-cocoa flex-1">
          <i data-lucide="download" class="w-4 h-4"></i> Ekspor
        </button>
      </div>
    </div>
  </div>

  <!-- TABEL PESANAN START -->
  <div class="card overflow-hidden">
    <div class="table-wrap">
      <table class="data">
        <thead>
          <tr><th>Kode pesanan</th><th>Pelanggan</th><th>Tgl pesan</th><th>Tgl ambil/kirim</th>
              <th>Metode</th><th>Status</th><th class="text-right">Total</th><th class="text-right">Aksi</th></tr>
        </thead>
        <tbody>
          <template x-for="o in hasil" :key="o.kode">
            <tr>
              <td>
                <button @click="lihat(o)" class="font-medium text-cocoa-700 hover:text-rose-600 transition" x-text="o.kode"></button>
                <p class="text-[11px] text-cocoa-300" x-text="o.items.length + ' item'"></p>
              </td>
              <td>
                <p class="text-cocoa-600" x-text="o.pelanggan"></p>
                <p class="text-[11px] text-cocoa-300" x-text="o.hp"></p>
              </td>
              <td class="text-cocoa-400 whitespace-nowrap" x-text="tglID(o.tanggal)"></td>
              <td class="text-cocoa-600 whitespace-nowrap font-medium" x-text="tglID(o.ambil)"></td>
              <td>
                <span class="badge" :class="o.metode==='J&T' ? 'badge-ship' : 'badge-neutral'">
                  <i :data-lucide="o.metode==='J&T' ? 'truck' : 'store'" class="w-3 h-3"></i><span x-text="o.metode"></span>
                </span>
              </td>
              <td>
                <span class="badge" :class="STATUS_PESANAN[o.status].cls">
                  <span class="badge-dot"></span><span x-text="STATUS_PESANAN[o.status].label"></span>
                </span>
              </td>
              <td class="text-right font-medium text-cocoa-700 whitespace-nowrap" x-text="rp(o.total)"></td>
              <td>
                <div class="flex items-center justify-end gap-1">
                  <button @click="lihat(o)" class="icon-btn tap" title="Lihat detail">
                    <i data-lucide="eye" class="w-4 h-4"></i></button>
                  <button @click="bukaStatus(o)" class="icon-btn tap" title="Ubah status">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i></button>
                  <button @click="toast('Struk pesanan ' + o.kode + ' dikirim ke printer (simulasi).','info')" class="icon-btn tap" title="Cetak struk">
                    <i data-lucide="printer" class="w-4 h-4"></i></button>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div x-show="hasil.length === 0" class="py-16 text-center">
      <span class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-cream-100 text-cocoa-300 mb-4"><i data-lucide="inbox" class="w-6 h-6"></i></span>
      <p class="font-display font-semibold text-cocoa-700 mb-1">Tidak ada pesanan pada filter ini</p>
      <p class="text-sm text-cocoa-400 mb-5">Coba pilih status lain atau kosongkan filter tanggal.</p>
      <button @click="q=''; fMetode=''; fTanggal=''; fStatus='semua'" class="btn btn-outline btn-sm mx-auto">Tampilkan semua pesanan</button>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-t border-cream-200 text-sm">
      <p class="text-cocoa-400">Menampilkan <span class="font-semibold text-cocoa-700" x-text="hasil.length"></span> dari <span x-text="list.length"></span> pesanan</p>
      <p class="text-cocoa-400">Nilai terfilter: <span class="font-display font-bold text-cocoa-700" x-text="rp(hasil.reduce((a,o)=>a+o.total,0))"></span></p>
    </div>
  </div>
  <!-- TABEL PESANAN END -->

  <!-- MODAL DETAIL START -->
  <div x-show="detail" x-cloak class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true"
       @keydown.escape.window="tutupSemua()">
    <div @click="detail=false" class="modal-overlay"></div>
    <div class="relative min-h-full flex items-start sm:items-center justify-center p-4 sm:p-6">
      <div x-show="detail" x-transition class="modal-panel card max-w-2xl overflow-hidden">
        <template x-if="aktif">
          <div>
            <div class="flex items-start justify-between gap-4 px-6 py-4 border-b border-cream-200">
              <div>
                <p class="text-xs text-cocoa-300 mb-0.5">Detail pesanan</p>
                <h2 class="font-display font-bold text-lg text-cocoa-700" x-text="aktif.kode"></h2>
              </div>
              <div class="flex items-center gap-2">
                <span class="badge" :class="STATUS_PESANAN[aktif.status].cls"><span class="badge-dot"></span><span x-text="STATUS_PESANAN[aktif.status].label"></span></span>
                <button @click="detail=false" class="icon-btn tap" aria-label="Tutup"><i data-lucide="x" class="w-5 h-5"></i></button>
              </div>
            </div>

            <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
              <div class="grid sm:grid-cols-2 gap-4">
                <div class="rounded-2xl bg-cream-100 p-4">
                  <p class="text-[11px] text-cocoa-300 mb-2">Pelanggan</p>
                  <p class="font-semibold text-cocoa-700 text-sm" x-text="aktif.pelanggan"></p>
                  <p class="text-xs text-cocoa-400 mt-1" x-text="aktif.hp"></p>
                  <p class="text-xs text-cocoa-400 mt-2 leading-relaxed" x-text="aktif.alamat"></p>
                </div>
                <div class="rounded-2xl bg-cream-100 p-4 space-y-2 text-xs">
                  <p class="text-[11px] text-cocoa-300 mb-2">Pengiriman &amp; pembayaran</p>
                  <p class="flex justify-between gap-3"><span class="text-cocoa-400">Metode</span><span class="font-medium text-cocoa-700" x-text="aktif.metode"></span></p>
                  <p class="flex justify-between gap-3"><span class="text-cocoa-400">Tanggal ambil/kirim</span><span class="font-medium text-cocoa-700" x-text="tglID(aktif.ambil)"></span></p>
                  <p class="flex justify-between gap-3"><span class="text-cocoa-400">Pembayaran</span><span class="font-medium text-cocoa-700" x-text="aktif.bayar"></span></p>
                  <p class="flex justify-between gap-3"><span class="text-cocoa-400">Nomor resi</span><span class="font-medium text-cocoa-700" x-text="aktif.resi"></span></p>
                </div>
              </div>

              <div>
                <p class="label">Item dipesan</p>
                <div class="space-y-2">
                  <template x-for="it in aktif.items" :key="it.nama">
                    <div class="flex items-center gap-3 rounded-2xl border border-cream-200 p-3">
                      <img :src="imgProduk(it.slug)" :alt="it.nama" class="w-12 h-12 rounded-xl object-cover bg-cream-100 shrink-0">
                      <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-cocoa-700 truncate" x-text="it.nama"></p>
                        <p class="text-[11px] text-cocoa-300" x-text="(it.varian ? it.varian + ' · ' : '') + rp(it.harga) + ' × ' + it.qty"></p>
                      </div>
                      <p class="text-sm font-semibold text-cocoa-700 shrink-0" x-text="rp(it.harga * it.qty)"></p>
                    </div>
                  </template>
                </div>
              </div>

              <dl class="space-y-2 text-sm pt-4 border-t border-cream-200">
                <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Subtotal</dt><dd class="text-cocoa-700" x-text="rp(aktif.total - aktif.ongkir)"></dd></div>
                <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Ongkos kirim</dt><dd class="text-cocoa-700" x-text="aktif.ongkir ? rp(aktif.ongkir) : 'Gratis'"></dd></div>
                <div class="flex justify-between gap-4 pt-2 border-t border-cream-200 items-baseline">
                  <dt class="font-semibold text-cocoa-700">Total</dt>
                  <dd class="font-display font-bold text-xl text-rose-600" x-text="rp(aktif.total)"></dd>
                </div>
              </dl>

              <div>
                <p class="label">Bukti pembayaran</p>
                <div class="rounded-2xl border border-cream-200 p-4 flex items-center gap-4">
                  <span class="grid place-items-center w-14 h-14 rounded-xl bg-cream-100 text-cocoa-400 shrink-0"><i data-lucide="receipt" class="w-6 h-6"></i></span>
                  <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-cocoa-700">bukti-transfer.jpg</p>
                    <p class="text-[11px] text-cocoa-300">Diunggah pelanggan &middot; belum diverifikasi</p>
                  </div>
                  <button @click="toast('Bukti transfer ditandai valid (simulasi).','success')" class="btn btn-outline btn-sm shrink-0">Verifikasi</button>
                </div>
              </div>
            </div>

            <div class="flex flex-wrap gap-3 px-6 py-4 border-t border-cream-200 bg-cream-50">
              <button @click="detail=false" class="btn btn-outline">Tutup</button>
              <button @click="detail=false; bukaStatus(aktif)" class="btn btn-primary ml-auto">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i> Ubah status
              </button>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
  <!-- MODAL DETAIL END -->

  <!-- MODAL UBAH STATUS START -->
  <div x-show="statusModal" x-cloak class="fixed inset-0 z-[60] grid place-items-center p-4" role="dialog" aria-modal="true"
       @keydown.escape.window="tutupSemua()">
    <div @click="statusModal=false" class="modal-overlay"></div>
    <div x-show="statusModal" x-transition class="modal-panel card max-w-md p-6">
      <template x-if="aktif">
        <div>
          <h2 class="font-display font-bold text-lg text-cocoa-700 mb-1">Ubah status pesanan</h2>
          <p class="text-sm text-cocoa-400 mb-5"><span class="font-medium text-cocoa-600" x-text="aktif.kode"></span> &middot; <span x-text="aktif.pelanggan"></span></p>
          <div class="space-y-2 mb-6">
            <template x-for="(s, key) in STATUS_PESANAN" :key="key">
              <button @click="statusBaru = key"
                class="w-full flex items-center gap-3 rounded-2xl border p-3 text-left transition"
                :class="statusBaru === key ? 'border-rose-400 bg-blush-50' : 'border-cream-200 hover:border-rose-300'">
                <span class="grid place-items-center w-9 h-9 rounded-xl shrink-0" :class="s.cls"><i :data-lucide="s.icon" class="w-4 h-4"></i></span>
                <span class="text-sm font-medium text-cocoa-700 flex-1" x-text="s.label"></span>
                <span class="w-4 h-4 rounded-full border-2 grid place-items-center" :class="statusBaru === key ? 'border-rose-500' : 'border-cream-300'">
                  <span x-show="statusBaru === key" class="w-2 h-2 rounded-full bg-rose-500"></span></span>
              </button>
            </template>
          </div>
          <div class="flex gap-3">
            <button @click="statusModal=false" class="btn btn-outline flex-1">Batal</button>
            <button @click="simpanStatus()" class="btn btn-primary flex-1">Simpan status</button>
          </div>
        </div>
      </template>
    </div>
  </div>
  <!-- MODAL UBAH STATUS END -->
</div>
@endsection

@push('scripts')
<script>
  function manajemenPesanan() {
    return {
      list: JSON.parse(JSON.stringify(semuaPesanan())),
      STATUS_PESANAN,
      q:'', fMetode:'', fTanggal:'', fStatus:'semua',
      detail:false, statusModal:false, aktif:null, statusBaru:'',
      chipStatus: [{ key:'semua', label:'Semua' },
        ...Object.entries(STATUS_PESANAN).map(([key, v]) => ({ key, label: v.label }))],

      tutupSemua() { this.detail = false; this.statusModal = false; },
      init() { this.$nextTick(() => icons()); this.$watch('hasil', () => this.$nextTick(() => icons())); },

      jumlah(key) { return key === 'semua' ? this.list.length : this.list.filter(o => o.status === key).length; },

      get hasil() {
        const q = this.q.trim().toLowerCase();
        return this.list.filter(o =>
          (this.fStatus === 'semua' || o.status === this.fStatus) &&
          (!q || o.kode.toLowerCase().includes(q) || o.pelanggan.toLowerCase().includes(q)) &&
          (!this.fMetode || o.metode === this.fMetode) &&
          (!this.fTanggal || o.ambil === this.fTanggal));
      },

      lihat(o) { this.aktif = o; this.detail = true; this.$nextTick(() => icons()); },
      bukaStatus(o) { this.aktif = o; this.statusBaru = o.status; this.statusModal = true; this.$nextTick(() => icons()); },
      simpanStatus() {
        if (this.statusBaru === this.aktif.status) { toast('Status tidak berubah.', 'info'); this.statusModal = false; return; }
        const lama = STATUS_PESANAN[this.aktif.status].label;
        this.aktif.status = this.statusBaru;
        
        // Auto-generate resi jika status diubah ke 'dikirim' via J&T
        if (this.statusBaru === 'dikirim' && this.aktif.metode === 'J&T' && this.aktif.resi === '-') {
          this.aktif.resi = 'JT88' + Math.floor(10000000 + Math.random() * 89999999);
          setTimeout(() => toast('Nomor resi otomatis dibuat: ' + this.aktif.resi, 'info'), 600);
        }
        
        toast(`${this.aktif.kode}: ${lama} → ${STATUS_PESANAN[this.statusBaru].label}`, 'success', 'Status diperbarui');
        this.statusModal = false;
        this.$nextTick(() => icons());
      }
    };
  }
</script>
@endpush
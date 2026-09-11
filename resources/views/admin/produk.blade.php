@extends('layouts.admin')

@section('title', 'Manajemen Produk · 4G Cake & Cookies')
@section('header_title', 'Manajemen Produk')
@section('header_subtitle', 'Tambah, ubah, dan atur ketersediaan produk')

@section('content')
<div x-data="manajemenProduk()" x-init="init()">

  <!-- RINGKASAN KECIL -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <div class="card p-4 flex items-center gap-3">
      <span class="grid place-items-center w-10 h-10 rounded-xl bg-blush-100 text-rose-600 shrink-0"><i data-lucide="cake" class="w-5 h-5"></i></span>
      <div><p class="font-display font-bold text-xl text-cocoa-700" x-text="list.length"></p><p class="text-[11px] text-cocoa-300">Total produk</p></div>
    </div>
    <div class="card p-4 flex items-center gap-3">
      <span class="grid place-items-center w-10 h-10 rounded-xl bg-[#E9F6EC] text-green-700 shrink-0"><i data-lucide="check-circle-2" class="w-5 h-5"></i></span>
      <div><p class="font-display font-bold text-xl text-cocoa-700" x-text="list.filter(p=>p.stok==='tersedia').length"></p><p class="text-[11px] text-cocoa-300">Tersedia</p></div>
    </div>
    <div class="card p-4 flex items-center gap-3">
      <span class="grid place-items-center w-10 h-10 rounded-xl bg-[#FDECEA] text-rose-700 shrink-0"><i data-lucide="package-x" class="w-5 h-5"></i></span>
      <div><p class="font-display font-bold text-xl text-cocoa-700" x-text="list.filter(p=>p.stok==='habis').length"></p><p class="text-[11px] text-cocoa-300">Habis</p></div>
    </div>
    <div class="card p-4 flex items-center gap-3">
      <span class="grid place-items-center w-10 h-10 rounded-xl bg-cream-200 text-gold-600 shrink-0"><i data-lucide="flame" class="w-5 h-5"></i></span>
      <div><p class="font-display font-bold text-xl text-cocoa-700" x-text="list.filter(p=>p.bestSeller).length"></p><p class="text-[11px] text-cocoa-300">Best seller</p></div>
    </div>
  </div>

  <!-- TOOLBAR -->
  <div class="card p-4 sm:p-5 mb-5">
    <div class="flex flex-col lg:flex-row gap-3">
      <div class="relative flex-1">
        <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-cocoa-300 pointer-events-none"></i>
        <input x-model="q" type="search" class="input !pl-11" placeholder="Cari nama produk&hellip;" aria-label="Cari produk">
      </div>
      <select x-model="fKategori" class="select lg:w-48" aria-label="Filter kategori">
        <option value="">Semua kategori</option>
        <template x-for="k in KATEGORI" :key="k"><option x-text="k" :value="k"></option></template>
      </select>
      <select x-model="fStok" class="select lg:w-40" aria-label="Filter status">
        <option value="">Semua status</option>
        <option value="tersedia">Tersedia</option>
        <option value="habis">Habis</option>
      </select>
      <button @click="bukaTambah()" class="btn btn-primary shrink-0">
        <i data-lucide="plus" class="w-4 h-4"></i> Tambah produk
      </button>
    </div>
  </div>

  <!-- TABEL PRODUK START -->
  <div class="card overflow-hidden">
    <div class="table-wrap">
      <table class="data">
        <thead>
          <tr>
            <th>Produk</th><th>Kategori</th><th>Harga</th><th>Pre-order</th>
            <th>Status</th><th>Terjual</th><th class="text-right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="p in hasil" :key="p.id">
            <tr>
              <td>
                <div class="flex items-center gap-3 min-w-[220px]">
                  <img :src="imgProduk(p.slug)" :alt="p.nama" class="w-12 h-12 rounded-xl object-cover bg-cream-100 shrink-0">
                  <div class="min-w-0">
                    <p class="font-medium text-cocoa-700 truncate" x-text="p.nama"></p>
                    <p class="text-[11px] text-cocoa-300 truncate" x-text="p.berat"></p>
                  </div>
                </div>
              </td>
              <td><span class="badge badge-neutral" x-text="p.kategori"></span></td>
              <td class="whitespace-nowrap">
                <p class="font-medium text-cocoa-700" x-text="rp(p.harga)"></p>
                <p class="text-[11px] text-cocoa-300 line-through" x-show="p.hargaCoret" x-text="p.hargaCoret ? rp(p.hargaCoret) : ''"></p>
              </td>
              <td class="text-cocoa-500 whitespace-nowrap">H-<span x-text="p.po"></span></td>
              <td>
                <button @click="toggleStok(p)" class="badge" :class="p.stok==='tersedia' ? 'badge-done' : 'badge-cancel'">
                  <span class="badge-dot"></span><span x-text="p.stok==='tersedia' ? 'Tersedia' : 'Habis'"></span>
                </button>
              </td>
              <td class="text-cocoa-500" x-text="p.terjual"></td>
              <td>
                <div class="flex items-center justify-end gap-1">
                  <!-- Link sudah disesuaikan agar bisa dilihat di toko -->
                  <a :href="'{{ url('/produk') }}/' + p.slug" target="_blank" class="icon-btn tap" title="Lihat di toko">
                    <i data-lucide="external-link" class="w-4 h-4"></i></a>
                  <button @click="bukaEdit(p)" class="icon-btn tap" title="Edit produk">
                    <i data-lucide="pencil" class="w-4 h-4"></i></button>
                  <button @click="konfirmHapus(p)" class="icon-btn icon-btn-danger tap" title="Hapus produk">
                    <i data-lucide="trash-2" class="w-4 h-4"></i></button>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div x-show="hasil.length === 0" class="py-16 text-center">
      <span class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-cream-100 text-cocoa-300 mb-4"><i data-lucide="search-x" class="w-6 h-6"></i></span>
      <p class="font-display font-semibold text-cocoa-700 mb-1">Produk tidak ditemukan</p>
      <p class="text-sm text-cocoa-400 mb-5">Coba ubah kata kunci atau kosongkan filternya.</p>
      <button @click="q=''; fKategori=''; fStok=''" class="btn btn-outline btn-sm mx-auto">Reset filter</button>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-t border-cream-200 text-sm">
      <p class="text-cocoa-400">Menampilkan <span class="font-semibold text-cocoa-700" x-text="hasil.length"></span> dari <span x-text="list.length"></span> produk</p>
      <div class="flex gap-1">
        <button class="btn btn-ghost btn-sm is-disabled">Sebelumnya</button>
        <button class="btn btn-outline btn-sm !px-3.5">1</button>
        <button @click="toast('Paginasi aktif setelah data dari database.','info')" class="btn btn-ghost btn-sm">Berikutnya</button>
      </div>
    </div>
  </div>
  <!-- TABEL PRODUK END -->

  <!-- MODAL TAMBAH / EDIT START -->
  <div x-show="modal" x-cloak class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true"
       @keydown.escape.window="tutupSemua()">
    <div @click="modal=false" class="modal-overlay"></div>
    <div class="relative min-h-full flex items-start sm:items-center justify-center p-4 sm:p-6">
      <div x-show="modal" x-transition class="card w-full max-w-2xl overflow-hidden">
        <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-cream-200">
          <div>
            <h2 class="font-display font-bold text-lg text-cocoa-700" x-text="mode==='tambah' ? 'Tambah produk baru' : 'Edit produk'"></h2>
            <p class="text-xs text-cocoa-300" x-text="mode==='tambah' ? 'Produk akan langsung tampil di katalog toko.' : 'Perubahan berlaku untuk katalog dan halaman detail.'"></p>
          </div>
          <button @click="modal=false" class="icon-btn tap" aria-label="Tutup"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="shrink-0">
              <p class="label">Foto produk</p>
              <label for="foto-produk" class="grid place-items-center w-32 h-32 rounded-2xl border-2 border-dashed border-cream-300 bg-cream-50 hover:border-rose-300 cursor-pointer overflow-hidden">
                <template x-if="!form.foto"><span class="text-center px-2"><i data-lucide="image-plus" class="w-6 h-6 mx-auto text-cocoa-300 mb-1.5"></i><span class="block text-[11px] text-cocoa-300">Pilih foto</span></span></template>
                <template x-if="form.foto"><img :src="form.foto" alt="Pratinjau foto produk" class="w-full h-full object-cover"></template>
              </label>
              <input id="foto-produk" type="file" accept="image/*" class="hidden" @change="pilihFoto($event)">
            </div>
            <div class="flex-1 space-y-4">
              <div>
                <label class="label" for="f-nama">Nama produk</label>
                <input id="f-nama" x-model="form.nama" type="text" class="input" placeholder="Contoh: Cheese Cake Jepang">
              </div>
              <div class="grid sm:grid-cols-2 gap-4">
                <div>
                  <label class="label" for="f-kat">Kategori</label>
                  <select id="f-kat" x-model="form.kategori" class="select">
                    <template x-for="k in KATEGORI" :key="k"><option x-text="k" :value="k"></option></template>
                  </select>
                </div>
                <div>
                  <label class="label" for="f-berat">Ukuran / berat</label>
                  <input id="f-berat" x-model="form.berat" type="text" class="input" placeholder="500 gram">
                </div>
              </div>
            </div>
          </div>

          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="label" for="f-harga">Harga jual (Rp)</label>
              <input id="f-harga" x-model.number="form.harga" type="number" min="0" step="1000" class="input" placeholder="65000">
            </div>
            <div>
              <label class="label" for="f-coret">Harga coret <span class="font-normal text-cocoa-300">(opsional)</span></label>
              <input id="f-coret" x-model.number="form.hargaCoret" type="number" min="0" step="1000" class="input" placeholder="75000">
            </div>
          </div>

          <div>
            <label class="label" for="f-desc">Deskripsi</label>
            <textarea id="f-desc" x-model="form.desc" rows="3" class="textarea" placeholder="Jelaskan rasa, tekstur, dan keunggulan produk."></textarea>
          </div>

          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <p class="label">Status ketersediaan</p>
              <div class="flex gap-2">
                <button type="button" @click="form.stok='tersedia'" class="btn btn-sm flex-1" :class="form.stok==='tersedia' ? 'btn-primary' : 'btn-outline'">Tersedia</button>
                <button type="button" @click="form.stok='habis'" class="btn btn-sm flex-1" :class="form.stok==='habis' ? 'btn-primary' : 'btn-outline'">Habis</button>
              </div>
            </div>
            <div>
              <label class="label" for="f-po">Minimal pre-order (hari)</label>
              <input id="f-po" x-model.number="form.po" type="number" min="0" max="14" class="input">
              <p class="hint">0 berarti bisa diambil di hari yang sama.</p>
            </div>
          </div>

          <label class="flex items-center gap-3 rounded-2xl bg-cream-100 p-4 cursor-pointer">
            <input type="checkbox" x-model="form.bestSeller" class="w-4 h-4 rounded border-cream-300 text-rose-500 focus:ring-rose-400">
            <span class="text-sm text-cocoa-500">Tandai sebagai <span class="font-semibold text-cocoa-700">best seller</span> di halaman depan</span>
          </label>
        </div>

        <div class="flex flex-wrap gap-3 px-6 py-4 border-t border-cream-200 bg-cream-50">
          <button @click="modal=false" class="btn btn-outline">Batal</button>
          <button @click="simpan()" class="btn btn-primary ml-auto">
            <i data-lucide="save" class="w-4 h-4"></i> <span x-text="mode==='tambah' ? 'Simpan produk' : 'Simpan perubahan'"></span>
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL TAMBAH / EDIT END -->

  <!-- MODAL HAPUS START -->
  <div x-show="hapusModal" x-cloak class="fixed inset-0 z-[60] grid place-items-center p-4" role="dialog" aria-modal="true"
       @keydown.escape.window="tutupSemua()">
    <div @click="hapusModal=false" class="modal-overlay"></div>
    <div x-show="hapusModal" x-transition class="relative card w-full max-w-sm p-7 text-center">
      <span class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-[#FDECEA] text-rose-700 mb-5"><i data-lucide="trash-2" class="w-6 h-6"></i></span>
      <h2 class="font-display font-bold text-lg text-cocoa-700 mb-2">Hapus produk ini?</h2>
      <p class="text-sm text-cocoa-400 mb-6">
        <span class="font-semibold text-cocoa-600" x-text="target?.nama"></span> akan hilang dari katalog.
        Pesanan lama yang sudah memakai produk ini tetap tersimpan.
      </p>
      <div class="flex gap-3">
        <button @click="hapusModal=false" class="btn btn-outline flex-1">Batal</button>
        <button @click="hapus()" class="btn btn-danger flex-1 !bg-rose-600 !text-white !border-rose-600">Ya, hapus</button>
      </div>
    </div>
  </div>
  <!-- MODAL HAPUS END -->
</div>
@endsection

@push('scripts')
<script>
  function manajemenProduk() {
    return {
      list: JSON.parse(JSON.stringify(PRODUK)),
      q:'', fKategori:'', fStok:'',
      modal:false, hapusModal:false, mode:'tambah', target:null,
      form: {},
      KATEGORI,

      tutupSemua() { this.modal = false; this.hapusModal = false; },
      init() { this.kosongkanForm(); this.$nextTick(() => icons()); this.$watch('hasil', () => this.$nextTick(() => icons())); },

      get hasil() {
        const q = this.q.trim().toLowerCase();
        return this.list.filter(p =>
          (!q || p.nama.toLowerCase().includes(q)) &&
          (!this.fKategori || p.kategori === this.fKategori) &&
          (!this.fStok || p.stok === this.fStok));
      },

      kosongkanForm() {
        this.form = { id:'', slug:'', nama:'', kategori:'Cake', berat:'', harga:null, hargaCoret:null,
                      desc:'', stok:'tersedia', po:2, bestSeller:false, foto:'' };
      },
      bukaTambah() { this.mode='tambah'; this.kosongkanForm(); this.modal=true; this.$nextTick(() => icons()); },
      bukaEdit(p) {
        this.mode='edit'; this.target=p;
        this.form = { ...p, foto: imgProduk(p.slug) };
        this.modal=true; this.$nextTick(() => icons());
      },
      pilihFoto(e) {
        const f = e.target.files[0];
        if (!f) return;
        this.form.foto = URL.createObjectURL(f);
        toast('Foto dipilih (belum diunggah ke server).', 'info');
      },
      simpan() {
        if (!this.form.nama.trim()) { toast('Nama produk wajib diisi.', 'error'); return; }
        if (!this.form.harga) { toast('Harga jual belum diisi.', 'error'); return; }
        if (this.mode === 'tambah') {
          const id = 'P-' + String(this.list.length + 1).padStart(2, '0');
          this.list.unshift({ ...this.form, id,
            slug: this.form.slug || 'placeholder',
            rating: 0, ulasan: 0, terjual: 0, varian: ['Standar'] });
          toast(`${this.form.nama} ditambahkan secara lokal.`, 'success', 'Produk tersimpan');
        } else {
          Object.assign(this.target, {
            nama: this.form.nama, kategori: this.form.kategori, berat: this.form.berat,
            harga: this.form.harga, hargaCoret: this.form.hargaCoret || null,
            desc: this.form.desc, stok: this.form.stok, po: this.form.po, bestSeller: this.form.bestSeller });
          toast(`Perubahan pada ${this.form.nama} disimpan.`, 'success');
        }
        this.modal = false;
        this.$nextTick(() => icons());
      },
      toggleStok(p) {
        p.stok = p.stok === 'tersedia' ? 'habis' : 'tersedia';
        toast(`${p.nama} sekarang berstatus ${p.stok === 'habis' ? 'habis' : 'tersedia'}.`, p.stok === 'habis' ? 'warning' : 'success');
      },
      konfirmHapus(p) { this.target = p; this.hapusModal = true; this.$nextTick(() => icons()); },
      hapus() {
        this.list = this.list.filter(x => x.id !== this.target.id);
        toast(`${this.target.nama} dihapus dari daftar lokal.`, 'warning');
        this.hapusModal = false; this.target = null;
        this.$nextTick(() => icons());
      }
    };
  }
</script>
@endpush
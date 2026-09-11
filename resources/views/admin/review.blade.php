@extends('layouts.admin')

@section('title', 'Manajemen Review · 4G Cake & Cookies')
@section('header_title', 'Manajemen Review')
@section('header_subtitle', 'Ringkasan penilaian dan moderasi ulasan pelanggan')

@section('content')
<div x-data="kelolaReview()" x-init="init()">
  <div class="grid lg:grid-cols-[1fr_1.6fr] gap-4 sm:gap-6 items-start">

    <!-- RINGKASAN -->
    <div class="space-y-4 sm:space-y-6">
      <div class="card p-6">
        <h2 class="font-display font-bold text-cocoa-700 mb-5">Rata-rata penilaian</h2>
        <div class="flex items-end gap-4 mb-5">
          <p class="font-display font-bold text-5xl text-cocoa-700" x-text="rata"></p>
          <div class="pb-1.5">
            <div class="flex gap-0.5 mb-1" x-html="starsHTML(parseFloat(rata), 'w-4 h-4')"></div>
            <p class="text-xs text-cocoa-300"><span x-text="list.length"></span> ulasan masuk</p>
          </div>
        </div>
        <div class="space-y-2">
          <template x-for="d in distribusi" :key="d.b">
            <div class="flex items-center gap-3 text-xs">
              <span class="w-10 text-cocoa-400 flex items-center gap-1"><span x-text="d.b"></span><i data-lucide="star" class="w-3 h-3 star fill-current"></i></span>
              <span class="flex-1 h-2 rounded-full bg-cream-200 overflow-hidden">
                <span class="block h-full rounded-full bg-gold-400" :style="'width:' + d.persen + '%'"></span>
              </span>
              <span class="w-6 text-right text-cocoa-300" x-text="d.n"></span>
            </div>
          </template>
        </div>
      </div>

      <div class="card p-6">
        <h2 class="font-display font-bold text-cocoa-700 mb-4">Rata-rata per aspek</h2>
        <div class="space-y-3.5">
          <template x-for="a in aspekRata" :key="a.key">
            <div>
              <div class="flex justify-between items-baseline gap-3 mb-1.5">
                <span class="text-sm text-cocoa-500" x-text="a.label"></span>
                <span class="text-sm font-semibold text-cocoa-700" x-text="a.nilai"></span>
              </div>
              <div class="h-2 rounded-full bg-cream-200 overflow-hidden">
                <div class="h-full rounded-full bg-rose-400" :style="'width:' + (a.nilai / 5 * 100) + '%'"></div>
              </div>
            </div>
          </template>
        </div>
      </div>

      <div class="card p-6">
        <h2 class="font-display font-bold text-cocoa-700 mb-4">Ulasan per produk</h2>
        <div class="space-y-2.5">
          <template x-for="p in perProduk" :key="p.nama">
            <button @click="fProduk = (fProduk === p.slug ? '' : p.slug)"
              class="w-full flex items-center gap-3 rounded-xl p-2.5 text-left transition"
              :class="fProduk === p.slug ? 'bg-blush-50 ring-1 ring-blush-200' : 'hover:bg-cream-100'">
              <img :src="imgProduk(p.slug)" :alt="p.nama" class="w-10 h-10 rounded-lg object-cover bg-cream-100 shrink-0">
              <span class="min-w-0 flex-1">
                <span class="block text-sm font-medium text-cocoa-700 truncate" x-text="p.nama"></span>
                <span class="block text-[11px] text-cocoa-300" x-text="p.n + ' ulasan · rata-rata ' + p.rata"></span>
              </span>
            </button>
          </template>
        </div>
      </div>
    </div>

    <!-- DAFTAR ULASAN -->
    <div class="card overflow-hidden">
      <div class="flex flex-wrap items-center gap-3 p-4 sm:p-5 border-b border-cream-200">
        <h2 class="font-display font-bold text-cocoa-700 mr-auto">Semua ulasan</h2>
        <select x-model="fBintang" class="select !py-2 w-40" aria-label="Filter bintang">
          <option value="">Semua bintang</option>
          <option value="5">5 bintang</option>
          <option value="4">4 bintang</option>
          <option value="3">3 bintang ke bawah</option>
        </select>
        <button x-show="fProduk" @click="fProduk=''" class="btn btn-ghost btn-sm"><i data-lucide="x" class="w-4 h-4"></i> Hapus filter produk</button>
      </div>

      <div class="divide-y divide-cream-200">
        <template x-for="(r, i) in hasil" :key="i">
          <article class="p-5 sm:p-6">
            <div class="flex items-start gap-3 mb-3">
              <img :src="imgAvatar(r.avatar)" :alt="'Ilustrasi ' + r.nama" class="w-10 h-10 rounded-full object-cover shrink-0 bg-cream-100">
              <div class="min-w-0 flex-1">
                <p class="font-semibold text-sm text-cocoa-700" x-text="r.nama"></p>
                <p class="text-[11px] text-cocoa-300"><span x-text="tglID(r.tgl)"></span> &middot; <span x-text="r.produk"></span></p>
              </div>
              <div class="shrink-0" x-html="starsHTML(r.bintang, 'w-4 h-4')"></div>
            </div>
            <p class="text-sm text-cocoa-400 leading-relaxed mb-3" x-text="r.teks"></p>
            <div class="flex flex-wrap items-center gap-2">
              <span class="badge badge-neutral" x-text="'Rasa ' + r.aspek.rasa + '/5'"></span>
              <span class="badge badge-neutral" x-text="'Kualitas ' + r.aspek.kualitas + '/5'"></span>
              <span class="badge badge-neutral" x-text="'Packaging ' + r.aspek.packaging + '/5'"></span>
              <span class="badge badge-neutral" x-text="'Pelayanan ' + r.aspek.pelayanan + '/5'"></span>
              <span class="ml-auto flex gap-1">
                <button @click="balas(r)" class="btn btn-ghost btn-sm"><i data-lucide="reply" class="w-4 h-4"></i> Balas</button>
                <button @click="toggleSembunyi(r)" class="btn btn-ghost btn-sm transition" :class="r.disembunyikan ? 'text-rose-500 bg-blush-50' : 'text-cocoa-300'">
                  <i :data-lucide="r.disembunyikan ? 'eye' : 'eye-off'" class="w-4 h-4"></i>
                  <span x-text="r.disembunyikan ? 'Tampilkan' : 'Sembunyikan'"></span>
                </button>
              </span>
            </div>
          </article>
        </template>
      </div>

      <div x-show="hasil.length === 0" class="py-16 text-center">
        <span class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-cream-100 text-cocoa-300 mb-4"><i data-lucide="message-square-dashed" class="w-6 h-6"></i></span>
        <p class="font-display font-semibold text-cocoa-700 mb-1">Belum ada ulasan pada filter ini</p>
        <p class="text-sm text-cocoa-400 mb-5">Ulasan baru muncul setelah pesanan pelanggan berstatus Selesai.</p>
        <button @click="fBintang=''; fProduk=''" class="btn btn-outline btn-sm mx-auto">Tampilkan semua</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function kelolaReview() {
    return {
      list: JSON.parse(JSON.stringify(REVIEWS)).map(r => ({ ...r, disembunyikan: false })),
      fBintang:'', fProduk:'', starsHTML,
      init() { this.$nextTick(() => icons()); this.$watch('hasil', () => this.$nextTick(() => icons())); },
      get hasil() {
        return this.list.filter(r =>
          (!this.fProduk || r.slug === this.fProduk) &&
          (!this.fBintang || (this.fBintang === '3' ? r.bintang <= 3 : r.bintang === +this.fBintang)));
      },
      get rata() { return (this.list.reduce((a,r)=>a+r.bintang,0) / this.list.length).toFixed(1); },
      get distribusi() {
        return [5,4,3,2,1].map(b => {
          const n = this.list.filter(r => r.bintang === b).length;
          return { b, n, persen: Math.round(n / this.list.length * 100) };
        });
      },
      get aspekRata() {
        const keys = [['rasa','Rasa'],['kualitas','Kualitas'],['packaging','Packaging'],['pelayanan','Pelayanan']];
        return keys.map(([key,label]) => ({ key, label,
          nilai: (this.list.reduce((a,r)=>a+r.aspek[key],0) / this.list.length).toFixed(1) }));
      },
      get perProduk() {
        const map = {};
        this.list.forEach(r => {
          if (!map[r.slug]) map[r.slug] = { nama:r.produk, slug:r.slug, n:0, total:0 };
          map[r.slug].n++; map[r.slug].total += r.bintang;
        });
        return Object.values(map).map(p => ({ ...p, rata:(p.total/p.n).toFixed(1) })).sort((a,b)=>b.n-a.n);
      },
      balas(r) { toast('Balasan untuk ulasan ' + r.nama + ' akan tersimpan setelah backend siap.', 'info'); },
      toggleSembunyi(r) {
        if (!r.disembunyikan) {
          konfirmasi({
            judul: 'Sembunyikan ulasan ini?',
            pesan: `Ulasan dari <span class="font-semibold text-cocoa-600">${r.nama}</span> tidak akan tampil lagi di halaman publik produk.`,
            label: 'Ya, sembunyikan', ikon: 'eye-off',
            aksi: () => {
              r.disembunyikan = true;
              toast('Ulasan ' + r.nama + ' disembunyikan.', 'warning');
              this.$nextTick(() => icons());
            }
          });
        } else {
          r.disembunyikan = false;
          toast('Ulasan ' + r.nama + ' kembali ditampilkan ke publik.', 'success');
          this.$nextTick(() => icons());
        }
      }
    };
  }
</script>
@endpush
@extends('layouts.admin')

@section('title', 'Manajemen Review · 4G Cake & Cookies')
@section('header_title', 'Manajemen Review')
@section('header_subtitle', 'Ringkasan penilaian dan moderasi ulasan pelanggan')

@section('content')
@php
    $reviewsQuery = \Illuminate\Support\Facades\DB::table('reviews')
        ->join('order_items', 'reviews.order_item_id', '=', 'order_items.id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->leftJoin('users', 'reviews.user_id', '=', 'users.id')
        ->select('reviews.*', 'users.name as user_name', 'products.name as product_name', 'products.slug as product_slug', 'products.photo_main', 'products.photos')
        ->orderBy('reviews.created_at', 'desc');
        
    $mappedReviews = $reviewsQuery->get()->map(function($r) {
        $fotoDb = $r->photo_main;
        if (!$fotoDb && $r->photos) {
            $pArr = is_string($r->photos) ? json_decode($r->photos, true) : $r->photos;
            if (is_array($pArr) && count($pArr) > 0) $fotoDb = $pArr[0];
        }
        $fotoAsli = $fotoDb ? asset('storage/' . $fotoDb) : asset('assets/img/products/placeholder.svg');

        return [
            'id' => $r->id,
            'nama' => $r->user_name ?? 'Pelanggan',
            'produk' => $r->product_name,
            'slug' => $r->product_slug,
            'foto' => $fotoAsli, 
            'bintang' => (int) $r->rating_overall,
            'teks' => $r->comment,
            'balasan' => $r->admin_reply ?? '',
            'tgl' => \Carbon\Carbon::parse($r->created_at)->format('Y-m-d'),
            'disembunyikan' => (bool) $r->is_hidden,
        ];
    })->values()->all();
@endphp

<div x-data="kelolaReview()" x-init="init()">
  <div class="grid lg:grid-cols-[1fr_1.6fr] gap-4 sm:gap-6 items-start">

    <!-- RINGKASAN & PRODUK KIRI -->
    <div class="space-y-4 sm:space-y-6">
      <div class="card p-6">
        <h2 class="font-display font-bold text-cocoa-700 mb-5">Rata-rata penilaian</h2>
        <div class="flex items-end gap-4 mb-5">
          <p class="font-display font-bold text-5xl text-cocoa-700" x-text="rata"></p>
          <div class="pb-1.5">
            <div class="flex gap-0.5 mb-1" x-html="typeof starsHTML === 'function' ? starsHTML(parseFloat(rata), 'w-4 h-4') : ''"></div>
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

      <div class="card p-6 flex flex-col">
        <h2 class="font-display font-bold text-cocoa-700 mb-4">Ulasan per produk</h2>
        
        <!-- PENCARIAN PRODUK REAL-TIME -->
        <div class="relative mb-4">
          <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-cocoa-400"></i>
          <input type="text" x-model="searchProduk" placeholder="Cari nama produk..." class="input pl-9 w-full text-sm py-2">
        </div>

        <!-- DAFTAR PRODUK (DIBUAT SCROLLABLE AGAR TIDAK KEPANJANGAN) -->
        <div class="space-y-2.5 max-h-[360px] overflow-y-auto pr-1 no-scrollbar">
          <template x-for="p in searchedPerProduk" :key="p.slug">
            <button @click="fProduk = (fProduk === p.slug ? '' : p.slug)"
              class="w-full flex items-center gap-3 rounded-xl p-2.5 text-left transition"
              :class="fProduk === p.slug ? 'bg-blush-50 ring-1 ring-blush-200' : 'hover:bg-cream-100'">
              <img :src="p.foto" onerror="this.onerror=null; this.src='{{ asset('assets/img/products/placeholder.svg') }}';" :alt="p.nama" class="w-10 h-10 rounded-lg object-cover bg-cream-100 shrink-0 border border-cream-200">
              <span class="min-w-0 flex-1">
                <span class="block text-sm font-medium text-cocoa-700 truncate" x-text="p.nama"></span>
                <span class="block text-[11px] text-cocoa-300" x-text="p.n + ' ulasan · rata-rata ' + p.rata"></span>
              </span>
            </button>
          </template>

          <!-- TAMPILAN JIKA PENCARIAN TIDAK DITEMUKAN -->
          <div x-show="searchedPerProduk.length === 0" class="text-center py-6 text-sm text-cocoa-400">
            <i data-lucide="package-search" class="w-8 h-8 mx-auto text-cream-300 mb-2"></i>
            Produk tidak ditemukan.
          </div>
        </div>
      </div>
    </div>

    <!-- DAFTAR ULASAN KANAN -->
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
        <template x-for="(r, i) in paginatedHasil" :key="r.id">
          <article class="p-5 sm:p-6" :class="r.disembunyikan && 'bg-cream-50 opacity-75'">
            <div class="flex items-start gap-3 mb-3">
              <span class="grid place-items-center w-10 h-10 rounded-full bg-rose-500 text-white text-xs font-semibold shrink-0 uppercase" x-text="r.nama.substring(0,2)"></span>
              <div class="min-w-0 flex-1">
                <p class="font-semibold text-sm text-cocoa-700" x-text="r.nama"></p>
                <p class="text-[11px] text-cocoa-300"><span x-text="typeof tglID === 'function' ? tglID(r.tgl) : r.tgl"></span> &middot; <span x-text="r.produk"></span></p>
              </div>
              <div class="shrink-0" x-html="typeof starsHTML === 'function' ? starsHTML(r.bintang, 'w-4 h-4') : ''"></div>
            </div>
            <p class="text-sm text-cocoa-400 leading-relaxed mb-3" x-text="r.teks"></p>
            
            <template x-if="r.balasan">
                <div class="mt-3 mb-4 bg-cream-100/70 p-4 rounded-xl border border-cream-200 relative">
                    <p class="text-[11px] font-bold text-cocoa-700 mb-1 flex items-center gap-1">
                        <i data-lucide="corner-down-right" class="w-3 h-3 text-cocoa-400"></i> Tanggapan Admin
                    </p>
                    <p class="text-sm text-cocoa-600 leading-relaxed" x-text="r.balasan"></p>
                </div>
            </template>

            <div class="flex flex-wrap items-center gap-2 mt-2">
              <span class="ml-auto flex gap-1">
                <button @click="bukaModalBalas(r)" class="btn btn-ghost btn-sm text-cocoa-500 hover:text-cocoa-700">
                    <i data-lucide="reply" class="w-4 h-4"></i> <span x-text="r.balasan ? 'Ubah Balasan' : 'Balas'"></span>
                </button>
                <button @click="toggleSembunyi(r, $event)" class="btn btn-ghost btn-sm transition" :class="r.disembunyikan ? 'text-rose-500 bg-blush-50' : 'text-cocoa-300 hover:text-cocoa-600'">
                  <i :data-lucide="r.disembunyikan ? 'eye' : 'eye-off'" class="w-4 h-4"></i>
                  <span x-text="r.disembunyikan ? 'Tampilkan' : 'Sembunyikan'"></span>
                </button>
              </span>
            </div>
          </article>
        </template>
      </div>

      <!-- PAGINATION CONTROLS -->
      <div x-show="totalPages > 1" class="flex flex-wrap items-center justify-between gap-4 p-4 sm:p-5 border-t border-cream-200 bg-cream-50/50">
        <p class="text-sm text-cocoa-400">Menampilkan <span class="font-semibold text-cocoa-700" x-text="paginatedHasil.length"></span> dari <span class="font-semibold text-cocoa-700" x-text="filteredHasil.length"></span> ulasan</p>
        <div class="flex gap-2">
          <button @click="currentPage > 1 ? currentPage-- : null" :disabled="currentPage === 1" class="btn btn-outline btn-sm !px-3 disabled:opacity-50 disabled:cursor-not-allowed">
            <i data-lucide="chevron-left" class="w-4 h-4"></i> Prev
          </button>
          <div class="grid place-items-center px-2 text-sm font-semibold text-cocoa-600" x-text="currentPage + ' / ' + totalPages"></div>
          <button @click="currentPage < totalPages ? currentPage++ : null" :disabled="currentPage === totalPages" class="btn btn-outline btn-sm !px-3 disabled:opacity-50 disabled:cursor-not-allowed">
            Next <i data-lucide="chevron-right" class="w-4 h-4"></i>
          </button>
        </div>
      </div>

      <div x-show="filteredHasil.length === 0" class="py-16 text-center">
        <span class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-cream-100 text-cocoa-300 mb-4"><i data-lucide="message-square-dashed" class="w-6 h-6"></i></span>
        <p class="font-display font-semibold text-cocoa-700 mb-1">Belum ada ulasan pada filter ini</p>
        <p class="text-sm text-cocoa-400 mb-5">Ulasan baru muncul setelah pesanan pelanggan berstatus Selesai.</p>
        <button @click="fBintang=''; fProduk=''; searchProduk=''" class="btn btn-outline btn-sm mx-auto">Tampilkan semua</button>
      </div>
    </div>
  </div>

  <div x-show="balasModal" x-cloak class="fixed inset-0 z-[60] grid place-items-center p-4" role="dialog" aria-modal="true" @keydown.escape.window="balasModal=false">
    <div @click="balasModal=false" class="modal-overlay"></div>
    <div x-show="balasModal" x-transition class="modal-panel card w-full max-w-lg p-6">
      <template x-if="aktifReview">
        <div>
          <h2 class="font-display font-bold text-lg text-cocoa-700 mb-4 flex items-center gap-2">
              <i data-lucide="message-circle" class="w-5 h-5 text-cocoa-400"></i> Beri Tanggapan
          </h2>
          
          <div class="bg-cream-50 p-4 rounded-xl border border-cream-200 mb-5 relative">
            <i data-lucide="quote" class="w-8 h-8 text-cream-200 absolute top-2 right-2"></i>
            <p class="font-semibold text-sm text-cocoa-700" x-text="aktifReview.nama"></p>
            <p class="text-xs text-cocoa-400 mt-1 mb-2" x-text="aktifReview.produk"></p>
            <p class="text-sm text-cocoa-500 italic" x-text="aktifReview.teks"></p>
          </div>
          
          <label class="label">Tanggapan Pemilik (Admin)</label>
          <textarea x-model="teksBalasan" rows="4" class="textarea mb-5" placeholder="Terima kasih atas pesanannya! Semoga cocok dengan rasanya..."></textarea>
          
          <div class="flex gap-3">
            <button @click="balasModal=false" class="btn btn-outline flex-1">Batal</button>
            <button @click="simpanBalasan()" class="btn btn-primary flex-1" id="btn-simpan-balasan">Simpan Balasan</button>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function kelolaReview() {
    return {
      list: @json($mappedReviews),
      fBintang:'', 
      fProduk:'',
      searchProduk: '', // State untuk form pencarian
      balasModal: false, aktifReview: null, teksBalasan: '',
      currentPage: 1, perPage: 10,

      init() { 
          if(typeof starsHTML !== 'function') window.starsHTML = () => '';
          
          this.$watch('fBintang', () => { this.currentPage = 1; });
          this.$watch('fProduk', () => { this.currentPage = 1; });
          this.$watch('paginatedHasil', () => this.$nextTick(() => { if(typeof icons==='function') icons() }));
          // Render ulang icon saat hasil pencarian produk muncul/hilang
          this.$watch('searchedPerProduk', () => this.$nextTick(() => { if(typeof icons==='function') icons() }));
          
          this.$nextTick(() => { if(typeof icons==='function') icons() }); 
      },
      tglID(tgl) {
         if (!tgl || tgl === '-') return '-';
         const d = new Date(tgl);
         if (isNaN(d)) return tgl;
         const bln = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
         return `${d.getDate()} ${bln[d.getMonth()]} ${d.getFullYear()}`;
      },
      get filteredHasil() {
        return this.list.filter(r =>
          (!this.fProduk || r.slug === this.fProduk) &&
          (!this.fBintang || (this.fBintang === '3' ? r.bintang <= 3 : r.bintang === +this.fBintang)));
      },
      get totalPages() {
          return Math.ceil(this.filteredHasil.length / this.perPage) || 1;
      },
      get paginatedHasil() {
          const start = (this.currentPage - 1) * this.perPage;
          return this.filteredHasil.slice(start, start + this.perPage);
      },
      get rata() { 
          if(this.list.length === 0) return '0.0';
          return (this.list.reduce((a,r)=>a+r.bintang,0) / this.list.length).toFixed(1); 
      },
      get distribusi() {
        return [5,4,3,2,1].map(b => {
          const n = this.list.filter(r => r.bintang === b).length;
          return { b, n, persen: this.list.length ? Math.round(n / this.list.length * 100) : 0 };
        });
      },
      get perProduk() {
        const map = {};
        this.list.forEach(r => {
          if (!map[r.slug]) map[r.slug] = { nama:r.produk, slug:r.slug, foto:r.foto, n:0, total:0 };
          map[r.slug].n++; map[r.slug].total += r.bintang;
        });
        return Object.values(map).map(p => ({ ...p, rata:(p.total/p.n).toFixed(1) })).sort((a,b)=>b.n-a.n);
      },
      
      // LOGIKA BARU: Menyaring daftar per produk berdasarkan teks pencarian
      get searchedPerProduk() {
          if (this.searchProduk.trim() === '') {
              return this.perProduk;
          }
          const cari = this.searchProduk.toLowerCase();
          return this.perProduk.filter(p => p.nama.toLowerCase().includes(cari));
      },

      bukaModalBalas(r) { 
          this.aktifReview = r;
          this.teksBalasan = r.balasan || '';
          this.balasModal = true;
          this.$nextTick(() => { if(typeof icons==='function') icons() });
      },

      simpanBalasan() {
          if (!this.teksBalasan.trim()) {
              if(typeof toast === 'function') toast('Tanggapan tidak boleh kosong.', 'error');
              return;
          }

          const btn = document.getElementById('btn-simpan-balasan');
          const ogHtml = btn.innerHTML;
          btn.innerHTML = '<span class="spinner w-4 h-4 border-2 mr-2"></span> Menyimpan...';
          btn.disabled = true;

          fetch(`{{ url('/admin/review') }}/${this.aktifReview.id}/reply`, {
              method: 'PUT',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
              body: JSON.stringify({ reply: this.teksBalasan })
          })
          .then(async res => {
              const data = await res.json().catch(() => null);
              if (!res.ok) throw new Error(data && data.message ? data.message : `HTTP Error ${res.status}`);
              return data;
          })
          .then(data => {
              btn.innerHTML = ogHtml;
              btn.disabled = false;
              if (data && data.status === 'success') {
                  this.aktifReview.balasan = this.teksBalasan;
                  this.balasModal = false;
                  if(typeof toast === 'function') toast('Tanggapan berhasil disimpan dan dipublikasikan.', 'success');
                  this.$nextTick(() => { if(typeof icons==='function') icons() });
              }
          })
          .catch(err => {
              btn.innerHTML = ogHtml;
              btn.disabled = false;
              if(typeof toast === 'function') toast('Gagal menyimpan: ' + err.message, 'error');
          });
      },

      toggleSembunyi(r, ev) {
          const btn = ev.currentTarget;
          const ogHtml = btn.innerHTML;
          btn.innerHTML = '<span class="spinner w-4 h-4 border-2"></span>';
          btn.disabled = true;
          
          fetch(`{{ url('/admin/review') }}/${r.id}/toggle`, {
              method: 'PUT',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
          })
          .then(async res => {
              const data = await res.json().catch(() => null);
              if (!res.ok) throw new Error(data && data.message ? data.message : `HTTP Error ${res.status}`);
              return data;
          })
          .then(data => {
              btn.innerHTML = ogHtml;
              btn.disabled = false;
              if (data && data.status === 'success') {
                  r.disembunyikan = data.new_status === 'hidden';
                  if(typeof toast === 'function') toast(`Ulasan berhasil ${r.disembunyikan ? 'disembunyikan' : 'ditampilkan'}.`, r.disembunyikan ? 'warning' : 'success');
                  this.$nextTick(() => { if(typeof icons==='function') icons() });
              }
          })
          .catch((err) => {
              btn.innerHTML = ogHtml;
              btn.disabled = false;
              if(typeof toast === 'function') toast('Gagal memproses: ' + err.message, 'error');
          });
      }
    };
  }
</script>
@endpush
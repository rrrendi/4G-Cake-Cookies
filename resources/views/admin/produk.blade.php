@extends('layouts.admin')

@section('title', 'Manajemen Produk · 4G Cake & Cookies')
@section('header_title', 'Manajemen Produk')
@section('header_subtitle', 'Tambah, ubah, dan atur ketersediaan produk')

@section('content')
  <div x-data="manajemenProduk()" x-init="init()">

    <!-- RINGKASAN KECIL -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
      <div class="card p-4 flex items-center gap-3">
        <span class="grid place-items-center w-10 h-10 rounded-xl bg-blush-100 text-rose-600 shrink-0"><i
            data-lucide="cake" class="w-5 h-5"></i></span>
        <div>
          <p class="font-display font-bold text-xl text-cocoa-700" x-text="list.length"></p>
          <p class="text-[11px] text-cocoa-300">Total produk</p>
        </div>
      </div>
      <div class="card p-4 flex items-center gap-3">
        <span class="grid place-items-center w-10 h-10 rounded-xl bg-[#E9F6EC] text-green-700 shrink-0"><i
            data-lucide="check-circle-2" class="w-5 h-5"></i></span>
        <div>
          <p class="font-display font-bold text-xl text-cocoa-700" x-text="list.filter(p=>p.stok==='tersedia').length">
          </p>
          <p class="text-[11px] text-cocoa-300">Tersedia</p>
        </div>
      </div>
      <div class="card p-4 flex items-center gap-3">
        <span class="grid place-items-center w-10 h-10 rounded-xl bg-[#FDECEA] text-rose-700 shrink-0"><i
            data-lucide="package-x" class="w-5 h-5"></i></span>
        <div>
          <p class="font-display font-bold text-xl text-cocoa-700" x-text="list.filter(p=>p.stok==='habis').length"></p>
          <p class="text-[11px] text-cocoa-300">Habis</p>
        </div>
      </div>
      <div class="card p-4 flex items-center gap-3">
        <span class="grid place-items-center w-10 h-10 rounded-xl bg-cream-200 text-gold-600 shrink-0"><i
            data-lucide="flame" class="w-5 h-5"></i></span>
        <div>
          <p class="font-display font-bold text-xl text-cocoa-700" x-text="list.filter(p=>p.bestSeller).length"></p>
          <p class="text-[11px] text-cocoa-300">Best seller</p>
        </div>
      </div>
    </div>

    <!-- TOOLBAR -->
    <div class="card p-4 sm:p-5 mb-5">
      <div class="flex flex-col lg:flex-row gap-3">
        <div class="relative flex-1">
          <i data-lucide="search"
            class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-cocoa-300 pointer-events-none"></i>
          <input x-model="q" type="search" class="input !pl-11" placeholder="Cari nama produk&hellip;"
            aria-label="Cari produk">
        </div>
        <select x-model="fKategori" class="select lg:w-40" aria-label="Filter kategori">
          <option value="">Semua kategori</option>
          <template x-for="k in KATEGORI" :key="k">
            <option x-text="k" :value="k"></option>
          </template>
        </select>
        <select x-model="fStok" class="select w-full lg:w-36" aria-label="Filter status">
          <option value="">Semua status</option>
          <option value="tersedia">Tersedia</option>
          <option value="habis">Habis</option>
        </select>
        <!-- PERBAIKAN: TAMBAHAN FILTER BEST SELLER -->
        <select x-model="fBestSeller" class="select w-full lg:w-36" aria-label="Filter tipe">
          <option value="">Semua tipe</option>
          <option value="ya">Best Seller</option>
          <option value="tidak">Reguler</option>
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
              <th>Produk</th>
              <th>Kategori</th>
              <th>Harga Mulai</th>
              <th>Pre-order</th>
              <th>Status</th>
              <th>Terjual</th>
              <th class="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="p in hasil" :key="p.id">
              <tr>
                <td>
                  <div class="flex items-center gap-3 min-w-[220px]">
                    <img :src="p.foto" :alt="p.nama"
                      onerror="this.onerror=null; this.src='{{ asset('assets/img/products/placeholder.svg') }}';"
                      class="w-12 h-12 rounded-xl object-cover bg-cream-100 shrink-0">
                    <div class="min-w-0">
                      <p class="font-medium text-cocoa-700 truncate" x-text="p.nama"></p>
                      <p class="text-[11px] text-cocoa-300 truncate" x-text="p.berat"></p>
                    </div>
                  </div>
                </td>
                <td><span class="badge badge-neutral" x-text="p.kategori"></span></td>
                <td class="whitespace-nowrap">
                  <p class="font-medium text-cocoa-700" x-text="formatRp(p.harga)"></p>
                  <p class="text-[11px] text-cocoa-300 line-through" x-show="p.hargaCoret"
                    x-text="p.hargaCoret ? formatRp(p.hargaCoret) : ''"></p>
                </td>
                <td class="text-cocoa-500 whitespace-nowrap">H-<span x-text="p.po"></span></td>
                <td>
                  <form :action="`{{ url('admin/produk') }}/${p.id}/stok`" method="POST" class="m-0 p-0">
                    @csrf @method('PATCH')
                    <button type="submit" class="badge hover:opacity-80 transition"
                      :class="p.stok==='tersedia' ? 'badge-done' : 'badge-cancel'">
                      <span class="badge-dot"></span><span x-text="p.stok==='tersedia' ? 'Tersedia' : 'Habis'"></span>
                    </button>
                  </form>
                </td>
                <td class="text-cocoa-500" x-text="p.terjual"></td>
                <td>
                  <div class="flex items-center justify-end gap-1">
                    <a :href="`{{ url('produk') }}/${p.slug}`" target="_blank" class="icon-btn tap" title="Lihat di toko">
                      <i data-lucide="external-link" class="w-4 h-4"></i>
                    </a>
                    <button @click="bukaEdit(p)" class="icon-btn tap" title="Edit produk">
                      <i data-lucide="pencil" class="w-4 h-4"></i>
                    </button>
                    <button @click="konfirmHapus(p)" class="icon-btn icon-btn-danger tap" title="Hapus produk">
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <div x-show="hasil.length === 0" class="py-16 text-center" x-cloak>
        <span class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-cream-100 text-cocoa-300 mb-4"><i
            data-lucide="search-x" class="w-6 h-6"></i></span>
        <p class="font-display font-semibold text-cocoa-700 mb-1">Produk tidak ditemukan</p>
        <p class="text-sm text-cocoa-400 mb-5">Coba ubah kata kunci atau kosongkan filternya.</p>
        <button @click="q=''; fKategori=''; fStok=''; fBestSeller='';" class="btn btn-outline btn-sm mx-auto">Reset
          filter</button>
      </div>

      <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-t border-cream-200 text-sm">
        <p class="text-cocoa-400">Menampilkan <span class="font-semibold text-cocoa-700" x-text="hasil.length"></span>
          dari <span x-text="list.length"></span> produk</p>
      </div>
    </div>

    <!-- MODAL TAMBAH / EDIT START -->
    <div x-show="modal" x-cloak class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true"
      @keydown.escape.window="tutupSemua()">
      <div @click="modal=false" class="modal-overlay"></div>
      <div class="relative min-h-full flex items-start sm:items-center justify-center p-4 sm:p-6">

        <form x-show="modal" x-transition
          :action="mode === 'tambah' ? '{{ route('admin.produk.store') }}' : `{{ url('admin/produk') }}/${target.id}`"
          method="POST" enctype="multipart/form-data" class="card w-full max-w-2xl overflow-hidden">
          @csrf
          <template x-if="mode === 'edit'">
            <input type="hidden" name="_method" value="PUT">
          </template>

          <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-cream-200">
            <div>
              <h2 class="font-display font-bold text-lg text-cocoa-700"
                x-text="mode==='tambah' ? 'Tambah produk baru' : 'Edit produk'"></h2>
              <p class="text-xs text-cocoa-300"
                x-text="mode==='tambah' ? 'Produk langsung tampil.' : 'Perubahan berlaku untuk katalog.'"></p>
            </div>
            <button type="button" @click="modal=false" class="icon-btn tap" aria-label="Tutup"><i data-lucide="x"
                class="w-5 h-5"></i></button>
          </div>

          <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
            <div class="flex flex-col sm:flex-row gap-4">
              <div class="shrink-0 w-full sm:w-40">
                <p class="label">Foto Produk (Bisa &gt; 1)</p>
                <label for="foto-produk"
                  class="grid place-items-center w-full aspect-square rounded-2xl border-2 border-dashed border-cream-300 bg-cream-50 hover:border-rose-300 cursor-pointer overflow-hidden relative">
                  <template x-if="form.fotoPreviews.length === 0">
                    <span class="text-center px-2"><i data-lucide="images"
                        class="w-6 h-6 mx-auto text-cocoa-300 mb-1.5"></i><span
                        class="block text-[11px] text-cocoa-300 leading-tight">Pilih beberapa foto</span></span>
                  </template>
                  <template x-if="form.fotoPreviews.length > 0">
                    <img :src="form.fotoPreviews[0]"
                      onerror="this.onerror=null; this.src='{{ asset('assets/img/products/placeholder.svg') }}';"
                      class="w-full h-full object-cover">
                  </template>
                  <div x-show="form.fotoPreviews.length > 1"
                    class="absolute bottom-2 right-2 badge badge-neutral shadow-sm text-[10px]">
                    +<span x-text="form.fotoPreviews.length - 1"></span>
                  </div>
                </label>
                <!-- MULTIPLE UPLOAD TAHAN CTRL/CMD -->
                <input id="foto-produk" name="photos[]" type="file" accept="image/*" multiple class="hidden"
                  @change="pilihFoto($event)">
                <p class="text-[10px] text-cocoa-300 mt-1.5 leading-tight">Tahan Ctrl/Cmd untuk pilih banyak foto
                  sekaligus.</p>
              </div>

              <div class="flex-1 space-y-4">
                <div>
                  <label class="label" for="f-nama">Nama produk</label>
                  <input id="f-nama" name="name" x-model="form.nama" type="text" class="input"
                    placeholder="Contoh: Cheese Cake" required>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                  <div>
                    <label class="label" for="f-kat">Kategori</label>
                    <input id="f-kat" name="category_name" x-model="form.kategori" type="text" list="kategori-list"
                      class="input" required>
                    <datalist id="kategori-list"><template x-for="k in KATEGORI" :key="k">
                        <option :value="k"></option>
                      </template></datalist>
                  </div>
                  <div>
                    <label class="label" for="f-po">Pre-order (hari)</label>
                    <input id="f-po" name="min_preorder_days" x-model.number="form.po" type="number" min="0" max="14"
                      class="input" required>
                  </div>
                </div>

                <div class="pt-2 border-t border-cream-200">
                  <p class="label">Varian & Harga</p>
                  <div class="space-y-2">
                    <template x-for="(v, idx) in form.varian" :key="idx">
                      <div class="flex gap-2 items-center">
                        <input type="text" :name="`variants[${idx}][name]`" x-model="v.name" class="input flex-1"
                          placeholder="Nama varian" required>
                        <input type="number" :name="`variants[${idx}][price]`" x-model.number="v.price"
                          class="input flex-1" placeholder="Harga (Rp)" required>
                        <button type="button" @click="form.varian.length > 1 ? form.varian.splice(idx, 1) : null"
                          class="icon-btn icon-btn-danger tap shrink-0 border-cream-200">
                          <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                      </div>
                    </template>
                  </div>
                  <button type="button" @click="form.varian.push({name:'', price:''})"
                    class="text-[11px] font-semibold text-rose-500 hover:text-rose-600 mt-2">+ Tambah varian lain</button>
                </div>
              </div>
            </div>

            <div>
              <label class="label" for="f-desc">Deskripsi Singkat</label>
              <textarea id="f-desc" name="description" x-model="form.desc" rows="3" class="textarea" required></textarea>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
              <div>
                <label class="label" for="f-berat">Ukuran Label (Opsional)</label>
                <input id="f-berat" name="weight_label" x-model="form.berat" type="text" class="input"
                  placeholder="1 kg (diameter 18 cm)">
              </div>
              <div>
                <label class="label" for="f-simpan">Cara Penyimpanan</label>
                <input id="f-simpan" name="storage_note" x-model="form.simpan" type="text" class="input"
                  placeholder="Kulkas 3-4 hari">
              </div>
              <div>
                <label class="label" for="f-kirim">Metode Pengiriman</label>
                <input id="f-kirim" name="delivery_info" x-model="form.pengiriman" type="text" class="input"
                  placeholder="J&T / ambil sendiri">
              </div>
              <div>
                <label class="label" for="f-kondisi">Kondisi Kue</label>
                <input id="f-kondisi" name="condition_info" x-model="form.kondisi" type="text" class="input"
                  placeholder="Dibuat setelah dipesan">
              </div>
              <div class="sm:col-span-2">
                <label class="label" for="f-bahan">Bahan Utama</label>
                <input id="f-bahan" name="ingredients" x-model="form.bahan" type="text" class="input"
                  placeholder="Tepung, telur, gula, butter...">
              </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4 border-t border-cream-200 pt-4 mt-2">
              <div>
                <label class="label" for="f-coret">Harga coret (Opsional Promo)</label>
                <input id="f-coret" name="compare_at_price" x-model.number="form.hargaCoret" type="number" min="0"
                  step="1000" class="input" placeholder="75000">
              </div>
              <div>
                <p class="label">Status ketersediaan</p>
                <input type="hidden" name="stock_status" :value="form.stok">
                <div class="flex gap-2">
                  <button type="button" @click="form.stok='tersedia'" class="btn btn-sm flex-1"
                    :class="form.stok==='tersedia' ? 'btn-primary' : 'btn-outline'">Tersedia</button>
                  <button type="button" @click="form.stok='habis'" class="btn btn-sm flex-1"
                    :class="form.stok==='habis' ? 'btn-primary' : 'btn-outline'">Habis</button>
                </div>
              </div>
            </div>

            <label class="flex items-center gap-3 rounded-2xl bg-cream-100 p-4 cursor-pointer">
              <input type="checkbox" name="is_best_seller" x-model="form.bestSeller" value="1"
                class="w-4 h-4 rounded border-cream-300 text-rose-500 focus:ring-rose-400">
              <span class="text-sm text-cocoa-500">Tandai sebagai <span class="font-semibold text-cocoa-700">best
                  seller</span> di halaman depan</span>
            </label>
          </div>

          <div class="flex flex-wrap gap-3 px-6 py-4 border-t border-cream-200 bg-cream-50">
            <button type="button" @click="modal=false" class="btn btn-outline">Batal</button>
            <button type="submit" class="btn btn-primary ml-auto">
              <i data-lucide="save" class="w-4 h-4"></i> <span
                x-text="mode==='tambah' ? 'Simpan produk' : 'Simpan perubahan'"></span>
            </button>
          </div>
        </form>
      </div>
    </div>
    <!-- MODAL TAMBAH / EDIT END -->

    <!-- MODAL HAPUS START -->
    <div x-show="hapusModal" x-cloak class="fixed inset-0 z-[60] grid place-items-center p-4" role="dialog"
      aria-modal="true" @keydown.escape.window="tutupSemua()">
      <div @click="hapusModal=false" class="modal-overlay"></div>
      <form :action="`{{ url('admin/produk') }}/${target.id}`" method="POST" x-show="hapusModal" x-transition
        class="relative card w-full max-w-sm p-7 text-center">
        @csrf @method('DELETE')
        <span class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-[#FDECEA] text-rose-700 mb-5"><i
            data-lucide="trash-2" class="w-6 h-6"></i></span>
        <h2 class="font-display font-bold text-lg text-cocoa-700 mb-2">Hapus produk ini?</h2>
        <p class="text-sm text-cocoa-400 mb-6"><span class="font-semibold text-cocoa-600" x-text="target?.nama"></span>
          akan dihapus dari katalog.</p>
        <div class="flex gap-3">
          <button type="button" @click="hapusModal=false" class="btn btn-outline flex-1">Batal</button>
          <button type="submit" class="btn btn-danger flex-1 !bg-rose-600 !text-white !border-rose-600">Ya, hapus</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
  @if ($errors->any())
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        if (typeof toast === 'function') toast("{{ $errors->first() }}", 'error');
        else alert("{{ $errors->first() }}");
      });
    </script>
  @endif

  @php
    $mappedProducts = $products->map(function ($p) {
      $photosDb = $p->photos;
      if (is_string($photosDb))
        $photosDb = json_decode($photosDb, true);
      if (!is_array($photosDb) || empty($photosDb))
        $photosDb = $p->photo_main ? [$p->photo_main] : [];

      $fotoPreviews = array_map(function ($path) {
        return asset('storage/' . $path); }, $photosDb);
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

      // ===============================================
      // KUNCI PERBAIKAN: SINKRONISASI LOGIKA TERJUAL
      // Hanya menghitung pesanan yang benar-benar 'selesai'
      // persis seperti di halaman Detail Produk
      // ===============================================
      $terjualAsli = \Illuminate\Support\Facades\DB::table('order_items')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->where('order_items.product_id', $p->id)
        ->where('orders.status', 'selesai')
        ->sum('order_items.quantity');

      return [
        'id' => $p->id,
        'slug' => $p->slug ?? 'produk',
        'nama' => $p->name ?? '',
        'kategori' => $p->category ? $p->category->name : 'Uncategorized',
        'berat' => $p->weight_label ?? '500 gram',
        'harga' => $p->price,
        'hargaCoret' => $p->compare_at_price,
        'stok' => $p->stock_status,
        'po' => $p->min_preorder_days,
        'bestSeller' => $p->is_best_seller == 1,
        'terjual' => (int) $terjualAsli,
        'foto' => $fotoPreviews[0],
        'fotoPreviews' => $fotoPreviews,
        'desc' => $p->description,
        'bahan' => $p->ingredients ?? '',
        'simpan' => $p->storage_note ?? '',
        'pengiriman' => $p->delivery_info ?? 'J&T / ambil sendiri',
        'kondisi' => $p->condition_info ?? 'Dibuat setelah dipesan',
        'varian' => $varian
      ];
    })->values()->all();

    $kategoriList = \App\Models\Category::pluck('name')->all();
  @endphp

  <script>
    function manajemenProduk() {
      return {
        list: @json($mappedProducts),
        KATEGORI: @json($kategoriList),

        q: '', fKategori: '', fStok: '', fBestSeller: '',
        modal: false, hapusModal: false, mode: 'tambah', target: {}, form: {},

        tutupSemua() { this.modal = false; this.hapusModal = false; },

        formatRp(n) { return 'Rp' + Number(n || 0).toLocaleString('id-ID'); },

        init() {
          this.kosongkanForm();
          this.$nextTick(() => { if (typeof icons === 'function') icons() });
          this.$watch('hasil', () => this.$nextTick(() => { if (typeof icons === 'function') icons() }));
        },

        get hasil() {
          const q = this.q.trim().toLowerCase();
          return this.list.filter(p =>
            (!q || (p.nama && p.nama.toLowerCase().includes(q))) &&
            (!this.fKategori || p.kategori === this.fKategori) &&
            (!this.fStok || p.stok === this.fStok) &&
            (!this.fBestSeller || (this.fBestSeller === 'ya' ? p.bestSeller : !p.bestSeller))
          );
        },

        kosongkanForm() {
          this.form = { id: '', nama: '', kategori: '', berat: '', hargaCoret: null, desc: '', stok: 'tersedia', po: 2, bestSeller: false, foto: '', fotoPreviews: [], bahan: '', simpan: '', pengiriman: 'J&T / ambil sendiri', kondisi: 'Dibuat setelah dipesan', varian: [{ name: 'Original', price: '' }] };
        },

        bukaTambah() {
          this.mode = 'tambah';
          this.kosongkanForm();
          this.modal = true;
          this.$nextTick(() => { if (typeof icons === 'function') icons() });
        },

        bukaEdit(p) {
          this.mode = 'edit';
          this.target = p;
          this.form = JSON.parse(JSON.stringify(p));
          this.modal = true;
          this.$nextTick(() => { if (typeof icons === 'function') icons() });
        },

        pilihFoto(e) {
          const files = Array.from(e.target.files);
          if (files.length === 0) return;
          this.form.fotoPreviews = files.map(f => URL.createObjectURL(f));
        },

        konfirmHapus(p) {
          this.target = p;
          this.hapusModal = true;
          this.$nextTick(() => { if (typeof icons === 'function') icons() });
        }
      };
    }
  </script>
@endpush
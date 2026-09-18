@extends('layouts.admin')

@section('title', 'Manajemen Pesanan · 4G Cake & Cookies')
@section('header_title', 'Manajemen Pesanan')
@section('header_subtitle', 'Pantau, filter, dan ubah status seluruh pesanan masuk')

@section('content')
<div x-data="manajemenPesanan()" x-init="init()">

  <!-- FILTER STATUS (chip) -->
  <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1 mb-5" x-cloak>
    <template x-for="s in chipStatus" :key="s.key">
      <button @click="fStatus = s.key"
        class="shrink-0 rounded-full border px-4 py-2 text-sm font-medium transition flex items-center gap-2"
        :class="fStatus === s.key ? 'bg-cocoa-700 border-cocoa-700 text-white' : 'bg-white border-cream-200 text-cocoa-500 hover:border-cocoa-300'">
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
        <button @click="toast('File Excel akan dibuat setelah backend ekspor siap.','info','Ekspor')" class="btn btn-cocoa flex-1">
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
          <template x-for="o in hasil" :key="o.id">
            <tr>
              <td>
                <button @click="lihat(o)" class="font-medium text-cocoa-700 hover:text-rose-600 transition" x-text="o.kode"></button>
                <p class="text-[11px] text-cocoa-300" x-text="o.items.length + ' item'"></p>
              </td>
              <td>
                <p class="text-cocoa-600 truncate max-w-[120px]" x-text="o.pelanggan"></p>
                <p class="text-[11px] text-cocoa-300" x-text="o.hp"></p>
              </td>
              <td class="text-cocoa-400 whitespace-nowrap" x-text="tglID(o.tanggal)"></td>
              <td class="text-cocoa-600 whitespace-nowrap font-medium" x-text="tglID(o.ambil)"></td>
              <td>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold" 
                      :class="o.metode==='J&T' ? 'bg-[#F0FDFA] text-[#0D9488]' : 'bg-cream-100 text-cocoa-600'">
                  <i :data-lucide="o.metode==='J&T' ? 'truck' : 'store'" class="w-3 h-3"></i>
                  <span x-text="o.metode"></span>
                </span>
              </td>
              <td>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold"
                      :class="STATUS_PESANAN[o.status] ? (STATUS_PESANAN[o.status].bg + ' ' + STATUS_PESANAN[o.status].text) : 'bg-gray-100 text-gray-600'">
                  <span class="w-1.5 h-1.5 rounded-full" :class="STATUS_PESANAN[o.status] ? STATUS_PESANAN[o.status].dot : 'bg-gray-400'"></span>
                  <span x-text="STATUS_PESANAN[o.status] ? STATUS_PESANAN[o.status].label : o.status"></span>
                </span>
              </td>
              <td class="text-right font-medium text-cocoa-700 whitespace-nowrap" x-text="formatRp(o.total)"></td>
              <td>
                <div class="flex items-center justify-end gap-1 text-cocoa-500">
                  <button @click="lihat(o)" class="icon-btn tap" title="Lihat detail">
                    <i data-lucide="eye" class="w-4 h-4"></i></button>
                  <button @click="bukaStatus(o)" class="icon-btn tap" title="Ubah status">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i></button>
                  <button @click="cetakStruk(o)" class="icon-btn tap" title="Cetak struk">
                    <i data-lucide="printer" class="w-4 h-4"></i></button>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div x-show="hasil.length === 0" class="py-16 text-center" x-cloak>
      <span class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-cream-100 text-cocoa-300 mb-4"><i data-lucide="inbox" class="w-6 h-6"></i></span>
      <p class="font-display font-semibold text-cocoa-700 mb-1">Tidak ada pesanan pada filter ini</p>
      <p class="text-sm text-cocoa-400 mb-5">Coba pilih status lain atau kosongkan filter tanggal.</p>
      <button @click="q=''; fMetode=''; fTanggal=''; fStatus='semua'" class="btn btn-outline btn-sm mx-auto">Tampilkan semua pesanan</button>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-t border-cream-200 text-sm">
      <p class="text-cocoa-400">Menampilkan <span class="font-semibold text-cocoa-700" x-text="hasil.length"></span> dari <span x-text="list.length"></span> pesanan</p>
      <p class="text-cocoa-400">Nilai terfilter: <span class="font-display font-bold text-cocoa-700" x-text="formatRp(hasil.reduce((a,o)=>a+(Number(o.total)||0),0))"></span></p>
    </div>
  </div>
  <!-- TABEL PESANAN END -->

  <!-- MODAL DETAIL START -->
  <div x-show="detail" x-cloak class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true" @keydown.escape.window="tutupSemua()">
    <div @click="detail=false" class="modal-overlay"></div>
    <div class="relative min-h-full flex items-start sm:items-center justify-center p-4 sm:p-6">
      <div x-show="detail" x-transition class="modal-panel card w-full max-w-2xl overflow-hidden">
        <template x-if="aktif">
          <div>
            <div class="flex items-start justify-between gap-4 px-6 py-4 border-b border-cream-200">
              <div>
                <p class="text-xs text-cocoa-300 mb-0.5">Detail pesanan</p>
                <h2 class="font-display font-bold text-lg text-cocoa-700" x-text="aktif.kode"></h2>
              </div>
              <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border border-white/20"
                      :class="STATUS_PESANAN[aktif.status] ? (STATUS_PESANAN[aktif.status].bg + ' ' + STATUS_PESANAN[aktif.status].text) : ''">
                  <span class="w-1.5 h-1.5 rounded-full" :class="STATUS_PESANAN[aktif.status] ? STATUS_PESANAN[aktif.status].dot : ''"></span>
                  <span x-text="STATUS_PESANAN[aktif.status] ? STATUS_PESANAN[aktif.status].label : aktif.status"></span>
                </span>
                <button @click="detail=false" class="icon-btn tap" aria-label="Tutup"><i data-lucide="x" class="w-5 h-5"></i></button>
              </div>
            </div>

            <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
              <div class="grid sm:grid-cols-2 gap-4">
                <div class="rounded-2xl bg-cream-100 p-4">
                  <p class="text-[11px] text-cocoa-300 mb-2">Pelanggan</p>
                  <p class="font-semibold text-cocoa-700 text-sm" x-text="aktif.pelanggan"></p>
                  <p class="text-xs text-cocoa-400 mt-1" x-text="aktif.hp"></p>
                  <p class="text-xs text-cocoa-400 mt-2 leading-relaxed" x-text="aktif.alamat || 'Diambil di toko'"></p>
                </div>
                <div class="rounded-2xl bg-cream-100 p-4 space-y-2 text-xs">
                  <p class="text-[11px] text-cocoa-300 mb-2">Pengiriman &amp; pembayaran</p>
                  <p class="flex justify-between gap-3"><span class="text-cocoa-400">Metode</span><span class="font-medium text-cocoa-700" x-text="aktif.metode"></span></p>
                  <p class="flex justify-between gap-3"><span class="text-cocoa-400">Tanggal ambil/kirim</span><span class="font-medium text-cocoa-700" x-text="tglID(aktif.ambil)"></span></p>
                  <p class="flex justify-between gap-3"><span class="text-cocoa-400">Pembayaran</span><span class="font-medium text-cocoa-700" x-text="aktif.bayar"></span></p>
                  <p class="flex justify-between gap-3"><span class="text-cocoa-400">Nomor resi</span><span class="font-medium text-cocoa-700" x-text="aktif.resi || '-'"></span></p>
                </div>
              </div>

              <div>
                <p class="label mb-3">Item dipesan</p>
                <div class="space-y-2">
                  <template x-for="it in aktif.items" :key="it.id">
                    <div class="flex items-start gap-3 rounded-2xl border border-cream-200 p-3">
                      <img :src="it.foto" :alt="it.nama" onerror="this.onerror=null; this.src='{{ asset('assets/img/products/placeholder.svg') }}';" class="w-12 h-12 rounded-xl object-cover bg-cream-100 shrink-0 border border-cream-200">
                      <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-cocoa-700 truncate" x-text="it.nama"></p>
                        <p class="text-[11px] text-cocoa-300 mb-1" x-text="formatRp(it.harga) + ' × ' + it.qty"></p>
                        <!-- BADGE VARIAN ADMIN -->
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-cream-200/50 px-2 py-1 text-[10px] font-semibold text-cocoa-600 border border-cream-200">
                          <i data-lucide="tag" class="w-3 h-3 text-cocoa-400"></i> <span x-text="it.varian"></span>
                        </span>
                      </div>
                      <p class="text-sm font-semibold text-cocoa-700 shrink-0" x-text="formatRp((it.harga || 0) * (it.qty || 1))"></p>
                    </div>
                  </template>
                </div>
              </div>

              <dl class="space-y-2 text-sm pt-4 border-t border-cream-200">
                <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Subtotal</dt><dd class="text-cocoa-700" x-text="formatRp((aktif.total || 0) - (aktif.ongkir || 0))"></dd></div>
                <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Ongkos kirim</dt><dd class="text-cocoa-700" x-text="aktif.ongkir ? formatRp(aktif.ongkir) : 'Gratis'"></dd></div>
                <div class="flex justify-between gap-4 pt-2 border-t border-cream-200 items-baseline">
                  <dt class="font-semibold text-cocoa-700">Total</dt>
                  <dd class="font-display font-bold text-xl text-rose-600" x-text="formatRp(aktif.total)"></dd>
                </div>
              </dl>

              <!-- BAGIAN BUKTI PEMBAYARAN CERDAS -->
              <div>
                <p class="label" x-text="aktif.bayar === 'Bayar di Tempat' ? 'Sistem pembayaran' : 'Bukti pembayaran'"></p>
                <div class="rounded-2xl border border-cream-200 p-4 flex items-center gap-4" :class="aktif.bayar === 'Bayar di Tempat' ? 'bg-cream-50' : ''">
                  
                  <!-- JIKA TRANSFER BANK -->
                  <template x-if="aktif.bayar !== 'Bayar di Tempat'">
                    <div class="w-full">
                      <template x-if="aktif.bukti_bayar">
                          <div class="flex w-full items-center gap-4">
                            <img :src="aktif.bukti_bayar" alt="Bukti Transfer" class="w-14 h-14 rounded-xl object-cover bg-cream-100 shrink-0 cursor-pointer hover:opacity-80 border border-cream-200" onclick="window.open(this.src, '_blank')">
                            <div class="min-w-0 flex-1">
                              <p class="text-sm font-medium text-cocoa-700">Bukti Transfer Pelanggan</p>
                              <p class="text-[11px] text-cocoa-300">Klik gambar untuk memperbesar resolusi</p>
                            </div>
                            
                            <button x-show="aktif.status === 'menunggu_pembayaran' || aktif.status === 'menunggu_konfirmasi'" 
                                    @click="verifikasiBukti(aktif)" class="btn btn-outline btn-sm shrink-0 border-green-500 text-green-600 hover:bg-green-50">
                              <i data-lucide="check-circle" class="w-4 h-4"></i> Verifikasi
                            </button>
                          </div>
                      </template>
    
                      <template x-if="!aktif.bukti_bayar">
                          <div class="flex w-full items-center gap-4">
                             <span class="grid place-items-center w-14 h-14 rounded-xl bg-cream-100 text-cocoa-400 shrink-0"><i data-lucide="receipt" class="w-6 h-6"></i></span>
                             <div class="min-w-0 flex-1">
                               <p class="text-sm font-medium text-rose-600">Belum ada bukti transfer</p>
                               <p class="text-[11px] text-cocoa-400">Pelanggan belum mengunggah resi pembayaran</p>
                             </div>
                          </div>
                      </template>
                    </div>
                  </template>

                  <!-- JIKA BAYAR DI TEMPAT -->
                  <template x-if="aktif.bayar === 'Bayar di Tempat'">
                    <div class="flex w-full items-center gap-4">
                      <span class="grid place-items-center w-14 h-14 rounded-xl bg-cream-200 text-gold-600 shrink-0"><i data-lucide="banknote" class="w-6 h-6"></i></span>
                      <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-cocoa-700">Bayar di Tempat (Tunai)</p>
                        <p class="text-[11px] text-cocoa-400">Tidak perlu verifikasi bukti transfer</p>
                      </div>
                      
                      <!-- Tombol langsung proses untuk COD -->
                      <button x-show="aktif.status === 'menunggu_pembayaran' || aktif.status === 'menunggu_konfirmasi'" 
                              @click="verifikasiBukti(aktif)" class="btn btn-outline btn-sm shrink-0 border-blue-500 text-blue-600 hover:bg-blue-50">
                        <i data-lucide="chef-hat" class="w-4 h-4"></i> Proses Pesanan
                      </button>
                    </div>
                  </template>

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
  <div x-show="statusModal" x-cloak class="fixed inset-0 z-[60] grid place-items-center p-4" role="dialog" aria-modal="true" @keydown.escape.window="tutupSemua()">
    <div @click="statusModal=false" class="modal-overlay"></div>
    <div x-show="statusModal" x-transition class="modal-panel card w-full max-w-md p-6">
      <template x-if="aktif">
        <div>
          <h2 class="font-display font-bold text-lg text-cocoa-700 mb-1">Ubah status pesanan</h2>
          <p class="text-sm text-cocoa-400 mb-5"><span class="font-medium text-cocoa-600" x-text="aktif.kode"></span> &middot; <span x-text="aktif.pelanggan"></span></p>
          <div class="space-y-2 mb-6">
            <template x-for="(s, key) in STATUS_PESANAN" :key="key">
              <button @click="statusBaru = key"
                class="w-full flex items-center gap-3 rounded-2xl border p-3 text-left transition"
                :class="statusBaru === key ? 'border-rose-400 bg-blush-50' : 'border-cream-200 hover:border-rose-300'">
                <span class="grid place-items-center w-9 h-9 rounded-xl shrink-0" :class="s.bg + ' ' + s.text"><i :data-lucide="s.icon" class="w-4 h-4"></i></span>
                <span class="text-sm font-medium text-cocoa-700 flex-1" x-text="s.label"></span>
                <span class="w-4 h-4 rounded-full border-2 grid place-items-center" :class="statusBaru === key ? 'border-rose-500' : 'border-cream-300'">
                  <span x-show="statusBaru === key" class="w-2 h-2 rounded-full bg-rose-500"></span></span>
              </button>
            </template>
          </div>
          <div class="flex gap-3">
            <button @click="statusModal=false" class="btn btn-outline flex-1">Batal</button>
            <button @click="simpanStatus()" class="btn btn-primary flex-1" id="btn-simpan-status">Simpan status</button>
          </div>
        </div>
      </template>
    </div>
  </div>
  <!-- MODAL UBAH STATUS END -->
</div>
@endsection

@push('scripts')
@php
    $KOLOM_BUKTI = 'payment_proof';

    $mappedOrders = $orders->map(function($o) use ($KOLOM_BUKTI) {
        $calculatedTotal = 0;

        $items = $o->items->map(function($i) use (&$calculatedTotal) {
            $product = clone $i->product;
            $fotoDb = $product ? $product->photos : null;
            if (is_string($fotoDb)) $fotoDb = json_decode($fotoDb, true);
            if (!is_array($fotoDb) || empty($fotoDb)) $fotoDb = ($product && $product->photo_main) ? [$product->photo_main] : [];
            $foto = !empty($fotoDb) ? asset('storage/' . $fotoDb[0]) : asset('assets/img/products/placeholder.svg');

            $harga = (int) ($i->price ?: ($product ? $product->price : 0));
            $qty = (int) ($i->quantity ?: 1);
            $calculatedTotal += ($harga * $qty);

            $varianAsliProduct = [];
            if ($product && $product->variant_options) {
                $varianAsliProduct = is_string($product->variant_options) ? json_decode($product->variant_options, true) : $product->variant_options;
                if (!is_array($varianAsliProduct)) $varianAsliProduct = [];
                if (!empty($varianAsliProduct) && isset($varianAsliProduct[0]) && is_array($varianAsliProduct[0])) {
                    $varianAsliProduct = array_column($varianAsliProduct, 'name');
                }
            }
            $opsiLower = array_map('strtolower', array_map('trim', $varianAsliProduct));
            $defaultVarian = count($varianAsliProduct) > 0 ? $varianAsliProduct[0] : 'Original';
            
            $varianBeli = null;

            foreach ((array)$i->getAttributes() as $key => $val) {
                if (!empty($val) && is_string($val)) {
                    $valLower = strtolower(trim($val));
                    $index = array_search($valLower, $opsiLower);
                    if ($index !== false) {
                        $varianBeli = $varianAsliProduct[$index];
                        break;
                    }
                    if ((str_starts_with(trim($val), '{') && str_ends_with(trim($val), '}')) || (str_starts_with(trim($val), '[') && str_ends_with(trim($val), ']'))) {
                        $decoded = json_decode($val, true);
                        if (is_array($decoded)) {
                            $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($decoded));
                            foreach($iterator as $jVal) {
                                if (is_string($jVal)) {
                                    $idx = array_search(strtolower(trim($jVal)), $opsiLower);
                                    if ($idx !== false) {
                                        $varianBeli = $varianAsliProduct[$idx];
                                        break 2;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if (!$varianBeli) {
                $varianBeli = $i->variant ?? ($i->varian ?? ($i->variant_name ?? ($i->variant_snapshot ?? null)));
            }
            if (empty(trim($varianBeli)) || strtolower(trim($varianBeli)) === 'null') {
                $varianBeli = $defaultVarian;
            }
            if (strtolower(trim($varianBeli)) === 'original' && !in_array('original', $opsiLower)) {
                $varianBeli = $defaultVarian;
            }

            return [
                'id' => $i->id,
                'nama' => $i->product_name ?? ($product ? $product->name : 'Produk Terhapus'),
                'slug' => $product ? $product->slug : 'produk',
                'varian' => $varianBeli,
                'harga' => $harga,
                'qty' => $qty,
                'foto' => $foto
            ];
        })->values()->all();

        $totalFinal = (int) ($o->total_amount ?: 0);
        if ($totalFinal === 0) {
            $totalFinal = $calculatedTotal + (int) ($o->shipping_fee ?: 0);
        }

        $tglAmbilRaw = $o->delivery_date ?? $o->shipping_date ?? $o->pickup_date;
        if (!$tglAmbilRaw && $o->created_at) {
            $poOffset = 2; 
            if ($o->items->first() && $o->items->first()->product) {
                $poOffset = $o->items->first()->product->min_preorder_days ?? 2;
            }
            $tglAmbilRaw = \Carbon\Carbon::parse($o->created_at)->addDays($poOffset);
        }

        $metodeAsli = strtolower($o->delivery_method ?? '');
        if (str_contains($metodeAsli, 'jnt') || str_contains($metodeAsli, 'j&t')) {
            $metodeBagus = 'J&T';
        } elseif (str_contains($metodeAsli, 'pickup') || str_contains($metodeAsli, 'ambil')) {
            $metodeBagus = 'Ambil di Tempat';
        } else {
            $metodeBagus = $o->delivery_method ?? 'Ambil di Tempat';
        }

        // ==========================================
        // ALGORITMA SCANNER PEMBAYARAN & LOGIKA CERDAS ADMIN
        // ==========================================
        $payMethodRaw = $o->payment_method ?? $o->metode_pembayaran ?? $o->metode_bayar ?? null;
        $isTunai = false;
        $isTransfer = false;
        $bankName = '';

        if ($payMethodRaw) {
            $valLower = strtolower($payMethodRaw);
            if (preg_match('/tunai|cash|tempat|cod|toko/', $valLower)) $isTunai = true;
            elseif (preg_match('/bca/', $valLower)) { $isTransfer = true; $bankName = ' BCA'; }
            elseif (preg_match('/mandiri/', $valLower)) { $isTransfer = true; $bankName = ' Mandiri'; }
            elseif (preg_match('/bsi/', $valLower)) { $isTransfer = true; $bankName = ' BSI'; }
            else { $isTransfer = true; } 
        } else {
            foreach ($o->getAttributes() as $key => $val) {
                if (!empty($val) && is_string($val)) {
                    $valLower = strtolower($val);
                    if (in_array($key, ['customer_name', 'delivery_address', 'customer_phone', 'notes'])) continue;
                    
                    if (preg_match('/tunai|cash|tempat|cod|bayar_di_toko/', $valLower)) {
                        $isTunai = true; break;
                    }
                    if (preg_match('/bca|mandiri|bsi/', $valLower, $matches)) {
                        $isTransfer = true; $bankName = ' ' . strtoupper($matches[0]); break;
                    }
                }
            }
        }

        if ($isTunai) {
            $bayarText = 'Bayar di Tempat';
        } elseif ($isTransfer) {
            $bayarText = 'Transfer' . $bankName;
        } else {
            if ($metodeBagus === 'Ambil di Tempat') {
                $bayarText = 'Bayar di Tempat';
            } else {
                $bayarText = 'Transfer Bank'; 
            }
        }

        $statusMentah = strtolower(trim($o->status ?? 'menunggu_pembayaran'));
        $statusBagus = str_replace(' ', '_', $statusMentah);
        if ($statusBagus === 'siap_diambil' || $statusBagus === 'menunggu_konfirmasi') $statusBagus = 'dikemas';

        $buktiDb = null;
        $buktiUrl = null;
        
        // MENCEGAH PENCARIAN GAMBAR BUKTI JIKA METODENYA BAYAR DI TEMPAT
        if ($bayarText !== 'Bayar di Tempat') {
            $kolomUmum = [$KOLOM_BUKTI, 'bukti_pembayaran', 'bukti_bayar', 'receipt', 'struk', 'bukti_tf'];
            foreach ($kolomUmum as $col) {
                if (!empty($o->$col)) {
                    $buktiDb = $o->$col;
                    break;
                }
            }
            
            if ($buktiDb) {
                $buktiUrl = str_starts_with($buktiDb, 'http') ? $buktiDb : asset('storage/' . $buktiDb);
            } else {
                foreach ($o->getAttributes() as $key => $val) {
                    if (is_string($val) && preg_match('/\.(jpg|jpeg|png|webp|pdf)$/i', $val)) {
                        $buktiUrl = str_starts_with($val, 'http') ? $val : asset('storage/' . $val);
                        break;
                    }
                }
            }
        }

        return [
            'id' => $o->id,
            'kode' => $o->order_number ?? 'KODE-ERR',
            'pelanggan' => $o->customer_name ?? 'Tanpa Nama',
            'hp' => $o->customer_phone ?? '-',
            'alamat' => $o->delivery_address,
            'tanggal' => $o->created_at ? \Carbon\Carbon::parse($o->created_at)->format('Y-m-d') : '-',
            'ambil' => $tglAmbilRaw ? \Carbon\Carbon::parse($tglAmbilRaw)->format('Y-m-d') : '-',
            'waktu_ambil' => $o->delivery_time,
            'metode' => $metodeBagus,
            'status' => $statusBagus,
            'total' => $totalFinal,
            'ongkir' => (int) ($o->shipping_fee ?? 0),
            'bayar' => $bayarText,
            'bukti_bayar' => $buktiUrl,
            'resi' => optional($o->shipping)->tracking_number,
            'items' => $items
        ];
    })->values()->all();
@endphp

<script>
  function manajemenPesanan() {
    const rawData = @json($mappedOrders);
    
    const mapStatus = {
        'menunggu_pembayaran': { label: 'Menunggu Pembayaran', text: 'text-[#9A7332]', bg: 'bg-[#FDF8EA]', dot: 'bg-[#D4A853]', icon: 'wallet' },
        'diproses': { label: 'Diproses', text: 'text-[#2D68F8]', bg: 'bg-[#EEF3FF]', dot: 'bg-[#2D68F8]', icon: 'chef-hat' },
        'dikemas': { label: 'Dikemas', text: 'text-[#7C3AED]', bg: 'bg-[#F5F3FF]', dot: 'bg-[#8B5CF6]', icon: 'package' },
        'dikirim': { label: 'Dikirim', text: 'text-[#0D9488]', bg: 'bg-[#F0FDFA]', dot: 'bg-[#14B8A6]', icon: 'truck' },
        'selesai': { label: 'Selesai', text: 'text-[#16A34A]', bg: 'bg-[#F0FDF4]', dot: 'bg-[#22C55E]', icon: 'check-circle-2' },
        'dibatalkan': { label: 'Dibatalkan', text: 'text-[#E11D48]', bg: 'bg-[#FFF1F2]', dot: 'bg-[#F43F5E]', icon: 'x-circle' }
    };

    return {
      list: rawData || [],
      STATUS_PESANAN: mapStatus,
      q:'', fMetode:'', fTanggal:'', fStatus:'semua',
      detail:false, statusModal:false, aktif:null, statusBaru:'',
      chipStatus: [
          { key:'semua', label:'Semua' },
          ...Object.entries(mapStatus).map(([key, v]) => ({ key, label: v.label }))
      ],

      tutupSemua() { this.detail = false; this.statusModal = false; },
      init() { 
          this.$nextTick(() => { if(typeof icons === 'function') icons() }); 
          this.$watch('hasil', () => this.$nextTick(() => { if(typeof icons === 'function') icons() })); 
      },

      formatRp(n) { return 'Rp' + Number(n || 0).toLocaleString('id-ID'); },

      cetakStruk(o) {
        const baris = o.items.map(it => `
          <tr>
            <td>${it.nama}${it.varian ? ' (' + it.varian + ')' : ''}</td>
            <td style="text-align:center">${it.qty}</td>
            <td style="text-align:right">${this.formatRp(it.harga)}</td>
            <td style="text-align:right">${this.formatRp(it.harga * it.qty)}</td>
          </tr>`).join('');

        const html = `<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><title>Struk ${o.kode}</title>
        <style>
          body{font-family:'Courier New',monospace;max-width:340px;margin:24px auto;color:#222;font-size:13px;}
          h1{font-size:16px;text-align:center;margin:0 0 2px}
          .sub{text-align:center;font-size:11px;color:#555;margin-bottom:14px}
          hr{border:none;border-top:1px dashed #999;margin:10px 0}
          table{width:100%;border-collapse:collapse}
          td{padding:3px 0;vertical-align:top}
          .total td{font-weight:bold;font-size:14px;padding-top:6px}
        </style></head><body>
        <h1>4G Cake &amp; Cookies</h1>
        <p class="sub">Gg. Kb. Jukut 4 No.18/26, Ciroyom, Kec. Andir, Kota Bandung</p>
        <hr>
        <p>No. Pesanan: <b>${o.kode}</b><br>Tanggal: ${this.tglID(o.tanggal)}<br>Pelanggan: ${o.pelanggan} (${o.hp})<br>Metode: ${o.metode}</p>
        <hr>
        <table><thead><tr><td>Item</td><td style="text-align:center">Qty</td><td style="text-align:right">Harga</td><td style="text-align:right">Subtotal</td></tr></thead>
        <tbody>${baris}</tbody></table>
        <hr>
        <table>
          <tr><td>Ongkos kirim</td><td style="text-align:right">${this.formatRp(o.ongkir || 0)}</td></tr>
          <tr class="total"><td>TOTAL</td><td style="text-align:right">${this.formatRp(o.total)}</td></tr>
        </table>
        <hr>
        <p style="text-align:center">Pembayaran: ${o.bayar}<br>Terima kasih sudah berbelanja!</p>
        </body></html>`;

        const w = window.open('', '_blank', 'width=420,height=640');
        if (!w) { toast('Izinkan pop-up di browser untuk mencetak struk.', 'error'); return; }
        w.document.write(html);
        w.document.close();
        w.focus();
        setTimeout(() => w.print(), 300);
      },
      
      tglID(tgl) { 
         if (!tgl || tgl === '-') return '-';
         if (typeof window.tglID === 'function') return window.tglID(tgl);
         
         const d = new Date(tgl);
         if (isNaN(d)) return tgl;
         const bln = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
         return `${d.getDate()} ${bln[d.getMonth()]} ${d.getFullYear()}`;
      },

      jumlah(key) { return key === 'semua' ? this.list.length : this.list.filter(o => o.status === key).length; },

      get hasil() {
        const query = (this.q || '').trim().toLowerCase();
        return this.list.filter(o => {
            const matchStatus = this.fStatus === 'semua' || o.status === this.fStatus;
            const matchMetode = !this.fMetode || o.metode === this.fMetode;
            const matchTgl = !this.fTanggal || o.ambil === this.fTanggal;
            
            const kode = (o.kode || '').toLowerCase();
            const pelanggan = (o.pelanggan || '').toLowerCase();
            const matchQ = !query || kode.includes(query) || pelanggan.includes(query);
            
            return matchStatus && matchMetode && matchTgl && matchQ;
        });
      },

      lihat(o) { this.aktif = o; this.detail = true; this.$nextTick(() => { if(typeof icons === 'function') icons() }); },
      
      bukaStatus(o) { this.aktif = o; this.statusBaru = o.status; this.statusModal = true; this.$nextTick(() => { if(typeof icons === 'function') icons() }); },
      
      verifikasiBukti(o) {
          const msg = o.bayar === 'Bayar di Tempat' 
              ? 'Terima pesanan COD ini dan ubah status menjadi DIPROSES?' 
              : 'Tandai bukti transfer ini valid dan ubah status pesanan menjadi DIPROSES?';
              
          if(!confirm(msg)) return;
          this.aktif = o;
          this.statusBaru = 'diproses';
          this.simpanStatus();
      },

      simpanStatus() {
        if (!this.aktif || this.statusBaru === this.aktif.status) { 
            if(typeof toast === 'function') toast('Status tidak berubah.', 'info'); 
            this.statusModal = false; 
            return; 
        }

        const btn = document.getElementById('btn-simpan-status');
        const ogText = btn ? btn.innerText : 'Simpan status';
        if(btn) btn.innerHTML = 'Menyimpan...';

        fetch(`{{ url('admin/pesanan') }}/${this.aktif.id}/status`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ status: this.statusBaru })
        })
        .then(async response => {
            const data = await response.json().catch(() => null);
            if (!response.ok) {
                throw new Error(data && data.message ? data.message : 'Gagal mengirim data ke server.');
            }
            return data;
        })
        .then(data => {
            if(btn) btn.innerText = ogText;
            if (data.status === 'success') {
                const labelLama = this.STATUS_PESANAN[this.aktif.status] ? this.STATUS_PESANAN[this.aktif.status].label : this.aktif.status;
                const labelBaru = this.STATUS_PESANAN[this.statusBaru] ? this.STATUS_PESANAN[this.statusBaru].label : this.statusBaru;
                this.aktif.status = this.statusBaru;

                if(typeof toast === 'function') toast(`${this.aktif.kode}: ${labelLama} → ${labelBaru}`, 'success');
                this.statusModal = false;
                this.$nextTick(() => { if(typeof icons === 'function') icons() });
            } else {
                if(typeof toast === 'function') toast('Gagal mengubah status', 'error');
            }
        }).catch(err => {
            if(btn) btn.innerText = ogText;
            if(typeof toast === 'function') toast(err.message, 'error');
        });
      }
    };
  }
</script>
@endpush

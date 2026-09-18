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
      <p class="text-cocoa-400 text-sm">Semua pesanan Anda beserta perkembangannya.</p>
    </div>
    <a href="{{ route('catalog') }}" class="btn btn-primary btn-sm"><i data-lucide="plus" class="w-4 h-4"></i> Pesan lagi</a>
  </div>

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

  <div class="space-y-5">
    <template x-for="o in hasil" :key="o.kode">
      <article class="card overflow-hidden">
        <div class="flex flex-wrap items-center gap-3 px-5 sm:px-6 py-4 bg-cream-100/70 border-b border-cream-200">
          <div>
            <p class="font-display font-bold text-cocoa-700" x-text="o.kode"></p>
            <p class="text-[11px] text-cocoa-300">Dipesan <span x-text="tglID(o.tanggal)"></span></p>
          </div>
          <span class="badge" :class="STATUS_PESANAN[o.status] ? STATUS_PESANAN[o.status].cls : ''">
            <span class="badge-dot" :class="STATUS_PESANAN[o.status] ? STATUS_PESANAN[o.status].dot : ''"></span>
            <span x-text="STATUS_PESANAN[o.status] ? STATUS_PESANAN[o.status].label : o.status"></span>
          </span>
          <div class="ml-auto text-right">
            <p class="text-[11px] text-cocoa-300">Total pembayaran</p>
            <p class="font-display font-bold text-cocoa-700" x-text="rp(o.total)"></p>
          </div>
        </div>

        <div class="p-5 sm:p-6 grid lg:grid-cols-[1.2fr_1fr] gap-6">
          <div>
            <div class="space-y-3 mb-5">
              <template x-for="it in o.items" :key="it.nama">
                <div class="flex items-start gap-3">
                  <img :src="it.foto" :alt="it.nama" onerror="this.onerror=null; this.src='{{ asset('assets/img/products/placeholder.svg') }}';" class="w-14 h-14 rounded-xl object-cover bg-cream-100 shrink-0 border border-cream-200">
                  <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-cocoa-700 truncate" x-text="it.nama"></p>
                    <p class="text-[11px] text-cocoa-300 mb-1" x-text="rp(it.harga) + ' × ' + it.qty"></p>
                    
                    <span class="inline-flex items-center gap-1 rounded bg-cream-200/50 px-1.5 py-0.5 text-[10px] font-semibold text-cocoa-500 border border-cream-200">
                      <i data-lucide="tag" class="w-3 h-3 text-cocoa-400"></i> <span x-text="it.varian"></span>
                    </span>
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
                <p class="text-cocoa-300 mb-1" x-text="o.metode.includes('J&T') ? 'Nomor resi J&T' : 'Metode'"></p>
                <p class="font-medium text-cocoa-700 flex items-center gap-2">
                  <span x-text="o.metode.includes('J&T') ? (o.resi !== '-' ? o.resi : 'Belum tersedia') : 'Ambil di tempat'"></span>
                  <button type="button" x-show="o.metode.includes('J&T') && o.resi !== '-'" @click="toast('Nomor resi ' + o.resi + ' disalin.','success')" class="icon-btn !w-9 !h-9 tap" aria-label="Salin nomor resi">
                    <i data-lucide="copy" class="w-4 h-4"></i></button>
                </p>
              </div>
            </div>
          </div>

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
          
          <a x-show="o.status === 'selesai' && !o.sudah_diulas" :href="'{{ url('/review') }}/' + o.kode" class="btn btn-gold btn-sm">
            <i data-lucide="star" class="w-4 h-4"></i> Beri review
          </a>
          <a x-show="o.status === 'selesai' && o.sudah_diulas" :href="o.url_ulasan" class="btn btn-outline btn-sm">
            <i data-lucide="star" class="w-4 h-4 text-gold-500 fill-current"></i> Lihat ulasan
          </a>
          
          <button x-show="o.status === 'menunggu'" @click="batal(o)" class="btn btn-ghost btn-sm ml-auto text-cocoa-300">Batalkan pesanan</button>
          
          <a :href="'https://wa.me/6287714391814?text=Halo%20Admin%204G%20Cake,%20saya%20butuh%20bantuan%20mengenai%20pesanan%20saya%20dengan%20kode:%20' + o.kode" target="_blank" class="btn btn-ghost btn-sm" :class="o.status !== 'menunggu' && 'ml-auto'">
            <i data-lucide="headphones" class="w-4 h-4"></i> Bantuan
          </a>
        </div>
      </article>
    </template>
  </div>

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
@php
    $statusMap = [
        'menunggu_pembayaran' => 'menunggu',
        'menunggu_konfirmasi' => 'menunggu',
        'diproses' => 'diproses',
        'siap_diambil' => 'dikemas',
        'dikemas' => 'dikemas',
        'dikirim' => 'dikirim',
        'selesai' => 'selesai',
        'dibatalkan' => 'dibatalkan'
    ];

    $mappedOrders = $orders->map(function($o) use ($statusMap) {
        $metodeRaw = strtolower($o->delivery_method ?? '');
        $metode_pengiriman = (str_contains($metodeRaw, 'jnt') || str_contains($metodeRaw, 'j&t')) ? 'Dikirim via J&T Express' : 'Ambil di Tempat';
        $alamat = $metode_pengiriman === 'Ambil di Tempat' ? 'Ambil di toko' : ($o->delivery_address ?? 'Alamat tidak ditemukan');

        $items = $o->items ?? collect();
        $itemIds = [];
        $slugPertama = 'produk';

        $mappedItems = $items->map(function($it) use (&$itemIds, &$slugPertama) {
            $slug = $it->product->slug ?? 'produk';
            if ($slugPertama === 'produk' && $slug !== 'produk') $slugPertama = $slug;
            if (isset($it->id)) $itemIds[] = $it->id;

            $fotoDb = $it->product->photo_main ?? null;
            if (!$fotoDb && $it->product && $it->product->photos) {
                $pArr = is_string($it->product->photos) ? json_decode($it->product->photos, true) : $it->product->photos;
                if (is_array($pArr) && count($pArr) > 0) $fotoDb = $pArr[0];
            }
            $foto = $fotoDb ? asset('storage/' . $fotoDb) : asset('assets/img/products/placeholder.svg');

            $varianAsliProduct = [];
            if ($it->product && $it->product->variant_options) {
                $varianAsliProduct = is_string($it->product->variant_options) ? json_decode($it->product->variant_options, true) : $it->product->variant_options;
                if (!is_array($varianAsliProduct)) $varianAsliProduct = [];
                if (!empty($varianAsliProduct) && isset($varianAsliProduct[0]) && is_array($varianAsliProduct[0])) {
                    $varianAsliProduct = array_column($varianAsliProduct, 'name');
                }
            }
            
            $opsiLower = array_map('strtolower', array_map('trim', $varianAsliProduct));
            $defaultVarian = count($varianAsliProduct) > 0 ? $varianAsliProduct[0] : 'Original';
            
            $varianBeli = null;

            foreach ((array)$it->getAttributes() as $key => $val) {
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
                $varianBeli = $it->variant ?? ($it->varian ?? ($it->variant_name ?? ($it->variant_snapshot ?? null)));
            }

            if (empty(trim($varianBeli)) || strtolower(trim($varianBeli)) === 'null') {
                $varianBeli = $defaultVarian;
            }

            if (strtolower(trim($varianBeli)) === 'original' && !in_array('original', $opsiLower)) {
                $varianBeli = $defaultVarian;
            }

            return [
                'nama' => $it->product_name ?? $it->product_name_snapshot ?? 'Produk',
                'slug' => $slug,
                'qty' => (int) $it->quantity,
                'harga' => (int) ($it->price ?? $it->price_snapshot ?? 0),
                'varian' => $varianBeli, 
                'foto' => $foto
            ];
        });

        $reviewCount = 0;
        if (count($itemIds) > 0 && \Illuminate\Support\Facades\Schema::hasTable('reviews')) {
            try {
                $reviewCount = \Illuminate\Support\Facades\DB::table('reviews')
                    ->whereIn('order_item_id', $itemIds)
                    ->count();
            } catch(\Exception $e) {}
        }
        $sudahDiulas = count($itemIds) > 0 && $reviewCount >= count($itemIds);

        $tglAmbilRaw = $o->delivery_date ?? $o->shipping_date ?? $o->pickup_delivery_date ?? $o->pickup_date;
        $tglKirimFix = $tglAmbilRaw ? \Carbon\Carbon::parse($tglAmbilRaw)->format('Y-m-d') : ($o->created_at ? \Carbon\Carbon::parse($o->created_at)->addDays(2)->format('Y-m-d') : '-');

        return [
            'kode' => $o->order_number,
            'tanggal' => $o->created_at ? \Carbon\Carbon::parse($o->created_at)->format('Y-m-d') : '-',
            'jamPesan' => $o->created_at ? \Carbon\Carbon::parse($o->created_at)->format('H:i') : '-',
            'ambil' => $tglKirimFix,
            'jam' => $o->delivery_time ?? $o->pickup_delivery_slot ?? 'Pagi/Siang',
            'metode' => $metode_pengiriman,
            'status' => $statusMap[strtolower($o->status ?? 'menunggu_pembayaran')] ?? 'menunggu',
            'total' => (int) ($o->total_amount ?? $o->total ?? 0),
            'resi' => optional($o->shipping)->tracking_number ?: '-',
            'alamat' => $alamat,
            'sudah_diulas' => $sudahDiulas,
            'url_ulasan' => url('/produk/' . $slugPertama) . '#ulasan',
            'items' => $mappedItems
        ];
    })->values()->all();
@endphp

<script>
  function riwayat() {
    return {
      list: @json($mappedOrders),
      
      STATUS_PESANAN: {
          'menunggu': { label: 'Menunggu', cls: 'badge-wait', dot: 'bg-gold-500' },
          'diproses': { label: 'Diproses', cls: 'badge-info', dot: 'bg-blue-500' },
          'dikemas': { label: 'Dikemas', cls: 'badge-info', dot: 'bg-purple-500' },
          'dikirim': { label: 'Dikirim', cls: 'badge-done', dot: 'bg-teal-500' },
          'selesai': { label: 'Selesai', cls: 'badge-done', dot: 'bg-green-500' },
          'dibatalkan': { label: 'Dibatalkan', cls: 'badge-cancel', dot: 'bg-rose-500' }
      }, 
      f: 'semua',

      get chip() {
         return [{key:'semua',label:'Semua'}, ...Object.entries(this.STATUS_PESANAN).map(([k,v]) => ({key:k, label:v.label}))];
      },

      init() { 
          this.$nextTick(() => { if(typeof icons === 'function') icons() }); 
          this.$watch('hasil', () => this.$nextTick(() => { if(typeof icons === 'function') icons() })); 
      },
      
      jumlah(k) { return k === 'semua' ? this.list.length : this.list.filter(o => o.status === k).length; },
      get hasil() { return this.f === 'semua' ? this.list : this.list.filter(o => o.status === this.f); },

      tglID(tgl, includeYear = false) {
         if (!tgl || tgl === '-') return '-';
         const cleanTgl = tgl.includes('T') ? tgl.split('T')[0] : tgl;
         const d = new Date(cleanTgl);
         if (isNaN(d)) return cleanTgl;
         const bln = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
         return `${d.getDate()} ${bln[d.getMonth()]} ${includeYear ? d.getFullYear() : ''}`;
      },
      rp(n) { return 'Rp' + Number(n || 0).toLocaleString('id-ID'); },

      timeline(o) { 
          const S = o.status;
          return [
              { id:'menunggu', label:'Dipesan', ket:'Sistem menunggu konfirmasi', icon:'wallet', aktif: true },
              { id:'diproses', label:'Diproses', ket:'Dapur menyiapkan pesanan', icon:'chef-hat', aktif: S!=='menunggu' && S!=='menunggu_pembayaran' },
              { id:'dikemas', label:'Dikemas', ket:'Kue dikemas dengan aman', icon:'package', aktif: ['dikemas','dikirim','selesai'].includes(S) },
              { id:'kirim', label: o.metode.includes('J&T') ? 'Dikirim' : 'Siap Diambil', ket: o.metode.includes('J&T') ? 'Paket dibawa kurir' : 'Menunggu diambil', icon: o.metode.includes('J&T') ? 'truck' : 'store', aktif: ['dikirim','selesai'].includes(S) },
              { id:'selesai', label:'Selesai', ket:'Pesanan selesai', icon:'check-circle-2', aktif: S==='selesai' }
          ];
      },

      batal(o) {
        if(typeof konfirmasi === 'function') {
            konfirmasi({
              judul: 'Batalkan pesanan ini?',
              pesan: `Pesanan <span class="font-semibold text-cocoa-600">${o.kode}</span> akan dibatalkan.`,
              label: 'Ya, batalkan',
              ikon: 'x-circle',
              aksi: () => {
                o.status = 'dibatalkan';
                if(typeof toast === 'function') toast(o.kode + ' dibatalkan.', 'warning');
                this.$nextTick(() => { if(typeof icons === 'function') icons() });
              }
            });
        }
      }
    };
  }
</script>
@endpush

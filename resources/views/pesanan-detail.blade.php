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
      <span class="badge" :class="STATUS_PESANAN[o.status] ? STATUS_PESANAN[o.status].cls : ''">
        <span class="badge-dot"></span><span x-text="STATUS_PESANAN[o.status] ? STATUS_PESANAN[o.status].label : o.status"></span>
      </span>
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
        <p class="text-sm font-medium text-cocoa-700" x-text="o.metode"></p>
      </div>
    </div>
  </div>

  <div class="grid lg:grid-cols-[1.4fr_1fr] gap-6 items-start">
    <div class="space-y-6">
      <!-- ITEM -->
      <div class="card p-6">
        <h2 class="font-display font-bold text-cocoa-700 mb-5">Rincian pesanan</h2>
        <div class="space-y-4">
          <template x-for="it in o.items" :key="it.id">
            <div class="flex items-center gap-4">
              <a :href="'{{ url('/produk') }}/' + it.slug" class="w-16 h-16 rounded-2xl overflow-hidden bg-cream-100 shrink-0 border border-cream-200">
                <img :src="it.foto" :alt="it.nama" onerror="this.onerror=null; this.src='{{ asset('assets/img/products/placeholder.svg') }}';" class="w-full h-full object-cover">
              </a>
              <div class="min-w-0 flex-1">
                <p class="font-medium text-cocoa-700 truncate" x-text="it.nama"></p>
                <p class="text-xs text-cocoa-300" x-text="rp(it.harga) + ' × ' + it.qty + ' (' + it.varian + ')'"></p>
              </div>
              <p class="font-semibold text-cocoa-700 shrink-0" x-text="rp(it.harga * it.qty)"></p>
            </div>
          </template>
        </div>
        <dl class="space-y-2.5 text-sm mt-6 pt-5 border-t border-cream-200">
          <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Subtotal produk</dt><dd class="text-cocoa-700" x-text="rp(o.total - o.ongkir - o.kemasan)"></dd></div>
          <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Biaya kemasan</dt><dd class="text-cocoa-700" x-text="rp(o.kemasan)"></dd></div>
          <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Ongkos kirim</dt><dd class="text-cocoa-700" x-text="o.ongkir > 0 ? rp(o.ongkir) : 'Gratis'"></dd></div>
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
        <div x-show="o.resi && o.resi !== '-'" class="mt-4 pt-4 border-t border-cream-200">
          <p class="text-[11px] text-cocoa-300 mb-1">Nomor resi pengiriman</p>
          <p class="font-medium text-cocoa-700 text-sm flex items-center gap-2">
            <span x-text="o.resi"></span>
            <button type="button" @click="toast('Nomor resi disalin.','success')" class="icon-btn !w-9 !h-9 tap" aria-label="Salin nomor resi"><i data-lucide="copy" class="w-4 h-4"></i></button>
          </p>
        </div>
      </div>

      <div class="card p-6">
        <h2 class="font-display font-bold text-cocoa-700 mb-4">Pembayaran</h2>
        <p class="text-sm text-cocoa-500 mb-3 font-semibold" x-text="o.bayar"></p>
        
        <!-- JIKA TRANSFER BANK -->
        <template x-if="o.bayar !== 'Bayar di Tempat'">
          <div>
            <template x-if="o.bukti">
              <div class="rounded-2xl border border-cream-200 p-4 flex items-center gap-3">
                <span class="grid place-items-center w-14 h-14 rounded-xl bg-cream-100 text-cocoa-400 shrink-0 overflow-hidden border border-cream-200 cursor-pointer hover:opacity-80 transition" onclick="window.open(this.querySelector('img').src, '_blank')">
                    <img :src="o.bukti" alt="Bukti Transfer" class="w-full h-full object-cover">
                </span>
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-medium text-cocoa-700 truncate">Bukti Transfer</p>
                  <p class="text-[11px] text-cocoa-300" x-text="o.status === 'menunggu' ? 'Menunggu verifikasi admin' : 'Sudah diverifikasi'"></p>
                </div>
              </div>
            </template>

            <template x-if="!o.bukti">
              <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 flex items-center gap-3">
                <span class="grid place-items-center w-11 h-11 rounded-xl bg-rose-100 text-rose-500 shrink-0"><i data-lucide="alert-circle" class="w-5 h-5"></i></span>
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-medium text-rose-700 truncate">Menunggu Pembayaran</p>
                  <p class="text-[11px] text-rose-500/80">Belum ada bukti yang diunggah</p>
                </div>
              </div>
            </template>
          </div>
        </template>

        <!-- JIKA BAYAR DI TEMPAT (COD / AMBIL TOKO) -->
        <template x-if="o.bayar === 'Bayar di Tempat'">
          <div class="rounded-2xl border border-cream-200 bg-cream-50 p-4 flex items-center gap-3">
            <span class="grid place-items-center w-11 h-11 rounded-xl bg-cream-200 text-gold-600 shrink-0"><i data-lucide="banknote" class="w-5 h-5"></i></span>
            <div class="min-w-0 flex-1">
              <p class="text-sm font-medium text-cocoa-700 truncate">Bayar di Tempat</p>
              <p class="text-[11px] text-cocoa-400">Dibayarkan saat pesanan diterima/diambil</p>
            </div>
          </div>
        </template>
      </div>

      <div class="card p-6 space-y-2">
        <a href="{{ route('order.history') }}" class="btn btn-outline btn-block"><i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar</a>
        <a x-show="o.status === 'selesai'" :href="'{{ url('/review') }}/' + o.kode" class="btn btn-gold btn-block">
          <i data-lucide="star" class="w-4 h-4"></i> Beri review
        </a>
        <button @click="toast('Struk pesanan disiapkan untuk diunduh.','info')" class="btn btn-ghost btn-block">
          <i data-lucide="download" class="w-4 h-4"></i> Unduh struk
        </button>
      </div>
    </aside>
  </div>
</section>
@endsection

@push('scripts')
@php
    $o = $order;
    $calculatedTotal = 0;

    $mappedItems = $o->items->map(function($it) use (&$calculatedTotal) {
        $product = $it->product;
        
        $fotoDb = $product ? $product->photo_main : null;
        if (!$fotoDb && $product && $product->photos) {
            $photosArr = is_string($product->photos) ? json_decode($product->photos, true) : $product->photos;
            if (is_array($photosArr) && count($photosArr) > 0) {
                $fotoDb = $photosArr[0];
            }
        }
        $foto = $fotoDb ? asset('storage/' . $fotoDb) : asset('assets/img/products/placeholder.svg');

        $harga = (int) ($it->price ?: ($product ? $product->price : 0));
        $qty = (int) ($it->quantity ?: 1);
        $calculatedTotal += ($harga * $qty);

        return [
            'id' => $it->id,
            'nama' => $it->product_name ?? ($product ? $product->name : 'Produk Terhapus'),
            'slug' => $product ? $product->slug : 'produk',
            'qty' => $qty,
            'harga' => $harga,
            'varian' => $it->variant ?? 'Original',
            'foto' => $foto
        ];
    })->values()->all();

    $tglKirimRaw = $o->delivery_date ?? $o->shipping_date ?? $o->pickup_delivery_date ?? $o->pickup_date;
    $tglKirimFix = $tglKirimRaw ? \Carbon\Carbon::parse($tglKirimRaw)->format('Y-m-d') : ($o->created_at ? \Carbon\Carbon::parse($o->created_at)->addDays(2)->format('Y-m-d') : '-');

    $metodeRaw = strtolower($o->delivery_method ?? '');
    $metode_pengiriman = (str_contains($metodeRaw, 'jnt') || str_contains($metodeRaw, 'j&t')) ? 'Dikirim via J&T Express' : 'Ambil di tempat';
    
    $alamat = $metode_pengiriman === 'Ambil di tempat' 
                ? 'Ambil di toko — Jl. Samudera No. 12, Banda Sakti, Lhokseumawe' 
                : ($o->delivery_address ?? 'Alamat tidak ditemukan');

    // ==========================================
    // ALGORITMA SCANNER PEMBAYARAN & LOGIKA CERDAS
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
        else { $isTransfer = true; } // Jika ada teks tapi tidak spesifik, anggap transfer
    } else {
        // SCAN AGRESSIF: Pindai semua kolom untuk mencari kata kunci pembayaran
        foreach ($o->getAttributes() as $key => $val) {
            if (!empty($val) && is_string($val)) {
                $valLower = strtolower($val);
                // Abaikan kolom yang mungkin berisi nama atau alamat
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
        // LOGIKA CERDAS: Jika pembayaran masih kosong, 
        // dan metode kirimnya "Ambil di tempat", asumsikan "Bayar di Tempat"
        if ($metode_pengiriman === 'Ambil di tempat') {
            $bayarText = 'Bayar di Tempat';
        } else {
            $bayarText = 'Transfer Bank'; // Final fallback
        }
    }

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
    $statusMentah = strtolower($o->status ?? 'menunggu_pembayaran');
    $statusJS = $statusMap[$statusMentah] ?? 'menunggu';

    $buktiUrl = null;
    
    // MENCEGAH PENCARIAN GAMBAR BUKTI JIKA METODENYA BAYAR DI TEMPAT
    if ($bayarText !== 'Bayar di Tempat') {
        $kolomUmum = ['payment_proof', 'bukti_pembayaran', 'bukti_bayar', 'receipt', 'struk', 'bukti_tf', 'photo', 'image'];
        foreach ($kolomUmum as $col) {
            if (!empty($o->$col)) {
                $val = $o->$col;
                $buktiUrl = str_starts_with($val, 'http') ? $val : asset('storage/' . $val);
                break;
            }
        }
        if (!$buktiUrl) {
            foreach ($o->getAttributes() as $key => $val) {
                if (is_string($val) && preg_match('/\.(jpg|jpeg|png|webp|pdf)$/i', $val)) {
                    $buktiUrl = str_starts_with($val, 'http') ? $val : asset('storage/' . $val);
                    break;
                }
            }
        }
    }

    $totalAkhir = (int) ($o->total_amount ?? $o->total ?? 0);
    $ongkir = (int) ($o->shipping_fee ?? $o->shipping_cost ?? 0);
    $kemasan = (int) ($o->packaging_fee ?? $o->packaging_cost ?? 0);
    if ($totalAkhir === 0) {
        $totalAkhir = $calculatedTotal + $ongkir + $kemasan;
    }

    $mappedOrder = [
        'kode' => $o->order_number ?? 'PESANAN',
        'pelanggan' => $o->customer_name ?? 'Pelanggan',
        'hp' => $o->customer_phone ?? '-',
        'tanggal' => $o->created_at ? \Carbon\Carbon::parse($o->created_at)->format('Y-m-d') : '-',
        'jamPesan' => $o->created_at ? \Carbon\Carbon::parse($o->created_at)->format('H:i') : '-',
        'ambil' => $tglKirimFix,
        'jam' => $o->delivery_time ?? $o->pickup_delivery_slot ?? 'Pagi/Siang',
        'metode' => $metode_pengiriman,
        'status' => $statusJS,
        'total' => $totalAkhir,
        'ongkir' => $ongkir,
        'kemasan' => $kemasan,
        'bayar' => $bayarText,
        'bukti' => $buktiUrl,
        'alamat' => $alamat,
        'resi' => $o->tracking_number && $o->tracking_number !== '-' ? $o->tracking_number : null,
        'catatan' => $o->notes ?? $o->catatan ?? '',
        'items' => $mappedItems
    ];
@endphp

<script>
  const ORDER = @json($mappedOrder);

  function detailPesanan() {
    return {
      o: ORDER, 
      init() { 
          document.title = this.o.kode + ' · 4G Cake & Cookies'; 
          this.$nextTick(() => { if(typeof icons === 'function') icons() }); 
      },
      get timeline() { 
          if(typeof timelinePesanan === 'function') {
              return timelinePesanan(this.o, true); 
          }
          return [];
      }
    };
  }
</script>
@endpush
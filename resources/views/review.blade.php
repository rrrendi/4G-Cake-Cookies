@extends('layouts.main')

@section('title', 'Beri Review · 4G Cake & Cookies')

@section('content')
<section class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-8 sm:py-12" x-data="formReview()" x-init="init()">
  <nav class="flex items-center gap-2 text-xs text-cocoa-300 mb-5" aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="hover:text-rose-600 transition">Beranda</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <a href="{{ route('order.history') }}" class="hover:text-rose-600 transition">Pesanan Saya</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <span class="text-cocoa-500 font-medium">Beri Review</span>
  </nav>

  <div x-show="!terkirim">
    <div class="text-center mb-8">
      <span class="inline-grid place-items-center w-14 h-14 rounded-3xl bg-cream-200 text-gold-600 mb-4"><i data-lucide="star" class="w-6 h-6"></i></span>
      <h1 class="font-display font-bold text-3xl text-cocoa-700 mb-2">Bagaimana kuenya?</h1>
      <p class="text-cocoa-400 text-sm max-w-md mx-auto">
        Ulasan Anda membantu pelanggan lain memilih, dan membantu kami memperbaiki apa yang masih kurang.
      </p>
    </div>

    <template x-if="!o || o.items.length === 0">
        <div class="card py-16 px-6 text-center">
            <h2 class="font-display font-bold text-xl text-cocoa-700 mb-2">Pesanan Tidak Valid</h2>
            <p class="text-sm text-cocoa-400 mb-6">Ulasan hanya bisa diberikan untuk pesanan yang sudah selesai.</p>
            <a href="{{ route('order.history') }}" class="btn btn-outline">Kembali ke riwayat</a>
        </div>
    </template>

    <div x-show="o && o.items.length > 0" class="card p-6 sm:p-8 space-y-7">
      <div>
        <p class="label flex justify-between">
          <span>Produk yang diulas</span>
          <span class="text-rose-500 font-medium text-xs"><span x-text="sudahReview.length"></span> dari <span x-text="o.items.length"></span> selesai</span>
        </p>
        <p class="text-xs text-cocoa-300 mb-3">Diambil dari pesanan <span class="font-medium text-cocoa-500" x-text="o ? o.kode : ''"></span>.</p>
        <div class="grid sm:grid-cols-2 gap-3">
          <template x-for="it in (o ? o.items : [])" :key="it.id">
            <button @click="if(!sudahReview.includes(it.id)) produk = it"
              class="relative flex items-center gap-3 rounded-2xl border p-3 text-left transition"
              :class="sudahReview.includes(it.id) ? 'border-green-200 bg-green-50 opacity-70 cursor-not-allowed' : (produk && produk.id === it.id ? 'border-rose-400 bg-blush-50' : 'border-cream-200 hover:border-rose-300')">
              
              <img :src="it.foto" :alt="it.nama" onerror="this.onerror=null; this.src='{{ asset('assets/img/products/placeholder.svg') }}';" class="w-12 h-12 rounded-xl object-cover bg-cream-100 shrink-0 border border-cream-200">
              <span class="min-w-0 flex-1">
                <span class="block text-sm font-medium" :class="sudahReview.includes(it.id) ? 'text-green-700 line-through' : 'text-cocoa-700 truncate'" x-text="it.nama"></span>
                <span class="block text-[11px]" :class="sudahReview.includes(it.id) ? 'text-green-600 font-semibold' : 'text-cocoa-300'" x-text="sudahReview.includes(it.id) ? 'Selesai diulas' : it.qty + ' pcs'"></span>
              </span>
              
              <span x-show="sudahReview.includes(it.id)" class="absolute top-3 right-3 w-5 h-5 rounded-full bg-green-500 text-white grid place-items-center shadow-sm">
                <i data-lucide="check" class="w-3 h-3"></i>
              </span>
            </button>
          </template>
        </div>
      </div>

      <div class="rounded-2xl bg-cream-100 p-6 text-center">
        <p class="label !mb-3">Penilaian keseluruhan</p>
        <div class="flex justify-center gap-2 mb-3">
          <template x-for="n in 5" :key="n">
            <button type="button" @click="nilai.total = n" @mouseenter="hover = n" @mouseleave="hover = 0"
              class="p-1 transition-transform hover:scale-110">
              <svg class="w-9 h-9" :class="(hover || nilai.total) >= n ? 'star' : 'star-empty'" viewBox="0 0 24 24" fill="currentColor">
                <path d="m12 17.27 5.18 3.13-1.37-5.9 4.58-3.96-6.03-.52L12 4.5 9.64 10.02l-6.03.52 4.58 3.96-1.37 5.9z"/>
              </svg>
            </button>
          </template>
        </div>
        <p class="text-sm font-medium text-cocoa-600" x-text="labelBintang"></p>
      </div>

      <div>
        <label class="label" for="komentar">Ceritakan pengalaman Anda</label>
        <textarea id="komentar" x-model="teks" rows="5" maxlength="500" class="textarea"
          placeholder="Contoh: teksturnya lembut, manisnya pas..."></textarea>
        <div class="flex justify-between items-center mt-2 text-xs">
          <p class="text-cocoa-400">Minimal 20 karakter.</p>
          <p class="text-cocoa-300"><span x-text="teks.length"></span>/500</p>
        </div>
      </div>

      <div class="flex flex-wrap gap-3 pt-2 border-t border-cream-200 mt-6 pt-6">
        <a href="{{ route('order.history') }}" class="btn btn-outline">Kembali</a>
        <button @click="kirim($event)" class="btn btn-primary ml-auto btn-lg px-8">
          <i data-lucide="send-horizontal" class="w-[18px] h-[18px]"></i> <span x-text="sudahReview.length === (o.items.length - 1) ? 'Selesaikan Review' : 'Kirim & Lanjut'"></span>
        </button>
      </div>
    </div>
  </div>

  <div x-show="terkirim" x-cloak class="card p-8 sm:p-12 text-center">
    <span class="grid place-items-center w-20 h-20 mx-auto rounded-3xl bg-[#E9F6EC] text-green-700 mb-6"><i data-lucide="heart-handshake" class="w-9 h-9"></i></span>
    <h1 class="font-display font-bold text-2xl text-cocoa-700 mb-3">Terima kasih banyak!</h1>
    <p class="text-cocoa-400 max-w-md mx-auto mb-7 leading-relaxed">
      Semua ulasan Anda untuk pesanan <span class="font-semibold text-cocoa-600" x-text="o ? o.kode : ''"></span> telah berhasil disimpan.
    </p>
    <div class="flex flex-wrap justify-center gap-3">
      <a href="{{ route('order.history') }}" class="btn btn-primary">Lihat pesanan saya</a>
      <a href="{{ route('catalog') }}" class="btn btn-outline">Belanja lagi</a>
    </div>
  </div>
</section>
@endsection

@push('scripts')
@php
    $orderRecord = \App\Models\Order::with('items.product')->where('order_number', $kode)->first();
    $mappedOrderR = null;
    $sudahDiulasIds = []; 
    
    if ($orderRecord && $orderRecord->status === 'selesai') {
        $orderItemIds = $orderRecord->items->pluck('id')->toArray();
        $reviewedOrderItemIds = \Illuminate\Support\Facades\DB::table('reviews')
            ->whereIn('order_item_id', $orderItemIds)
            ->pluck('order_item_id')->toArray();

        $mappedOrderR = [
            'kode' => $orderRecord->order_number,
            'items' => $orderRecord->items->map(function($it) use ($reviewedOrderItemIds, &$sudahDiulasIds) {
                // KUNCI PERBAIKAN: Gunakan ID Item Pesanan, bukan ID Produk agar ulasannya tidak bentrok
                if (in_array($it->id, $reviewedOrderItemIds)) {
                    $sudahDiulasIds[] = $it->id;
                }
                $slug = $it->product->slug ?? 'produk';
                $fotoDb = $it->product->photo_main ?? null;
                if (!$fotoDb && $it->product && $it->product->photos) {
                    $pArr = is_string($it->product->photos) ? json_decode($it->product->photos, true) : $it->product->photos;
                    if (is_array($pArr) && count($pArr) > 0) $fotoDb = $pArr[0];
                }
                return [
                    'id' => $it->id,
                    'product_id' => $it->product_id,
                    'nama' => $it->product_name ?? $it->product_name_snapshot ?? 'Produk',
                    'slug' => $slug,
                    'qty' => $it->quantity,
                    'foto' => $fotoDb ? asset('storage/' . $fotoDb) : asset('assets/img/products/placeholder.svg')
                ];
            })->values()->all()
        ];
    }
@endphp

<script>
  const KODE_R = "{{ $kode }}";
  const ORDER_R = @json($mappedOrderR);
  const SUDAH_REVIEW = @json($sudahDiulasIds);

  function formReview() {
    return {
      o: ORDER_R, 
      sudahReview: SUDAH_REVIEW,
      produk: ORDER_R && ORDER_R.items.length > 0 ? (ORDER_R.items.find(i => !SUDAH_REVIEW.includes(i.id)) || ORDER_R.items[0]) : null,
      nilai: { total:5 },
      hover: 0, teks:'', 
      terkirim: SUDAH_REVIEW.length > 0 && SUDAH_REVIEW.length === (ORDER_R ? ORDER_R.items.length : 0),
      
      init() { this.$nextTick(() => { if(typeof icons === 'function') icons() }); this.$watch('terkirim', () => this.$nextTick(() => { if(typeof icons === 'function') icons() })); },
      
      get labelBintang() {
        return ['Belum dinilai','Kecewa','Kurang memuaskan','Cukup','Memuaskan','Sangat memuaskan'][this.hover || this.nilai.total];
      },
      
      kirim(ev) {
        if (this.teks.trim().length < 20) { if(typeof toast === 'function') toast('Tulis minimal 20 karakter.', 'error'); return; }
        const btn = ev.currentTarget;
        let selesai = () => {};
        if (typeof tombolMuat === 'function') selesai = tombolMuat(btn, 'Mengirim...');

        fetch('{{ url('/review') }}/' + KODE_R, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({
                product_id: this.produk ? this.produk.product_id : null,
                rating_total: this.nilai.total,
                komentar: this.teks
            })
        }).then(res => res.json()).then(data => {
            selesai();
            if(data.status === 'success') {
                this.sudahReview.push(this.produk.id);
                const sisaProduk = this.o.items.filter(item => !this.sudahReview.includes(item.id));
                
                if (sisaProduk.length > 0) {
                    if(typeof toast === 'function') toast('Ulasan disimpan. Lanjut ke produk berikutnya!', 'success');
                    this.produk = sisaProduk[0]; 
                    this.nilai.total = 5;       
                    this.teks = '';              
                } else {
                    this.terkirim = true;
                    if(typeof toast === 'function') toast('Semua ulasan selesai dikirim!', 'success');
                    window.scrollTo({ top:0, behavior:'smooth' });
                }
            } else {
                if(typeof toast === 'function') toast(data.message || 'Gagal mengirim', 'error');
            }
        }).catch(err => {
            selesai();
            if(typeof toast === 'function') toast('Terjadi kesalahan jaringan.', 'error');
        });
      }
    };
  }
</script>
@endpush
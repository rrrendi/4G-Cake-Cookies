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

    <div class="card p-6 sm:p-8 space-y-7">
      <!-- Pilih produk -->
      <div>
        <p class="label">Produk yang diulas</p>
        <p class="text-xs text-cocoa-300 mb-3">Diambil dari pesanan <span class="font-medium text-cocoa-500" x-text="o.kode"></span> yang sudah selesai.</p>
        <div class="grid sm:grid-cols-2 gap-3">
          <template x-for="it in o.items" :key="it.slug">
            <button @click="produk = it"
              class="flex items-center gap-3 rounded-2xl border p-3 text-left transition"
              :class="produk.slug === it.slug ? 'border-rose-400 bg-blush-50' : 'border-cream-200 hover:border-rose-300'">
              <img :src="imgProduk(it.slug)" :alt="it.nama" class="w-12 h-12 rounded-xl object-cover bg-cream-100 shrink-0">
              <span class="min-w-0 flex-1">
                <span class="block text-sm font-medium text-cocoa-700 truncate" x-text="it.nama"></span>
                <span class="block text-[11px] text-cocoa-300" x-text="it.qty + ' pcs'"></span>
              </span>
            </button>
          </template>
        </div>
      </div>

      <!-- Rating keseluruhan -->
      <div class="rounded-2xl bg-cream-100 p-6 text-center">
        <p class="label !mb-3">Penilaian keseluruhan</p>
        <div class="flex justify-center gap-2 mb-3">
          <template x-for="n in 5" :key="n">
            <button type="button" @click="nilai.total = n" @mouseenter="hover = n" @mouseleave="hover = 0" :aria-label="'Beri ' + n + ' bintang'"
              class="p-1 transition-transform hover:scale-110">
              <svg class="w-9 h-9" :class="(hover || nilai.total) >= n ? 'star' : 'star-empty'" viewBox="0 0 24 24" fill="currentColor">
                <path d="m12 17.27 5.18 3.13-1.37-5.9 4.58-3.96-6.03-.52L12 4.5 9.64 10.02l-6.03.52 4.58 3.96-1.37 5.9z"/>
              </svg>
            </button>
          </template>
        </div>
        <p class="text-sm font-medium text-cocoa-600" x-text="labelBintang"></p>
      </div>

      <!-- Rating per aspek -->
      <div>
        <p class="label mb-3">Penilaian per aspek</p>
        <div class="space-y-3">
          <template x-for="a in aspek" :key="a.key">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-2 rounded-2xl border border-cream-200 p-3.5">
              <span class="grid place-items-center w-9 h-9 rounded-xl bg-cream-100 text-cocoa-500 shrink-0"><i :data-lucide="a.icon" class="w-4 h-4"></i></span>
              <span class="flex-1 min-w-[8rem] text-sm text-cocoa-600" x-text="a.label"></span>
              <span class="flex gap-0.5 shrink-0">
                <template x-for="n in 5" :key="n">
                  <button type="button" @click="nilai[a.key] = n" :aria-label="a.label + ' ' + n + ' bintang'" class="shrink-0 px-2 py-1.5 -mx-0.5 transition-transform hover:scale-110">
                    <svg class="w-6 h-6" :class="nilai[a.key] >= n ? 'star' : 'star-empty'" viewBox="0 0 24 24" fill="currentColor">
                      <path d="m12 17.27 5.18 3.13-1.37-5.9 4.58-3.96-6.03-.52L12 4.5 9.64 10.02l-6.03.52 4.58 3.96-1.37 5.9z"/>
                    </svg>
                  </button>
                </template>
              </span>
            </div>
          </template>
        </div>
      </div>

      <!-- Komentar -->
      <div>
        <label class="label" for="komentar">Ceritakan pengalaman Anda</label>
        <textarea id="komentar" x-model="teks" rows="5" maxlength="500" class="textarea"
          placeholder="Contoh: teksturnya lembut, manisnya pas, dan sampai dalam kondisi rapi. Pengiriman juga tepat waktu."></textarea>
        <div class="flex justify-between items-center mt-2">
          <p class="hint !mt-0">Minimal 20 karakter supaya bermanfaat untuk pembaca lain.</p>
          <p class="text-xs text-cocoa-300"><span x-text="teks.length"></span>/500</p>
        </div>
      </div>

      <!-- Foto -->
      <div>
        <p class="label">Tambahkan foto <span class="font-normal text-cocoa-300">(opsional)</span></p>
        <label for="foto-review" class="block rounded-2xl border-2 border-dashed border-cream-300 bg-cream-50 hover:border-rose-300 transition cursor-pointer p-6 text-center">
          <template x-if="!foto">
            <span class="block">
              <i data-lucide="camera" class="w-7 h-7 mx-auto text-cocoa-300 mb-2"></i>
              <span class="block text-sm text-cocoa-500">Unggah foto kue yang Anda terima</span>
            </span>
          </template>
          <template x-if="foto">
            <span class="flex items-center justify-center gap-3">
              <img :src="fotoUrl" alt="Pratinjau foto ulasan" class="w-16 h-16 rounded-xl object-cover border border-cream-200">
              <span class="text-sm text-cocoa-500">Foto siap dikirim &middot; klik untuk mengganti</span>
            </span>
          </template>
        </label>
        <input id="foto-review" type="file" accept="image/*" class="hidden" @change="pilihFoto($event)">
      </div>

      <label class="flex items-start gap-3">
        <input type="checkbox" x-model="anonim" class="mt-1 w-4 h-4 rounded border-cream-300 text-rose-500 focus:ring-rose-400 cursor-pointer">
        <span class="text-sm text-cocoa-400 cursor-pointer">Tampilkan sebagai anonim (nama disingkat menjadi inisial)</span>
      </label>

      <div class="flex flex-wrap gap-3 pt-2">
        <a href="{{ route('order.history') }}" class="btn btn-outline">Nanti saja</a>
        <button @click="kirim($event)" class="btn btn-primary ml-auto btn-lg"><i data-lucide="send-horizontal" class="w-[18px] h-[18px]"></i> Kirim review</button>
      </div>
    </div>
  </div>

  <!-- SETELAH TERKIRIM -->
  <div x-show="terkirim" x-cloak class="card p-8 sm:p-12 text-center">
    <span class="grid place-items-center w-20 h-20 mx-auto rounded-3xl bg-[#E9F6EC] text-green-700 mb-6"><i data-lucide="heart-handshake" class="w-9 h-9"></i></span>
    <h1 class="font-display font-bold text-2xl text-cocoa-700 mb-3">Terima kasih atas ulasannya</h1>
    <p class="text-cocoa-400 max-w-md mx-auto mb-7 leading-relaxed">
      Ulasan Anda untuk <span class="font-semibold text-cocoa-600" x-text="produk.nama"></span> sudah kami terima
      dan akan tampil di halaman produk setelah dicek admin.
    </p>
    <div class="inline-flex items-center gap-2 rounded-2xl bg-cream-100 px-6 py-4 mb-7">
      <template x-for="n in 5" :key="n">
        <svg class="w-6 h-6" :class="nilai.total >= n ? 'star' : 'star-empty'" viewBox="0 0 24 24" fill="currentColor">
          <path d="m12 17.27 5.18 3.13-1.37-5.9 4.58-3.96-6.03-.52L12 4.5 9.64 10.02l-6.03.52 4.58 3.96-1.37 5.9z"/></svg>
      </template>
    </div>
    <div class="flex flex-wrap justify-center gap-3">
      <a href="{{ route('order.history') }}" class="btn btn-primary">Kembali ke pesanan saya</a>
      <a href="{{ route('catalog') }}" class="btn btn-outline">Pesan lagi</a>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
  // Ambil variabel $kode dari Controller
  const KODE_R = "{{ $kode }}";
  const SEMUA = semuaPesanan();
  const ORDER_R = SEMUA.find(o => o.kode === KODE_R && o.status === 'selesai') 
               || SEMUA.find(o => o.status === 'selesai') // Fallback jika kode tidak valid
               || SEMUA[0]; // Fallback terakhir jika belum ada pesanan selesai

  function formReview() {
    return {
      o: ORDER_R, 
      produk: ORDER_R.items[0],
      nilai: { total:5, rasa:5, kualitas:5, packaging:5, pelayanan:5 },
      hover: 0, teks:'', foto: null, fotoUrl: '', anonim:false, terkirim:false,
      aspek: [
        { key:'rasa',      label:'Rasa',      icon:'utensils' },
        { key:'kualitas',  label:'Kualitas & kesegaran', icon:'badge-check' },
        { key:'packaging', label:'Packaging', icon:'package' },
        { key:'pelayanan', label:'Pelayanan & ketepatan waktu', icon:'clock' }
      ],
      init() { this.$nextTick(() => icons()); this.$watch('terkirim', () => this.$nextTick(() => icons())); },
      get labelBintang() {
        return ['Belum dinilai','Kecewa','Kurang memuaskan','Cukup','Memuaskan','Sangat memuaskan'][this.hover || this.nilai.total];
      },
      pilihFoto(e) {
        const f = e.target.files[0];
        if (!f) return;
        this.foto = f;
        this.fotoUrl = URL.createObjectURL(f);
        toast('Foto ditambahkan ke ulasan.', 'info');
      },
      kirim(ev) {
        if (this.teks.trim().length < 20) { toast('Tulis minimal 20 karakter supaya ulasannya bermanfaat.', 'error'); return; }
        
        const btn = ev.currentTarget;
        const selesai = tombolMuat(btn, 'Mengirim...');

        // Kirim menggunakan AJAX ke Laravel
        fetch('{{ url('/review') }}/' + KODE_R, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                rating_total: this.nilai.total,
                komentar: this.teks
            })
        })
        .then(res => res.json())
        .then(data => {
            selesai();
            if(data.status === 'success') {
                this.terkirim = true;
                toast(data.message, 'success', 'Ulasan terkirim');
                window.scrollTo({ top:0, behavior:'smooth' });
            } else {
                toast('Gagal mengirim ulasan', 'error');
            }
        })
        .catch(err => {
            selesai();
            toast('Terjadi kesalahan koneksi.', 'error');
        });
      }
    };
  }
</script>
@endpush
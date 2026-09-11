@extends('layouts.main')

@section('title', 'Checkout · 4G Cake & Cookies')

@section('content')
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 sm:py-12" x-data="checkout()" x-init="init()">

  <!-- STEP INDICATOR START -->
  <div class="mb-8 sm:mb-10">
    <div class="flex items-center justify-between max-w-3xl mx-auto">
      <template x-for="(s, i) in langkah" :key="i">
        <div class="flex items-center flex-1 last:flex-none">
          <div class="flex flex-col items-center gap-2 shrink-0">
            <span class="grid place-items-center w-8 h-8 sm:w-10 sm:h-10 rounded-full text-xs font-bold transition-all duration-300"
                  :class="step > i+1 ? 'bg-rose-500 text-white'
                        : step === i+1 ? 'bg-cocoa-500 text-white ring-4 ring-cream-200'
                        : 'bg-white border border-cream-200 text-cocoa-300'">
              <template x-if="step > i+1"><i data-lucide="check" class="w-4 h-4"></i></template>
              <span x-show="step <= i+1" x-text="i+1"></span>
            </span>
            <span class="text-[10px] sm:text-xs text-center leading-tight w-12 sm:w-20"
                  :class="step >= i+1 ? 'text-cocoa-600 font-medium' : 'text-cocoa-300'" x-text="s"></span>
          </div>
          <span x-show="i < langkah.length - 1" class="flex-1 h-[2px] mx-0.5 sm:mx-2 -mt-6 rounded"
                :class="step > i+1 ? 'bg-rose-400' : 'bg-cream-200'"></span>
        </div>
      </template>
    </div>
  </div>
  <!-- STEP INDICATOR END -->

  <div class="grid lg:grid-cols-[1.5fr_1fr] gap-6 lg:gap-8 items-start">
    <div class="space-y-5">

      <!-- LANGKAH 2: DATA DIRI -->
      <div x-show="step === 2" x-transition.opacity class="card p-6 sm:p-8">
        <div class="flex items-center gap-3 mb-6">
          <span class="grid place-items-center w-10 h-10 rounded-2xl bg-blush-100 text-rose-600"><i data-lucide="user-round" class="w-5 h-5"></i></span>
          <div>
            <h2 class="font-display font-bold text-lg text-cocoa-700">Data pemesan</h2>
            <p class="text-xs text-cocoa-300">Dipakai untuk konfirmasi lewat WhatsApp bila ada yang perlu ditanyakan.</p>
          </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
          <div class="sm:col-span-2">
            <label class="label" for="nama">Nama lengkap</label>
            <input id="nama" x-model="f.nama" type="text" class="input" :class="salah.nama && 'is-error'" placeholder="Nadia Safitri" required>
            <p x-show="salah.nama" x-cloak class="error-text"><i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> Nama lengkap wajib diisi.</p>
          </div>
          <div>
            <label class="label" for="hp">Nomor WhatsApp</label>
            <input id="hp" x-model="f.hp" type="tel" class="input" :class="salah.hp && 'is-error'" placeholder="0812-3344-5566" required>
            <p x-show="salah.hp" x-cloak class="error-text"><i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> Nomor WhatsApp wajib diisi.</p>
          </div>
          <div>
            <label class="label" for="email">Email</label>
            <input id="email" x-model="f.email" type="email" class="input" placeholder="nama@email.com">
          </div>
          <div>
            <label class="label" for="tanggal">Tanggal ambil / kirim</label>
            <input id="tanggal" x-model="f.tanggal" type="date" class="input" :class="salah.tanggal && 'is-error'"
                   :min="minTanggal" @change="cekTanggal()" required>
            <p class="hint">Paling cepat <span class="font-semibold text-cocoa-500" x-text="minTanggalTeks"></span> karena ada produk pre-order H-<span x-text="maxPo"></span> di keranjang.</p>
            <p x-show="salah.tanggal" x-cloak class="error-text"><i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> Tanggal ambil atau kirim belum dipilih.</p>
          </div>
          <div>
            <label class="label" for="jam">Jam ambil / kirim</label>
            <select id="jam" x-model="f.jam" class="select">
              <template x-for="j in SLOT_JAM" :key="j"><option x-text="j" :value="j"></option></template>
            </select>
            <p class="hint">Kurir J&amp;T biasanya menjemput paket pada slot pagi.</p>
          </div>
          <div class="sm:col-span-2">
            <label class="label" for="catatan2">Catatan tambahan <span class="font-normal text-cocoa-300">(opsional)</span></label>
            <textarea id="catatan2" x-model="f.catatan" rows="3" class="textarea" placeholder="Tulisan di atas kue, permintaan khusus, patokan alamat, dll."></textarea>
          </div>
        </div>
        <div class="flex flex-wrap gap-3 mt-7">
          <a href="{{ route('cart.index') }}" class="btn btn-outline"><i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke keranjang</a>
          <button type="button" @click="lanjut()" class="btn btn-primary ml-auto">Lanjut ke pengiriman <i data-lucide="arrow-right" class="w-4 h-4"></i></button>
        </div>
      </div>

      <!-- LANGKAH 3: PENGIRIMAN -->
      <div x-show="step === 3" x-cloak x-transition.opacity class="card p-6 sm:p-8">
        <div class="flex items-center gap-3 mb-6">
          <span class="grid place-items-center w-10 h-10 rounded-2xl bg-cream-200 text-gold-600"><i data-lucide="truck" class="w-5 h-5"></i></span>
          <div>
            <h2 class="font-display font-bold text-lg text-cocoa-700">Metode pengiriman</h2>
            <p class="text-xs text-cocoa-300">Pilih dikirim lewat J&amp;T atau diambil sendiri di toko.</p>
          </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-3 mb-6">
          <button type="button" @click="f.metode='jnt'"
            :class="f.metode==='jnt' ? 'border-rose-400 bg-blush-50 ring-2 ring-blush-100' : 'border-cream-200 bg-white hover:border-rose-300'"
            class="text-left rounded-2xl border p-4 transition">
            <span class="flex items-center justify-between mb-2">
              <i data-lucide="truck" class="w-5 h-5 text-cocoa-500"></i>
              <span class="w-4 h-4 rounded-full border-2 grid place-items-center" :class="f.metode==='jnt' ? 'border-rose-500' : 'border-cream-300'">
                <span x-show="f.metode==='jnt'" class="w-2 h-2 rounded-full bg-rose-500"></span></span>
            </span>
            <span class="block font-display font-semibold text-cocoa-700 text-sm">Kirim via J&amp;T Express</span>
            <span class="block text-xs text-cocoa-400 mt-1">Ongkir Rp20.000 &ndash; Rp30.000, sampai 1&ndash;2 hari untuk area Aceh.</span>
          </button>

          <button type="button" @click="f.metode='ambil'"
            :class="f.metode==='ambil' ? 'border-rose-400 bg-blush-50 ring-2 ring-blush-100' : 'border-cream-200 bg-white hover:border-rose-300'"
            class="text-left rounded-2xl border p-4 transition">
            <span class="flex items-center justify-between mb-2">
              <i data-lucide="store" class="w-5 h-5 text-cocoa-500"></i>
              <span class="w-4 h-4 rounded-full border-2 grid place-items-center" :class="f.metode==='ambil' ? 'border-rose-500' : 'border-cream-300'">
                <span x-show="f.metode==='ambil'" class="w-2 h-2 rounded-full bg-rose-500"></span></span>
            </span>
            <span class="block font-display font-semibold text-cocoa-700 text-sm">Ambil di tempat</span>
            <span class="block text-xs text-cocoa-400 mt-1">Gratis ongkir. Jl. Samudera No. 12, Banda Sakti, Lhokseumawe.</span>
          </button>
        </div>

        <div x-show="f.metode==='jnt'" x-transition class="grid sm:grid-cols-2 gap-4">
          <div class="sm:col-span-2">
            <label class="label" for="alamat">Alamat lengkap</label>
            <textarea id="alamat" x-model="f.alamat" rows="3" class="textarea" :class="salah.alamat && 'is-error'" placeholder="Nama jalan, nomor rumah, kelurahan, kecamatan" required></textarea>
            <p x-show="salah.alamat" x-cloak class="error-text"><i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> Alamat pengiriman wajib diisi.</p>
          </div>
          <div>
            <label class="label" for="kota">Kota / kabupaten</label>
            <select id="kota" x-model="f.kota" @change="hitungOngkir()" class="select">
              <option value="Lhokseumawe">Lhokseumawe &mdash; Rp20.000</option>
              <option value="Aceh Utara">Aceh Utara &mdash; Rp22.000</option>
              <option value="Bireuen">Bireuen &mdash; Rp25.000</option>
              <option value="Kota Langsa">Kota Langsa &mdash; Rp25.000</option>
              <option value="Banda Aceh">Banda Aceh &mdash; Rp30.000</option>
            </select>
          </div>
          <div>
            <label class="label" for="kecamatan">Kecamatan</label>
            <input id="kecamatan" x-model="f.kecamatan" type="text" class="input" placeholder="Banda Sakti">
          </div>
          <div>
            <label class="label" for="pos">Kode pos</label>
            <input id="pos" x-model="f.pos" type="text" inputmode="numeric" class="input" placeholder="24351">
          </div>
        </div>

        <div x-show="f.metode==='ambil'" x-cloak x-transition class="space-y-4">
          <div class="rounded-2xl bg-cream-100 p-5 flex gap-3.5">
            <i data-lucide="map-pin" class="w-5 h-5 text-rose-500 shrink-0 mt-0.5"></i>
            <div class="text-sm">
              <p class="font-semibold text-cocoa-700 mb-1">Toko 4G Cake &amp; Cookies</p>
              <p class="text-cocoa-400 leading-relaxed">Jl. Samudera No. 12, Banda Sakti, Lhokseumawe, Aceh 24351<br>
                Buka Senin&ndash;Sabtu 08.00&ndash;20.00, Minggu 10.00&ndash;17.00</p>
            </div>
          </div>
        </div>

        <div class="flex flex-wrap gap-3 mt-7">
          <button type="button" @click="step=2" class="btn btn-outline"><i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali</button>
          <button type="button" @click="lanjut()" class="btn btn-primary ml-auto">Lanjut ke pembayaran <i data-lucide="arrow-right" class="w-4 h-4"></i></button>
        </div>
      </div>

      <!-- LANGKAH 4: PEMBAYARAN -->
      <div x-show="step === 4" x-cloak x-transition.opacity class="card p-6 sm:p-8">
        <div class="flex items-center gap-3 mb-6">
          <span class="grid place-items-center w-10 h-10 rounded-2xl bg-blush-100 text-rose-600"><i data-lucide="wallet" class="w-5 h-5"></i></span>
          <div>
            <h2 class="font-display font-bold text-lg text-cocoa-700">Pembayaran</h2>
            <p class="text-xs text-cocoa-300">Transfer manual, lalu unggah bukti supaya admin bisa mengonfirmasi.</p>
          </div>
        </div>

        <div class="rounded-2xl bg-cocoa-700 text-cream-100 p-5 mb-5 flex flex-wrap items-center gap-4">
          <div>
            <p class="text-[11px] text-cream-200/60 mb-0.5">Nominal yang harus ditransfer</p>
            <p class="font-display font-bold text-2xl text-white" x-text="rp(total)"></p>
          </div>
          <button type="button" @click="navigator.clipboard.writeText(total).then(() => toast('Nominal ' + rp(total) + ' disalin.','success'))"
                  class="btn btn-gold btn-sm ml-auto"><i data-lucide="copy" class="w-4 h-4"></i> Salin nominal</button>
        </div>

        <div class="space-y-3 mb-6">
          <template x-for="b in BANK" :key="b.kode">
            <button type="button" @click="f.bayar = b.kode"
              :class="f.bayar===b.kode ? 'border-rose-400 bg-blush-50' : 'border-cream-200 bg-white hover:border-rose-300'"
              class="w-full text-left rounded-2xl border p-4 flex items-center gap-4 transition">
              <span class="grid place-items-center w-12 h-9 rounded-lg bg-cocoa-500 text-white text-[11px] font-bold shrink-0" x-text="b.nama"></span>
              <span class="min-w-0 flex-1">
                <span class="block text-sm font-semibold text-cocoa-700" x-text="b.rek"></span>
                <span class="block text-xs text-cocoa-400" x-text="b.an"></span>
              </span>
              <span class="w-4 h-4 rounded-full border-2 grid place-items-center shrink-0" :class="f.bayar===b.kode ? 'border-rose-500' : 'border-cream-300'">
                <span x-show="f.bayar===b.kode" class="w-2 h-2 rounded-full bg-rose-500"></span></span>
            </button>
          </template>
          <button type="button" @click="f.bayar='tunai'" x-show="f.metode==='ambil'"
            :class="f.bayar==='tunai' ? 'border-rose-400 bg-blush-50' : 'border-cream-200 bg-white hover:border-rose-300'"
            class="w-full text-left rounded-2xl border p-4 flex items-center gap-4 transition">
            <span class="grid place-items-center w-12 h-9 rounded-lg bg-gold-500 text-white shrink-0"><i data-lucide="banknote" class="w-4 h-4"></i></span>
            <span class="min-w-0 flex-1">
              <span class="block text-sm font-semibold text-cocoa-700">Bayar tunai saat ambil</span>
              <span class="block text-xs text-cocoa-400">Tidak perlu unggah bukti transfer.</span>
            </span>
            <span class="w-4 h-4 rounded-full border-2 grid place-items-center shrink-0" :class="f.bayar==='tunai' ? 'border-rose-500' : 'border-cream-300'">
              <span x-show="f.bayar==='tunai'" class="w-2 h-2 rounded-full bg-rose-500"></span></span>
          </button>
        </div>

        <!-- UPLOAD BUKTI -->
        <div x-show="f.bayar !== 'tunai'" x-transition>
          <p class="label">Unggah bukti transfer</p>
          <label for="bukti" class="block rounded-2xl border-2 border-dashed border-cream-300 bg-cream-50 hover:border-rose-300 hover:bg-blush-50/40 transition cursor-pointer p-8 text-center">
            <template x-if="!f.bukti">
              <span class="block">
                <i data-lucide="cloud-upload" class="w-8 h-8 mx-auto text-cocoa-300 mb-3"></i>
                <span class="block text-sm font-medium text-cocoa-600">Klik untuk memilih file</span>
                <span class="block text-xs text-cocoa-300 mt-1">JPG, PNG, atau PDF maksimal 2 MB</span>
              </span>
            </template>
            <template x-if="f.bukti">
              <span class="flex items-center justify-center gap-4">
                <img :src="f.buktiUrl" x-show="f.buktiUrl" alt="Pratinjau bukti transfer" class="w-20 h-20 object-cover rounded-xl border border-cream-200">
                <span class="text-left">
                  <span class="flex items-center gap-2 text-sm font-medium text-cocoa-700"><i data-lucide="file-check-2" class="w-4 h-4 text-green-600"></i><span x-text="f.bukti"></span></span>
                  <span class="block text-xs text-cocoa-300 mt-1">Klik lagi untuk mengganti file</span>
                </span>
              </span>
            </template>
          </label>
          <input id="bukti" type="file" accept="image/*,.pdf" class="hidden" @change="pilihBukti($event)">
        </div>

        <div class="mt-6 flex items-start gap-3">
          <input id="setuju" type="checkbox" x-model="f.setuju" class="mt-1 w-4 h-4 rounded border-cream-300 text-rose-500 focus:ring-rose-400">
          <label for="setuju" class="text-sm text-cocoa-400 leading-relaxed cursor-pointer">
            Saya sudah membaca ketentuan pre-order dan memahami bahwa pesanan diproses setelah pembayaran dikonfirmasi admin.
          </label>
        </div>

        <div class="flex flex-wrap gap-3 mt-7">
          <button type="button" @click="step=3" class="btn btn-outline"><i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali</button>
          <button type="button" @click="kirim($event)" class="btn btn-primary ml-auto btn-lg">
            <i data-lucide="send-horizontal" class="w-[18px] h-[18px]"></i> Kirim pesanan
          </button>
        </div>
      </div>

      <!-- LANGKAH 5: SELESAI -->
      <div x-show="step === 5" x-cloak x-transition.opacity class="card p-8 sm:p-12 text-center">
        <span class="grid place-items-center w-20 h-20 mx-auto rounded-3xl bg-green-50 text-green-600 mb-6">
          <i data-lucide="party-popper" class="w-9 h-9"></i>
        </span>
        <h2 class="font-display font-bold text-2xl text-cocoa-700 mb-3">Pesanan Anda sudah kami terima</h2>
        <p class="text-cocoa-400 max-w-md mx-auto mb-7 leading-relaxed">
          Admin akan mengecek bukti pembayaran dan mengubah status pesanan menjadi
          <span class="font-semibold text-cocoa-600">Diproses</span> paling lambat 1&times;24 jam.
        </p>
        <div class="max-w-md mx-auto rounded-2xl bg-cream-100 p-6 mb-7 text-left">
          <div class="flex items-center justify-between gap-4 pb-4 mb-4 border-b border-cream-200">
            <div>
              <p class="text-[11px] text-cocoa-300 mb-0.5">Nomor pesanan</p>
              <p class="font-display font-bold text-xl text-cocoa-700 tracking-wide" x-text="kodePesanan"></p>
            </div>
            <span class="badge badge-wait"><span class="badge-dot"></span> Menunggu Pembayaran</span>
          </div>
          <dl class="space-y-2.5 text-sm">
            <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Tanggal pesan</dt><dd class="font-medium text-cocoa-700" x-text="tglID(HARI_INI)"></dd></div>
            <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Ambil / kirim</dt><dd class="font-medium text-cocoa-700 text-right" x-text="tglPilih + ' · ' + f.jam"></dd></div>
            <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Metode</dt><dd class="font-medium text-cocoa-700 text-right" x-text="f.metode==='ambil' ? 'Ambil di tempat' : 'J&T Express — ' + f.kota"></dd></div>
            <div class="flex justify-between gap-4 pt-2 border-t border-cream-200 items-baseline">
              <dt class="font-semibold text-cocoa-700">Total bayar</dt>
              <dd class="font-display font-bold text-lg text-rose-600" x-text="rp(totalAkhir)"></dd>
            </div>
          </dl>
        </div>
        <div class="flex flex-wrap justify-center gap-3">
          <a href="{{ route('order.history') }}" class="btn btn-outline">Riwayat pesanan</a>
          <a href="{{ route('catalog') }}" class="btn btn-ghost">Belanja lagi</a>
        </div>
      </div>
    </div>

    <!-- RINGKASAN PESANAN -->
    <aside class="card p-6 lg:sticky lg:top-28">
      <h2 class="font-display font-bold text-lg text-cocoa-700 mb-5">Ringkasan pesanan</h2>
      <div id="ringkas-item" class="space-y-3.5 max-h-72 overflow-y-auto pr-1 mb-5"></div>
      <dl class="space-y-3 text-sm pt-4 border-t border-cream-200">
        <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Subtotal</dt><dd class="font-medium text-cocoa-700" x-text="rp(subtotal)"></dd></div>
        <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Ongkos kirim</dt>
          <dd class="font-medium text-cocoa-700" x-text="f.metode==='ambil' ? 'Gratis' : rp(ongkir)"></dd></div>
        <div class="flex justify-between gap-4"><dt class="text-cocoa-400">Biaya kemasan</dt><dd class="font-medium text-cocoa-700" x-text="rp(kemasan)"></dd></div>
        <div class="pt-3 border-t border-cream-200 flex justify-between gap-4 items-baseline">
          <dt class="font-semibold text-cocoa-700">Total bayar</dt>
          <dd class="font-display font-bold text-2xl text-rose-600" x-text="rp(total)"></dd>
        </div>
      </dl>
      <div class="mt-5 rounded-2xl bg-cream-100 p-4 text-xs text-cocoa-400 space-y-2">
        <p class="flex gap-2"><i data-lucide="calendar-check" class="w-4 h-4 shrink-0 text-cocoa-300"></i>
          <span>Tanggal terpilih: <span class="font-semibold text-cocoa-600" x-text="tglPilih || 'belum dipilih'"></span></span></p>
        <p class="flex gap-2"><i data-lucide="clock" class="w-4 h-4 shrink-0 text-cocoa-300"></i>
          <span>Jam: <span class="font-semibold text-cocoa-600" x-text="f.jam"></span></span></p>
        <p class="flex gap-2"><i data-lucide="truck" class="w-4 h-4 shrink-0 text-cocoa-300"></i>
          <span>Metode: <span class="font-semibold text-cocoa-600" x-text="f.metode==='ambil' ? 'Ambil di tempat' : 'J&T Express — ' + f.kota"></span></span></p>
      </div>
      <a href="{{ route('cart.index') }}" x-show="step < 5" class="btn btn-ghost btn-sm btn-block mt-4">Ubah isi keranjang</a>
    </aside>
  </div>
</section>
@endsection

@push('scripts')
<script>
  // Mengambil data item dari controller Laravel
  const ITEMS = @json($cartItems);
  
  // 1. Mengambil tanggal hari ini SECARA REAL-TIME dari server Laravel (WIB)
  const TANGGAL_SERVER = '{{ \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d') }}';

  function checkout() {
    // 2. Kalkulasi PO menggunakan tanggal server yang akurat
    const maxPo = ITEMS.length ? Math.max(...ITEMS.map(i => i.produk.po)) : 1;
    const minIso = tambahHari(TANGGAL_SERVER, maxPo);

    return {
      langkah: ['Keranjang', 'Informasi', 'Pengiriman', 'Pembayaran', 'Selesai'],
      step: 2, maxPo, minTanggal: minIso, minTanggalTeks: tglID(minIso, true),
      subtotal: ITEMS.reduce((sum, it) => sum + it.subtotal, 0),
      kemasan: ITEMS.length * 3000,
      kodePesanan: '', totalAkhir: 0,
      SLOT_JAM, BANK, 
      
      // 3. Timpa variabel HARI_INI dummy dengan TANGGAL_SERVER
      HARI_INI: TANGGAL_SERVER, 
      
      salah: { nama:false, hp:false, tanggal:false, alamat:false },
      f: { nama:'', hp:'', email:'', tanggal:'', jam: SLOT_JAM[0], catatan:'', metode:'jnt',
           alamat:'', kecamatan:'', kota:'Lhokseumawe', pos:'',
           bayar:'bca', bukti:'', buktiUrl:'', fileInput:null, setuju:false },

      init() {
        renderRingkas(ITEMS);
        this.$nextTick(() => icons());
        this.$watch('step', () => { window.scrollTo({ top:0, behavior:'smooth' }); this.$nextTick(() => icons()); });
        this.$watch('f.metode', v => { if (v === 'jnt' && this.f.bayar === 'tunai') this.f.bayar = 'bca'; });
      },

      get ongkir() { return this.f.metode === 'ambil' ? 0 : (ONGKIR_KOTA[this.f.kota] || 20000); },
      get total() { return this.subtotal + this.ongkir + this.kemasan; },
      get tglPilih() { return this.f.tanggal ? tglID(this.f.tanggal, true) : ''; },

      cekTanggal() {
        this.salah.tanggal = !this.f.tanggal;
        if (this.f.tanggal && this.f.tanggal < this.minTanggal) {
          toast(`Pesanan ini membutuhkan minimal pre-order H-${this.maxPo}. Tanggal disesuaikan otomatis.`,
                'warning', 'Ketentuan pre-order');
          this.f.tanggal = this.minTanggal;
        }
      },
      hitungOngkir() { toast('Ongkir diperbarui: ' + rp(this.ongkir) + ' untuk ' + this.f.kota, 'info'); },

      lanjut() {
        if (this.step === 2) {
          this.salah.nama = !this.f.nama.trim();
          this.salah.hp = !this.f.hp.trim();
          this.salah.tanggal = !this.f.tanggal;
          this.$nextTick(() => icons());
          if (this.salah.nama || this.salah.hp || this.salah.tanggal) {
            toast('Beberapa isian wajib masih kosong. Periksa kembali form di atas.', 'error', 'Belum lengkap');
            return;
          }
          this.cekTanggal();
        }
        if (this.step === 3 && this.f.metode === 'jnt') {
          this.salah.alamat = !this.f.alamat.trim();
          this.$nextTick(() => icons());
          if (this.salah.alamat) { toast('Alamat pengiriman belum diisi.', 'error'); return; }
        }
        this.step++;
      },

      pilihBukti(e) {
        const file = e.target.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
          toast('Ukuran file melebihi 2 MB. Pilih file yang lebih kecil.', 'error', 'File terlalu besar');
          e.target.value = ''; return;
        }
        this.f.bukti = file.name;
        this.f.fileInput = file;
        this.f.buktiUrl = file.type.startsWith('image/') ? URL.createObjectURL(file) : '';
        toast('Bukti transfer "' + file.name + '" siap dikirim.', 'success', 'File dipilih');
        this.$nextTick(() => icons());
      },

      kirim(ev) {
        if (this.f.bayar !== 'tunai' && !this.f.bukti) { toast('Unggah dulu bukti transfernya.', 'error'); return; }
        if (!this.f.setuju) { toast('Centang persetujuan ketentuan pre-order dulu.', 'error'); return; }

        const selesai = tombolMuat(ev && ev.currentTarget, 'Mengirim pesanan…');
        
        const formData = new FormData();
        formData.append('nama', this.f.nama);
        formData.append('hp', this.f.hp);
        formData.append('email', this.f.email);
        formData.append('tanggal', this.f.tanggal);
        formData.append('jam', this.f.jam);
        formData.append('metode', this.f.metode);
        formData.append('catatan', this.f.catatan);
        
        if(this.f.fileInput) {
            formData.append('bukti', this.f.fileInput);
        }

        fetch('{{ route('checkout.proses') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            selesai();
            if(data.status === 'success') {
                this.kodePesanan = data.order_id;
                this.totalAkhir = this.total;
                this.step = 5;
                
                /* Patch: Simpan ke localStorage agar tampil di halaman Riwayat Pesanan */
                simpanPesanan({
                    kode: this.kodePesanan, pelanggan: this.f.nama, hp: this.f.hp, email: this.f.email,
                    tanggal: this.HARI_INI, jamPesan: new Date().toTimeString().slice(0,5),
                    ambil: this.f.tanggal, jam: this.f.jam,
                    metode: this.f.metode === 'ambil' ? 'Ambil di Tempat' : 'J&T',
                    status: 'menunggu', total: this.totalAkhir, ongkir: this.ongkir,
                    bayar: this.f.bayar === 'tunai' ? 'Tunai' : 'Transfer ' + (BANK.find(b => b.kode === this.f.bayar) || {}).nama,
                    alamat: this.f.metode === 'ambil' ? 'Ambil di toko — Jl. Samudera No. 12, Banda Sakti, Lhokseumawe' : `${this.f.alamat}, ${this.f.kecamatan}, ${this.f.kota} ${this.f.pos}`.replace(/,\s*,/g, ','),
                    resi: '-', catatan: this.f.catatan, baru: true,
                    items: ITEMS.map(i => ({ nama: i.produk.nama, qty: i.qty, harga: i.produk.harga, slug: i.produk.slug, varian: i.varian || 'Original' }))
                });
                
                window.LARAVEL_CART_COUNT = 0;
                if (typeof window.badgeKeranjang === 'function') window.badgeKeranjang();
                
                toast(data.message, 'success', 'Pesanan terkirim');
                this.$nextTick(() => icons());
            } else {
                toast('Gagal memproses pesanan.', 'error');
            }
        })
        .catch(error => {
            selesai();
            toast('Terjadi kesalahan jaringan saat mengirim pesanan.', 'error');
        });
      }
    };
  }

  function renderRingkas(items) {
    document.getElementById('ringkas-item').innerHTML = items.map(it => `
      <div class="flex gap-3">
        <span class="relative w-14 h-14 rounded-xl overflow-hidden bg-cream-100 shrink-0">
          <img src="${imgProduk(it.produk.slug)}" alt="${it.produk.nama}" class="w-full h-full object-cover">
          <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 grid place-items-center rounded-full bg-cocoa-500 text-white text-[10px] font-bold">${it.qty}</span>
        </span>
        <span class="min-w-0 flex-1">
          <span class="block text-sm font-medium text-cocoa-700 truncate">${it.produk.nama}</span>
          <span class="block text-[11px] text-cocoa-300 truncate">${it.varian || 'Original'}</span>
        </span>
        <span class="text-sm font-semibold text-cocoa-700 shrink-0">${rp(it.subtotal)}</span>
      </div>`).join('');
  }
</script>
@endpush
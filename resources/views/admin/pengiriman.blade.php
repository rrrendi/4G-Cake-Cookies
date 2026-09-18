@extends('layouts.admin')

@section('title', 'Manajemen Pengiriman · 4G Cake & Cookies')
@section('header_title', 'Manajemen Pengiriman')
@section('header_subtitle', 'Input nomor resi dan perbarui status paket J&T')

@section('content')
<div x-data="manajemenPengiriman()" x-init="init()">

  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <div class="card p-4 flex items-center gap-3">
      <span class="grid place-items-center w-10 h-10 rounded-xl bg-cream-100 text-cocoa-500 shrink-0"><i data-lucide="package" class="w-5 h-5"></i></span>
      <div><p class="font-display font-bold text-xl text-cocoa-700" x-text="list.length"></p><p class="text-[11px] text-cocoa-300">Total paket</p></div>
    </div>
    <div class="card p-4 flex items-center gap-3">
      <span class="grid place-items-center w-10 h-10 rounded-xl bg-[#FEF4E2] text-[#96650B] shrink-0"><i data-lucide="clock" class="w-5 h-5"></i></span>
      <div><p class="font-display font-bold text-xl text-cocoa-700" x-text="list.filter(p=>p.resi==='-').length"></p><p class="text-[11px] text-cocoa-300">Belum ada resi</p></div>
    </div>
    <div class="card p-4 flex items-center gap-3">
      <span class="grid place-items-center w-10 h-10 rounded-xl bg-[#E6F4F1] text-[#12695A] shrink-0"><i data-lucide="truck" class="w-5 h-5"></i></span>
      <div><p class="font-display font-bold text-xl text-cocoa-700" x-text="list.filter(p=>p.status==='jalan').length"></p><p class="text-[11px] text-cocoa-300">Sedang dikirim</p></div>
    </div>
    <div class="card p-4 flex items-center gap-3">
      <span class="grid place-items-center w-10 h-10 rounded-xl bg-[#E9F6EC] text-green-700 shrink-0"><i data-lucide="check-circle-2" class="w-5 h-5"></i></span>
      <div><p class="font-display font-bold text-xl text-cocoa-700" x-text="list.filter(p=>p.status==='sampai').length"></p><p class="text-[11px] text-cocoa-300">Sampai tujuan</p></div>
    </div>
  </div>

  <div class="card p-4 sm:p-5 mb-5">
    <div class="grid gap-3 md:grid-cols-3">
      <div class="relative md:col-span-2">
        <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-cocoa-300 pointer-events-none"></i>
        <input x-model="q" @input="halaman = 1" type="search" class="input !pl-11" placeholder="Cari kode pesanan, nama, atau nomor resi" aria-label="Cari pengiriman">
      </div>
      <select x-model="fStatus" @change="halaman = 1" class="select" aria-label="Filter status pengiriman">
        <option value="">Semua status</option>
        <template x-for="(s, key) in STATUS_KIRIM" :key="key">
          <option :value="key" x-text="s.label"></option>
        </template>
      </select>
    </div>
  </div>

  <!-- TABEL PENGIRIMAN START -->
  <div class="card overflow-hidden">
    <div class="table-wrap">
      <table class="data">
        <thead><tr><th>Kode pesanan</th><th>Penerima</th><th>Tujuan</th><th>Kurir</th>
          <th>Nomor resi</th><th>Tanggal kirim</th><th>Status</th><th>Update terakhir</th><th class="text-right">Aksi</th></tr></thead>
        <tbody>
          <template x-for="p in halamanIni" :key="p.id">
            <tr>
              <td class="font-medium text-cocoa-700 whitespace-nowrap"><a href="{{ route('admin.pesanan.index') }}" class="hover:text-rose-600 transition" x-text="p.kode"></a></td>
              <td class="text-cocoa-500" x-text="p.pelanggan"></td>
              <td class="text-cocoa-500 whitespace-nowrap">
                <span x-text="p.kota"></span>
                <span class="block text-[11px] text-cocoa-300" x-text="'Ongkir ' + rp(p.ongkir)"></span>
              </td>
              <td class="text-cocoa-400 whitespace-nowrap" x-text="p.kurir"></td>
              <td>
                <div class="flex items-center gap-2">
                  <span class="font-mono text-xs" :class="p.resi === '-' ? 'text-cocoa-300' : 'text-cocoa-700'" x-text="p.resi === '-' ? 'belum ada' : p.resi"></span>
                  <button type="button" @click="bukaResi(p)" class="icon-btn !w-9 !h-9 tap" :aria-label="'Ubah nomor resi pesanan ' + p.kode">
                    <i data-lucide="pencil" class="w-4 h-4"></i></button>
                </div>
              </td>
              <td class="text-cocoa-500 whitespace-nowrap" x-text="p.tanggalKirim === '-' ? 'Belum dikirim' : tglID(p.tanggalKirim)"></td>
              <td>
                <span class="badge" :class="STATUS_KIRIM[p.status].cls"><i :data-lucide="STATUS_KIRIM[p.status].icon" class="w-3 h-3"></i><span x-text="STATUS_KIRIM[p.status].label"></span></span>
              </td>
              <td class="text-cocoa-400 text-xs whitespace-nowrap" x-text="p.update"></td>
              <td>
                <div class="flex justify-end gap-1">
                  <button @click="majukan(p)" class="icon-btn tap" title="Majukan status">
                    <i data-lucide="chevrons-right" class="w-4 h-4"></i></button>
                  <button @click="cetakLabel(p)" class="icon-btn tap" title="Cetak label">
                    <i data-lucide="printer" class="w-4 h-4"></i></button>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div x-show="hasil.length === 0" class="py-16 text-center">
      <span class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-cream-100 text-cocoa-300 mb-4"><i data-lucide="truck" class="w-6 h-6"></i></span>
      <p class="font-display font-semibold text-cocoa-700 mb-1">Tidak ada paket pada filter ini</p>
      <p class="text-sm text-cocoa-400 mb-5">Semua pesanan J&amp;T akan otomatis muncul di sini.</p>
      <button @click="q=''; fStatus=''; halaman=1" class="btn btn-outline btn-sm mx-auto">Reset filter</button>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-t border-cream-200 text-sm">
      <p class="text-cocoa-400">Menampilkan <span class="font-semibold text-cocoa-700" x-text="halamanIni.length"></span> dari <span x-text="hasil.length"></span> paket <span x-show="hasil.length !== list.length">(total <span x-text="list.length"></span>)</span></p>
      <div class="flex items-center gap-1" x-show="totalHalaman > 1">
        <button @click="halaman--" :disabled="halaman === 1" class="icon-btn tap" :class="halaman === 1 && 'opacity-40 pointer-events-none'" aria-label="Halaman sebelumnya"><i data-lucide="chevron-left" class="w-4 h-4"></i></button>
        <template x-for="n in nomorHalaman" :key="n">
          <button @click="halaman = n" class="w-9 h-9 rounded-xl text-sm font-medium transition" :class="n===halaman ? 'bg-rose-500 text-white' : 'text-cocoa-500 hover:bg-cream-100'" x-text="n"></button>
        </template>
        <button @click="halaman++" :disabled="halaman === totalHalaman" class="icon-btn tap" :class="halaman === totalHalaman && 'opacity-40 pointer-events-none'" aria-label="Halaman berikutnya"><i data-lucide="chevron-right" class="w-4 h-4"></i></button>
      </div>
    </div>
  </div>
  <!-- TABEL PENGIRIMAN END -->

  <!-- MODAL RESI START -->
  <div x-show="modal" x-cloak class="fixed inset-0 z-[60] grid place-items-center p-4" role="dialog" aria-modal="true"
       @keydown.escape.window="tutupSemua()">
    <div @click="modal=false" class="modal-overlay"></div>
    <div x-show="modal" x-transition class="modal-panel card max-w-md p-6">
      <template x-if="aktif">
        <div>
          <h2 class="font-display font-bold text-lg text-cocoa-700 mb-1">Input nomor resi</h2>
          <p class="text-sm text-cocoa-400 mb-5"><span class="font-medium text-cocoa-600" x-text="aktif.kode"></span> &middot; <span x-text="aktif.pelanggan"></span> (<span x-text="aktif.kota"></span>)</p>

          <div class="space-y-4">
            <div>
              <label class="label" for="r-kurir">Kurir</label>
              <select id="r-kurir" x-model="formKurir" class="select">
                <option>J&amp;T Express</option>
                <option>J&amp;T Cargo</option>
                <option>Kurir internal</option>
              </select>
            </div>
            <div>
              <label class="label" for="r-tglkirim">Tanggal kirim</label>
              <input id="r-tglkirim" x-model="formTanggal" type="date" class="input">
            </div>
            <div>
              <label class="label" for="r-resi">Nomor resi <span class="text-cocoa-300 font-normal">(kosongkan jika kurir tidak menerbitkan resi, mis. ojek online)</span></label>
              <input id="r-resi" x-model="formResi" type="text" class="input font-mono" placeholder="JT8842190365">
              <p class="hint mt-2" x-show="formKurir === 'Kurir internal'">Kurir internal tidak wajib diisi resi.</p>
            </div>
            <div>
              <p class="label">Status pengiriman</p>
              <div class="grid grid-cols-2 gap-2">
                <template x-for="(v, key) in STATUS_KIRIM" :key="key">
                  <button type="button" @click="formStatus = key" class="btn btn-sm" :class="formStatus === key ? 'btn-primary' : 'btn-outline'" x-text="v.label"></button>
                </template>
              </div>
            </div>
          </div>

          <div class="flex gap-3 mt-7">
            <button @click="modal=false" class="btn btn-outline flex-1">Batal</button>
            <button @click="simpanResi()" class="btn btn-primary flex-1" :disabled="menyimpan"><i data-lucide="save" class="w-4 h-4"></i> <span x-text="menyimpan ? 'Menyimpan…' : 'Simpan'"></span></button>
          </div>
        </div>
      </template>
    </div>
  </div>
  <!-- MODAL RESI END -->
</div>
@endsection

@push('scripts')
<script>
  function manajemenPengiriman() {
    const dataAwal = @json($shippings);

    const STATUS_KIRIM = {
      pickup: { label:'Menunggu Pickup',   cls:'badge-wait',    icon:'package-search' },
      kurir:  { label:'Diambil Kurir',     cls:'badge-process', icon:'package-check' },
      jalan:  { label:'Dalam Perjalanan',  cls:'badge-ship',    icon:'truck' },
      sampai: { label:'Sampai Tujuan',     cls:'badge-done',    icon:'map-pin-check' }
    };

    return {
      list: dataAwal,
      STATUS_KIRIM, q:'', fStatus:'',
      halaman:1, perPage:10,
      modal:false, aktif:null, formResi:'', formStatus:'', formKurir:'', formTanggal:'', menyimpan:false,

      tutupSemua() { this.modal = false; },
      init() { this.$nextTick(() => icons()); this.$watch('halamanIni', () => this.$nextTick(() => icons())); },
      cetakLabel(p) {
        const html = `<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><title>Label ${p.kode}</title>
        <style>
          body{font-family:Arial,Helvetica,sans-serif;margin:0;display:flex;justify-content:center;padding:24px;}
          .label{width:340px;border:2px dashed #333;padding:18px;font-size:13px;color:#222;}
          .label h1{font-size:15px;margin:0 0 2px}
          .label .sub{color:#555;font-size:11px;margin-bottom:10px}
          hr{border:none;border-top:1px solid #333;margin:10px 0}
          .besar{font-size:20px;font-weight:bold;letter-spacing:1px}
          .baris{margin:4px 0}
          .lbl{color:#666;font-size:11px}
        </style></head><body>
        <div class="label">
          <h1>4G Cake &amp; Cookies</h1>
          <p class="sub">Gg. Kb. Jukut 4 No.18/26, Ciroyom, Kec. Andir, Kota Bandung</p>
          <hr>
          <p class="lbl">KEPADA</p>
          <p class="besar">${p.pelanggan}</p>
          <p class="baris">${p.kota}</p>
          <hr>
          <p class="baris"><span class="lbl">Kurir:</span> ${p.kurir}</p>
          <p class="baris"><span class="lbl">No. Resi:</span> ${p.resi === '-' ? '(belum ada)' : p.resi}</p>
          <p class="baris"><span class="lbl">Kode pesanan:</span> ${p.kode}</p>
        </div>
        </body></html>`;

        const w = window.open('', '_blank', 'width=420,height=560');
        if (!w) { toast('Izinkan pop-up di browser untuk mencetak label.', 'error'); return; }
        w.document.write(html);
        w.document.close();
        w.focus();
        setTimeout(() => w.print(), 300);
      },
      get hasil() {
        const q = this.q.trim().toLowerCase();
        return this.list.filter(p =>
          (!q || p.kode.toLowerCase().includes(q) || p.pelanggan.toLowerCase().includes(q) || p.resi.toLowerCase().includes(q)) &&
          (!this.fStatus || p.status === this.fStatus));
      },
      get totalHalaman() { return Math.max(1, Math.ceil(this.hasil.length / this.perPage)); },
      get halamanIni() {
        const awal = (this.halaman - 1) * this.perPage;
        return this.hasil.slice(awal, awal + this.perPage);
      },
      get nomorHalaman() {
        const total = this.totalHalaman;
        let awal = Math.max(1, this.halaman - 2);
        let akhir = Math.min(total, awal + 4);
        awal = Math.max(1, akhir - 4);
        const arr = [];
        for (let i = awal; i <= akhir; i++) arr.push(i);
        return arr;
      },
      bukaResi(p) {
        this.aktif = p; this.formResi = p.resi === '-' ? '' : p.resi;
        this.formStatus = p.status; this.formKurir = p.kurir;
        this.formTanggal = p.tanggalKirim === '-' ? '{{ now()->format('Y-m-d') }}' : p.tanggalKirim;
        this.modal = true; this.$nextTick(() => icons());
      },
      simpanResi() {
        if (this.formStatus !== 'pickup' && this.formKurir !== 'Kurir internal' && !this.formResi.trim()) {
          toast('Status ini membutuhkan nomor resi J&T terlebih dahulu.', 'error', 'Resi belum diisi'); return;
        }
        this.menyimpan = true;
        fetch(`{{ url('admin/pengiriman') }}/${this.aktif.id}/resi`, {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
          body: JSON.stringify({ kurir: this.formKurir, status: this.formStatus, resi: this.formResi.trim() || null, tanggal_kirim: this.formTanggal || null })
        })
        .then(async res => {
          const data = await res.json().catch(() => null);
          if (!res.ok) throw new Error(data && data.message ? data.message : 'Gagal menyimpan pengiriman.');
          return data;
        })
        .then(data => {
          Object.assign(this.aktif, data.data);
          toast(this.aktif.kode + ' diperbarui: ' + STATUS_KIRIM[this.aktif.status].label, 'success', 'Pengiriman tersimpan');
          if (data.order_cascade) setTimeout(() => toast(data.order_cascade, 'info', 'Status pesanan ikut diperbarui'), 500);
          this.modal = false;
        })
        .catch(err => toast(err.message, 'error'))
        .finally(() => { this.menyimpan = false; this.$nextTick(() => icons()); });
      },
      majukan(p) {
        const urut = Object.keys(STATUS_KIRIM);
        if (urut.indexOf(p.status) === urut.length - 1) { toast(p.kode + ' sudah sampai tujuan.', 'info'); return; }

        fetch(`{{ url('admin/pengiriman') }}/${p.id}/majukan`, {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(async res => {
          const data = await res.json().catch(() => null);
          if (!res.ok) throw new Error(data && data.message ? data.message : 'Gagal memperbarui status.');
          return data;
        })
        .then(data => {
          Object.assign(p, data.data);
          toast(p.kode + ' → ' + STATUS_KIRIM[p.status].label, 'success', 'Status pengiriman');
          if (data.order_cascade) setTimeout(() => toast(data.order_cascade, 'info', 'Status pesanan ikut diperbarui'), 500);
          this.$nextTick(() => icons());
        })
        .catch(err => toast(err.message, 'error'));
      }
    };
  }
</script>
@endpush

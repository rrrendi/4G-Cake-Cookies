@extends('layouts.admin')

@section('title', 'Jadwal Produksi · 4G Cake & Cookies')
@section('header_title', 'Jadwal Produksi')
@section('header_subtitle', 'Rekap produk (beserta variannya) yang harus dibuat pada setiap tanggal')

@section('content')
<div x-data="jadwalProduksi()" x-init="init()">
  <div class="grid lg:grid-cols-[1fr_1.15fr] gap-4 sm:gap-6 items-start">

    <!-- KALENDER -->
    <div class="card p-5 sm:p-6">
      <div class="flex items-center justify-between gap-3 mb-5">
        <div>
          <h2 class="font-display font-bold text-cocoa-700" x-text="namaBulan"></h2>
          <p class="text-xs text-cocoa-300">Klik tanggal untuk melihat rekap produksinya</p>
        </div>
        <div class="flex gap-1">
          <button @click="geser(-1)" class="grid place-items-center w-9 h-9 rounded-xl border border-cream-200 text-cocoa-500 hover:bg-cream-100 transition" aria-label="Bulan sebelumnya"><i data-lucide="chevron-left" class="w-4 h-4"></i></button>
          <button @click="geser(1)" class="grid place-items-center w-9 h-9 rounded-xl border border-cream-200 text-cocoa-500 hover:bg-cream-100 transition" aria-label="Bulan berikutnya"><i data-lucide="chevron-right" class="w-4 h-4"></i></button>
        </div>
      </div>

      <div class="grid grid-cols-7 gap-1 mb-2">
        <template x-for="h in ['Min','Sen','Sel','Rab','Kam','Jum','Sab']" :key="h">
          <div class="text-center text-[11px] font-semibold text-cocoa-300 py-1" x-text="h"></div>
        </template>
      </div>
      <div class="grid grid-cols-7 gap-1">
        <template x-for="(sel, i) in grid" :key="i">
          <button @click="sel.iso && (pilih = sel.iso, halamanItem = 1)" :disabled="!sel.iso"
            class="relative aspect-square rounded-xl text-sm flex flex-col items-center justify-center gap-1 transition"
            :class="!sel.iso ? 'opacity-0 cursor-default'
                  : pilih === sel.iso ? 'bg-rose-500 text-white font-semibold'
                  : sel.jumlah ? 'bg-cream-100 text-cocoa-700 hover:bg-blush-100 font-medium'
                  : 'text-cocoa-400 hover:bg-cream-100'">
            <span x-text="sel.hari"></span>
            <span x-show="sel.jumlah" class="w-1.5 h-1.5 rounded-full" :class="pilih === sel.iso ? 'bg-white' : 'bg-rose-500'"></span>
          </button>
        </template>
      </div>

      <div class="mt-5 pt-5 border-t border-cream-200 flex flex-wrap gap-4 text-xs text-cocoa-400">
        <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Ada pesanan</span>
        <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-cream-200"></span> Kosong</span>
        <span class="ml-auto">Klik badge status untuk memutar: Belum Mulai &rarr; Diproses &rarr; Selesai</span>
      </div>
    </div>

    <!-- REKAP TANGGAL -->
    <div class="space-y-4 sm:space-y-6">
      <div class="card p-5 sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
          <div>
            <h2 class="font-display font-bold text-cocoa-700" x-text="tglID(pilih, true)"></h2>
            <p class="text-xs text-cocoa-300" x-show="hariIni" x-text="hariIni ? hariIni.pesanan + ' pesanan · ' + totalItem + ' item harus siap' : ''"></p>
          </div>
          <button @click="cetakDaftar()" class="btn btn-outline btn-sm">
            <i data-lucide="printer" class="w-4 h-4"></i> Cetak daftar
          </button>
        </div>

        <template x-if="hariIni">
          <div>
            <div class="table-wrap">
              <table class="data !min-w-[560px]">
                <thead><tr><th>Produk &amp; varian</th><th>Jumlah</th><th>Kode pesanan</th><th class="text-right">Status produksi</th></tr></thead>
                <tbody>
                  <template x-for="(it, i) in itemHalamanIni" :key="it.id">
                    <tr>
                      <td class="whitespace-nowrap">
                        <p class="font-medium text-cocoa-700" x-text="it.nama"></p>
                        <p class="text-[11px] text-cocoa-300" x-text="it.varian || 'Tanpa varian'"></p>
                      </td>
                      <td><span class="badge badge-neutral" x-text="it.qty + ' pcs'"></span></td>
                      <td class="text-cocoa-400 whitespace-nowrap" x-text="it.kode"></td>
                      <td class="text-right">
                        <button @click="putar(it)" class="badge tap" :class="LABEL[it.status].cls"
                                :aria-label="'Ubah status ' + it.nama + ', sekarang ' + LABEL[it.status].teks">
                          <i :data-lucide="LABEL[it.status].icon" class="w-3 h-3"></i>
                          <span x-text="LABEL[it.status].teks"></span>
                        </button>
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>

            <!-- PAGINASI ITEM PER HARI -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-4 text-sm" x-show="hariIni.items.length > perPageItem">
              <p class="text-cocoa-400">Menampilkan <span class="font-semibold text-cocoa-700" x-text="itemHalamanIni.length"></span> dari <span x-text="hariIni.items.length"></span> item</p>
              <div class="flex items-center gap-1">
                <button @click="halamanItem--" :disabled="halamanItem === 1" class="icon-btn tap" :class="halamanItem === 1 && 'opacity-40 pointer-events-none'" aria-label="Halaman item sebelumnya"><i data-lucide="chevron-left" class="w-4 h-4"></i></button>
                <span class="text-xs text-cocoa-400 px-2">Hal. <span x-text="halamanItem"></span> / <span x-text="totalHalamanItem"></span></span>
                <button @click="halamanItem++" :disabled="halamanItem === totalHalamanItem" class="icon-btn tap" :class="halamanItem === totalHalamanItem && 'opacity-40 pointer-events-none'" aria-label="Halaman item berikutnya"><i data-lucide="chevron-right" class="w-4 h-4"></i></button>
              </div>
            </div>

            <div class="mt-5 pt-4 border-t border-cream-200">
              <div class="flex justify-between text-sm mb-2">
                <span class="text-cocoa-400">Progres produksi hari ini</span>
                <span class="font-semibold text-cocoa-700"><span x-text="jumlahSelesai"></span> / <span x-text="hariIni.items.length"></span></span>
              </div>
              <div class="h-2.5 rounded-full bg-cream-200 overflow-hidden">
                <div class="h-full rounded-full bg-rose-500 transition-all duration-500"
                     :style="'width:' + (hariIni.items.length ? jumlahSelesai / hariIni.items.length * 100 : 0) + '%'"></div>
              </div>
            </div>
          </div>
        </template>

        <template x-if="!hariIni">
          <div class="py-12 text-center">
            <span class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-cream-100 text-cocoa-300 mb-4"><i data-lucide="calendar-off" class="w-6 h-6"></i></span>
            <p class="font-display font-semibold text-cocoa-700 mb-1">Tidak ada produksi di tanggal ini</p>
            <p class="text-sm text-cocoa-400">Dapur bisa dipakai untuk stok kue kering atau libur.</p>
          </div>
        </template>
      </div>

      <!-- REKAP HARI MENDATANG -->
      <div class="card p-5 sm:p-6">
        <h2 class="font-display font-bold text-cocoa-700 mb-1">Rekap hari mendatang</h2>
        <p class="text-xs text-cocoa-300 mb-5">Total item yang harus diproduksi per tanggal</p>
        <div class="space-y-3">
          <template x-for="j in akanDatangHalamanIni" :key="j.tanggal">
            <button @click="pilih = j.tanggal; halamanItem = 1" class="w-full flex items-center gap-4 rounded-2xl border p-3.5 text-left transition"
                    :class="pilih === j.tanggal ? 'border-rose-400 bg-blush-50' : 'border-cream-200 hover:border-rose-300'">
              <span class="grid place-items-center w-12 h-12 rounded-xl bg-cream-100 text-cocoa-600 shrink-0 leading-none">
                <span class="text-[10px]" x-text="j.hari.slice(0,3)"></span>
                <span class="font-display font-bold text-sm" x-text="j.tanggal.slice(-2)"></span>
              </span>
              <span class="min-w-0 flex-1">
                <span class="block text-sm font-medium text-cocoa-700" x-text="tglID(j.tanggal)"></span>
                <span class="block text-[11px] text-cocoa-300" x-text="j.pesanan + ' pesanan'"></span>
              </span>
              <span class="shrink-0 text-right">
                <span class="block font-display font-bold text-cocoa-700" x-text="j.items.reduce((a,i)=>a+i.qty,0)"></span>
                <span class="block text-[11px] text-cocoa-300">item</span>
              </span>
              <span class="w-20 shrink-0">
                <span class="block h-2 rounded-full bg-cream-200 overflow-hidden">
                  <span class="block h-full rounded-full bg-gold-400" :style="'width:' + Math.min(100, j.items.reduce((a,i)=>a+i.qty,0) / 14 * 100) + '%'"></span>
                </span>
              </span>
            </button>
          </template>
          <p class="text-sm text-cocoa-300 text-center py-6" x-show="!akanDatang.length">Belum ada pesanan terjadwal ke depan.</p>
        </div>

        <!-- PAGINASI HARI MENDATANG -->
        <div class="flex items-center justify-between gap-3 pt-4 mt-1 border-t border-cream-200 text-sm" x-show="akanDatang.length > perPageHari">
          <p class="text-cocoa-400"><span x-text="akanDatang.length"></span> tanggal terjadwal</p>
          <div class="flex items-center gap-1">
            <button @click="halamanHari--" :disabled="halamanHari === 1" class="icon-btn tap" :class="halamanHari === 1 && 'opacity-40 pointer-events-none'" aria-label="Tanggal sebelumnya"><i data-lucide="chevron-left" class="w-4 h-4"></i></button>
            <span class="text-xs text-cocoa-400 px-2">Hal. <span x-text="halamanHari"></span> / <span x-text="totalHalamanHari"></span></span>
            <button @click="halamanHari++" :disabled="halamanHari === totalHalamanHari" class="icon-btn tap" :class="halamanHari === totalHalamanHari && 'opacity-40 pointer-events-none'" aria-label="Tanggal berikutnya"><i data-lucide="chevron-right" class="w-4 h-4"></i></button>
          </div>
        </div>

        <p class="hint mt-4">Kapasitas dapur saat ini sekitar 14 item per hari. Batang penuh berarti jadwal sudah padat.</p>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function jadwalProduksi() {
    const dataJadwal = @json($jadwal);

    return {
      jadwal: dataJadwal,

      // Ambil tanggal real-time dari Laravel
      pilih: '{{ $tgl_iso }}',
      hariIniIso: '{{ $tgl_iso }}',
      bulan: {{ $bulan }},
      tahun: {{ $tahun }},

      halamanItem: 1, perPageItem: 8,
      halamanHari: 1, perPageHari: 5,

      LABEL: {
        belum_mulai: { teks:'Belum Mulai', cls:'badge-neutral', icon:'circle-dashed' },
        diproses:    { teks:'Diproses',    cls:'badge-process', icon:'chef-hat' },
        selesai:     { teks:'Selesai',     cls:'badge-done',    icon:'check' }
      },

      init() {
        this.$nextTick(() => icons());
        this.$watch('pilih', () => { this.$nextTick(() => icons()); });
        this.$watch('grid', () => this.$nextTick(() => icons()));
        this.$watch('halamanItem', () => this.$nextTick(() => icons()));
        this.$watch('halamanHari', () => this.$nextTick(() => icons()));
      },
      get namaBulan() { return BULAN[this.bulan] + ' ' + this.tahun; },
      get hariIni() { return this.jadwal.find(j => j.tanggal === this.pilih) || null; },
      get jumlahSelesai() { return this.hariIni ? this.hariIni.items.filter(i => i.status === 'selesai').length : 0; },
      get totalItem() { return this.hariIni ? this.hariIni.items.reduce((a,i)=>a+i.qty,0) : 0; },

      get totalHalamanItem() { return this.hariIni ? Math.max(1, Math.ceil(this.hariIni.items.length / this.perPageItem)) : 1; },
      get itemHalamanIni() {
        if (!this.hariIni) return [];
        const awal = (this.halamanItem - 1) * this.perPageItem;
        return this.hariIni.items.slice(awal, awal + this.perPageItem);
      },

      get akanDatang() { return this.jadwal.filter(j => j.tanggal >= this.hariIniIso).sort((a,b)=>a.tanggal.localeCompare(b.tanggal)); },
      get totalHalamanHari() { return Math.max(1, Math.ceil(this.akanDatang.length / this.perPageHari)); },
      get akanDatangHalamanIni() {
        const awal = (this.halamanHari - 1) * this.perPageHari;
        return this.akanDatang.slice(awal, awal + this.perPageHari);
      },

      get grid() {
        const awal = new Date(this.tahun, this.bulan, 1).getDay();
        const jml = new Date(this.tahun, this.bulan + 1, 0).getDate();
        const sel = [];
        for (let i = 0; i < awal; i++) sel.push({ iso:null, hari:'', jumlah:0 });
        for (let d = 1; d <= jml; d++) {
          const iso = `${this.tahun}-${String(this.bulan+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
          const j = this.jadwal.find(x => x.tanggal === iso);
          sel.push({ iso, hari:d, jumlah: j ? j.pesanan : 0 });
        }
        return sel;
      },
      geser(n) {
        this.bulan += n;
        if (this.bulan > 11) { this.bulan = 0; this.tahun++; }
        if (this.bulan < 0)  { this.bulan = 11; this.tahun--; }
        this.$nextTick(() => icons());
      },
      cetakDaftar() {
        if (!this.hariIni || !this.hariIni.items.length) {
          toast('Tidak ada item produksi pada tanggal ini.', 'error'); return;
        }
        const baris = this.hariIni.items.map(it => `
          <tr>
            <td>${it.nama}<br><span style="color:#777;font-size:11px">${it.varian || 'Tanpa varian'}</span></td>
            <td style="text-align:center">${it.qty}</td>
            <td>${it.kode}</td>
            <td>${this.LABEL[it.status].teks}</td>
          </tr>`).join('');

        const html = `<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><title>Daftar Produksi ${tglID(this.pilih)}</title>
        <style>
          body{font-family:Arial,Helvetica,sans-serif;max-width:640px;margin:32px auto;color:#222;font-size:13px;}
          h1{font-size:18px;margin:0 0 4px}
          .sub{color:#555;margin-bottom:18px}
          table{width:100%;border-collapse:collapse}
          th,td{border:1px solid #ccc;padding:8px;text-align:left;vertical-align:top}
          th{background:#f6efe6}
        </style></head><body>
        <h1>4G Cake &amp; Cookies — Daftar Produksi</h1>
        <p class="sub">${tglID(this.pilih, true)} &middot; ${this.hariIni.pesanan} pesanan &middot; ${this.totalItem} item</p>
        <table><thead><tr><th>Produk &amp; varian</th><th>Jumlah</th><th>Kode pesanan</th><th>Status</th></tr></thead>
        <tbody>${baris}</tbody></table>
        </body></html>`;

        const w = window.open('', '_blank', 'width=700,height=780');
        if (!w) { toast('Izinkan pop-up di browser untuk mencetak daftar.', 'error'); return; }
        w.document.write(html);
        w.document.close();
        w.focus();
        setTimeout(() => w.print(), 300);
      },
      putar(it) {
        const urut = ['belum_mulai','diproses','selesai'];
        const statusBaru = urut[(urut.indexOf(it.status) + 1) % urut.length];
        const statusLama = it.status;
        it.status = statusBaru; // optimistic update

        fetch(`{{ url('admin/jadwal/item') }}/${it.id}/status`, {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
          body: JSON.stringify({ status: statusBaru })
        })
        .then(async res => {
          const data = await res.json().catch(() => null);
          if (!res.ok) throw new Error(data && data.message ? data.message : 'Gagal memperbarui status.');
          toast(`${it.nama} ditandai "${this.LABEL[statusBaru].teks}".`, statusBaru === 'selesai' ? 'success' : 'info');
          this.$nextTick(() => icons());
          if (data.order_cascade) {
            setTimeout(() => toast(data.order_cascade, 'info', 'Status pesanan ikut diperbarui'), 600);
          } else if (this.hariIni && this.jumlahSelesai === this.hariIni.items.length) {
            setTimeout(() => toast('Semua produksi ' + tglID(this.pilih) + ' selesai.', 'success', 'Kerja bagus'), 600);
          }
        })
        .catch(err => {
          it.status = statusLama; // rollback
          toast(err.message, 'error');
        });
      }
    };
  }
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Jadwal Produksi · 4G Cake & Cookies')
@section('header_title', 'Jadwal Produksi')
@section('header_subtitle', 'Rekap produk yang harus dibuat pada setiap tanggal')

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
          <button @click="sel.iso && (pilih = sel.iso)" :disabled="!sel.iso"
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
          <button @click="toast('Daftar produksi ' + tglID(pilih) + ' dikirim ke printer (simulasi).','info')" class="btn btn-outline btn-sm">
            <i data-lucide="printer" class="w-4 h-4"></i> Cetak daftar
          </button>
        </div>

        <template x-if="hariIni">
          <div>
            <div class="table-wrap">
              <table class="data !min-w-[520px]">
                <thead><tr><th>Produk</th><th>Jumlah</th><th>Kode pesanan</th><th class="text-right">Status produksi</th></tr></thead>
                <tbody>
                  <template x-for="(it, i) in hariIni.items" :key="i">
                    <tr>
                      <td class="font-medium text-cocoa-700 whitespace-nowrap" x-text="it.nama"></td>
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

      <!-- REKAP MINGGU -->
      <div class="card p-5 sm:p-6">
        <h2 class="font-display font-bold text-cocoa-700 mb-1">Rekap lima hari ke depan</h2>
        <p class="text-xs text-cocoa-300 mb-5">Total item yang harus diproduksi per tanggal</p>
        <div class="space-y-3">
          <template x-for="j in jadwal" :key="j.tanggal">
            <button @click="pilih = j.tanggal" class="w-full flex items-center gap-4 rounded-2xl border p-3.5 text-left transition"
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
    return {
      jadwal: JSON.parse(JSON.stringify(JADWAL)),
      
      // Ambil tanggal real-time dari Laravel
      pilih: '{{ $tgl_iso }}', 
      bulan: {{ $bulan }}, 
      tahun: {{ $tahun }},
      
      LABEL: {
        belum:   { teks:'Belum Mulai', cls:'badge-neutral', icon:'circle-dashed' },
        proses:  { teks:'Diproses',    cls:'badge-process', icon:'chef-hat' },
        selesai: { teks:'Selesai',     cls:'badge-done',    icon:'check' }
      },

      init() {
        this.$nextTick(() => icons());
        this.$watch('pilih', () => this.$nextTick(() => icons()));
        this.$watch('grid', () => this.$nextTick(() => icons()));
        
        // Peringatan karena kita tidak memakai dummy date statis lagi
        if(!this.hariIni) {
            toast('Menampilkan kalender hari ini. Silakan klik tanggal awal bulan untuk melihat dummy data.', 'info');
        }
      },
      get namaBulan() { return BULAN[this.bulan] + ' ' + this.tahun; },
      get hariIni() { return this.jadwal.find(j => j.tanggal === this.pilih) || null; },
      get jumlahSelesai() { return this.hariIni ? this.hariIni.items.filter(i => i.status === 'selesai').length : 0; },
      get totalItem() { return this.hariIni ? this.hariIni.items.reduce((a,i)=>a+i.qty,0) : 0; },
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
      putar(it) {
        const urut = ['belum','proses','selesai'];
        it.status = urut[(urut.indexOf(it.status) + 1) % urut.length];
        toast(`${it.nama} ditandai "${this.LABEL[it.status].teks}".`, it.status === 'selesai' ? 'success' : 'info');
        this.$nextTick(() => icons());
        if (this.hariIni && this.jumlahSelesai === this.hariIni.items.length)
          setTimeout(() => toast('Semua produksi ' + tglID(this.pilih) + ' selesai.', 'success', 'Kerja bagus'), 600);
      }
    };
  }
</script>
@endpush
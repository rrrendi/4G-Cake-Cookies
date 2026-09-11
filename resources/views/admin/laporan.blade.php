@extends('layouts.admin')

@section('title', 'Laporan · 4G Cake & Cookies')
@section('header_title', 'Laporan')
@section('header_subtitle', 'Rekap penjualan, laba rugi, dan produk terjual per periode')

@section('content')
<div x-data="laporan()" x-init="init()">

  <!-- FILTER PERIODE -->
  <div class="card p-4 sm:p-5 mb-6 no-print">
    <div class="grid gap-3 lg:grid-cols-[1fr_auto_auto_auto]">
      <div class="grid sm:grid-cols-2 gap-3">
        <div>
          <label class="label" for="l-dari">Dari tanggal</label>
          <input id="l-dari" x-model="dari" type="date" class="input">
        </div>
        <div>
          <label class="label" for="l-sampai">Sampai tanggal</label>
          <input id="l-sampai" x-model="sampai" type="date" class="input">
        </div>
      </div>
      <div>
        <label class="label" for="l-jenis">Jenis laporan</label>
        <select id="l-jenis" x-model="jenis" class="select lg:w-52">
          <option value="penjualan">Laporan Penjualan</option>
          <option value="keuangan">Laporan Laba Rugi</option>
          <option value="produk">Laporan Produk Terjual</option>
        </select>
      </div>
      <div class="flex items-end">
        <button @click="terapkan()" class="btn btn-cocoa w-full lg:w-auto"><i data-lucide="filter" class="w-4 h-4"></i> Terapkan</button>
      </div>
      <div class="flex items-end gap-2">
        <button @click="ekspor('PDF')" class="btn btn-outline flex-1"><i data-lucide="file-text" class="w-4 h-4"></i> PDF</button>
        <button @click="ekspor('Excel')" class="btn btn-outline flex-1"><i data-lucide="sheet" class="w-4 h-4"></i> Excel</button>
        <button @click="window.print()" class="btn btn-ghost !px-3" title="Cetak"><i data-lucide="printer" class="w-4 h-4"></i></button>
      </div>
    </div>
  </div>

  <!-- KOP LAPORAN -->
  <div class="card p-6 sm:p-8 mb-6" id="area-cetak">
    <div class="flex flex-wrap items-start justify-between gap-4 pb-5 border-b border-cream-200">
      <div class="flex items-center gap-3">
        <span class="grid place-items-center w-12 h-12 rounded-2xl bg-rose-500 text-white font-display font-bold">4G</span>
        <div>
          <p class="font-display font-bold text-lg text-cocoa-700">4G Cake &amp; Cookies</p>
          <p class="text-xs text-cocoa-300">Jl. Samudera No. 12, Banda Sakti, Lhokseumawe, Aceh</p>
        </div>
      </div>
      <div class="text-right">
        <p class="font-display font-semibold text-cocoa-700" x-text="judul"></p>
        <p class="text-xs text-cocoa-300" x-text="tglID(dari) + ' – ' + tglID(sampai)"></p>
        <p class="text-[11px] text-cocoa-300 mt-1">Dicetak {{ $tanggalCetak }}</p>
      </div>
    </div>

    <!-- RINGKASAN ANGKA -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 py-6">
      <div>
        <p class="text-xs text-cocoa-300 mb-1.5">Jumlah pesanan</p>
        <p class="font-display font-bold text-2xl text-cocoa-700" x-text="ringkas.pesanan"></p>
        <p class="text-[11px] text-cocoa-300 mt-1"><span x-text="ringkas.transaksi"></span> transaksi keuangan</p>
      </div>
      <div>
        <p class="text-xs text-cocoa-300 mb-1.5">Total pemasukan</p>
        <p class="font-display font-bold text-2xl text-green-700" x-text="rp(ringkas.masuk)"></p>
      </div>
      <div>
        <p class="text-xs text-cocoa-300 mb-1.5">Total pengeluaran</p>
        <p class="font-display font-bold text-2xl text-rose-700" x-text="rp(ringkas.keluar)"></p>
      </div>
      <div>
        <p class="text-xs text-cocoa-300 mb-1.5">Laba bersih</p>
        <p class="font-display font-bold text-2xl text-cocoa-700" x-text="rp(ringkas.laba)"></p>
      </div>
    </div>

    <div class="h-[260px] pt-2 border-t border-cream-200"><canvas id="c-laporan" role="img" aria-label="Grafik ringkasan laporan periode terpilih"></canvas></div>
  </div>

  <!-- TABEL LAPORAN -->
  <div class="card overflow-hidden">
    <div class="flex items-center justify-between gap-3 px-5 sm:px-6 py-4 border-b border-cream-200">
      <h2 class="font-display font-bold text-cocoa-700" x-text="judul"></h2>
      <span class="badge badge-neutral" x-text="baris.length + ' baris'"></span>
    </div>

    <div class="table-wrap">
      <table class="data">
        <thead>
          <tr>
            <template x-for="h in kolom" :key="h"><th :class="h === 'Nominal' || h === 'Total' || h === 'Jumlah' ? 'text-right' : ''" x-text="h"></th></template>
          </tr>
        </thead>
        <tbody>
          <template x-for="(r, i) in baris" :key="i">
            <tr>
              <template x-for="(sel, j) in r" :key="j">
                <td :class="j === r.length - 1 ? 'text-right font-medium text-cocoa-700 whitespace-nowrap' : 'text-cocoa-500'" x-html="sel"></td>
              </template>
            </tr>
          </template>
        </tbody>
        <tfoot>
          <tr class="bg-cream-100">
            <td class="font-semibold text-cocoa-700 py-3 px-4" :colspan="kolom.length - 1">Total</td>
            <td class="text-right font-display font-bold text-cocoa-700 py-3 px-4" x-text="totalTeks"></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <div x-show="baris.length === 0" class="py-16 text-center">
      <span class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-cream-100 text-cocoa-300 mb-4"><i data-lucide="file-search" class="w-6 h-6"></i></span>
      <p class="font-display font-semibold text-cocoa-700 mb-1">Tidak ada data pada rentang ini</p>
      <p class="text-sm text-cocoa-400">Coba perlebar rentang tanggalnya.</p>
    </div>

    <div class="px-5 sm:px-6 py-5 border-t border-cream-200 grid sm:grid-cols-2 gap-6 text-xs text-cocoa-400">
      <p class="leading-relaxed">
        Laporan ini dihasilkan otomatis dari data transaksi. Pada fase Laravel, angka diambil langsung dari
        tabel <span class="font-mono text-cocoa-600">pesanan</span> dan <span class="font-mono text-cocoa-600">keuangan</span>.
      </p>
      <div class="sm:text-right">
        <p class="mb-10">Lhokseumawe, {{ $tanggalCetak }}</p>
        <p class="font-semibold text-cocoa-700">{{ Auth::user()->name ?? 'Gustina Rahmi' }}</p>
        <p class="capitalize">{{ Auth::user()->role === 'owner' ? 'Pemilik 4G Cake & Cookies' : 'Admin 4G Cake & Cookies' }}</p>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
  let chartLap = null;

  function laporan() {
    return {
      // Nilai dummy awal yang pas dengan data Anda
      dari:'2026-08-25', sampai:'{{ now()->format('Y-m-d') }}', jenis:'penjualan',

      init() { this.$nextTick(() => { icons(); this.gambar(); });
               this.$watch('jenis', () => this.$nextTick(() => { this.gambar(); icons(); })); },

      get judul() {
        return { penjualan:'Laporan Penjualan', keuangan:'Laporan Laba Rugi', produk:'Laporan Produk Terjual' }[this.jenis];
      },
      get pesananPeriode() { return semuaPesanan().filter(o => o.tanggal >= this.dari && o.tanggal <= this.sampai); },
      get keuPeriode() { return KEUANGAN.filter(e => e.tgl >= this.dari && e.tgl <= this.sampai); },
      get ringkas() {
        const masuk = this.keuPeriode.filter(e=>e.jenis==='pemasukan').reduce((a,e)=>a+e.nominal,0);
        const keluar = this.keuPeriode.filter(e=>e.jenis==='pengeluaran').reduce((a,e)=>a+e.nominal,0);
        return { pesanan: this.pesananPeriode.length, transaksi: this.keuPeriode.length,
                 masuk, keluar, laba: masuk - keluar };
      },
      get kolom() {
        if (this.jenis === 'penjualan') return ['Kode pesanan','Tanggal','Pelanggan','Metode','Status','Total'];
        if (this.jenis === 'keuangan')  return ['Tanggal','Jenis','Kategori','Keterangan','Nominal'];
        return ['Produk','Kategori','Harga satuan','Jumlah'];
      },
      get baris() {
        if (this.jenis === 'penjualan') {
          return this.pesananPeriode.map(o => [o.kode, tglID(o.tanggal), o.pelanggan, o.metode,
            `<span class="badge ${STATUS_PESANAN[o.status].cls}">${STATUS_PESANAN[o.status].label}</span>`, rp(o.total)]);
        }
        if (this.jenis === 'keuangan') {
          return this.keuPeriode.map(e => [tglID(e.tgl),
            `<span class="badge ${e.jenis==='pemasukan'?'badge-done':'badge-cancel'}">${e.jenis==='pemasukan'?'Pemasukan':'Pengeluaran'}</span>`,
            e.kategori, e.ket, (e.jenis==='pemasukan'?'+ ':'- ') + rp(e.nominal)]);
        }
        const map = {};
        this.pesananPeriode.forEach(o => o.items.forEach(it => {
          if (!map[it.nama]) map[it.nama] = { nama:it.nama, harga:it.harga, qty:0 };
          map[it.nama].qty += it.qty;
        }));
        return Object.values(map).sort((a,b)=>b.qty-a.qty).map(p => {
          const prod = PRODUK.find(x => x.nama === p.nama);
          return [p.nama, prod ? prod.kategori : '-', rp(p.harga), p.qty + ' pcs'];
        });
      },
      get totalTeks() {
        if (this.jenis === 'penjualan') return rp(this.pesananPeriode.reduce((a,o)=>a+o.total,0));
        if (this.jenis === 'keuangan')  return rp(this.ringkas.laba);
        return this.baris.reduce((a,r)=>a+parseInt(r[3]),0) + ' pcs';
      },

      terapkan() {
        if (this.dari > this.sampai) { toast('Tanggal awal melewati tanggal akhir.', 'error'); return; }
        toast(`${this.judul} ${tglID(this.dari)} – ${tglID(this.sampai)} dimuat.`, 'success', 'Filter diterapkan');
        this.$nextTick(() => { this.gambar(); icons(); });
      },
      ekspor(f) { toast('Export laporan disimulasikan. Fitur akan dihubungkan ke Laravel pada Fase 2.', 'info', 'Export ' + f); },

      gambar() {
        const el = document.getElementById('c-laporan');
        if (!el) return;
        if (chartLap) chartLap.destroy();
        let cfg;
        if (this.jenis === 'produk') {
          const b = this.baris.slice(0, 6);
          cfg = { type:'bar',
            data:{ labels: b.map(r=>r[0]), datasets:[{ data: b.map(r=>parseInt(r[3])), backgroundColor:'#C9A227', borderRadius:8, barPercentage:.6 }] },
            options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false},
              tooltip:{ backgroundColor:'#3C2A21', padding:12, cornerRadius:10, displayColors:false, callbacks:{ label:c=>c.parsed.y+' pcs' } } },
              scales:{ y:{ beginAtZero:true, grid:{color:'#F7EADB'}, border:{display:false} },
                       x:{ grid:{display:false}, border:{display:false}, ticks:{ font:{size:10}, maxRotation:0, callback(v){ const s=this.getLabelForValue(v); return s.length>14 ? s.slice(0,13)+'…' : s; } } } } } };
        } else {
          const hari = [...new Set(this.keuPeriode.map(e=>e.tgl))].sort();
          cfg = { type:'line',
            data:{ labels: hari.map(t=>tglID(t).split(' ').slice(0,2).join(' ')),
              datasets:[
                { label:'Pemasukan', data: hari.map(t=>this.keuPeriode.filter(e=>e.tgl===t&&e.jenis==='pemasukan').reduce((a,e)=>a+e.nominal,0)),
                  borderColor:'#5E9450', backgroundColor:'rgba(94,148,80,.12)', fill:true, tension:.35, borderWidth:3, pointRadius:4, pointBackgroundColor:'#fff', pointBorderWidth:2 },
                { label:'Pengeluaran', data: hari.map(t=>this.keuPeriode.filter(e=>e.tgl===t&&e.jenis==='pengeluaran').reduce((a,e)=>a+e.nominal,0)),
                  borderColor:'#D97E7E', backgroundColor:'rgba(217,126,126,.12)', fill:true, tension:.35, borderWidth:3, pointRadius:4, pointBackgroundColor:'#fff', pointBorderWidth:2 }
              ] },
            options:{ responsive:true, maintainAspectRatio:false,
              plugins:{ legend:{ position:'top', align:'end', labels:{ usePointStyle:true, boxWidth:8, padding:14, font:{size:11} } },
                tooltip:{ backgroundColor:'#3C2A21', padding:12, cornerRadius:10, displayColors:false, callbacks:{ label:c=>c.dataset.label+': '+rp(c.parsed.y) } } },
              scales:{ y:{ beginAtZero:true, grid:{color:'#F7EADB'}, border:{display:false}, ticks:{ callback:v=>rpShort(v), font:{size:10} } },
                       x:{ grid:{display:false}, border:{display:false}, ticks:{font:{size:10}} } } } };
        }
        chartLap = new Chart(el, cfg);
      }
    };
  }
</script>
@endpush
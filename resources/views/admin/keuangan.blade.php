@extends('layouts.admin')

@section('title', 'Modul Keuangan · 4G Cake & Cookies')
@section('header_title', 'Modul Keuangan')
@section('header_subtitle', 'Catat pemasukan dan pengeluaran, laba bersih dihitung otomatis')

@section('content')
<div x-data="modulKeuangan()" x-init="init()">

  <!-- PERIODE -->
  <div class="flex flex-wrap items-center gap-3 mb-5">
    <div class="flex gap-1 rounded-full bg-white border border-cream-200 p-1">
      <template x-for="p in periodeOpsi" :key="p.key">
        <button @click="periode = p.key" class="rounded-full px-4 py-2 text-sm font-medium transition"
          :class="periode === p.key ? 'bg-cocoa-500 text-white' : 'text-cocoa-400 hover:text-cocoa-600'"
          x-text="p.label"></button>
      </template>
    </div>
    <p class="text-sm text-cocoa-400" x-text="'Rentang: ' + labelPeriode"></p>
    <button @click="bukaForm('pemasukan')" class="btn btn-primary btn-sm ml-auto">
      <i data-lucide="plus" class="w-4 h-4"></i> Catat transaksi
    </button>
  </div>

  <!-- KARTU RINGKAS -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="card p-5">
      <div class="flex items-center gap-3 mb-4">
        <span class="grid place-items-center w-11 h-11 rounded-2xl bg-[#E9F6EC] text-green-700"><i data-lucide="arrow-down-left" class="w-5 h-5"></i></span>
        <p class="text-xs text-cocoa-300">Total pemasukan</p>
      </div>
      <p class="font-display font-bold text-2xl text-cocoa-700" x-text="rp(totalMasuk)"></p>
      <p class="text-[11px] text-cocoa-300 mt-1.5"><span x-text="entri.filter(e=>e.jenis==='pemasukan').length"></span> transaksi tercatat</p>
    </div>
    <div class="card p-5">
      <div class="flex items-center gap-3 mb-4">
        <span class="grid place-items-center w-11 h-11 rounded-2xl bg-[#FDECEA] text-rose-700"><i data-lucide="arrow-up-right" class="w-5 h-5"></i></span>
        <p class="text-xs text-cocoa-300">Total pengeluaran</p>
      </div>
      <p class="font-display font-bold text-2xl text-cocoa-700" x-text="rp(totalKeluar)"></p>
      <p class="text-[11px] text-cocoa-300 mt-1.5"><span x-text="entri.filter(e=>e.jenis==='pengeluaran').length"></span> transaksi tercatat</p>
    </div>
    <div class="card p-5 !bg-cocoa-700 !border-cocoa-700">
      <div class="flex items-center gap-3 mb-4">
        <span class="grid place-items-center w-11 h-11 rounded-2xl bg-white/10 text-gold-400"><i data-lucide="piggy-bank" class="w-5 h-5"></i></span>
        <p class="text-xs text-cream-200/60">Laba bersih <span class="text-cream-200/40">(otomatis)</span></p>
      </div>
      <p class="font-display font-bold text-2xl" :class="laba >= 0 ? 'text-white' : 'text-rose-300'" x-text="rp(laba)"></p>
      <p class="text-[11px] text-cream-200/50 mt-1.5">Margin <span x-text="margin"></span>% dari pemasukan</p>
    </div>
  </div>

  <div class="grid lg:grid-cols-[1.5fr_1fr] gap-4 sm:gap-6">
    <!-- TABEL TRANSAKSI -->
    <div class="card overflow-hidden">
      <div class="flex flex-wrap items-center gap-3 p-4 sm:p-5 border-b border-cream-200">
        <div class="flex gap-1 rounded-full bg-cream-100 p-1">
          <button @click="tab='semua'" class="rounded-full px-4 py-2 text-sm font-medium transition" :class="tab==='semua' ? 'bg-white text-cocoa-700 shadow-sm' : 'text-cocoa-400'">Semua</button>
          <button @click="tab='pemasukan'" class="rounded-full px-4 py-2 text-sm font-medium transition" :class="tab==='pemasukan' ? 'bg-white text-green-700 shadow-sm' : 'text-cocoa-400'">Pemasukan</button>
          <button @click="tab='pengeluaran'" class="rounded-full px-4 py-2 text-sm font-medium transition" :class="tab==='pengeluaran' ? 'bg-white text-rose-700 shadow-sm' : 'text-cocoa-400'">Pengeluaran</button>
        </div>
        <button @click="toast('Rekap ' + labelPeriode + ' disiapkan untuk diunduh (simulasi).','info','Ekspor')" class="btn btn-outline btn-sm ml-auto">
          <i data-lucide="download" class="w-4 h-4"></i> Unduh rekap
        </button>
      </div>

      <div class="table-wrap">
        <table class="data">
          <thead><tr><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th class="text-right">Nominal</th><th class="text-right">Aksi</th></tr></thead>
          <tbody>
            <template x-for="e in hasil" :key="e.id">
              <tr>
                <td class="text-cocoa-400 whitespace-nowrap" x-text="tglID(e.tgl)"></td>
                <td>
                  <span class="badge" :class="e.jenis==='pemasukan' ? 'badge-done' : 'badge-cancel'">
                    <span class="badge-dot"></span><span x-text="e.kategori"></span>
                  </span>
                </td>
                <td class="text-cocoa-500" x-text="e.ket"></td>
                <td class="text-right font-medium whitespace-nowrap"
                    :class="e.jenis==='pemasukan' ? 'text-green-700' : 'text-rose-700'"
                    x-text="(e.jenis==='pemasukan' ? '+ ' : '- ') + rp(e.nominal)"></td>
                <td>
                  <div class="flex justify-end gap-1">
                    <button @click="bukaEdit(e)" class="icon-btn tap" title="Edit"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                    <button @click="hapus(e)" class="icon-btn icon-btn-danger tap" title="Hapus"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <div x-show="hasil.length === 0" class="py-16 text-center">
        <span class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-cream-100 text-cocoa-300 mb-4"><i data-lucide="wallet" class="w-6 h-6"></i></span>
        <p class="font-display font-semibold text-cocoa-700 mb-1">Belum ada transaksi di periode ini</p>
        <p class="text-sm text-cocoa-400 mb-5">Catat pemasukan atau pengeluaran pertama untuk periode ini.</p>
        <button @click="bukaForm('pemasukan')" class="btn btn-primary btn-sm mx-auto">Catat transaksi</button>
      </div>

      <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-t border-cream-200 text-sm">
        <p class="text-cocoa-400"><span class="font-semibold text-cocoa-700" x-text="hasil.length"></span> transaksi ditampilkan</p>
        <p class="text-cocoa-400">Selisih periode: <span class="font-display font-bold" :class="laba>=0 ? 'text-green-700' : 'text-rose-700'" x-text="rp(laba)"></span></p>
      </div>
    </div>

    <!-- SISI KANAN: CHART + REKAP -->
    <div class="space-y-4 sm:space-y-6">
      <div class="card p-5 sm:p-6">
        <h2 class="font-display font-bold text-cocoa-700 mb-1">Arus kas</h2>
        <p class="text-xs text-cocoa-300 mb-5" x-text="labelPeriode"></p>
        <div class="h-[220px]"><canvas id="c-kas"></canvas></div>
      </div>

      <div class="card p-5 sm:p-6">
        <h2 class="font-display font-bold text-cocoa-700 mb-4">Rekap per kategori</h2>
        <div class="space-y-3">
          <template x-for="k in rekapKategori" :key="k.nama">
            <div>
              <div class="flex justify-between items-baseline gap-3 mb-1.5">
                <span class="text-sm text-cocoa-500" x-text="k.nama"></span>
                <span class="text-sm font-medium text-cocoa-700" x-text="rp(k.total)"></span>
              </div>
              <div class="h-2 rounded-full bg-cream-200 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500"
                     :class="k.jenis==='pemasukan' ? 'bg-green-600' : 'bg-rose-500'"
                     :style="'width:' + k.persen + '%'"></div>
              </div>
            </div>
          </template>
        </div>
      </div>

      <div class="card p-5 sm:p-6">
        <div class="flex items-center gap-3 mb-3">
          <span class="grid place-items-center w-9 h-9 rounded-xl bg-cream-200 text-gold-600"><i data-lucide="lightbulb" class="w-4 h-4"></i></span>
          <h2 class="font-display font-bold text-cocoa-700">Catatan cepat</h2>
        </div>
        <p class="text-sm text-cocoa-400 leading-relaxed">
          Laba bersih dihitung otomatis dari selisih pemasukan dan pengeluaran pada periode terpilih,
          jadi tidak perlu lagi menjumlah manual di buku tulis.
        </p>
      </div>
    </div>
  </div>

  <!-- MODAL FORM TRANSAKSI START -->
  <div x-show="modal" x-cloak class="fixed inset-0 z-[60] grid place-items-center p-4" role="dialog" aria-modal="true"
       @keydown.escape.window="tutupSemua()">
    <div @click="modal=false" class="modal-overlay"></div>
    <div x-show="modal" x-transition class="relative card w-full max-w-lg overflow-hidden">
      <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-cream-200">
        <h2 class="font-display font-bold text-lg text-cocoa-700" x-text="mode==='tambah' ? 'Catat transaksi baru' : 'Edit transaksi'"></h2>
        <button @click="modal=false" class="icon-btn tap" aria-label="Tutup"><i data-lucide="x" class="w-5 h-5"></i></button>
      </div>

      <div class="p-6 space-y-4">
        <div class="grid grid-cols-2 gap-2">
          <button @click="form.jenis='pemasukan'" class="btn" :class="form.jenis==='pemasukan' ? 'btn-primary' : 'btn-outline'">
            <i data-lucide="arrow-down-left" class="w-4 h-4"></i> Pemasukan</button>
          <button @click="form.jenis='pengeluaran'" class="btn" :class="form.jenis==='pengeluaran' ? 'btn-primary' : 'btn-outline'">
            <i data-lucide="arrow-up-right" class="w-4 h-4"></i> Pengeluaran</button>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="label" for="k-tgl">Tanggal</label>
            <input id="k-tgl" x-model="form.tgl" type="date" class="input">
          </div>
          <div>
            <label class="label" for="k-kat">Kategori</label>
            <select id="k-kat" x-model="form.kategori" class="select">
              <template x-for="k in (form.jenis==='pemasukan' ? katMasuk : katKeluar)" :key="k">
                <option x-text="k" :value="k"></option>
              </template>
            </select>
          </div>
        </div>
        <div>
          <label class="label" for="k-ket">Keterangan</label>
          <input id="k-ket" x-model="form.ket" type="text" class="input" placeholder="Contoh: Pesanan 4G-2409-0032 / Beli tepung 10 kg">
        </div>
        <div>
          <label class="label" for="k-nom">Nominal (Rp)</label>
          <input id="k-nom" x-model.number="form.nominal" type="number" min="0" step="1000" class="input" placeholder="150000">
          <p class="hint" x-show="form.nominal" x-text="'Terbaca: ' + rp(form.nominal || 0)"></p>
        </div>
      </div>

      <div class="flex gap-3 px-6 py-4 border-t border-cream-200 bg-cream-50">
        <button @click="modal=false" class="btn btn-outline">Batal</button>
        <button @click="simpan()" class="btn btn-primary ml-auto"><i data-lucide="save" class="w-4 h-4"></i> Simpan</button>
      </div>
    </div>
  </div>
  <!-- MODAL FORM TRANSAKSI END -->
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
  let chartKas = null;

  function modulKeuangan() {
    return {
      semua: JSON.parse(JSON.stringify(KEUANGAN)),
      tab:'semua', periode:'bulanan',
      periodeOpsi: [{key:'harian',label:'Harian'},{key:'mingguan',label:'Mingguan'},{key:'bulanan',label:'Bulanan'}],
      katMasuk: ['Penjualan Online','Penjualan Offline','Pesanan Custom','Lain-lain'],
      katKeluar:['Bahan Baku','Kemasan','Operasional','Gaji Harian','Ongkos Kirim','Promosi'],
      modal:false, mode:'tambah', target:null, form:{},

      tutupSemua() { this.modal = false; },
      init() {
        this.kosongkanForm();
        this.$nextTick(() => { icons(); this.gambarChart(); });
        this.$watch('periode', () => this.$nextTick(() => { this.gambarChart(); icons(); }));
        this.$watch('hasil', () => this.$nextTick(() => icons()));
      },

      /* Batas periode dihitung dari tanggal transaksi terbaru pada data dummy */
      get batas() {
        const akhir = new Date('2026-09-01T00:00:00');
        const mulai = new Date(akhir);
        if (this.periode === 'harian') mulai.setDate(akhir.getDate() - 0);
        else if (this.periode === 'mingguan') mulai.setDate(akhir.getDate() - 6);
        else mulai.setDate(akhir.getDate() - 29);
        return { mulai: mulai.toISOString().slice(0,10), akhir: akhir.toISOString().slice(0,10) };
      },
      get labelPeriode() {
        const b = this.batas;
        return this.periode === 'harian' ? tglID(b.akhir, true) : tglID(b.mulai) + ' – ' + tglID(b.akhir);
      },
      get entri() {
        const b = this.batas;
        return this.semua.filter(e => e.tgl >= b.mulai && e.tgl <= b.akhir);
      },
      get hasil() {
        return this.entri.filter(e => this.tab === 'semua' || e.jenis === this.tab)
                         .sort((a,b) => b.tgl.localeCompare(a.tgl));
      },
      get totalMasuk()  { return this.entri.filter(e=>e.jenis==='pemasukan').reduce((a,e)=>a+e.nominal,0); },
      get totalKeluar() { return this.entri.filter(e=>e.jenis==='pengeluaran').reduce((a,e)=>a+e.nominal,0); },
      get laba() { return this.totalMasuk - this.totalKeluar; },
      get margin() { return this.totalMasuk ? Math.round(this.laba / this.totalMasuk * 100) : 0; },
      get rekapKategori() {
        const map = {};
        this.entri.forEach(e => {
          const k = e.kategori;
          if (!map[k]) map[k] = { nama:k, jenis:e.jenis, total:0 };
          map[k].total += e.nominal;
        });
        const arr = Object.values(map).sort((a,b) => b.total - a.total);
        const max = Math.max(1, ...arr.map(a => a.total));
        arr.forEach(a => a.persen = Math.round(a.total / max * 100));
        return arr;
      },

      kosongkanForm() {
        this.form = { id:null, tgl:'2026-09-01', jenis:'pemasukan', kategori:'Penjualan Online', ket:'', nominal:null };
      },
      bukaForm(jenis) { this.mode='tambah'; this.kosongkanForm(); this.form.jenis = jenis; this.modal = true; this.$nextTick(() => icons()); },
      bukaEdit(e) { this.mode='edit'; this.target=e; this.form = { ...e }; this.modal = true; this.$nextTick(() => icons()); },
      simpan() {
        if (!this.form.ket.trim()) { toast('Keterangan wajib diisi.', 'error'); return; }
        if (!this.form.nominal || this.form.nominal <= 0) { toast('Nominal harus lebih dari nol.', 'error'); return; }
        if (this.mode === 'tambah') {
          this.semua.unshift({ ...this.form, id: Date.now() });
          toast(`${this.form.jenis === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran'} ${rp(this.form.nominal)} tercatat.`, 'success', 'Transaksi tersimpan');
        } else {
          Object.assign(this.target, this.form);
          toast('Transaksi diperbarui.', 'success');
        }
        this.modal = false;
        this.$nextTick(() => { this.gambarChart(); icons(); });
      },
      hapus(e) {
        this.semua = this.semua.filter(x => x.id !== e.id);
        toast('Transaksi dihapus dari catatan.', 'warning');
        this.$nextTick(() => { this.gambarChart(); icons(); });
      },

      gambarChart() {
        const el = document.getElementById('c-kas');
        if (!el) return;
        const hari = [...new Set(this.entri.map(e => e.tgl))].sort();
        const masuk  = hari.map(t => this.entri.filter(e => e.tgl === t && e.jenis === 'pemasukan').reduce((a,e)=>a+e.nominal,0));
        const keluar = hari.map(t => this.entri.filter(e => e.tgl === t && e.jenis === 'pengeluaran').reduce((a,e)=>a+e.nominal,0));
        if (chartKas) chartKas.destroy();
        chartKas = new Chart(el, {
          type:'bar',
          data:{ labels: hari.map(t => tglID(t).split(' ').slice(0,2).join(' ')),
            datasets:[
              { label:'Pemasukan', data:masuk, backgroundColor:'#5E9450', borderRadius:6, barPercentage:.7 },
              { label:'Pengeluaran', data:keluar, backgroundColor:'#D97E7E', borderRadius:6, barPercentage:.7 }
            ] },
          options:{ responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ position:'top', align:'end', labels:{ usePointStyle:true, boxWidth:8, padding:14, font:{size:11} } },
              tooltip:{ backgroundColor:'#3C2A21', padding:12, cornerRadius:10, displayColors:false,
                callbacks:{ label: c => c.dataset.label + ': ' + rp(c.parsed.y) } } },
            scales:{ y:{ beginAtZero:true, grid:{color:'#F7EADB'}, border:{display:false}, ticks:{ callback:v=>rpShort(v), font:{size:10} } },
                     x:{ grid:{display:false}, border:{display:false}, ticks:{font:{size:10}} } } }
        });
      }
    };
  }
</script>
@endpush
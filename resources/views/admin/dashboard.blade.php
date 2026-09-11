@extends('layouts.admin')

@section('title', 'Dashboard · 4G Cake & Cookies')
@section('header_title', 'Dashboard')
@section('header_subtitle', 'Ringkasan penjualan, pengeluaran, dan antrean produksi')

@section('content')
<!-- CARD RINGKASAN START -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4" id="kartu-ringkas"></div>
<!-- CARD RINGKASAN END -->

<div class="grid lg:grid-cols-[1.6fr_1fr] gap-4 sm:gap-6">
  <!-- CHART PENJUALAN -->
  <div class="card p-5 sm:p-6">
    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
      <div>
        <h2 class="font-display font-bold text-cocoa-700">Penjualan vs pengeluaran</h2>
        <p class="text-xs text-cocoa-300 mt-1">Enam bulan terakhir &middot; dalam rupiah</p>
      </div>
      <div class="flex gap-1 rounded-full bg-cream-100 p-1" role="group" aria-label="Rentang waktu grafik">
        <button type="button" id="rentang-6" onclick="ubahRentang(6)"
                class="rentang rounded-full px-3.5 py-2.5 text-xs font-medium bg-white text-cocoa-700 shadow-sm" aria-pressed="true">6 bulan</button>
        <button type="button" id="rentang-12" onclick="ubahRentang(12)"
                class="rentang rounded-full px-3.5 py-2.5 text-xs font-medium text-cocoa-400 hover:text-cocoa-600" aria-pressed="false">12 bulan</button>
      </div>
    </div>
    <div class="h-[280px] sm:h-[320px]"><canvas id="c-penjualan" role="img" aria-label="Grafik garis penjualan dan pengeluaran enam bulan terakhir"></canvas></div>
  </div>

  <!-- CHART PENGELUARAN -->
  <div class="card p-5 sm:p-6">
    <h2 class="font-display font-bold text-cocoa-700 mb-1">Pengeluaran per kategori</h2>
    <p class="text-xs text-cocoa-300 mb-5">Total bulan Agustus 2026</p>
    <div class="h-[240px]"><canvas id="c-pengeluaran" role="img" aria-label="Diagram donat pengeluaran per kategori"></canvas></div>
    <div id="legend-pengeluaran" class="mt-5 space-y-2"></div>
  </div>
</div>

<div class="grid lg:grid-cols-[1fr_1.3fr] gap-4 sm:gap-6">
  <!-- PRODUK TERLARIS -->
  <div class="card p-5 sm:p-6">
    <div class="flex items-start justify-between gap-3 mb-5">
      <div>
        <h2 class="font-display font-bold text-cocoa-700">Produk terlaris</h2>
        <p class="text-xs text-cocoa-300 mt-1">Jumlah item terjual sepanjang 2026</p>
      </div>
      <a href="{{ route('admin.produk.index') }}" class="btn btn-ghost btn-sm">Kelola</a>
    </div>
    <div class="h-[300px]"><canvas id="c-terlaris" role="img" aria-label="Diagram batang lima produk terlaris"></canvas></div>
  </div>

  <!-- PESANAN TERBARU -->
  <div class="card p-5 sm:p-6">
    <div class="flex items-start justify-between gap-3 mb-5">
      <div>
        <h2 class="font-display font-bold text-cocoa-700">Pesanan terbaru</h2>
        <p class="text-xs text-cocoa-300 mt-1">5 pesanan terakhir yang masuk</p>
      </div>
      <a href="{{ route('admin.pesanan.index') }}" class="btn btn-outline btn-sm">Semua pesanan</a>
    </div>
    <div class="table-wrap">
      <table class="data">
        <thead><tr><th>Kode</th><th>Pelanggan</th><th>Tanggal ambil</th><th>Status</th><th class="text-right">Total</th></tr></thead>
        <tbody id="tb-pesanan"></tbody>
      </table>
    </div>
  </div>
</div>

<div class="grid lg:grid-cols-2 gap-4 sm:gap-6">
  <!-- JADWAL HARI INI -->
  <div class="card p-5 sm:p-6">
    <div class="flex items-start justify-between gap-3 mb-5">
      <div>
        <h2 class="font-display font-bold text-cocoa-700">Antrean produksi terdekat</h2>
        <p class="text-xs text-cocoa-300 mt-1">Yang harus mulai dikerjakan dalam 3 hari ke depan</p>
      </div>
      <a href="{{ route('admin.jadwal.index') }}" class="btn btn-ghost btn-sm">Lihat jadwal</a>
    </div>
    <div id="antrean" class="space-y-4"></div>
  </div>

  <!-- AKSI CEPAT + STOK -->
  <div class="space-y-4 sm:space-y-6">
    <div class="card p-5 sm:p-6">
      <h2 class="font-display font-bold text-cocoa-700 mb-4">Aksi cepat</h2>
      <div class="grid sm:grid-cols-2 gap-3">
        <a href="{{ route('admin.produk.index') }}" class="flex items-center gap-3 rounded-2xl border border-cream-200 p-4 hover:border-rose-300 hover:bg-blush-50/40 transition">
          <span class="grid place-items-center w-10 h-10 rounded-xl bg-blush-100 text-rose-600 shrink-0"><i data-lucide="plus" class="w-5 h-5"></i></span>
          <span><span class="block text-sm font-semibold text-cocoa-700">Tambah produk</span><span class="block text-xs text-cocoa-300">Menu baru atau musiman</span></span>
        </a>
        <a href="{{ route('admin.keuangan.index') }}" class="flex items-center gap-3 rounded-2xl border border-cream-200 p-4 hover:border-rose-300 hover:bg-blush-50/40 transition">
          <span class="grid place-items-center w-10 h-10 rounded-xl bg-cream-200 text-gold-600 shrink-0"><i data-lucide="wallet" class="w-5 h-5"></i></span>
          <span><span class="block text-sm font-semibold text-cocoa-700">Catat keuangan</span><span class="block text-xs text-cocoa-300">Pemasukan / pengeluaran</span></span>
        </a>
        <a href="{{ route('admin.pengiriman.index') }}" class="flex items-center gap-3 rounded-2xl border border-cream-200 p-4 hover:border-rose-300 hover:bg-blush-50/40 transition">
          <span class="grid place-items-center w-10 h-10 rounded-xl bg-cocoa-200/60 text-cocoa-600 shrink-0"><i data-lucide="truck" class="w-5 h-5"></i></span>
          <span><span class="block text-sm font-semibold text-cocoa-700">Input resi</span><span class="block text-xs text-cocoa-300" id="aksi-resi">Paket menunggu resi</span></span>
        </a>
        <a href="{{ route('admin.laporan.index') }}" class="flex items-center gap-3 rounded-2xl border border-cream-200 p-4 hover:border-rose-300 hover:bg-blush-50/40 transition">
          <span class="grid place-items-center w-10 h-10 rounded-xl bg-blush-50 text-rose-500 shrink-0"><i data-lucide="file-bar-chart" class="w-5 h-5"></i></span>
          <span><span class="block text-sm font-semibold text-cocoa-700">Cetak laporan</span><span class="block text-xs text-cocoa-300">Harian sampai bulanan</span></span>
        </a>
      </div>
    </div>

    <div class="card p-5 sm:p-6">
      <div class="flex items-center gap-3 mb-4">
        <span class="grid place-items-center w-9 h-9 rounded-xl bg-[#FDECEA] text-rose-600"><i data-lucide="alert-triangle" class="w-4 h-4"></i></span>
        <h2 class="font-display font-bold text-cocoa-700">Perlu perhatian</h2>
      </div>
      <ul id="perhatian" class="space-y-3 text-sm"></ul>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
  const WARNA = { rose:'#D97E7E', gold:'#C9A227', cocoa:'#8B6B58', cream:'#F7EADB', teal:'#5E9450', dust:'#B49B8C' };

  function kartu(judul, nilai, delta, ikon, tone, sub) {
    const naik = delta >= 0;
    return `
    <div class="card card-hover p-5">
      <div class="flex items-start justify-between mb-4">
        <span class="grid place-items-center w-11 h-11 rounded-2xl ${tone}"><i data-lucide="${ikon}" class="w-5 h-5"></i></span>
        <span class="badge ${naik ? 'badge-done' : 'badge-cancel'}">
          <i data-lucide="${naik ? 'trending-up' : 'trending-down'}" class="w-3 h-3"></i>${naik ? '+' : ''}${delta}%
        </span>
      </div>
      <p class="text-xs text-cocoa-300 mb-1">${judul}</p>
      <p class="font-display font-bold text-2xl text-cocoa-700 mb-1.5">${nilai}</p>
      <p class="text-[11px] text-cocoa-300">${sub}</p>
    </div>`;
  }

  let chartPenjualan = null;

  function ubahRentang(n) {
    const pakai12 = n === 12;
    chartPenjualan.data.labels = pakai12 ? CHART_DATA.bulan12 : CHART_DATA.bulan;
    chartPenjualan.data.datasets[0].data = pakai12 ? CHART_DATA.penjualan12 : CHART_DATA.penjualan;
    chartPenjualan.data.datasets[1].data = pakai12 ? CHART_DATA.pengeluaran12 : CHART_DATA.pengeluaran;
    chartPenjualan.update();
    document.querySelectorAll('.rentang').forEach(b => {
      const aktif = b.id === 'rentang-' + n;
      b.className = 'rentang rounded-full px-3.5 py-2.5 text-xs font-medium ' +
        (aktif ? 'bg-white text-cocoa-700 shadow-sm' : 'text-cocoa-400 hover:text-cocoa-600');
      b.setAttribute('aria-pressed', aktif);
    });
    toast('Grafik menampilkan ' + n + ' bulan terakhir.', 'info');
  }

  document.addEventListener('DOMContentLoaded', () => {
    const R = RINGKASAN;
    document.getElementById('kartu-ringkas').innerHTML =
      kartu('Total Pesanan', R.totalPesanan.nilai + ' pesanan', R.totalPesanan.delta, 'receipt-text', 'bg-blush-100 text-rose-600', 'Agustus 2026 · Juli ' + R.totalPesanan.sebelum + ' pesanan') +
      kartu('Total Penjualan', rp(R.totalPenjualan.nilai), R.totalPenjualan.delta, 'trending-up', 'bg-cream-200 text-gold-600', 'Agustus 2026 · Juli ' + rp(R.totalPenjualan.sebelum)) +
      kartu('Total Pengeluaran', rp(R.totalPengeluaran.nilai), R.totalPengeluaran.delta, 'shopping-cart', 'bg-cocoa-200/60 text-cocoa-600', 'Bahan baku menyumbang 53%') +
      kartu('Laba Bersih', rp(R.labaBersih.nilai), R.labaBersih.delta, 'piggy-bank', 'bg-[#E9F6EC] text-green-700', 'Margin 54% dari total penjualan');

    /* --- Chart 1: penjualan vs pengeluaran --- */
    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.color = '#8B6B58';
    chartPenjualan = new Chart(document.getElementById('c-penjualan'), {
      type: 'line',
      data: {
        labels: CHART_DATA.bulan,
        datasets: [
          { label:'Penjualan', data: CHART_DATA.penjualan, borderColor: WARNA.rose, backgroundColor:'rgba(217,126,126,.14)',
            fill:true, tension:.38, borderWidth:3, pointRadius:4, pointBackgroundColor:'#fff', pointBorderWidth:2 },
          { label:'Pengeluaran', data: CHART_DATA.pengeluaran, borderColor: WARNA.cocoa, backgroundColor:'rgba(139,107,88,.10)',
            fill:true, tension:.38, borderWidth:3, pointRadius:4, pointBackgroundColor:'#fff', pointBorderWidth:2, borderDash:[6,4] }
        ]
      },
      options: {
        responsive:true, maintainAspectRatio:false,
        plugins: {
          legend:{ position:'top', align:'end', labels:{ usePointStyle:true, boxWidth:8, padding:16, font:{size:12} } },
          tooltip:{ backgroundColor:'#3C2A21', padding:12, cornerRadius:10, displayColors:false,
            callbacks:{ label: c => c.dataset.label + ': ' + rp(c.parsed.y) } }
        },
        scales: {
          y:{ beginAtZero:true, grid:{ color:'#F7EADB' }, border:{display:false}, ticks:{ callback:v => rpShort(v), font:{size:11} } },
          x:{ grid:{ display:false }, border:{display:false} }
        }
      }
    });

    /* --- Chart 2: pengeluaran per kategori --- */
    const palet = [WARNA.rose, WARNA.gold, WARNA.cocoa, WARNA.teal, WARNA.dust];
    new Chart(document.getElementById('c-pengeluaran'), {
      type: 'doughnut',
      data: { labels: CHART_DATA.pengeluaranKategori.label,
        datasets: [{ data: CHART_DATA.pengeluaranKategori.nilai, backgroundColor: palet, borderWidth:0, hoverOffset:8 }] },
      options: { responsive:true, maintainAspectRatio:false, cutout:'62%',
        plugins:{ legend:{display:false},
          tooltip:{ backgroundColor:'#3C2A21', padding:12, cornerRadius:10, displayColors:false,
            callbacks:{ label: c => c.label + ': ' + rp(c.parsed) } } } }
    });
    const totalPeng = CHART_DATA.pengeluaranKategori.nilai.reduce((a,b)=>a+b,0);
    document.getElementById('legend-pengeluaran').innerHTML = CHART_DATA.pengeluaranKategori.label.map((l,i) => `
      <div class="flex items-center gap-3 text-sm">
        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:${palet[i]}"></span>
        <span class="flex-1 text-cocoa-500 truncate">${l}</span>
        <span class="text-cocoa-300 text-xs">${Math.round(CHART_DATA.pengeluaranKategori.nilai[i]/totalPeng*100)}%</span>
        <span class="font-medium text-cocoa-700 w-20 text-right">${rp(CHART_DATA.pengeluaranKategori.nilai[i])}</span>
      </div>`).join('');

    /* --- Chart 3: produk terlaris --- */
    new Chart(document.getElementById('c-terlaris'), {
      type: 'bar',
      data: { labels: CHART_DATA.terlaris.label,
        datasets: [{ data: CHART_DATA.terlaris.nilai, backgroundColor: WARNA.gold, borderRadius:8, barThickness:18,
          hoverBackgroundColor: WARNA.rose }] },
      options: { indexAxis:'y', responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{display:false}, tooltip:{ backgroundColor:'#3C2A21', padding:12, cornerRadius:10, displayColors:false,
          callbacks:{ label: c => c.parsed.x + ' item terjual' } } },
        scales:{ x:{ beginAtZero:true, grid:{color:'#F7EADB'}, border:{display:false}, ticks:{font:{size:11}} },
                 y:{ grid:{display:false}, border:{display:false}, ticks:{font:{size:11}} } } }
    });

    /* --- Tabel pesanan terbaru --- */
    document.getElementById('tb-pesanan').innerHTML = semuaPesanan().slice(0,5).map(o => {
      const s = STATUS_PESANAN[o.status];
      return `<tr>
        <td class="whitespace-nowrap"><a href="{{ route('admin.pesanan.index') }}" class="font-medium text-cocoa-700 hover:text-rose-600 transition">${o.kode}</a></td>
        <td class="text-cocoa-500">${o.pelanggan}</td>
        <td class="text-cocoa-400 whitespace-nowrap">${tglID(o.ambil)}</td>
        <td><span class="badge ${s.cls}"><span class="badge-dot"></span>${s.label}</span></td>
        <td class="text-right font-medium text-cocoa-700 whitespace-nowrap">${rp(o.total)}</td>
      </tr>`;
    }).join('');

    /* --- Antrean produksi --- */
    document.getElementById('antrean').innerHTML = JADWAL.slice(0,3).map(j => `
      <div class="rounded-2xl border border-cream-200 p-4">
        <div class="flex items-center justify-between gap-3 mb-3">
          <div class="flex items-center gap-3">
            <span class="grid place-items-center w-11 h-11 rounded-xl bg-cream-100 text-cocoa-600 shrink-0 leading-none">
              <span class="text-[10px]">${j.hari.slice(0,3)}</span>
              <span class="font-display font-bold text-sm">${j.tanggal.slice(-2)}</span>
            </span>
            <div>
              <p class="text-sm font-semibold text-cocoa-700">${tglID(j.tanggal)}</p>
              <p class="text-[11px] text-cocoa-300">${j.pesanan} pesanan &middot; ${j.items.reduce((a,i)=>a+i.qty,0)} item</p>
            </div>
          </div>
          <a href="{{ route('admin.jadwal.index') }}" class="text-xs text-rose-600 hover:underline shrink-0">Detail</a>
        </div>
        <div class="flex flex-wrap gap-1.5">
          ${j.items.map(i => `<span class="badge badge-neutral">${i.nama} &times;${i.qty}</span>`).join('')}
        </div>
      </div>`).join('');

    /* --- Perlu perhatian --- */
    const habis = PRODUK.filter(p => p.stok === 'habis');
    const menunggu = semuaPesanan().filter(p => p.status === 'menunggu');
    const tanpaResi = PENGIRIMAN.filter(p => p.resi === '-');
    document.getElementById('perhatian').innerHTML = `
      <li class="flex gap-3">
        <i data-lucide="clock" class="w-4 h-4 text-gold-600 shrink-0 mt-0.5"></i>
        <span class="text-cocoa-500"><a href="{{ route('admin.pesanan.index') }}" class="font-semibold text-cocoa-700 hover:text-rose-600">${menunggu.length} pesanan</a> masih menunggu konfirmasi pembayaran.</span>
      </li>
      <li class="flex gap-3">
        <i data-lucide="package-x" class="w-4 h-4 text-rose-600 shrink-0 mt-0.5"></i>
        <span class="text-cocoa-500"><a href="{{ route('admin.produk.index') }}" class="font-semibold text-cocoa-700 hover:text-rose-600">${habis.length} produk</a> berstatus habis: ${habis.map(p=>p.nama).join(', ')}.</span>
      </li>
      <li class="flex gap-3">
        <i data-lucide="truck" class="w-4 h-4 text-cocoa-500 shrink-0 mt-0.5"></i>
        <span class="text-cocoa-500"><a href="{{ route('admin.pengiriman.index') }}" class="font-semibold text-cocoa-700 hover:text-rose-600">${tanpaResi.length} paket J&amp;T</a> belum diinput nomor resinya.</span>
      </li>
      <li class="flex gap-3">
        <i data-lucide="star" class="w-4 h-4 text-gold-500 shrink-0 mt-0.5"></i>
        <span class="text-cocoa-500"><a href="{{ route('admin.review.index') }}" class="font-semibold text-cocoa-700 hover:text-rose-600">3 ulasan baru</a> minggu ini, rata-rata 4,8 bintang.</span>
      </li>`;

    document.getElementById('aksi-resi').textContent = tanpaResi.length + ' paket menunggu resi';
    icons();
  });
</script>
@endpush
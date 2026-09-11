/* ==========================================================================
   4G Cake & Cookies — Helper UI (Fase 1.5, simulasi tanpa backend)
   Semua "aksi" di sini hanya mengubah tampilan / localStorage, bukan server.
   ========================================================================== */

/* Prefix path relatif: halaman admin memakai <body data-base="../"> */
const BASE = document.body?.dataset?.base ?? '';

/* ---------------------------------------------------------------- FORMAT -- */
const rp = n => 'Rp' + Number(n || 0).toLocaleString('id-ID');
const rpShort = n => {
  n = Number(n || 0);
  if (n >= 1000000) return 'Rp' + (n / 1000000).toFixed(1).replace('.0', '') + ' jt';
  if (n >= 1000) return 'Rp' + Math.round(n / 1000) + 'rb';
  return 'Rp' + n;
};
const BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const HARI = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

function tglID(iso, withHari = false) {
  if (!iso || iso === '-') return '-';
  const d = new Date(iso + 'T00:00:00');
  if (isNaN(d)) return iso;
  const s = `${d.getDate()} ${BULAN[d.getMonth()]} ${d.getFullYear()}`;
  return withHari ? `${HARI[d.getDay()]}, ${s}` : s;
}
/* Tanggal hari ini dalam prototype (dibekukan supaya data dummy tetap masuk akal) */
const HARI_INI = '2026-09-02';
function tambahHari(iso, n) {
  const d = new Date(iso + 'T00:00:00');
  d.setDate(d.getDate() + n);
  return d.toISOString().slice(0, 10);
}
/* Nomor pesanan: 4G-YYMMDD-NNN */
function nomorPesanan(iso, urutan) {
  return '4G-' + iso.slice(2).replace(/-/g, '') + '-' + String(urutan).padStart(3, '0');
}

/* ------------------------------------------------------------- PATH ASET -- */
const imgProduk  = slug => `${BASE}assets/img/products/${slug}.svg`;
const imgAvatar  = slug => `${BASE}assets/img/testimonials/${slug}.svg`;
const imgHero    = file => `${BASE}assets/img/hero/${file}`;

/* ------------------------------------------------------------------ STAR -- */
function starsHTML(rating, cls = 'w-4 h-4') {
  let out = '';
  for (let i = 1; i <= 5; i++) {
    const full = rating >= i - 0.25;
    out += `<svg class="${cls} ${full ? 'star' : 'star-empty'}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m12 17.27 5.18 3.13-1.37-5.9 4.58-3.96-6.03-.52L12 4.5 9.64 10.02l-6.03.52 4.58 3.96-1.37 5.9z"/></svg>`;
  }
  return `<span class="inline-flex items-center gap-[2px]" role="img" aria-label="Rating ${rating} dari 5">${out}</span>`;
}

/* ----------------------------------------------------------------- TOAST --
   Satu sistem toast global berbasis Alpine store. Dipanggil dengan:
   toast('pesan', 'success' | 'warning' | 'error' | 'info', 'Judul opsional')
   -------------------------------------------------------------------------- */
const TOAST_SVG = {
  success: '<svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>',
  warning: '<svg class="w-5 h-5 text-gold-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>',
  error:   '<svg class="w-5 h-5 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>',
  info:    '<svg class="w-5 h-5 text-cocoa-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>'
};
const ikonToast = tipe => TOAST_SVG[tipe] || TOAST_SVG.info;

let _toastId = 0;
const _antreanToast = [];

function toast(pesan, tipe = 'success', judul = null) {
  if (tipe === 'warn') tipe = 'warning';           /* alias */
  const item = { id: ++_toastId, pesan, tipe, judul };
  const store = window.Alpine && window.Alpine.store('toast');
  if (store) store.tambah(item); else _antreanToast.push(item);
}

document.addEventListener('alpine:init', () => {
  window.Alpine.store('toast', {
    items: [],
    tambah(t) {
      this.items.push(t);
      setTimeout(() => this.tutup(t.id), 4000);
    },
    tutup(id) { this.items = this.items.filter(x => x.id !== id); }
  });
  while (_antreanToast.length) window.Alpine.store('toast').tambah(_antreanToast.shift());

  /* Dialog konfirmasi global untuk aksi destruktif */
  window.Alpine.store('konfirmasi', {
    buka: false, judul: '', pesan: '', label: 'Ya, lanjutkan', ikon: 'triangle-alert', aksi: null,
    tanya(opt) {
      this.judul = opt.judul || 'Lanjutkan tindakan ini?';
      this.pesan = opt.pesan || '';
      this.label = opt.label || 'Ya, lanjutkan';
      this.ikon  = opt.ikon || 'triangle-alert';
      this.aksi  = opt.aksi || null;
      this.buka  = true;
      setTimeout(icons, 30);
    },
    ya() { const f = this.aksi; this.buka = false; this.aksi = null; if (f) f(); },
    batal() { this.buka = false; this.aksi = null; }
  });
});

/* Panggil dialog konfirmasi; bila Alpine belum siap, aksi dijalankan langsung. */
function konfirmasi(opt) {
  const store = window.Alpine && window.Alpine.store('konfirmasi');
  if (store) store.tanya(opt);
  else if (opt.aksi) opt.aksi();
}

/* ------------------------------------------------------- STATE TOMBOL LOAD -- */
/* Memberi tombol state "loading" sementara, lalu mengembalikannya. */
function tombolMuat(btn, teks = 'Memproses…') {
  if (!btn) return () => {};
  const asal = btn.innerHTML;
  btn.classList.add('is-loading');
  btn.setAttribute('aria-busy', 'true');
  btn.innerHTML = `<span class="spinner"></span> ${teks}`;
  return () => {
    btn.classList.remove('is-loading');
    btn.removeAttribute('aria-busy');
    btn.innerHTML = asal;
    icons();
  };
}

/* ------------------------------------------------------------- SKELETON --- */
function skeletonKartu(n = 8) {
  return Array.from({ length: n }).map(() => `
    <div class="card overflow-hidden" aria-hidden="true">
      <div class="skeleton !rounded-none aspect-[4/3]"></div>
      <div class="p-5 space-y-3">
        <div class="skeleton skeleton-line w-1/3"></div>
        <div class="skeleton skeleton-line w-4/5"></div>
        <div class="skeleton skeleton-line w-1/2"></div>
        <div class="flex gap-2 pt-2"><div class="skeleton h-9 flex-1 !rounded-full"></div><div class="skeleton h-9 flex-1 !rounded-full"></div></div>
      </div>
    </div>`).join('');
}
function skeletonBaris(kolom = 5, baris = 5) {
  return Array.from({ length: baris }).map(() => `
    <tr aria-hidden="true">${Array.from({ length: kolom }).map(() =>
      `<td><div class="skeleton skeleton-line"></div></td>`).join('')}</tr>`).join('');
}

/* --------------------------------------------- KERANJANG (simulasi lokal) -- */
const CART_KEY = '4g_cart';
let _memCart = null;   /* cadangan bila localStorage diblokir (mis. file:// ketat) */

const CART_AWAL = [
  { id:'P-02', qty:2, varian:'Topping Keju' },
  { id:'P-03', qty:1, varian:'Toples 250 gr' },
  { id:'P-07', qty:1, varian:'Box 350 ml' }
];

const Cart = {
  read() {
    if (_memCart) return _memCart;
    try {
      const raw = localStorage.getItem(CART_KEY);
      if (raw === null) { this.write(JSON.parse(JSON.stringify(CART_AWAL))); return this.read(); }
      return JSON.parse(raw);
    } catch (e) {
      _memCart = JSON.parse(JSON.stringify(CART_AWAL));
      return _memCart;
    }
  },
  write(items) {
    _memCart = items;
    try { localStorage.setItem(CART_KEY, JSON.stringify(items)); _memCart = null; } catch (e) {}
    document.dispatchEvent(new CustomEvent('cart:changed'));
    badgeKeranjang();
  },
  add(id, qty = 1, varian = null) {
    const items = this.read();
    const found = items.find(i => i.id === id && (varian === null || i.varian === varian));
    if (found) found.qty = Math.min(99, found.qty + qty); else items.push({ id, qty, varian });
    this.write(items);
  },
  setQty(idx, qty) {
    const items = this.read();
    if (!items[idx]) return;
    items[idx].qty = Math.max(1, Math.min(99, qty));
    this.write(items);
  },
  remove(idx) { const items = this.read(); items.splice(idx, 1); this.write(items); },
  clear() { this.write([]); },
  detail() {
    return this.read().map(i => {
      const p = (typeof PRODUK !== 'undefined') ? PRODUK.find(x => x.id === i.id) : null;
      return p ? { ...i, produk: p, subtotal: p.harga * i.qty } : null;
    }).filter(Boolean);
  },
  count() { return this.read().reduce((a, i) => a + i.qty, 0); },
  subtotal() { return this.detail().reduce((a, i) => a + i.subtotal, 0); },
  maxPO() { const d = this.detail(); return d.length ? Math.max(...d.map(i => i.produk.po)) : 1; }
};

function badgeKeranjang() {
  const n = Cart.count();
  document.querySelectorAll('[data-cart-badge]').forEach(el => {
    el.textContent = n;
    el.classList.toggle('hidden', n === 0);
    el.setAttribute('aria-label', n + ' item di keranjang');
  });
}

/* ----------------------------------- PESANAN BARU HASIL CHECKOUT (simulasi) --
   Pesanan yang dibuat lewat checkout disimpan sementara di localStorage supaya
   ikut muncul di Riwayat Pesanan, Detail Pesanan, dan panel admin.
   Di fase Laravel bagian ini digantikan tabel `pesanan`.
   -------------------------------------------------------------------------- */
const PESANAN_KEY = '4g_pesanan_baru';

function pesananBaru() {
  try { return JSON.parse(localStorage.getItem(PESANAN_KEY) || '[]'); }
  catch (e) { return []; }
}
function simpanPesanan(o) {
  try {
    const arr = pesananBaru();
    arr.unshift(o);
    localStorage.setItem(PESANAN_KEY, JSON.stringify(arr.slice(0, 10)));
  } catch (e) { /* localStorage diblokir — pesanan tetap tampil di halaman sukses */ }
}
function semuaPesanan() {
  const dasar = (typeof PESANAN !== 'undefined') ? PESANAN : [];
  const baru = pesananBaru().filter(o => !dasar.some(d => d.kode === o.kode));
  return [...baru, ...dasar];
}
function nomorPesananBaru() {
  const hariIni = semuaPesanan().filter(o => o.tanggal === HARI_INI).length;
  return nomorPesanan(HARI_INI, hariIni + 1);
}

/* ------------------------------------------------- TIMELINE STATUS PESANAN --
   Enam tahap sesuai alur bisnis 4G Cake & Cookies. Dipakai bersama oleh
   riwayat-pesanan.html dan pesanan-detail.html.
   -------------------------------------------------------------------------- */
const TAHAP_PESANAN = [
  { key:'dibuat',     label:'Pesanan Dibuat',          icon:'shopping-bag' },
  { key:'verifikasi', label:'Pembayaran Diverifikasi', icon:'badge-check' },
  { key:'diproses',   label:'Diproses',                icon:'chef-hat' },
  { key:'dikemas',    label:'Dikemas',                 icon:'package' },
  { key:'dikirim',    label:'Dikirim',                 icon:'truck' },
  { key:'selesai',    label:'Selesai',                 icon:'check-circle-2' }
];
const CAPAIAN = { menunggu:0, diproses:2, dikemas:3, dikirim:4, selesai:5 };

function timelinePesanan(o, panjang = false) {
  if (o.status === 'dibatalkan') {
    return [
      { label:'Pesanan Dibuat', icon:'shopping-bag',
        ket: panjang ? 'Pesanan masuk pada ' + tglID(o.tanggal, true) : tglID(o.tanggal), aktif:true },
      { label:'Dibatalkan', icon:'x-circle',
        ket:'Pembayaran tidak diselesaikan sampai batas waktu', aktif:true }
    ];
  }
  const capai = CAPAIAN[o.status] ?? 0;
  const ket = [
    panjang ? `Pesanan masuk pada ${tglID(o.tanggal, true)} pukul ${o.jamPesan || '-'}` : tglID(o.tanggal),
    'Bukti transfer dicek dan disetujui admin',
    'Bahan disiapkan, kue mulai dibuat',
    'Kue selesai dibuat dan dikemas rapi',
    o.metode === 'J&T'
      ? (o.resi && o.resi !== '-' ? 'Diserahkan ke kurir, resi ' + o.resi : 'Menunggu penjemputan kurir')
      : 'Siap diambil di toko sesuai jadwal',
    'Pesanan diterima pelanggan'
  ];
  return TAHAP_PESANAN.map((t, i) => ({
    label: t.label, icon: t.icon,
    ket: i <= capai ? ket[i] : 'Menunggu tahap sebelumnya',
    aktif: i <= capai
  }));
}

/* -------------------------------------------------------------- IKON ----- */
function icons() {
  if (window.lucide && window.lucide.createIcons) window.lucide.createIcons();
}

/* ------------------------------------------------- KARTU PRODUK (komponen) --
   Di fase Laravel ini menjadi <x-produk-card :produk="$p" />
   -------------------------------------------------------------------------- */
function kartuProduk(p) {
  const habis = p.stok === 'habis';
  const diskon = p.hargaCoret ? Math.round((1 - p.harga / p.hargaCoret) * 100) : 0;
  return `
  <article class="card card-hover overflow-hidden flex flex-col group h-full">
    <a href="${BASE}produk-detail.html?p=${p.slug}" class="relative block aspect-[4/3] overflow-hidden bg-cream-100"
       aria-label="Lihat detail ${p.nama}">
      <img src="${imgProduk(p.slug)}" alt="${p.nama}" loading="lazy"
           class="w-full h-full object-cover transition duration-500 group-hover:scale-[1.06] ${habis ? 'grayscale opacity-70' : ''}">
      <span class="absolute top-3 left-3 flex flex-col gap-1.5 items-start">
        ${p.bestSeller ? '<span class="badge badge-gold shadow-sm"><i data-lucide="flame" class="w-3 h-3"></i> Best Seller</span>' : ''}
        ${diskon ? `<span class="badge badge-rose shadow-sm">Hemat ${diskon}%</span>` : ''}
      </span>
      ${habis ? '<span class="absolute inset-x-0 bottom-0 bg-cocoa-700/85 text-white text-xs font-semibold text-center py-2">Stok Habis &mdash; buka PO minggu depan</span>' : ''}
    </a>
    <div class="p-4 sm:p-5 flex flex-col flex-1">
      <div class="flex items-center gap-2 mb-2">
        <span class="badge badge-neutral">${p.kategori}</span>
        <span class="flex items-center gap-1 text-xs text-cocoa-400">
          <i data-lucide="star" class="w-3.5 h-3.5 star fill-current"></i>${p.rating.toFixed(1)}
          <span class="text-cocoa-300">(${p.ulasan})</span>
        </span>
      </div>
      <h3 class="font-display font-semibold text-cocoa-700 leading-snug mb-1">
        <a href="${BASE}produk-detail.html?p=${p.slug}" class="hover:text-rose-600 transition">${p.nama}</a>
      </h3>
      <p class="text-xs text-cocoa-300 mb-3">${p.berat} &middot; PO min. H-${p.po}</p>
      <div class="mt-auto">
        <div class="flex items-baseline gap-2 mb-3">
          <span class="font-display font-bold text-lg text-rose-600">${rp(p.harga)}</span>
          ${p.hargaCoret ? `<span class="text-xs text-cocoa-300 line-through">${rp(p.hargaCoret)}</span>` : ''}
        </div>
        <div class="flex gap-2">
          <a href="${BASE}produk-detail.html?p=${p.slug}" class="btn btn-outline btn-sm flex-1">Detail</a>
          ${habis
            ? `<button type="button" class="btn btn-primary btn-sm flex-1 is-disabled" disabled aria-disabled="true">Habis</button>`
            : `<button type="button" class="btn btn-primary btn-sm flex-1" onclick="tambahKeranjang('${p.id}')" aria-label="Tambah ${p.nama} ke keranjang">
                 <i data-lucide="plus" class="w-4 h-4"></i> Keranjang</button>`}
        </div>
      </div>
    </div>
  </article>`;
}

function tambahKeranjang(id, qty = 1, varian = null) {
  const p = PRODUK.find(x => x.id === id);
  if (!p) return;
  if (p.stok === 'habis') { toast(`${p.nama} sedang habis, silakan pilih produk lain.`, 'error', 'Stok habis'); return; }
  Cart.add(id, qty, varian || p.varian[0]);
  toast(`${qty} &times; ${p.nama} berhasil ditambahkan ke keranjang.`, 'success', 'Masuk keranjang');
  if (p.po >= 2) {
    setTimeout(() => toast(
      `${p.nama} membutuhkan pre-order minimal H-${p.po} sebelum tanggal ambil atau kirim.`,
      'warning', 'Ketentuan pre-order'), 700);
  }
}

/* --------------------------------------------------------------- NAV AKTIF -- */
function tandaiNavAktif() {
  const file = location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('[data-nav]').forEach(a => {
    if (a.dataset.nav === file) {
      a.classList.add('active', 'text-rose-600');
      a.classList.remove('text-cocoa-500');
      a.setAttribute('aria-current', 'page');
    }
  });
}

/* ------------------------------------------------------------- INISIALISASI -- */
document.addEventListener('DOMContentLoaded', () => {
  icons();
  badgeKeranjang();
  tandaiNavAktif();

  /* Semua form di prototype ini tidak mengirim apa pun ke server. */
  document.querySelectorAll('form[data-simulasi]').forEach(f => {
    f.addEventListener('submit', ev => {
      ev.preventDefault();
      const tombol = f.querySelector('button[type="submit"]');
      const selesai = tombolMuat(tombol, f.dataset.muat || 'Memproses…');
      setTimeout(() => {
        selesai();
        toast(f.dataset.simulasi, f.dataset.tipe || 'success', f.dataset.judul || null);
        if (f.dataset.tujuan) setTimeout(() => { location.href = f.dataset.tujuan; }, 900);
      }, 700);
    });
  });
});

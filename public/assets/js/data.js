/* ==========================================================================
   4G Cake & Cookies — DATA DUMMY (Fase 1.5, masih tanpa backend)
   Di fase Laravel, isi file ini digantikan data dari database via Eloquent.

   Format nomor pesanan : 4G-YYMMDD-NNN   (contoh: 4G-260901-001)
   Format nomor resi    : JT + 10 digit   (contoh: JT8842190365)
   ========================================================================== */

const KATEGORI = ['Cake', 'Cookies', 'Brownies', 'Dessert', 'Hampers'];

/* --------------------------------------------------------------- PRODUK -- */
const PRODUK = [
  { id:'P-01', slug:'red-velvet-cake', nama:'Red Velvet Cake', kategori:'Cake',
    harga:285000, hargaCoret:320000, rating:4.9, ulasan:64, stok:'tersedia', po:2, terjual:186,
    bestSeller:true, berat:'1 kg (diameter 18 cm)', dibuat:'2026-02-14',
    desc:'Red velvet lembut dengan cream cheese frosting yang tidak terlalu manis. Dibuat fresh setiap hari, cocok untuk ulang tahun maupun hantaran.',
    bahan:'Tepung terigu protein sedang, gula, telur, mentega, buttermilk, bubuk kakao, pewarna makanan food grade, cream cheese, whipping cream.',
    simpan:'Kulkas 3–4 hari. Keluarkan 15 menit sebelum disajikan.',
    varian:['1 kg (18 cm)','1,5 kg (20 cm)','2 kg (22 cm)'] },
  { id:'P-02', slug:'brownies-kukus', nama:'Brownies Kukus Premium', kategori:'Brownies',
    harga:65000, hargaCoret:75000, rating:4.8, ulasan:132, stok:'tersedia', po:1, terjual:412,
    bestSeller:true, berat:'500 gram (20x10 cm)', dibuat:'2025-11-03',
    desc:'Brownies kukus dengan cokelat Belgia, teksturnya lumer dan lembap. Topping keju parut atau almond slice sesuai permintaan.',
    bahan:'Dark chocolate compound, tepung terigu, gula, telur, minyak sayur, susu bubuk, keju cheddar atau almond slice (sesuai varian).',
    simpan:'Suhu ruang 2 hari, kulkas sampai 5 hari.',
    varian:['Original','Topping Keju','Topping Almond'] },
  { id:'P-03', slug:'choco-chip-cookies', nama:'Choco Chip Cookies', kategori:'Cookies',
    harga:48000, hargaCoret:null, rating:4.7, ulasan:98, stok:'tersedia', po:1, terjual:305,
    bestSeller:true, berat:'250 gram (toples)', dibuat:'2026-01-20',
    desc:'Cookies renyah di luar, chewy di dalam, penuh choco chip. Dipanggang harian dalam batch kecil supaya tetap segar.',
    bahan:'Tepung terigu, mentega, gula palem, telur, choco chip, vanila, garam laut.',
    simpan:'Toples tertutup rapat di suhu ruang, tahan sampai 3 minggu.',
    varian:['Toples 250 gr','Toples 500 gr'] },
  { id:'P-04', slug:'nastar-kastengel', nama:'Nastar & Kastengel Spesial', kategori:'Cookies',
    harga:95000, hargaCoret:110000, rating:4.9, ulasan:210, stok:'tersedia', po:3, terjual:520,
    bestSeller:true, berat:'2 toples @300 gram', dibuat:'2025-09-08',
    desc:'Paket nastar selai nanas homemade dan kastengel keju Edam. Favorit saat Lebaran, wajib pesan H-3 karena antrean panjang.',
    bahan:'Tepung terigu, mentega wijsman, kuning telur, selai nanas homemade, keju Edam, keju cheddar.',
    simpan:'Toples tertutup rapat di suhu ruang, tahan sampai 1 bulan.',
    varian:['Paket 2 toples','Paket 4 toples'] },
  { id:'P-05', slug:'birthday-cake-custom', nama:'Birthday Cake Custom', kategori:'Cake',
    harga:350000, hargaCoret:null, rating:5.0, ulasan:41, stok:'tersedia', po:3, terjual:77,
    bestSeller:false, berat:'1 kg, desain sesuai permintaan', dibuat:'2026-04-02',
    desc:'Kue ulang tahun dengan desain sesuai tema yang diminta. Sertakan referensi gambar saat checkout, tim kami akan konfirmasi lewat WhatsApp.',
    bahan:'Sponge cake vanila atau cokelat, buttercream, fondant food grade, hiasan sesuai tema.',
    simpan:'Kulkas maksimal 2 hari, sebaiknya disantap di hari acara.',
    varian:['1 kg','2 kg','2 tingkat'] },
  { id:'P-06', slug:'cheese-cake-jepang', nama:'Cheese Cake Jepang', kategori:'Cake',
    harga:175000, hargaCoret:null, rating:4.8, ulasan:57, stok:'tersedia', po:2, terjual:143,
    bestSeller:true, berat:'700 gram (diameter 16 cm)', dibuat:'2026-03-11',
    desc:'Japanese cotton cheesecake yang ringan dan lembut seperti kapas. Rasa keju lembut, tidak eneg meski dimakan sepotong besar.',
    bahan:'Cream cheese, telur, gula halus, tepung maizena, susu cair, mentega tawar, air lemon.',
    simpan:'Kulkas 3 hari. Paling enak disajikan dingin.',
    varian:['Diameter 16 cm','Diameter 20 cm'] },
  { id:'P-07', slug:'dessert-box-tiramisu', nama:'Dessert Box Tiramisu', kategori:'Dessert',
    harga:55000, hargaCoret:65000, rating:4.7, ulasan:88, stok:'tersedia', po:1, terjual:268,
    bestSeller:false, berat:'350 ml per box', dibuat:'2026-05-06',
    desc:'Lapisan ladyfinger kopi, mascarpone cream, dan bubuk kakao. Disajikan dalam box tebal, aman untuk dikirim.',
    bahan:'Ladyfinger, mascarpone, whipping cream, kopi espresso, bubuk kakao, gula halus.',
    simpan:'Wajib di kulkas, tahan 2 hari.',
    varian:['Box 350 ml','Box 1 liter'] },
  { id:'P-08', slug:'hampers-lebaran', nama:'Hampers Lebaran Cookies', kategori:'Hampers',
    harga:275000, hargaCoret:310000, rating:4.9, ulasan:36, stok:'tersedia', po:5, terjual:64,
    bestSeller:false, berat:'4 toples + kartu ucapan', dibuat:'2026-02-01',
    desc:'Hampers berisi nastar, kastengel, putri salju, dan choco chip cookies dalam keranjang rotan. Termasuk kartu ucapan personal.',
    bahan:'4 toples kue kering pilihan, keranjang rotan, kain pelapis, kartu ucapan.',
    simpan:'Suhu ruang, hindari terkena sinar matahari langsung.',
    varian:['Keranjang Rotan','Box Eksklusif'] },
  { id:'P-09', slug:'bolu-pandan-keju', nama:'Bolu Pandan Keju', kategori:'Cake',
    harga:85000, hargaCoret:null, rating:4.6, ulasan:45, stok:'tersedia', po:1, terjual:129,
    bestSeller:false, berat:'600 gram (loyang tulban)', dibuat:'2026-05-22',
    desc:'Bolu pandan dari daun pandan asli, lembut dan wangi, dengan taburan keju cheddar di atasnya. Cocok untuk teman minum teh.',
    bahan:'Tepung terigu, telur, gula, santan, sari daun pandan asli, keju cheddar, mentega.',
    simpan:'Suhu ruang 2 hari, kulkas sampai 4 hari.',
    varian:['Loyang tulban','Loyang persegi'] },
  { id:'P-10', slug:'putri-salju', nama:'Putri Salju Almond', kategori:'Cookies',
    harga:52000, hargaCoret:null, rating:4.8, ulasan:73, stok:'habis', po:2, terjual:198,
    bestSeller:false, berat:'300 gram (toples)', dibuat:'2025-12-12',
    desc:'Kue kering almond yang lumer di mulut, dibalut gula halus tebal. Stok menyesuaikan ketersediaan almond impor.',
    bahan:'Tepung terigu, mentega, kacang almond bubuk, gula halus, susu bubuk, vanila.',
    simpan:'Toples tertutup rapat di suhu ruang, tahan sampai 1 bulan.',
    varian:['Toples 300 gr'] },
  { id:'P-11', slug:'choco-lava-cake', nama:'Choco Lava Cake', kategori:'Dessert',
    harga:42000, hargaCoret:null, rating:4.7, ulasan:52, stok:'tersedia', po:1, terjual:174,
    bestSeller:false, berat:'2 pcs per paket', dibuat:'2026-04-18',
    desc:'Cokelat lumer di tengah, cukup dihangatkan 30 detik sebelum disajikan. Dikirim dalam kondisi beku dengan ice gel.',
    bahan:'Dark chocolate, mentega tawar, telur, gula halus, tepung terigu, sedikit garam.',
    simpan:'Freezer sampai 2 minggu. Hangatkan 30 detik sebelum disantap.',
    varian:['Isi 2 pcs','Isi 4 pcs'] },
  { id:'P-12', slug:'dessert-box-oreo', nama:'Dessert Box Oreo Cheese', kategori:'Dessert',
    harga:58000, hargaCoret:null, rating:4.6, ulasan:61, stok:'habis', po:1, terjual:151,
    bestSeller:false, berat:'350 ml per box', dibuat:'2026-05-30',
    desc:'Remahan oreo, cream cheese lembut, dan lapisan susu. Manisnya pas, jadi favorit anak-anak.',
    bahan:'Biskuit oreo, cream cheese, whipping cream, susu kental manis, susu cair.',
    simpan:'Wajib di kulkas, tahan 2 hari.',
    varian:['Box 350 ml'] }
];

/* -------------------------------------------------------- STATUS PESANAN -- */
const STATUS_PESANAN = {
  menunggu:  { label:'Menunggu Pembayaran', singkat:'Menunggu Bayar', cls:'badge-wait',    icon:'clock' },
  diproses:  { label:'Diproses',            singkat:'Diproses',       cls:'badge-process', icon:'chef-hat' },
  dikemas:   { label:'Dikemas',             singkat:'Dikemas',        cls:'badge-pack',    icon:'package' },
  dikirim:   { label:'Dikirim',             singkat:'Dikirim',        cls:'badge-ship',    icon:'truck' },
  selesai:   { label:'Selesai',             singkat:'Selesai',        cls:'badge-done',    icon:'check-circle-2' },
  dibatalkan:{ label:'Dibatalkan',          singkat:'Dibatalkan',     cls:'badge-cancel',  icon:'x-circle' }
};

/* Status khusus pengiriman J&T (berbeda dari status pesanan) */
const STATUS_KIRIM = {
  pickup: { label:'Menunggu Pickup',   cls:'badge-wait',    icon:'package-search' },
  kurir:  { label:'Diambil Kurir',     cls:'badge-process', icon:'package-check' },
  jalan:  { label:'Dalam Perjalanan',  cls:'badge-ship',    icon:'truck' },
  sampai: { label:'Sampai Tujuan',     cls:'badge-done',    icon:'map-pin-check' }
};

/* -------------------------------------------------------------- PESANAN -- */
const PESANAN = [
  { kode:'4G-260901-002', pelanggan:'Nadia Safitri', hp:'0812-3344-5566', email:'nadia.safitri@gmail.com',
    tanggal:'2026-09-01', jamPesan:'09:24', ambil:'2026-09-04', jam:'13.00 – 15.00',
    metode:'J&T', status:'menunggu', total:350000, ongkir:22000, bayar:'Transfer BCA',
    alamat:'Jl. Merdeka No. 45, Kec. Banda Sakti, Lhokseumawe, Aceh 24351', resi:'-', catatan:'Tulisan di atas kue: "Selamat Ulang Tahun Aira".',
    items:[{nama:'Birthday Cake Custom', qty:1, harga:350000, slug:'birthday-cake-custom', varian:'1 kg'}] },
  { kode:'4G-260901-001', pelanggan:'Rizky Ananda', hp:'0813-2211-9080', email:'rizky.ananda@gmail.com',
    tanggal:'2026-09-01', jamPesan:'07:58', ambil:'2026-09-03', jam:'11.00 – 13.00',
    metode:'Ambil di Tempat', status:'diproses', total:196000, ongkir:0, bayar:'Transfer BCA',
    alamat:'Ambil di toko — Jl. Samudera No. 12, Banda Sakti, Lhokseumawe', resi:'-', catatan:'Brownies dipisah dua box ya.',
    items:[{nama:'Brownies Kukus Premium', qty:2, harga:65000, slug:'brownies-kukus', varian:'Topping Keju'},
           {nama:'Choco Chip Cookies', qty:1, harga:48000, slug:'choco-chip-cookies', varian:'Toples 250 gr'},
           {nama:'Choco Lava Cake', qty:1, harga:42000, slug:'choco-lava-cake', varian:'Isi 2 pcs'}] },
  { kode:'4G-260831-001', pelanggan:'Putri Maharani', hp:'0852-7788-1122', email:'putri.mhr@gmail.com',
    tanggal:'2026-08-31', jamPesan:'19:12', ambil:'2026-09-03', jam:'09.00 – 11.00',
    metode:'J&T', status:'dikemas', total:297000, ongkir:22000, bayar:'Transfer Mandiri',
    alamat:'Jl. Darussalam No. 9, Kota Langsa, Aceh 24354', resi:'-', catatan:'',
    items:[{nama:'Nastar & Kastengel Spesial', qty:1, harga:95000, slug:'nastar-kastengel', varian:'Paket 2 toples'},
           {nama:'Cheese Cake Jepang', qty:1, harga:175000, slug:'cheese-cake-jepang', varian:'Diameter 16 cm'}] },
  { kode:'4G-260830-001', pelanggan:'Ahmad Fauzan', hp:'0821-4455-6677', email:'a.fauzan21@gmail.com',
    tanggal:'2026-08-30', jamPesan:'10:41', ambil:'2026-09-02', jam:'13.00 – 15.00',
    metode:'J&T', status:'dikirim', total:340000, ongkir:25000, bayar:'Transfer BCA',
    alamat:'Jl. Cut Meutia No. 88, Bireuen, Aceh 24261', resi:'JT8842190365', catatan:'Tolong tambah bubble wrap.',
    items:[{nama:'Hampers Lebaran Cookies', qty:1, harga:275000, slug:'hampers-lebaran', varian:'Keranjang Rotan'},
           {nama:'Dessert Box Tiramisu', qty:1, harga:55000, slug:'dessert-box-tiramisu', varian:'Box 350 ml'}] },
  { kode:'4G-260829-001', pelanggan:'Siti Kamila', hp:'0857-9900-3344', email:'siti.kamila@gmail.com',
    tanggal:'2026-08-29', jamPesan:'08:03', ambil:'2026-08-31', jam:'15.00 – 17.00',
    metode:'Ambil di Tempat', status:'selesai', total:285000, ongkir:0, bayar:'Tunai',
    alamat:'Ambil di toko — Jl. Samudera No. 12, Banda Sakti, Lhokseumawe', resi:'-', catatan:'',
    items:[{nama:'Red Velvet Cake', qty:1, harga:285000, slug:'red-velvet-cake', varian:'1 kg (18 cm)'}] },
  { kode:'4G-260828-001', pelanggan:'Dedi Kurniawan', hp:'0811-2233-4455', email:'dedi.kurniawan@gmail.com',
    tanggal:'2026-08-28', jamPesan:'16:35', ambil:'2026-08-30', jam:'09.00 – 11.00',
    metode:'J&T', status:'selesai', total:151000, ongkir:20000, bayar:'Transfer BCA',
    alamat:'Jl. Iskandar Muda No. 3, Lhokseumawe, Aceh 24351', resi:'JT8842118742', catatan:'',
    items:[{nama:'Bolu Pandan Keju', qty:1, harga:85000, slug:'bolu-pandan-keju', varian:'Loyang tulban'},
           {nama:'Dessert Box Oreo Cheese', qty:1, harga:58000, slug:'dessert-box-oreo', varian:'Box 350 ml'}] },
  { kode:'4G-260827-001', pelanggan:'Fitri Handayani', hp:'0895-6677-8899', email:'fitrihandayani@gmail.com',
    tanggal:'2026-08-27', jamPesan:'21:07', ambil:'2026-08-29', jam:'11.00 – 13.00',
    metode:'J&T', status:'dibatalkan', total:110000, ongkir:22000, bayar:'Belum dibayar',
    alamat:'Jl. Pase No. 21, Lhokseumawe, Aceh 24351', resi:'-', catatan:'',
    items:[{nama:'Dessert Box Tiramisu', qty:2, harga:55000, slug:'dessert-box-tiramisu', varian:'Box 350 ml'}] },
  { kode:'4G-260826-001', pelanggan:'Bayu Pratama', hp:'0838-1010-2020', email:'bayu.pratama@gmail.com',
    tanggal:'2026-08-26', jamPesan:'13:50', ambil:'2026-08-28', jam:'17.00 – 19.00',
    metode:'Ambil di Tempat', status:'selesai', total:190000, ongkir:0, bayar:'Tunai',
    alamat:'Ambil di toko — Jl. Samudera No. 12, Banda Sakti, Lhokseumawe', resi:'-', catatan:'',
    items:[{nama:'Nastar & Kastengel Spesial', qty:2, harga:95000, slug:'nastar-kastengel', varian:'Paket 2 toples'}] }
];

/* ------------------------------------------------- JADWAL PRODUKSI HARIAN -- */
/* status item: belum | proses | selesai */
const JADWAL = [
  { tanggal:'2026-09-02', hari:'Rabu', pesanan:3, items:[
      {nama:'Hampers Lebaran Cookies', qty:1, kode:'4G-260830-001', status:'selesai'},
      {nama:'Dessert Box Tiramisu', qty:1, kode:'4G-260830-001', status:'selesai'},
      {nama:'Brownies Kukus Premium', qty:4, kode:'4G-260901-003', status:'proses'} ] },
  { tanggal:'2026-09-03', hari:'Kamis', pesanan:4, items:[
      {nama:'Nastar & Kastengel Spesial', qty:1, kode:'4G-260831-001', status:'proses'},
      {nama:'Cheese Cake Jepang', qty:1, kode:'4G-260831-001', status:'belum'},
      {nama:'Brownies Kukus Premium', qty:2, kode:'4G-260901-001', status:'belum'},
      {nama:'Choco Chip Cookies', qty:1, kode:'4G-260901-001', status:'belum'},
      {nama:'Choco Lava Cake', qty:1, kode:'4G-260901-001', status:'belum'} ] },
  { tanggal:'2026-09-04', hari:'Jumat', pesanan:2, items:[
      {nama:'Birthday Cake Custom', qty:1, kode:'4G-260901-002', status:'belum'},
      {nama:'Red Velvet Cake', qty:1, kode:'4G-260901-004', status:'belum'} ] },
  { tanggal:'2026-09-05', hari:'Sabtu', pesanan:5, items:[
      {nama:'Red Velvet Cake', qty:2, kode:'4G-260901-005', status:'belum'},
      {nama:'Bolu Pandan Keju', qty:3, kode:'4G-260901-006', status:'belum'},
      {nama:'Choco Chip Cookies', qty:4, kode:'4G-260901-007', status:'belum'} ] },
  { tanggal:'2026-09-06', hari:'Minggu', pesanan:1, items:[
      {nama:'Dessert Box Oreo Cheese', qty:6, kode:'4G-260901-008', status:'belum'} ] }
];

/* ------------------------------------------------------------ PENGIRIMAN -- */
const PENGIRIMAN = [
  { kode:'4G-260830-001', pelanggan:'Ahmad Fauzan', kota:'Bireuen', resi:'JT8842190365',
    status:'jalan', kurir:'J&T Express', ongkir:25000, tanggalKirim:'2026-09-01', update:'2026-09-01 14:20' },
  { kode:'4G-260831-001', pelanggan:'Putri Maharani', kota:'Kota Langsa', resi:'-',
    status:'pickup', kurir:'J&T Express', ongkir:22000, tanggalKirim:'-', update:'2026-09-01 09:05' },
  { kode:'4G-260901-002', pelanggan:'Nadia Safitri', kota:'Lhokseumawe', resi:'-',
    status:'pickup', kurir:'J&T Express', ongkir:22000, tanggalKirim:'-', update:'2026-09-01 08:40' },
  { kode:'4G-260828-001', pelanggan:'Dedi Kurniawan', kota:'Lhokseumawe', resi:'JT8842118742',
    status:'sampai', kurir:'J&T Express', ongkir:20000, tanggalKirim:'2026-08-29', update:'2026-08-30 16:10' },
  { kode:'4G-260825-001', pelanggan:'Rani Oktaviani', kota:'Aceh Utara', resi:'JT8841990233',
    status:'sampai', kurir:'J&T Express', ongkir:20000, tanggalKirim:'2026-08-24', update:'2026-08-25 11:35' },
  { kode:'4G-260824-001', pelanggan:'Maulida Zahra', kota:'Banda Aceh', resi:'JT8842203118',
    status:'kurir', kurir:'J&T Express', ongkir:30000, tanggalKirim:'2026-08-24', update:'2026-08-24 08:15' }
];

/* -------------------------------------------------------------- KEUANGAN -- */
const KEUANGAN = [
  { id:1, tgl:'2026-09-01', jenis:'pemasukan',   kategori:'Penjualan Online',  ket:'Pesanan 4G-260901-001', nominal:196000 },
  { id:2, tgl:'2026-09-01', jenis:'pengeluaran', kategori:'Bahan Baku',        ket:'Tepung, gula, mentega', nominal:420000 },
  { id:3, tgl:'2026-08-31', jenis:'pemasukan',   kategori:'Penjualan Online',  ket:'Pesanan 4G-260831-001', nominal:297000 },
  { id:4, tgl:'2026-08-31', jenis:'pengeluaran', kategori:'Kemasan',           ket:'Box kue 100 pcs',       nominal:185000 },
  { id:5, tgl:'2026-08-30', jenis:'pemasukan',   kategori:'Penjualan Online',  ket:'Pesanan 4G-260830-001', nominal:340000 },
  { id:6, tgl:'2026-08-30', jenis:'pengeluaran', kategori:'Operasional',       ket:'Gas LPG 2 tabung',      nominal:80000 },
  { id:7, tgl:'2026-08-29', jenis:'pemasukan',   kategori:'Penjualan Offline', ket:'Pesanan 4G-260829-001', nominal:285000 },
  { id:8, tgl:'2026-08-29', jenis:'pengeluaran', kategori:'Bahan Baku',        ket:'Cokelat & keju',        nominal:310000 },
  { id:9, tgl:'2026-08-28', jenis:'pemasukan',   kategori:'Penjualan Online',  ket:'Pesanan 4G-260828-001', nominal:151000 },
  { id:10,tgl:'2026-08-28', jenis:'pengeluaran', kategori:'Ongkos Kirim',      ket:'Setoran J&T mingguan',  nominal:95000 },
  { id:11,tgl:'2026-08-27', jenis:'pemasukan',   kategori:'Penjualan Offline', ket:'Penjualan toko harian', nominal:240000 },
  { id:12,tgl:'2026-08-26', jenis:'pemasukan',   kategori:'Penjualan Online',  ket:'Pesanan 4G-260826-001', nominal:190000 },
  { id:13,tgl:'2026-08-26', jenis:'pengeluaran', kategori:'Gaji Harian',       ket:'Upah 2 asisten dapur',  nominal:200000 },
  { id:14,tgl:'2026-08-25', jenis:'pengeluaran', kategori:'Bahan Baku',        ket:'Telur 10 kg',           nominal:290000 }
];

/* ---------------------------------------------------------------- REVIEW -- */
const REVIEWS = [
  { produk:'Red Velvet Cake', slug:'red-velvet-cake', nama:'Siti Kamila', avatar:'siti-kamila',
    kode:'4G-260829-001', tgl:'2026-08-31', bintang:5, aspek:{rasa:5, kualitas:5, packaging:5, pelayanan:5},
    teks:'Kuenya lembut banget dan cream cheese-nya nggak bikin eneg. Dikirim tepat waktu untuk ulang tahun mama. Pasti pesan lagi.' },
  { produk:'Brownies Kukus Premium', slug:'brownies-kukus', nama:'Rizky Ananda', avatar:'rizky-ananda',
    kode:'4G-260901-001', tgl:'2026-08-30', bintang:5, aspek:{rasa:5, kualitas:5, packaging:4, pelayanan:5},
    teks:'Brownies-nya lumer, cokelatnya berasa pekat. Packaging rapi walau boxnya agak tipis. Recommended.' },
  { produk:'Nastar & Kastengel Spesial', slug:'nastar-kastengel', nama:'Bayu Pratama', avatar:'bayu-pratama',
    kode:'4G-260826-001', tgl:'2026-08-29', bintang:5, aspek:{rasa:5, kualitas:5, packaging:5, pelayanan:4},
    teks:'Nastarnya selai nanas asli, tidak terlalu manis. Kastengel kejunya melimpah. Toples juga rapat.' },
  { produk:'Dessert Box Tiramisu', slug:'dessert-box-tiramisu', nama:'Ahmad Fauzan', avatar:'ahmad-fauzan',
    kode:'4G-260830-001', tgl:'2026-08-28', bintang:4, aspek:{rasa:5, kualitas:4, packaging:4, pelayanan:4},
    teks:'Rasa tiramisunya enak, kopinya pas. Semoga next time dikasih sendok plastik ya.' },
  { produk:'Cheese Cake Jepang', slug:'cheese-cake-jepang', nama:'Putri Maharani', avatar:'putri-maharani',
    kode:'4G-260831-001', tgl:'2026-08-27', bintang:5, aspek:{rasa:5, kualitas:5, packaging:5, pelayanan:5},
    teks:'Teksturnya benar-benar seperti kapas, ringan. Anak-anak habis satu loyang dalam sehari.' },
  { produk:'Bolu Pandan Keju', slug:'bolu-pandan-keju', nama:'Dedi Kurniawan', avatar:'dedi-kurniawan',
    kode:'4G-260828-001', tgl:'2026-08-26', bintang:4, aspek:{rasa:4, kualitas:4, packaging:5, pelayanan:5},
    teks:'Wangi pandannya asli, tidak seperti pakai perisa. Kejunya boleh ditambah sedikit lagi.' },
  { produk:'Choco Chip Cookies', slug:'choco-chip-cookies', nama:'Fitri Handayani', avatar:'fitri-handayani',
    kode:'4G-260827-001', tgl:'2026-08-24', bintang:5, aspek:{rasa:5, kualitas:5, packaging:4, pelayanan:5},
    teks:'Renyah di luar, chewy di dalam, persis seperti deskripsi. Sudah pesan tiga kali dan konsisten.' },
  { produk:'Hampers Lebaran Cookies', slug:'hampers-lebaran', nama:'Rani Oktaviani', avatar:'rani-oktaviani',
    kode:'4G-260825-001', tgl:'2026-08-22', bintang:5, aspek:{rasa:5, kualitas:5, packaging:5, pelayanan:5},
    teks:'Dikirim ke keluarga di Medan dan mereka senang sekali. Keranjang rotannya cantik, kartu ucapannya manis.' },
  { produk:'Choco Lava Cake', slug:'choco-lava-cake', nama:'Maulida Zahra', avatar:'putri-maharani',
    kode:'4G-260824-001', tgl:'2026-08-20', bintang:3, aspek:{rasa:4, kualitas:3, packaging:3, pelayanan:4},
    teks:'Rasanya enak, tapi sampai di rumah sudah agak mencair karena perjalanan jauh. Mungkin ice gel-nya ditambah.' }
];

/* ---------------------------------------------------------------- PENGGUNA -- */
const USERS = [
  { id:1, nama:'Nadia Safitri',   email:'nadia.safitri@gmail.com',  role:'Customer', status:'aktif',    daftar:'2026-08-14', pesanan:4 },
  { id:2, nama:'Rizky Ananda',    email:'rizky.ananda@gmail.com',   role:'Customer', status:'aktif',    daftar:'2026-08-11', pesanan:7 },
  { id:3, nama:'Putri Maharani',  email:'putri.mhr@gmail.com',      role:'Customer', status:'aktif',    daftar:'2026-07-29', pesanan:3 },
  { id:4, nama:'Ahmad Fauzan',    email:'a.fauzan21@gmail.com',     role:'Customer', status:'aktif',    daftar:'2026-07-18', pesanan:6 },
  { id:5, nama:'Fitri Handayani', email:'fitrihandayani@gmail.com', role:'Customer', status:'nonaktif', daftar:'2026-06-30', pesanan:1 },
  { id:6, nama:'Ibu Gustina',     email:'owner@4gcake.id',          role:'Owner',    status:'aktif',    daftar:'2026-05-02', pesanan:0 },
  { id:7, nama:'Dewi Anggraini',  email:'admin@4gcake.id',          role:'Admin',    status:'aktif',    daftar:'2026-05-02', pesanan:0 },
  { id:8, nama:'Bayu Pratama',    email:'bayu.pratama@gmail.com',   role:'Customer', status:'aktif',    daftar:'2026-06-11', pesanan:5 }
];

/* Angka ringkasan dashboard (periode: Agustus 2026 vs Juli 2026) */
const RINGKASAN = {
  totalPesanan:    { nilai:128,      delta:+12.5, sebelum:114 },
  totalPenjualan:  { nilai:24850000, delta:+18.2, sebelum:21000000 },
  totalPengeluaran:{ nilai:11430000, delta:+6.4,  sebelum:10700000 },
  labaBersih:      { nilai:13420000, delta:+28.9, sebelum:10300000 }
};

const CHART_DATA = {
  bulan: ['Mar','Apr','Mei','Jun','Jul','Ags'],
  penjualan:   [12400000, 15800000, 22600000, 17900000, 21000000, 24850000],
  pengeluaran: [ 6800000,  8100000, 10900000,  9200000, 10700000, 11430000],

  /* Rentang 12 bulan (Sep 2025 - Ags 2026) untuk tombol "12 bulan" di dashboard */
  bulan12: ['Sep','Okt','Nov','Des','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags'],
  penjualan12:   [9800000, 11200000, 13600000, 19400000, 10900000, 13100000, 12400000, 15800000, 22600000, 17900000, 21000000, 24850000],
  pengeluaran12: [5400000,  6100000,  7300000,  9800000,  6200000,  7000000,  6800000,  8100000, 10900000,  9200000, 10700000, 11430000],
  terlaris: {
    label: ['Brownies Kukus','Nastar & Kastengel','Choco Chip Cookies','Red Velvet Cake','Cheese Cake Jepang'],
    nilai: [412, 520, 305, 186, 143]
  },
  pengeluaranKategori: {
    label: ['Bahan Baku','Kemasan','Operasional','Gaji Harian','Ongkos Kirim'],
    nilai: [6100000, 1750000, 1420000, 1600000, 560000]
  }
};

/* Rekening tujuan pembayaran (dummy) */
const BANK = [
  { kode:'bca',     nama:'BCA',     rek:'0451 887 2210',  an:'a.n. Gustina Rahmi' },
  { kode:'mandiri', nama:'MANDIRI', rek:'1050 0088 7734', an:'a.n. Gustina Rahmi' },
  { kode:'bsi',     nama:'BSI',     rek:'7211 0093 55',   an:'a.n. Gustina Rahmi' }
];

/* Pilihan jam ambil / kirim */
const SLOT_JAM = ['09.00 – 11.00','11.00 – 13.00','13.00 – 15.00','15.00 – 17.00','17.00 – 19.00'];

/* Ongkir per kota tujuan (dummy) */
const ONGKIR_KOTA = {
  'Lhokseumawe': 20000, 'Aceh Utara': 22000, 'Bireuen': 25000,
  'Kota Langsa': 25000, 'Banda Aceh': 30000
};

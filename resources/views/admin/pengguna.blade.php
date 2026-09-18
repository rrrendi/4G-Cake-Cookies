@extends('layouts.admin')

@section('title', 'Pengguna & Role · 4G Cake & Cookies')
@section('header_title', 'Pengguna & Role')
@section('header_subtitle', 'Daftar akun pelanggan, admin, dan owner beserta hak aksesnya')

@section('content')
<div x-data="kelolaUser()" x-init="init()">

  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <div class="card p-4 flex items-center gap-3">
      <span class="grid place-items-center w-10 h-10 rounded-xl bg-blush-100 text-rose-600 shrink-0"><i data-lucide="users" class="w-5 h-5"></i></span>
      <div><p class="font-display font-bold text-xl text-cocoa-700" x-text="list.length"></p><p class="text-[11px] text-cocoa-300">Total pengguna</p></div>
    </div>
    <div class="card p-4 flex items-center gap-3">
      <span class="grid place-items-center w-10 h-10 rounded-xl bg-cream-200 text-gold-600 shrink-0"><i data-lucide="user-round" class="w-5 h-5"></i></span>
      <div><p class="font-display font-bold text-xl text-cocoa-700" x-text="list.filter(u=>u.role==='Customer').length"></p><p class="text-[11px] text-cocoa-300">Customer</p></div>
    </div>
    <div class="card p-4 flex items-center gap-3">
      <span class="grid place-items-center w-10 h-10 rounded-xl bg-cocoa-200/60 text-cocoa-600 shrink-0"><i data-lucide="shield-check" class="w-5 h-5"></i></span>
      <div><p class="font-display font-bold text-xl text-cocoa-700" x-text="list.filter(u=>u.role!=='Customer').length"></p><p class="text-[11px] text-cocoa-300">Admin &amp; Owner</p></div>
    </div>
    <div class="card p-4 flex items-center gap-3">
      <span class="grid place-items-center w-10 h-10 rounded-xl bg-[#E9F6EC] text-green-700 shrink-0"><i data-lucide="user-check" class="w-5 h-5"></i></span>
      <div><p class="font-display font-bold text-xl text-cocoa-700" x-text="list.filter(u=>u.status==='aktif').length"></p><p class="text-[11px] text-cocoa-300">Akun aktif</p></div>
    </div>
  </div>

  <!--
    Catatan desain: tombol "Undang pengguna" versi prototipe dihapus di sini.
    Sistem ini tidak punya alur invite-token — semua akun (customer maupun
    calon admin) mendaftar sendiri lewat Google/registrasi biasa, lalu role-nya
    dipromosikan lewat dropdown Role pada tabel di bawah. Slotnya diganti filter
    status yang sebelumnya belum ada.
  -->
  <div class="card p-4 sm:p-5 mb-5">
    <div class="grid gap-3 md:grid-cols-4">
      <div class="relative md:col-span-2">
        <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-cocoa-300 pointer-events-none"></i>
        <input x-model="q" @input="halaman = 1" type="search" class="input !pl-11" placeholder="Cari nama atau email" aria-label="Cari pengguna">
      </div>
      <select x-model="fRole" @change="halaman = 1" class="select" aria-label="Filter role">
        <option value="">Semua role</option>
        <option>Customer</option><option>Admin</option><option>Owner</option>
      </select>
      <select x-model="fStatus" @change="halaman = 1" class="select" aria-label="Filter status akun">
        <option value="">Semua status</option>
        <option value="aktif">Aktif</option>
        <option value="nonaktif">Nonaktif</option>
      </select>
    </div>
  </div>

  <!-- TABEL PENGGUNA START -->
  <div class="card overflow-hidden">
    <div class="table-wrap">
      <table class="data">
        <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Terdaftar</th><th>Pesanan</th><th class="text-right">Aksi</th></tr></thead>
        <tbody>
          <template x-for="u in halamanIni" :key="u.id">
            <tr>
              <td>
                <div class="flex items-center gap-3 min-w-[180px]">
                  <span class="grid place-items-center w-9 h-9 rounded-full text-white text-[11px] font-semibold shrink-0"
                        :class="u.role === 'Owner' ? 'bg-gold-500' : u.role === 'Admin' ? 'bg-cocoa-500' : 'bg-rose-400'"
                        x-text="u.nama.split(' ').map(w=>w[0]).slice(0,2).join('')"></span>
                  <span class="font-medium text-cocoa-700" x-text="u.nama"></span>
                </div>
              </td>
              <td class="text-cocoa-400" x-text="u.email"></td>
              <td>
                <select :value="u.role" @change="ubahRole(u, $event.target.value)" class="select !py-1.5 !px-3 !pr-8 !text-xs w-32" :aria-label="'Role ' + u.nama">
                  <option>Customer</option><option>Admin</option><option>Owner</option>
                </select>
              </td>
              <td>
                <button @click="toggleStatus(u)" class="badge" :class="u.status === 'aktif' ? 'badge-done' : 'badge-neutral'">
                  <span class="badge-dot"></span><span x-text="u.status === 'aktif' ? 'Aktif' : 'Nonaktif'"></span>
                </button>
              </td>
              <td class="text-cocoa-400 whitespace-nowrap" x-text="tglID(u.daftar)"></td>
              <td class="text-cocoa-500" x-text="u.pesanan"></td>
              <td>
                <div class="flex justify-end gap-1">
                  <button @click="lihatDetail(u)" class="grid place-items-center w-9 h-9 rounded-xl text-cocoa-400 hover:bg-cream-100 hover:text-cocoa-700 transition" title="Lihat detail">
                    <i data-lucide="eye" class="w-4 h-4"></i></button>
                  <button @click="resetSandi(u)" class="grid place-items-center w-9 h-9 rounded-xl text-cocoa-400 hover:bg-cream-100 hover:text-cocoa-700 transition" title="Reset sandi">
                    <i data-lucide="key-round" class="w-4 h-4"></i></button>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div x-show="hasil.length === 0" class="py-16 text-center">
      <span class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-cream-100 text-cocoa-300 mb-4"><i data-lucide="user-x" class="w-6 h-6"></i></span>
      <p class="font-display font-semibold text-cocoa-700 mb-1">Pengguna tidak ditemukan</p>
      <p class="text-sm text-cocoa-400 mb-5">Coba kata kunci lain atau ubah filter role/status.</p>
      <button @click="q=''; fRole=''; fStatus=''; halaman=1" class="btn btn-outline btn-sm mx-auto">Reset filter</button>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-t border-cream-200 text-sm">
      <p class="text-cocoa-400">Menampilkan <span class="font-semibold text-cocoa-700" x-text="halamanIni.length"></span> dari <span x-text="hasil.length"></span> pengguna <span x-show="hasil.length !== list.length">(total <span x-text="list.length"></span>)</span></p>
      <div class="flex items-center gap-1" x-show="totalHalaman > 1">
        <button @click="halaman--" :disabled="halaman === 1" class="icon-btn tap" :class="halaman === 1 && 'opacity-40 pointer-events-none'" aria-label="Halaman sebelumnya"><i data-lucide="chevron-left" class="w-4 h-4"></i></button>
        <template x-for="n in nomorHalaman" :key="n">
          <button @click="halaman = n" class="w-9 h-9 rounded-xl text-sm font-medium transition" :class="n===halaman ? 'bg-rose-500 text-white' : 'text-cocoa-500 hover:bg-cream-100'" x-text="n"></button>
        </template>
        <button @click="halaman++" :disabled="halaman === totalHalaman" class="icon-btn tap" :class="halaman === totalHalaman && 'opacity-40 pointer-events-none'" aria-label="Halaman berikutnya"><i data-lucide="chevron-right" class="w-4 h-4"></i></button>
      </div>
    </div>
  </div>
  <!-- TABEL PENGGUNA END -->

  <div class="card p-6 mt-6">
    <h2 class="font-display font-bold text-cocoa-700 mb-4">Hak akses per role</h2>
    <div class="grid sm:grid-cols-3 gap-4">
      <div class="rounded-2xl border border-cream-200 p-5">
        <span class="grid place-items-center w-10 h-10 rounded-xl bg-rose-400 text-white mb-3"><i data-lucide="user-round" class="w-5 h-5"></i></span>
        <p class="font-display font-semibold text-cocoa-700 mb-2">Customer</p>
        <ul class="space-y-1.5 text-xs text-cocoa-400">
          <li class="flex gap-2"><i data-lucide="check" class="w-3.5 h-3.5 text-green-600 shrink-0 mt-0.5"></i> Pesan &amp; lacak pesanan sendiri</li>
          <li class="flex gap-2"><i data-lucide="check" class="w-3.5 h-3.5 text-green-600 shrink-0 mt-0.5"></i> Menulis ulasan produk</li>
          <li class="flex gap-2"><i data-lucide="x" class="w-3.5 h-3.5 text-rose-500 shrink-0 mt-0.5"></i> Tidak bisa membuka panel admin</li>
        </ul>
      </div>
      <div class="rounded-2xl border border-cream-200 p-5">
        <span class="grid place-items-center w-10 h-10 rounded-xl bg-cocoa-500 text-white mb-3"><i data-lucide="shield-check" class="w-5 h-5"></i></span>
        <p class="font-display font-semibold text-cocoa-700 mb-2">Admin</p>
        <ul class="space-y-1.5 text-xs text-cocoa-400">
          <li class="flex gap-2"><i data-lucide="check" class="w-3.5 h-3.5 text-green-600 shrink-0 mt-0.5"></i> Kelola produk, pesanan, pengiriman</li>
          <li class="flex gap-2"><i data-lucide="check" class="w-3.5 h-3.5 text-green-600 shrink-0 mt-0.5"></i> Moderasi ulasan</li>
          <li class="flex gap-2"><i data-lucide="x" class="w-3.5 h-3.5 text-rose-500 shrink-0 mt-0.5"></i> Tidak bisa melihat modul keuangan</li>
        </ul>
      </div>
      <div class="rounded-2xl border border-cream-200 p-5">
        <span class="grid place-items-center w-10 h-10 rounded-xl bg-gold-500 text-white mb-3"><i data-lucide="crown" class="w-5 h-5"></i></span>
        <p class="font-display font-semibold text-cocoa-700 mb-2">Owner</p>
        <ul class="space-y-1.5 text-xs text-cocoa-400">
          <li class="flex gap-2"><i data-lucide="check" class="w-3.5 h-3.5 text-green-600 shrink-0 mt-0.5"></i> Semua hak akses admin</li>
          <li class="flex gap-2"><i data-lucide="check" class="w-3.5 h-3.5 text-green-600 shrink-0 mt-0.5"></i> Keuangan &amp; laporan laba rugi</li>
          <li class="flex gap-2"><i data-lucide="check" class="w-3.5 h-3.5 text-green-600 shrink-0 mt-0.5"></i> Mengatur role pengguna lain</li>
        </ul>
      </div>
    </div>
  </div>

  <!-- MODAL DETAIL PENGGUNA START -->
  <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[60] grid place-items-center p-4" role="dialog" aria-modal="true" @keydown.escape.window="modalDetail=false">
    <div @click="modalDetail=false" class="modal-overlay"></div>
    <div x-show="modalDetail" x-transition class="relative card w-full max-w-md p-6">
      <button @click="modalDetail=false" class="icon-btn tap absolute right-4 top-4" aria-label="Tutup"><i data-lucide="x" class="w-5 h-5"></i></button>
      <template x-if="memuatDetail">
        <p class="text-sm text-cocoa-300 text-center py-10">Memuat detail&hellip;</p>
      </template>
      <template x-if="!memuatDetail && detail">
        <div>
          <div class="flex items-center gap-3 mb-5">
            <span class="grid place-items-center w-12 h-12 rounded-full text-white text-sm font-semibold shrink-0"
                  :class="detail.role === 'Owner' ? 'bg-gold-500' : detail.role === 'Admin' ? 'bg-cocoa-500' : 'bg-rose-400'"
                  x-text="detail.nama.split(' ').map(w=>w[0]).slice(0,2).join('')"></span>
            <div>
              <p class="font-display font-bold text-cocoa-700" x-text="detail.nama"></p>
              <p class="text-xs text-cocoa-400" x-text="detail.email"></p>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3 mb-5 text-sm">
            <p class="flex justify-between gap-3 col-span-2"><span class="text-cocoa-400">Telepon</span><span class="font-medium text-cocoa-700" x-text="detail.telepon || '-'"></span></p>
            <p class="flex justify-between gap-3 col-span-2"><span class="text-cocoa-400">Role</span><span class="font-medium text-cocoa-700" x-text="detail.role"></span></p>
            <p class="flex justify-between gap-3 col-span-2"><span class="text-cocoa-400">Status</span><span class="font-medium text-cocoa-700" x-text="detail.status === 'aktif' ? 'Aktif' : 'Nonaktif'"></span></p>
            <p class="flex justify-between gap-3 col-span-2"><span class="text-cocoa-400">Terdaftar</span><span class="font-medium text-cocoa-700" x-text="tglID(detail.daftar)"></span></p>
            <p class="flex justify-between gap-3 col-span-2"><span class="text-cocoa-400">Masuk lewat</span><span class="font-medium text-cocoa-700" x-text="detail.lewatGoogle ? 'Google' : 'Email & sandi'"></span></p>
            <p class="flex justify-between gap-3 col-span-2"><span class="text-cocoa-400">Total pesanan</span><span class="font-medium text-cocoa-700" x-text="detail.totalPesanan"></span></p>
            <p class="flex justify-between gap-3 col-span-2"><span class="text-cocoa-400">Total belanja</span><span class="font-medium text-cocoa-700" x-text="rp(detail.totalBelanja)"></span></p>
          </div>
          <p class="text-xs font-semibold text-cocoa-300 uppercase tracking-wide mb-2">Pesanan terakhir</p>
          <div class="space-y-2 max-h-40 overflow-y-auto">
            <template x-for="o in detail.pesananTerakhir" :key="o.kode">
              <div class="flex items-center justify-between text-sm border-b border-cream-100 pb-2">
                <span class="text-cocoa-600" x-text="o.kode"></span>
                <span class="text-cocoa-400" x-text="tglID(o.tanggal)"></span>
                <span class="font-medium text-cocoa-700" x-text="rp(o.total)"></span>
              </div>
            </template>
            <p class="text-xs text-cocoa-300 text-center py-3" x-show="!detail.pesananTerakhir.length">Belum pernah memesan.</p>
          </div>
        </div>
      </template>
    </div>
  </div>
  <!-- MODAL DETAIL PENGGUNA END -->

  <!-- MODAL HASIL RESET SANDI START -->
  <div x-show="modalSandi" x-cloak class="fixed inset-0 z-[60] grid place-items-center p-4" role="dialog" aria-modal="true" @keydown.escape.window="modalSandi=false">
    <div @click="modalSandi=false" class="modal-overlay"></div>
    <div x-show="modalSandi" x-transition class="relative card w-full max-w-sm p-6 text-center">
      <span class="grid place-items-center w-12 h-12 mx-auto rounded-2xl bg-[#E9F6EC] text-green-700 mb-4"><i data-lucide="key-round" class="w-5 h-5"></i></span>
      <p class="font-display font-bold text-cocoa-700 mb-1">Sandi baru dibuat</p>
      <p class="text-sm text-cocoa-400 mb-4">Sampaikan sandi ini secara manual ke <span class="font-medium text-cocoa-600" x-text="targetSandi ? targetSandi.nama : ''"></span> (mis. lewat WhatsApp) — sistem ini belum tersambung email.</p>
      <div class="flex items-center gap-2 rounded-xl bg-cream-100 px-4 py-3 mb-4">
        <span class="font-mono text-lg font-semibold text-cocoa-700 flex-1 text-left" x-text="sandiBaru"></span>
        <button @click="navigator.clipboard.writeText(sandiBaru); toast('Sandi disalin.','success')" class="icon-btn tap" title="Salin"><i data-lucide="copy" class="w-4 h-4"></i></button>
      </div>
      <button @click="modalSandi=false" class="btn btn-primary w-full">Selesai</button>
    </div>
  </div>
  <!-- MODAL HASIL RESET SANDI END -->
</div>
@endsection

@push('scripts')
<script>
  function kelolaUser() {
    const dataAwal = @json($users);

    return {
      list: dataAwal, q:'', fRole:'', fStatus:'',
      halaman: 1, perPage: 10,
      modalDetail: false, memuatDetail: false, detail: null,
      modalSandi: false, sandiBaru: '', targetSandi: null,

      init() { this.$nextTick(() => icons()); this.$watch('halamanIni', () => this.$nextTick(() => icons())); },
      get hasil() {
        const q = this.q.trim().toLowerCase();
        return this.list.filter(u =>
          (!q || u.nama.toLowerCase().includes(q) || u.email.toLowerCase().includes(q)) &&
          (!this.fRole || u.role === this.fRole) &&
          (!this.fStatus || u.status === this.fStatus));
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
      ubahRole(u, role) {
        const lama = u.role;
        u.role = role; // optimistic

        fetch(`{{ url('admin/pengguna') }}/${u.id}/role`, {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
          body: JSON.stringify({ role })
        })
        .then(async res => {
          const data = await res.json().catch(() => null);
          if (!res.ok) throw new Error(data && data.message ? data.message : 'Gagal mengubah role.');
          toast(`${u.nama}: ${lama} → ${role}`, 'success', 'Role diperbarui');
          this.$nextTick(() => icons());
        })
        .catch(err => { u.role = lama; toast(err.message, 'error'); });
      },
      toggleStatus(u) {
        const jadi = u.status === 'aktif' ? 'nonaktif' : 'aktif';
        const lanjut = () => {
          const lama = u.status;
          u.status = jadi; // optimistic

          fetch(`{{ url('admin/pengguna') }}/${u.id}/status`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
          })
          .then(async res => {
            const data = await res.json().catch(() => null);
            if (!res.ok) throw new Error(data && data.message ? data.message : 'Gagal mengubah status.');
            toast(jadi === 'nonaktif' ? `Akun ${u.nama} dinonaktifkan.` : `Akun ${u.nama} diaktifkan kembali.`, jadi === 'nonaktif' ? 'warning' : 'success');
            this.$nextTick(() => icons());
          })
          .catch(err => { u.status = lama; toast(err.message, 'error'); });
        };

        if (jadi === 'nonaktif') {
          konfirmasi({
            judul: 'Nonaktifkan akun ini?',
            pesan: `<span class="font-semibold text-cocoa-600">${u.nama}</span> tidak akan bisa masuk sampai diaktifkan kembali.`,
            label: 'Ya, nonaktifkan', ikon: 'user-x',
            aksi: lanjut
          });
        } else {
          lanjut();
        }
      },
      lihatDetail(u) {
        this.modalDetail = true; this.memuatDetail = true; this.detail = null;
        this.$nextTick(() => icons());

        fetch(`{{ url('admin/pengguna') }}/${u.id}/detail`, { headers: { 'Accept': 'application/json' } })
          .then(async res => {
            const data = await res.json().catch(() => null);
            if (!res.ok) throw new Error(data && data.message ? data.message : 'Gagal memuat detail pengguna.');
            this.detail = data.data;
          })
          .catch(err => { this.modalDetail = false; toast(err.message, 'error'); })
          .finally(() => { this.memuatDetail = false; this.$nextTick(() => icons()); });
      },
      resetSandi(u) {
        konfirmasi({
          judul: 'Buat sandi baru untuk akun ini?',
          pesan: `Sandi lama <span class="font-semibold text-cocoa-600">${u.nama}</span> tidak akan berlaku lagi setelah ini.`,
          label: 'Ya, buat sandi baru', ikon: 'key-round',
          aksi: () => {
            fetch(`{{ url('admin/pengguna') }}/${u.id}/reset-password`, {
              method: 'PATCH',
              headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            })
            .then(async res => {
              const data = await res.json().catch(() => null);
              if (!res.ok) throw new Error(data && data.message ? data.message : 'Gagal membuat sandi baru.');
              this.targetSandi = u; this.sandiBaru = data.sandi_baru; this.modalSandi = true;
              this.$nextTick(() => icons());
            })
            .catch(err => toast(err.message, 'error'));
          }
        });
      }
    };
  }
</script>
@endpush

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

  <div class="card p-4 sm:p-5 mb-5">
    <div class="grid gap-3 md:grid-cols-4">
      <div class="relative md:col-span-2">
        <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-cocoa-300 pointer-events-none"></i>
        <input x-model="q" type="search" class="input !pl-11" placeholder="Cari nama atau email" aria-label="Cari pengguna">
      </div>
      <select x-model="fRole" class="select" aria-label="Filter role">
        <option value="">Semua role</option>
        <option>Customer</option><option>Admin</option><option>Owner</option>
      </select>
      <button @click="toast('Form undang pengguna baru menyusul di fase Laravel.','info')" class="btn btn-primary">
        <i data-lucide="user-plus" class="w-4 h-4"></i> Undang pengguna
      </button>
    </div>
  </div>

  <!-- TABEL PENGGUNA START -->
  <div class="card overflow-hidden">
    <div class="table-wrap">
      <table class="data">
        <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Terdaftar</th><th>Pesanan</th><th class="text-right">Aksi</th></tr></thead>
        <tbody>
          <template x-for="u in hasil" :key="u.id">
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
                  <button @click="toast('Detail aktivitas ' + u.nama + ' menyusul di fase Laravel.','info')" class="grid place-items-center w-9 h-9 rounded-xl text-cocoa-400 hover:bg-cream-100 hover:text-cocoa-700 transition" title="Lihat detail">
                    <i data-lucide="eye" class="w-4 h-4"></i></button>
                  <button @click="toast('Tautan atur ulang sandi dikirim ke ' + u.email + ' (simulasi).','success')" class="grid place-items-center w-9 h-9 rounded-xl text-cocoa-400 hover:bg-cream-100 hover:text-cocoa-700 transition" title="Reset sandi">
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
      <p class="text-sm text-cocoa-400 mb-5">Coba kata kunci lain atau ubah filter role.</p>
      <button @click="q=''; fRole=''" class="btn btn-outline btn-sm mx-auto">Reset filter</button>
    </div>

    <div class="px-5 py-4 border-t border-cream-200 text-sm text-cocoa-400">
      Menampilkan <span class="font-semibold text-cocoa-700" x-text="hasil.length"></span> dari <span x-text="list.length"></span> pengguna
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
</div>
@endsection

@push('scripts')
<script>
  function kelolaUser() {
    return {
      list: JSON.parse(JSON.stringify(USERS)), q:'', fRole:'',
      init() { this.$nextTick(() => icons()); this.$watch('hasil', () => this.$nextTick(() => icons())); },
      get hasil() {
        const q = this.q.trim().toLowerCase();
        return this.list.filter(u =>
          (!q || u.nama.toLowerCase().includes(q) || u.email.toLowerCase().includes(q)) &&
          (!this.fRole || u.role === this.fRole));
      },
      ubahRole(u, role) {
        const lama = u.role; u.role = role;
        toast(`${u.nama}: ${lama} → ${role}`, 'success', 'Role diperbarui');
        this.$nextTick(() => icons());
      },
      toggleStatus(u) {
        const jadi = u.status === 'aktif' ? 'nonaktif' : 'aktif';
        if (jadi === 'nonaktif') {
          konfirmasi({
            judul: 'Nonaktifkan akun ini?',
            pesan: `<span class="font-semibold text-cocoa-600">${u.nama}</span> tidak akan bisa masuk sampai diaktifkan kembali.`,
            label: 'Ya, nonaktifkan', ikon: 'user-x',
            aksi: () => { u.status = 'nonaktif'; toast(`Akun ${u.nama} dinonaktifkan.`, 'warning'); this.$nextTick(() => icons()); }
          });
        } else {
          u.status = 'aktif';
          toast(`Akun ${u.nama} diaktifkan kembali.`, 'success');
          this.$nextTick(() => icons());
        }
      }
    };
  }
</script>
@endpush
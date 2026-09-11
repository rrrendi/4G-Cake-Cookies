<footer class="mt-20 bg-cocoa-700 text-cream-200">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
      <div class="lg:col-span-1">
        <div class="flex items-center gap-3 mb-4">
          <span class="grid place-items-center w-10 h-10 rounded-2xl bg-rose-500 text-white font-display font-bold text-sm">4G</span>
          <span class="font-display font-bold text-white text-[15px]">4G Cake &amp; Cookies</span>
        </div>
        <p class="text-sm leading-relaxed text-cream-200/70">
          Usaha rumahan yang membuat cake, cookies, dan dessert dalam batch kecil supaya rasanya terjaga.
          Semua pesanan dibuat setelah dipesan, bukan stok lama.
        </p>
        <div class="flex gap-2 mt-5">
          <a href="#" onclick="toast('Halaman Instagram akan dibuka di fase berikutnya.','info');return false;" class="grid place-items-center w-10 h-10 rounded-full bg-white/10 hover:bg-rose-500 transition tap" aria-label="Instagram"><i data-lucide="instagram" class="w-4 h-4"></i></a>
          <a href="#" onclick="toast('Chat WhatsApp masih simulasi di prototype ini.','info');return false;" class="grid place-items-center w-10 h-10 rounded-full bg-white/10 hover:bg-rose-500 transition tap" aria-label="WhatsApp"><i data-lucide="message-circle" class="w-4 h-4"></i></a>
          <a href="#" onclick="toast('Halaman Facebook akan dibuka di fase berikutnya.','info');return false;" class="grid place-items-center w-10 h-10 rounded-full bg-white/10 hover:bg-rose-500 transition tap" aria-label="Facebook"><i data-lucide="facebook" class="w-4 h-4"></i></a>
        </div>
      </div>

      <div>
        <h3 class="font-display font-semibold text-white text-sm mb-4">Jelajahi</h3>
        <ul class="space-y-2.5 text-sm text-cream-200/70">
          <li><a href="{{ route('catalog') }}" class="hover:text-rose-300 transition">Katalog Produk</a></li>
          <li><a href="{{ route('catalog') }}#best" class="hover:text-rose-300 transition">Best Seller</a></li>
          <li><a href="{{ route('cart.index') }}" class="hover:text-rose-300 transition">Keranjang</a></li>
          <li><a href="{{ route('order.index') }}" class="hover:text-rose-300 transition">Lacak Pesanan</a></li>
          <!-- Diubah mengarah ke Pesanan Saya karena ulasan harus berbasis transaksi -->
          <li><a href="{{ route('order.history') }}" class="hover:text-rose-300 transition">Beri Ulasan</a></li>
        </ul>
      </div>

      <div>
        <h3 class="font-display font-semibold text-white text-sm mb-4">Jam Operasional</h3>
        <ul class="space-y-2.5 text-sm text-cream-200/70">
          <li class="flex justify-between gap-4"><span>Senin &ndash; Jumat</span><span class="text-cream-200">08.00 &ndash; 20.00</span></li>
          <li class="flex justify-between gap-4"><span>Sabtu</span><span class="text-cream-200">08.00 &ndash; 21.00</span></li>
          <li class="flex justify-between gap-4"><span>Minggu</span><span class="text-cream-200">10.00 &ndash; 17.00</span></li>
          <li class="pt-2 text-xs text-cream-200/50">Pesanan masuk setelah pukul 18.00 diproses keesokan harinya.</li>
        </ul>
      </div>

      <div>
        <h3 class="font-display font-semibold text-white text-sm mb-4">Hubungi Kami</h3>
        <ul class="space-y-3 text-sm text-cream-200/70">
          <li class="flex gap-2.5"><i data-lucide="map-pin" class="w-4 h-4 shrink-0 mt-0.5 text-rose-300"></i><span>Jl. Samudera No. 12, Banda Sakti,<br>Lhokseumawe, Aceh</span></li>
          <li class="flex gap-2.5"><i data-lucide="phone" class="w-4 h-4 shrink-0 mt-0.5 text-rose-300"></i><span>0812-6070-4004</span></li>
          <li class="flex gap-2.5"><i data-lucide="mail" class="w-4 h-4 shrink-0 mt-0.5 text-rose-300"></i><span>halo@4gcake.id</span></li>
        </ul>
      </div>
    </div>

    <div class="mt-12 pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-cream-200/50">
      <p>&copy; {{ date('Y') }} 4G Cake &amp; Cookies. TA Project.</p>
      <p class="flex items-center gap-2">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-rose-300 transition">Panel Admin</a>
        <span class="divider-dot"></span>
        <span>Dibuat dengan Tailwind + Alpine</span>
      </p>
    </div>
  </div>
</footer>
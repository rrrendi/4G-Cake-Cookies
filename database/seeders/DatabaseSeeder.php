<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin dan Owner

        //OWNER ieu mah
        User::create(['name' => 'Owner 4G', 
            'email' => 'owner@4gcake.com', 
            'phone' => '081234567890', 
            'password' => Hash::make('password123'), 
            'role' => 'owner']);

        //ADMIN ieu mah
        User::create(['name' => 'Admin Operasional', 
            'email' => 'admin@4gcake.com', 
            'phone' => '081234567891', 
            'password' => Hash::make('password123'), 
            'role' => 'admin']);

        // 2. Kategori Master
        $kategori = [
            'Cake' => Category::create(['name' => 'Cake', 'slug' => 'cake']),
            'Cookies' => Category::create(['name' => 'Cookies', 'slug' => 'cookies']),
            'Brownies' => Category::create(['name' => 'Brownies', 'slug' => 'brownies']),
            'Dessert' => Category::create(['name' => 'Dessert', 'slug' => 'dessert']),
            'Hampers' => Category::create(['name' => 'Hampers', 'slug' => 'hampers']),
        ];

        // 3. Seluruh 12 Produk dari data.js
        $produkData = [
            [
                'category_id' => $kategori['Cake']->id,
                'slug' => 'red-velvet-cake',
                'name' => 'Red Velvet Cake',
                'price' => 285000,
                'compare_at_price' => 320000,
                'is_best_seller' => true,
                'stock_status' => 'tersedia',
                'min_preorder_days' => 2,
                'weight_label' => '1 kg (diameter 18 cm)',
                'description' => 'Red velvet lembut dengan cream cheese frosting yang tidak terlalu manis. Dibuat fresh setiap hari, cocok untuk ulang tahun maupun hantaran.',
                'ingredients' => 'Tepung terigu protein sedang, gula, telur, mentega, buttermilk, bubuk kakao, pewarna makanan food grade, cream cheese, whipping cream.',
                'storage_note' => 'Kulkas 3–4 hari. Keluarkan 15 menit sebelum disajikan.',
                'variant_options' => ['1 kg (18 cm)', '1,5 kg (20 cm)', '2 kg (22 cm)']
            ],
            [
                'category_id' => $kategori['Brownies']->id,
                'slug' => 'brownies-kukus',
                'name' => 'Brownies Kukus Premium',
                'price' => 65000,
                'compare_at_price' => 75000,
                'is_best_seller' => true,
                'stock_status' => 'tersedia',
                'min_preorder_days' => 1,
                'weight_label' => '500 gram (20x10 cm)',
                'description' => 'Brownies kukus dengan cokelat Belgia, teksturnya lumer dan lembap. Topping keju parut atau almond slice sesuai permintaan.',
                'ingredients' => 'Dark chocolate compound, tepung terigu, gula, telur, minyak sayur, susu bubuk, keju cheddar atau almond slice (sesuai varian).',
                'storage_note' => 'Suhu ruang 2 hari, kulkas sampai 5 hari.',
                'variant_options' => ['Original', 'Topping Keju', 'Topping Almond']
            ],
            [
                'category_id' => $kategori['Cookies']->id,
                'slug' => 'choco-chip-cookies',
                'name' => 'Choco Chip Cookies',
                'price' => 48000,
                'compare_at_price' => null,
                'is_best_seller' => true,
                'stock_status' => 'tersedia',
                'min_preorder_days' => 1,
                'weight_label' => '250 gram (toples)',
                'description' => 'Cookies renyah di luar, chewy di dalam, penuh choco chip. Dipanggang harian dalam batch kecil supaya tetap segar.',
                'ingredients' => 'Tepung terigu, mentega, gula palem, telur, choco chip, vanila, garam laut.',
                'storage_note' => 'Toples tertutup rapat di suhu ruang, tahan sampai 3 minggu.',
                'variant_options' => ['Toples 250 gr', 'Toples 500 gr']
            ],
            [
                'category_id' => $kategori['Cookies']->id,
                'slug' => 'nastar-kastengel',
                'name' => 'Nastar & Kastengel Spesial',
                'price' => 95000,
                'compare_at_price' => 110000,
                'is_best_seller' => true,
                'stock_status' => 'tersedia',
                'min_preorder_days' => 3,
                'weight_label' => '2 toples @300 gram',
                'description' => 'Paket nastar selai nanas homemade dan kastengel keju Edam. Favorit saat Lebaran, wajib pesan H-3 karena antrean panjang.',
                'ingredients' => 'Tepung terigu, mentega wijsman, kuning telur, selai nanas homemade, keju Edam, keju cheddar.',
                'storage_note' => 'Toples tertutup rapat di suhu ruang, tahan sampai 1 bulan.',
                'variant_options' => ['Paket 2 toples', 'Paket 4 toples']
            ],
            [
                'category_id' => $kategori['Cake']->id,
                'slug' => 'birthday-cake-custom',
                'name' => 'Birthday Cake Custom',
                'price' => 350000,
                'compare_at_price' => null,
                'is_best_seller' => false,
                'stock_status' => 'tersedia',
                'min_preorder_days' => 3,
                'weight_label' => '1 kg, desain sesuai permintaan',
                'description' => 'Kue ulang tahun dengan desain sesuai tema yang diminta. Sertakan referensi gambar saat checkout, tim kami akan konfirmasi lewat WhatsApp.',
                'ingredients' => 'Sponge cake vanila atau cokelat, buttercream, fondant food grade, hiasan sesuai tema.',
                'storage_note' => 'Kulkas maksimal 2 hari, sebaiknya disantap di hari acara.',
                'variant_options' => ['1 kg', '2 kg', '2 tingkat']
            ],
            [
                'category_id' => $kategori['Cake']->id,
                'slug' => 'cheese-cake-jepang',
                'name' => 'Cheese Cake Jepang',
                'price' => 175000,
                'compare_at_price' => null,
                'is_best_seller' => true,
                'stock_status' => 'tersedia',
                'min_preorder_days' => 2,
                'weight_label' => '700 gram (diameter 16 cm)',
                'description' => 'Japanese cotton cheesecake yang ringan dan lembut seperti kapas. Rasa keju lembut, tidak eneg meski dimakan sepotong besar.',
                'ingredients' => 'Cream cheese, telur, gula halus, tepung maizena, susu cair, mentega tawar, air lemon.',
                'storage_note' => 'Kulkas 3 hari. Paling enak disajikan dingin.',
                'variant_options' => ['Diameter 16 cm', 'Diameter 20 cm']
            ],
            [
                'category_id' => $kategori['Dessert']->id,
                'slug' => 'dessert-box-tiramisu',
                'name' => 'Dessert Box Tiramisu',
                'price' => 55000,
                'compare_at_price' => 65000,
                'is_best_seller' => false,
                'stock_status' => 'tersedia',
                'min_preorder_days' => 1,
                'weight_label' => '350 ml per box',
                'description' => 'Lapisan ladyfinger kopi, mascarpone cream, dan bubuk kakao. Disajikan dalam box tebal, aman untuk dikirim.',
                'ingredients' => 'Ladyfinger, mascarpone, whipping cream, kopi espresso, bubuk kakao, gula halus.',
                'storage_note' => 'Wajib di kulkas, tahan 2 hari.',
                'variant_options' => ['Box 350 ml', 'Box 1 liter']
            ],
            [
                'category_id' => $kategori['Hampers']->id,
                'slug' => 'hampers-lebaran',
                'name' => 'Hampers Lebaran Cookies',
                'price' => 275000,
                'compare_at_price' => 310000,
                'is_best_seller' => false,
                'stock_status' => 'tersedia',
                'min_preorder_days' => 5,
                'weight_label' => '4 toples + kartu ucapan',
                'description' => 'Hampers berisi nastar, kastengel, putri salju, dan choco chip cookies dalam keranjang rotan. Termasuk kartu ucapan personal.',
                'ingredients' => '4 toples kue kering pilihan, keranjang rotan, kain pelapis, kartu ucapan.',
                'storage_note' => 'Suhu ruang, hindari terkena sinar matahari langsung.',
                'variant_options' => ['Keranjang Rotan', 'Box Eksklusif']
            ],
            [
                'category_id' => $kategori['Cake']->id,
                'slug' => 'bolu-pandan-keju',
                'name' => 'Bolu Pandan Keju',
                'price' => 85000,
                'compare_at_price' => null,
                'is_best_seller' => false,
                'stock_status' => 'tersedia',
                'min_preorder_days' => 1,
                'weight_label' => '600 gram (loyang tulban)',
                'description' => 'Bolu pandan dari daun pandan asli, lembut dan wangi, dengan taburan keju cheddar di atasnya. Cocok untuk teman minum teh.',
                'ingredients' => 'Tepung terigu, telur, gula, santan, sari daun pandan asli, keju cheddar, mentega.',
                'storage_note' => 'Suhu ruang 2 hari, kulkas sampai 4 hari.',
                'variant_options' => ['Loyang tulban', 'Loyang persegi']
            ],
            [
                'category_id' => $kategori['Cookies']->id,
                'slug' => 'putri-salju',
                'name' => 'Putri Salju Almond',
                'price' => 52000,
                'compare_at_price' => null,
                'is_best_seller' => false,
                'stock_status' => 'habis',
                'min_preorder_days' => 2,
                'weight_label' => '300 gram (toples)',
                'description' => 'Kue kering almond yang lumer di mulut, dibalut gula halus tebal. Stok menyesuaikan ketersediaan almond impor.',
                'ingredients' => 'Tepung terigu, mentega, kacang almond bubuk, gula halus, susu bubuk, vanila.',
                'storage_note' => 'Toples tertutup rapat di suhu ruang, tahan sampai 1 bulan.',
                'variant_options' => ['Toples 300 gr']
            ],
            [
                'category_id' => $kategori['Dessert']->id,
                'slug' => 'choco-lava-cake',
                'name' => 'Choco Lava Cake',
                'price' => 42000,
                'compare_at_price' => null,
                'is_best_seller' => false,
                'stock_status' => 'tersedia',
                'min_preorder_days' => 1,
                'weight_label' => '2 pcs per paket',
                'description' => 'Cokelat lumer di tengah, cukup dihangatkan 30 detik sebelum disajikan. Dikirim dalam kondisi beku dengan ice gel.',
                'ingredients' => 'Dark chocolate, mentega tawar, telur, gula halus, tepung terigu, sedikit garam.',
                'storage_note' => 'Freezer sampai 2 minggu. Hangatkan 30 detik sebelum disantap.',
                'variant_options' => ['Isi 2 pcs', 'Isi 4 pcs']
            ],
            [
                'category_id' => $kategori['Dessert']->id,
                'slug' => 'dessert-box-oreo',
                'name' => 'Dessert Box Oreo Cheese',
                'price' => 58000,
                'compare_at_price' => null,
                'is_best_seller' => false,
                'stock_status' => 'habis',
                'min_preorder_days' => 1,
                'weight_label' => '350 ml per box',
                'description' => 'Remahan oreo, cream cheese lembut, dan lapisan susu. Manisnya pas, jadi favorit anak-anak.',
                'ingredients' => 'Biskuit oreo, cream cheese, whipping cream, susu kental manis, susu cair.',
                'storage_note' => 'Wajib di kulkas, tahan 2 hari.',
                'variant_options' => ['Box 350 ml']
            ]
        ];

        foreach ($produkData as $produk) {
            Product::create($produk);
        }
    }
}
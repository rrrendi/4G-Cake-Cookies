<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    // Menampilkan daftar produk dari Database
    public function index()
    {
        // Mengambil semua produk, diurutkan dari yang terbaru
        $products = Product::orderBy('created_at', 'desc')->get();
        
        return view('admin.produk', compact('products'));
    }

    // Menyimpan produk baru ke Database
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->nama) . '-' . rand(100, 999); // Slug unik
        $data['is_available'] = true;

        // Logika Upload Gambar sesungguhnya
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        Product::create($data);

        return back()->with('success_toast', 'Produk berhasil ditambahkan ke database!');
    }

    // Memperbarui produk
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->all();

        // Update gambar jika ada gambar baru yang diunggah
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($product->gambar && Storage::disk('public')->exists($product->gambar)) {
                Storage::disk('public')->delete($product->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        $product->update($data);

        return back()->with('success_toast', 'Data produk berhasil diperbarui!');
    }

    // Menghapus produk
    public function destroy(Product $product)
    {
        if ($product->gambar && Storage::disk('public')->exists($product->gambar)) {
            Storage::disk('public')->delete($product->gambar);
        }
        
        $product->delete();

        return back()->with('success_toast', 'Produk dihapus permanen dari database.');
    }

    // Toggle Ketersediaan Stok
    public function toggleStock(Product $product)
    {
        $product->update([
            'is_available' => !$product->is_available
        ]);

        $status = $product->is_available ? 'Tersedia' : 'Habis';
        return back()->with('info_toast', "Status stok diubah menjadi $status.");
    }
}
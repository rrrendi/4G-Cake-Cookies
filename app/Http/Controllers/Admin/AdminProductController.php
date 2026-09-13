<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        return view('admin.produk', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'category_name' => 'required|string|max:50',
            'description' => 'required|string',
            'min_preorder_days' => 'required|integer|min:0|max:14',
            'stock_status' => 'required|in:tersedia,habis',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $category = Category::firstOrCreate(['name' => $request->category_name], ['slug' => Str::slug($request->category_name)]);

        // Olah Varian
        $variants = [];
        if ($request->has('variants')) {
            foreach ($request->variants as $v) {
                if (!empty($v['name']) && isset($v['price'])) {
                    $variants[] = ['name' => $v['name'], 'price' => (int)$v['price']];
                }
            }
        }
        if(empty($variants)) $variants = [['name' => 'Original', 'price' => 0]];
        $basePrice = min(array_column($variants, 'price'));

        $data = [
            'category_id' => $category->id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . rand(100, 999),
            'description' => $request->description,
            'ingredients' => $request->ingredients,
            'storage_note' => $request->storage_note,
            'weight_label' => $request->weight_label,
            'delivery_info' => $request->delivery_info,
            'condition_info' => $request->condition_info,
            'variant_options' => $variants,
            'price' => $basePrice,
            'compare_at_price' => $request->compare_at_price,
            'is_preorder' => $request->min_preorder_days > 0,
            'min_preorder_days' => $request->min_preorder_days,
            'stock_status' => $request->stock_status,
            'is_best_seller' => $request->has('is_best_seller'),
        ];

        // Simpan Banyak Foto
        if ($request->hasFile('photos')) {
            $photosPaths = [];
            foreach ($request->file('photos') as $index => $file) {
                $path = $file->store('products', 'public');
                $photosPaths[] = $path;
                if ($index === 0) $data['photo_main'] = $path;
            }
            $data['photos'] = $photosPaths;
        }

        Product::create($data);
        return back()->with('success_toast', 'Produk berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:150',
            'category_name' => 'required|string|max:50',
            'description' => 'required|string',
            'min_preorder_days' => 'required|integer|min:0|max:14',
            'stock_status' => 'required|in:tersedia,habis',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $category = Category::firstOrCreate(['name' => $request->category_name], ['slug' => Str::slug($request->category_name)]);

        $variants = [];
        if ($request->has('variants')) {
            foreach ($request->variants as $v) {
                if (!empty($v['name']) && isset($v['price'])) {
                    $variants[] = ['name' => $v['name'], 'price' => (int)$v['price']];
                }
            }
        }
        if(empty($variants)) $variants = [['name' => 'Original', 'price' => 0]];
        $basePrice = min(array_column($variants, 'price'));

        $data = [
            'category_id' => $category->id,
            'name' => $request->name,
            'description' => $request->description,
            'ingredients' => $request->ingredients,
            'storage_note' => $request->storage_note,
            'weight_label' => $request->weight_label,
            'delivery_info' => $request->delivery_info,
            'condition_info' => $request->condition_info,
            'variant_options' => $variants,
            'price' => $basePrice,
            'compare_at_price' => $request->compare_at_price,
            'is_preorder' => $request->min_preorder_days > 0,
            'min_preorder_days' => $request->min_preorder_days,
            'stock_status' => $request->stock_status,
            'is_best_seller' => $request->has('is_best_seller'),
        ];

        if ($request->hasFile('photos')) {
            if (is_array($product->photos)) {
                foreach ($product->photos as $oldPhoto) { Storage::disk('public')->delete($oldPhoto); }
            }
            $photosPaths = [];
            foreach ($request->file('photos') as $index => $file) {
                $path = $file->store('products', 'public');
                $photosPaths[] = $path;
                if ($index === 0) $data['photo_main'] = $path;
            }
            $data['photos'] = $photosPaths;
        }

        $product->update($data);
        return back()->with('success_toast', 'Data produk berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return back()->with('warning_toast', 'Produk dihapus dari katalog.');
    }

    public function toggleStock($id)
    {
        $product = Product::findOrFail($id);
        $newStatus = $product->stock_status === 'tersedia' ? 'habis' : 'tersedia';
        $product->update(['stock_status' => $newStatus]);
        return back()->with('info_toast', "Status stok diubah menjadi " . ucfirst($newStatus) . ".");
    }
}
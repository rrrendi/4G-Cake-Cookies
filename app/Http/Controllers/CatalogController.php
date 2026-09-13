<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        // Tarik semua produk tanpa difilter kategori, karena filter dilakukan instan oleh JavaScript di View
        $query = Product::with('category');

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $products = $query->latest()->get();
        $categories = Category::all();

        return view('catalog', compact('products', 'categories'));
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index()
    {
        return view('admin.review');
    }

    // Method update disiapkan untuk fase backend (menyembunyikan review)
    public function update(Request $request, $id)
    {
        // Logika update akan diisi nanti
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // Melempar tanggal hari ini ke Blade untuk kop surat laporan
        $tanggalCetak = Carbon::now()->locale('id')->isoFormat('D MMMM Y');
        
        return view('admin.laporan', compact('tanggalCetak'));
    }
}
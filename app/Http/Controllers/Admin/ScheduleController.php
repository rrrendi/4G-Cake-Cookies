<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        // Berikan tanggal hari ini (sebagai nilai default untuk kalender Alpine)
        $hariIni = Carbon::now();
        $tahun = $hariIni->year;
        $bulan = $hariIni->month - 1; // Alpine array index dimulai dari 0
        $tgl_iso = $hariIni->format('Y-m-d');
        
        return view('admin.jadwal', compact('tahun', 'bulan', 'tgl_iso'));
    }
}
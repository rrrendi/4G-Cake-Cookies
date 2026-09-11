<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. Cek apakah peran sesuai dengan yang diminta rute
        if (!in_array($user->role, $roles)) {
            
            // Menggunakan back() agar pengguna tidak berpindah ke mana-mana
            // Melainkan langsung dipantulkan kembali dengan membawa notifikasi
            
            if (in_array($user->role, ['admin', 'owner'])) {
                return back()->with('error_toast', 'Akses ditolak. Halaman ini khusus Owner.');
            }
            
            return back()->with('error_toast', 'Akses ditolak. Anda tidak memiliki izin.');
        }

        return $next($request);
    }
}
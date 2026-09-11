<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Akun berhasil dibuat. Selamat datang, ' . $user->name
        ]);
    }

    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Cek peran (role) untuk menentukan arah pintu masuk
            // Asumsi: admin dan owner masuk ke panel admin, user biasa ke order history
            if (in_array($user->role, ['admin', 'owner'])) {
                $redirectUrl = route('admin.dashboard');
            } else {
                $redirectUrl = route('order.history');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Selamat datang kembali, ' . $user->name,
                'redirect' => $redirectUrl // Kirim rute tujuan ke JavaScript
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Email atau kata sandi tidak ditemukan.'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('info_toast', 'Anda telah keluar dari sistem.');
    }
}
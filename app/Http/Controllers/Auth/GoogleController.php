<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
        $driver = Socialite::driver('google');

        return $driver->with(['prompt' => 'select_account'])->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cek apakah email sudah terdaftar
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Jika belum pernah mendaftar, buat akun baru
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(24)) // Beri password acak yang kuat
                ]);
            } elseif (!$user->google_id) {
                // Jika email sudah ada tapi belum menautkan Google, tautkan sekarang
                $user->update(['google_id' => $googleUser->getId()]);
            }

            Auth::login($user);

            // Arahkan ke dashboard atau beranda setelah berhasil login
            return redirect()->route('home');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Gagal masuk menggunakan Google. Pastikan koneksi aman.']);
        }
    }
}
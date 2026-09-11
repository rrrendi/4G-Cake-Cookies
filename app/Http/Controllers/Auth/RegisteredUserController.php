<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'no_hp' => ['required', 'string', 'max:20'], // Tambahan untuk 4G Cake
            'alamat' => ['required', 'string'], // Tambahan untuk 4G Cake
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer', // Default role untuk pendaftar baru
            'status' => 'aktif',
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Arahkan ke beranda setelah berhasil daftar
        return redirect()->route('home')->with('success_toast', 'Pendaftaran berhasil. Selamat datang di 4G Cake & Cookies!');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('orders')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'nama' => $u->name,
                    'email' => $u->email,
                    'role' => ucfirst($u->role),
                    'status' => $u->status,
                    'daftar' => $u->created_at->format('Y-m-d'),
                    'pesanan' => $u->orders_count,
                ];
            })
            ->values();

        return view('admin.pengguna', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:Customer,Admin,Owner',
        ]);

        $roleBaru = strtolower($request->role);

        if ($user->id === auth()->id() && $roleBaru !== $user->role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak bisa mengubah role akun sendiri.',
            ], 422);
        }

        $user->update(['role' => $roleBaru]);

        return response()->json([
            'status' => 'success',
            'message' => "Role {$user->name} diperbarui menjadi {$request->role}.",
        ]);
    }

    public function updateStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak bisa menonaktifkan akun sendiri.',
            ], 422);
        }

        $statusBaru = $user->status === 'aktif' ? 'nonaktif' : 'aktif';
        $user->update(['status' => $statusBaru]);

        return response()->json([
            'status' => 'success',
            'message' => "Akun {$user->name} sekarang {$statusBaru}.",
            'status_baru' => $statusBaru,
        ]);
    }

    public function detail(User $user)
    {
        $pesananTerakhir = $user->orders()
            ->latest()
            ->take(5)
            ->get(['order_number', 'created_at', 'status', 'total'])
            ->map(fn ($o) => [
                'kode' => $o->order_number,
                'tanggal' => $o->created_at->format('Y-m-d'),
                'status' => $o->status,
                'total' => (float) $o->total,
            ])
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => [
                'nama' => $user->name,
                'email' => $user->email,
                'telepon' => $user->phone,
                'role' => ucfirst($user->role),
                'status' => $user->status,
                'daftar' => $user->created_at->format('Y-m-d'),
                'lewatGoogle' => (bool) $user->google_id,
                'totalPesanan' => $user->orders()->count(),
                'totalBelanja' => (float) $user->orders()->where('status', '!=', 'dibatalkan')->sum('total'),
                'pesananTerakhir' => $pesananTerakhir,
            ],
        ]);
    }

    /**
     * Tidak ada sistem email/SMTP yang terpasang di project ini, jadi sandi baru
     * ditampilkan langsung ke admin untuk disampaikan manual ke pengguna (lewat
     * WhatsApp/telepon) — bukan dikirim otomatis lewat email.
     */
    public function resetPassword(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gunakan halaman profil Anda sendiri untuk mengganti sandi akun sendiri.',
            ], 422);
        }

        $sandiBaru = \Illuminate\Support\Str::password(10, true, true, false, false);
        $user->update(['password' => $sandiBaru]);

        return response()->json([
            'status' => 'success',
            'message' => "Sandi baru untuk {$user->name} berhasil dibuat.",
            'sandi_baru' => $sandiBaru,
        ]);
    }
}

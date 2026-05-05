<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Autentikasi\CekLogin;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    // tampilin halaman login biar user bisa absen
    public function create(): View
    {
        $roles = Role::orderBy('id')->get();
        return view('auth.login', compact('roles'));
    }

    // proses login, cek idsama password, plus pastiin role nya bener
    public function store(CekLogin $request): RedirectResponse
    {
        $request->authenticate();

        // cek role terpilih
        $user = Auth::user();
        $selectedRoleId = (int) $request->role_id;

        if (!$user->hasRole($selectedRoleId)) {
            // tolak login kalo role ga cocok
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $roleName = Role::find($selectedRoleId)?->name ?? 'Unknown';

            throw ValidationException::withMessages([
                'role_id' => "Akses Ditolak! Akun Anda tidak terdaftar sebagai {$roleName}. Silakan pilih role yang sesuai.",
            ]);
        }

        // save role di session
        $request->session()->regenerate();
        session(['active_role_id' => $selectedRoleId]);

        // Arahkan ke dashboard sesuai role
        $dashboardRoute = User::getDashboardRoute($selectedRoleId);

        \Illuminate\Support\Facades\Log::info('User login success', [
            'user_id' => $user->id,
            'selected_role' => $selectedRoleId,
            'dashboard_route' => $dashboardRoute,
            'session_id' => session()->getId()
        ]);

        if ($dashboardRoute && Route::has($dashboardRoute)) {
            return redirect()->route($dashboardRoute);
        }

        return redirect()->route('dashboard');
    }

    // buat user yang mau pamit (logout)
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

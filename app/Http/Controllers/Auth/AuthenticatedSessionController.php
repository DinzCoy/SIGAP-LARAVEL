<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // After credentials are verified, check if user has the selected role
        $user = Auth::user();
        $selectedRoleId = (int) $request->role_id;

        if (!$user->hasRole($selectedRoleId)) {
            // User doesn't have this role — logout and show error
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $roleName = Role::find($selectedRoleId)?->name ?? 'Unknown';

            throw ValidationException::withMessages([
                'role_id' => "Akses Ditolak! Akun Anda tidak terdaftar sebagai {$roleName}. Silakan pilih role yang sesuai.",
            ]);
        }

        // Role is valid — store active role in session
        $request->session()->regenerate();
        session(['active_role_id' => $selectedRoleId]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

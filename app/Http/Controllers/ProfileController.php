<?php

namespace App\Http\Controllers;

use App\Http\Requests\CekUpdateProfil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    // buat ngedit profil biar makin ganteng/cantik
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    // simpan perubahan profil biar ga ilang
    public function update(CekUpdateProfil $request): RedirectResponse
    {
        $user = $request->user();
        
        $user->fill($request->safe()->only(['name', 'email']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('foto_profil') && $request->file('foto_profil')->isValid()) {
            // Hapus foto lama jika ada
            if ($user->photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->photo_path);
            }
            // Simpan foto baru
            $user->photo_path = $request->file('foto_profil')->store('profile-photos', 'public');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    // hapus akun kalau udah ga mau jadi bagian dari kita lagi :(
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

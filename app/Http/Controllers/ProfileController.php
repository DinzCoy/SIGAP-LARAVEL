<?php

namespace App\Http\Controllers;

use App\Http\Requests\CekUpdateProfil;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(CekUpdateProfil $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->safe()->only(['name', 'email']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('foto_profil') && $request->file('foto_profil')->isValid()) {

            if ($user->photo_path) {
                Storage::disk('public')->delete($user->photo_path);
            }

            $user->photo_path = ImageService::storeCompressed(
                $request->file('foto_profil'),
                'profile-photos',
                maxWidth: 400,
                quality: 85
            );
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

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

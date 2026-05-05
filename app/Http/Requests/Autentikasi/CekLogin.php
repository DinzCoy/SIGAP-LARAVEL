<?php

namespace App\Http\Requests\Autentikasi;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CekLogin extends FormRequest
{
    //Menentukan apakah user diizinkan melakukan request ini.
    public function authorize(): bool
    {
        return true;
    }

    //Aturan validasi untuk request login.
    public function rules(): array
    {
        return [
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
            'role_id'  => ['required', 'integer', 'exists:roles,id'],
        ];
    }

    //Mencoba melakukan autentikasi — support email atau username.
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginField = trim($this->input('login'));

        // Coba login pakai email dulu, kalau gagal coba pakai username
        $credentials = filter_var($loginField, FILTER_VALIDATE_EMAIL)
            ? ['email'    => $loginField, 'password' => $this->input('password')]
            : ['username' => $loginField, 'password' => $this->input('password')];

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    //Memastikan request login tidak melebihi batas percobaan (rate limit).
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    //Mendapatkan kunci pembatas (throttle key) untuk rate limiting.
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('login')).'|'.$this->ip());
    }
}

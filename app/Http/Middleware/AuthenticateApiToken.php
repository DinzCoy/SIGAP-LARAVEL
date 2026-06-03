<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticateApiToken
{
    /**
     * Autentikasi request API menggunakan header X-Auth-Token.
     *
     * Menggunakan header custom karena server cPanel/Apache memblokir
     * header 'Authorization: Bearer ...' sebelum sampai ke PHP.
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-Auth-Token');

        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Perbarui waktu penggunaan token terakhir
        $accessToken->forceFill(['last_used_at' => now()])->save();

        // Set user yang terautentikasi ke dalam request
        $request->setUserResolver(function () use ($accessToken) {
            return $accessToken->tokenable;
        });

        return $next($request);
    }
}

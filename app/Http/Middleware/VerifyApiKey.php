<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyApiKey
{
    public function handle(Request $request, Closure $next)
    {
        // Nilai ini HARUS sama dengan AppConfig.apiKey di Flutter
        $expectedKey = env('APP_MOBILE_API_KEY', 'SIGAP_SECRET_API_KEY_2026');

        if ($request->header('SUPER-API-KEY') !== $expectedKey) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized: Invalid API Key',
            ], 401);
        }

        return $next($request);
    }
}

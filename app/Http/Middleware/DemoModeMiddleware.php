<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->email === 'demo@bps.go.id') {
            if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                // Allow logout for demo user (logout is POST)
                if ($request->is('logout') || $request->routeIs('logout')) {
                    return $next($request);
                }

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Akun Demo hanya memiliki akses Read-Only. Perubahan data diblokir.'
                    ], 403);
                }
                
                return redirect()->back()->with('error', 'Akun Demo hanya memiliki akses Read-Only. Perubahan data diblokir.');
            }
        }

        return $next($request);
    }
}

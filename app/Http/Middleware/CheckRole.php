<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $allowedRoles = explode(',', $role);
        $activeRole = (string) session('active_role_id');

        // Admin (role 2) has access to all, or if active role is in allowed roles
        if ($activeRole === '2' || in_array($activeRole, $allowedRoles)) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}

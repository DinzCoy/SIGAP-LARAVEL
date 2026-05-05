<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekPeran
{
    //Menangani request masuk dan memvalidasi peran (role) user.
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $activeRoleId = session('active_role_id');

        if (!$activeRoleId) {
            return redirect()->route('login');
        }

        if (empty($roles)) {
            return $next($request);
        }

        if (in_array((int) $activeRoleId, array_map('intval', $roles), true)) {
            return $next($request);
        }

        abort(403, 'Maaf, Anda tidak memiliki akses (Peran Tidak Sesuai).');
    }
}

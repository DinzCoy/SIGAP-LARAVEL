<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Room;
use App\Models\Ticket;
use App\Models\User;
use App\Services\Dashboard\DashboardStatsService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected $layananDashboard;

    public function __construct(DashboardStatsService $layananDashboard)
    {
        $this->layananDashboard = $layananDashboard;
    }

    public function index(Request $request): RedirectResponse|View
    {
        $activeRole = session('active_role_id');
        $routeName = User::getDashboardRoute($activeRole);

        if ($routeName) {
            return redirect()->route($routeName);
        }

        return view('dashboard', ['user' => Auth::user()]);
    }

    public function switchRole($roleId)
    {
        $user = Auth::user();
        $allowedRoles = [
            User::ROLE_PIMPINAN,
            User::ROLE_ADMIN,
            User::ROLE_TEKNISI,
            User::ROLE_PENGELOLA_ASET,
            User::ROLE_PIC_RUANGAN,
            User::ROLE_USER,
            User::ROLE_KETUA_TIM
        ];

        $roleId = (int) $roleId;

        if (in_array($roleId, $allowedRoles, true) && $user->roles->contains('id', $roleId)) {
            session(['active_role_id' => $roleId]);
            return redirect()->route('dashboard')->with('success', 'Role berhasil diubah.');
        }

        return redirect()->back()->with('error', 'Role tidak valid atau Anda tidak memiliki akses.');
    }

    public function teknisi(): View
    {
        $stats = $this->layananDashboard->getTeknisiStats(Auth::user());
        return view('teknisi.dashboard', $stats);
    }

    public function pengelolaAset(): View
    {
        $stats = $this->layananDashboard->getPengelolaAsetStats();
        return view('pengelola_aset.dashboard', $stats);
    }

    public function ruangan(): View
    {
        $stats = $this->layananDashboard->getRuanganStats(Auth::user());
        return view('rooms.dashboard', $stats);
    }

    public function user(): View
    {
        $stats = $this->layananDashboard->getUserStats(Auth::user());
        return view('user.dashboard', $stats);
    }

    public function ketuaTim(): View
    {
        $stats = $this->layananDashboard->getKetuaTimStats(Auth::user());
        return view('ketua_tim.dashboard', $stats);
    }
}
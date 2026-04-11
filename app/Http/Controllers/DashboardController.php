<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Room;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming dashboard request and dispatch to the correct
     * role-specific dashboard based on the active session role.
     */
    public function index(Request $request): RedirectResponse|View
    {
        $roleRouteMap = [
            1 => 'pimpinan.dashboard',
            2 => 'admin.dashboard',
            3 => 'teknisi.dashboard',
            4 => 'pengelola_aset.dashboard',
            5 => 'ruangan.dashboard',
            6 => 'user.dashboard',
        ];

        $activeRole = session('active_role_id');

        if (isset($roleRouteMap[$activeRole])) {
            return redirect()->route($roleRouteMap[$activeRole]);
        }

        return view('dashboard', ['user' => Auth::user()]);
    }

    /**
     * Display the Teknisi (Technician) dashboard with open/completed ticket stats.
     */
    public function teknisi(): View
    {
        $openTickets = Ticket::whereIn('status', ['Open', 'In Progress', 'Menunggu Persetujuan Biaya'])->count();
        $completedTickets = Ticket::where('status', 'Selesai')->count();
        $recentTickets = Ticket::with(['asset', 'reporter'])
            ->latest()
            ->take(5)
            ->get();

        return view('teknisi.dashboard', compact('openTickets', 'completedTickets', 'recentTickets'));
    }

    /**
     * Display the Pengelola Aset (Asset Manager) dashboard with asset and ticket summaries.
     */
    public function pengelolaAset(): View
    {
        $totalAssets   = Asset::count();
        $brokenAssets  = Asset::whereIn('status_kondisi', ['Rusak Ringan', 'Rusak Berat'])->count();
        $pendingTickets = Ticket::where('type', 'Asset')
            ->where('status', 'Menunggu Pengecekan Pengelola')
            ->count();

        $recentAssets = Asset::with(['room', 'deviceName'])
            ->latest()
            ->take(5)
            ->get();

        return view('pengelola_aset.dashboard', compact('totalAssets', 'brokenAssets', 'pendingTickets', 'recentAssets'));
    }

    /**
     * Display the Pengelola Ruangan (Room Manager) dashboard with room and asset summaries.
     */
    public function ruangan(): View
    {
        $totalRooms   = Room::count();
        $totalAssets  = Asset::count();
        $recentAssets = Asset::with(['room', 'deviceName'])->latest()->take(5)->get();

        return view('ruangan.dashboard', compact('totalRooms', 'totalAssets', 'recentAssets'));
    }

    /**
     * Display the regular User dashboard with personal ticket and asset summaries.
     */
    public function user(): View
    {
        $user = Auth::user();

        $myTicketsCount = Ticket::where('reported_by', $user->id)->count();
        $myAssetsCount  = Asset::where('user_id', $user->id)->count();
        $myTickets      = Ticket::where('reported_by', $user->id)->latest()->take(5)->get();
        $myAssets       = Asset::where('user_id', $user->id)->with('deviceName')->take(5)->get();

        return view('user.dashboard', compact('myTicketsCount', 'myAssetsCount', 'myTickets', 'myAssets'));
    }
}
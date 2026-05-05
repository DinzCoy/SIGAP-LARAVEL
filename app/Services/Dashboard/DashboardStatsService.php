<?php

namespace App\Services\Dashboard;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Room;
use App\Models\Ticket;
use App\Models\User;

class DashboardStatsService
{
    // Mengambil statistik untuk dashboard Teknisi.
    public function getTeknisiStats(User $user): array
    {
        return [
            // Tiket aktif yang sudah di-assign ke teknisi ini (belum selesai)
            'openTickets' => Ticket::where('technician_id', $user->id)
                ->active()
                ->count(),

            // Tiket yang sudah diselesaikan oleh teknisi ini
            'completedTickets' => Ticket::where('technician_id', $user->id)
                ->where('status', Ticket::STATUS_SELESAI)
                ->count(),

            // Daftar tiket terbaru yang di-assign ke teknisi ini
            'recentTickets' => Ticket::with(['asset.deviceName', 'reporter'])
                ->where('technician_id', $user->id)
                ->latest()
                ->take(5)
                ->get(),
        ];
    }


    // Mengambil statistik untuk dashboard Pengelola Aset.
    public function getPengelolaAsetStats(): array
    {
        return [
            'totalAssets' => Asset::count(),

            // Pakai konstanta model — tidak ada magic string
            'brokenAssets' => Asset::whereIn('status_kondisi', [
                Asset::KONDISI_RUSAK_RINGAN,
                Asset::KONDISI_RUSAK_BERAT,
            ])->count(),

            'pendingTickets' => Ticket::where('status', Ticket::STATUS_MENUNGGU_PENGELOLA)->count(),

            'recentAssets' => Asset::with(['deviceName', 'room'])
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    // Mengambil statistik untuk dashboard PIC Ruangan.
    public function getRuanganStats(User $user): array
    {
        $roomIds  = Room::where('pic_id', $user->id)->pluck('id');
        $assetIds = Asset::whereIn('room_id', $roomIds)->pluck('id');

        return [
            'totalRooms'  => $roomIds->count(),
            'totalAssets' => $assetIds->count(),

            // Aset dengan kondisi rusak (ringan atau berat) di ruangan ini
            'brokenAssets' => Asset::whereIn('room_id', $roomIds)
                ->whereIn('status_kondisi', [Asset::KONDISI_RUSAK_RINGAN, Asset::KONDISI_RUSAK_BERAT])
                ->count(),

            // Tiket aktif yang berasal dari aset di ruangan ini
            'activeTickets' => Ticket::whereIn('asset_id', $assetIds)
                ->active()
                ->count(),

            // Daftar aset terbaru beserta kondisinya
            'recentAssets' => Asset::with(['deviceName', 'room'])
                ->whereIn('room_id', $roomIds)
                ->latest()
                ->take(10)
                ->get(),
        ];
    }

    // Mengambil statistik untuk dashboard User.
    public function getUserStats(User $user): array
    {
        return [
            // Angka ringkasan
            'myTicketsCount' => Ticket::where('reported_by', $user->id)->count(),
            'myAssetsCount'  => Asset::where('user_id', $user->id)->count(),

            // Daftar tiket terbaru milik user
            'myTickets' => Ticket::with(['asset.deviceName', 'technician'])
                ->where('reported_by', $user->id)
                ->latest()
                ->take(5)
                ->get(),

            // Daftar aset yang dipertanggungjawabkan user
            'myAssets' => Asset::with(['deviceName', 'activeLoan.borrower'])
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get(),

            // Daftar aset yang sedang dipinjam user
            'borrowedAssets' => Asset::with(['deviceName', 'activeLoan.lender'])
                ->whereHas('activeLoan', fn($q) => $q->where('borrower_id', $user->id))
                ->get(),
        ];
    }

    // Mengambil statistik untuk dashboard Ketua Tim.
    public function getKetuaTimStats(User $user): array
    {
        return [
            'pendingAssignment' => Ticket::where('status', Ticket::STATUS_KE_KETUA_TIM)->count(),

            'inProgressByTeam' => Ticket::where('team_leader_id', $user->id)
                ->active()
                ->where('status', '!=', Ticket::STATUS_KE_KETUA_TIM)
                ->count(),

            'completedByTeam' => Ticket::where('team_leader_id', $user->id)
                ->where('status', Ticket::STATUS_SELESAI)
                ->count(),

            'recentTickets' => Ticket::with(['asset.deviceName', 'reporter', 'technician'])
                ->where(function ($q) use ($user) {
                    $q->where('status', Ticket::STATUS_KE_KETUA_TIM)
                      ->orWhere('team_leader_id', $user->id);
                })
                ->latest()
                ->take(5)
                ->get(),

            'technicians' => User::withRole(User::ROLE_TEKNISI)->get(),
        ];
    }
}

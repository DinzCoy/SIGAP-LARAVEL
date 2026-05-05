<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Ticket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function userDashboard(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;

        // === Statistik Tiket (breakdown per status group) ===
        $tickets = Ticket::where('reported_by', $userId)->get();

        $pendingStatuses = [
            Ticket::STATUS_MENUNGGU_PENGELOLA,
            Ticket::STATUS_KE_KETUA_TIM,
            Ticket::STATUS_KE_TEKNISI,
            Ticket::STATUS_MENUNGGU_BIAYA,
            Ticket::STATUS_APPROVED
        ];

        $pendingCount = $tickets->whereIn('status', $pendingStatuses)->count();
        $inProgressCount = $tickets->where('status', Ticket::STATUS_IN_PROGRESS)->count();
        $completedCount = $tickets->whereIn('status', [Ticket::STATUS_SELESAI, Ticket::STATUS_DIBATALKAN])->count();

        // === Total Counts (sama dengan web) ===
        $myTicketsCount = $tickets->count();
        $myAssetsCount = Asset::where('user_id', $userId)->count();

        // === 5 Tiket Terbaru (dengan relasi asset & technician, sama dengan web) ===
        $recentTickets = Ticket::where('reported_by', $userId)
            ->with(['asset.deviceName', 'technician'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'title' => $ticket->title ?: ($ticket->asset ? $ticket->asset->name . ' Bermasalah' : 'Kendala Aset TIK'),
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                    'type' => $ticket->type,
                    'date' => $ticket->created_at->translatedFormat('d M Y'),
                    'asset_name' => $ticket->asset ? $ticket->asset->name : null,
                    'technician_name' => $ticket->technician ? $ticket->technician->name : null,
                ];
            });

        // === Aset yang dipertanggungjawabkan user (sama dengan web) ===
        $myAssets = Asset::with(['deviceName', 'room'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($asset) {
                return [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'asset_code' => $asset->asset_code,
                    'status_kondisi' => $asset->status_kondisi,
                    'room' => $asset->room ? $asset->room->name : '-',
                    'device_name' => $asset->deviceName ? $asset->deviceName->name : null,
                ];
            });

        // === Aset yang sedang dipinjam oleh user ini ===
        $borrowedAssets = Asset::with(['deviceName', 'activeLoan.lender'])
            ->whereHas('activeLoan', fn($q) => $q->where('borrower_id', $userId))
            ->get()
            ->map(function ($asset) {
                return [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'asset_code' => $asset->asset_code,
                    'status_kondisi' => $asset->status_kondisi,
                    'lender_name' => $asset->activeLoan && $asset->activeLoan->lender
                        ? $asset->activeLoan->lender->name : '-',
                    'due_date' => $asset->activeLoan && $asset->activeLoan->due_date
                        ? $asset->activeLoan->due_date->format('d M Y') : '-',
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'stats' => [
                    'pending' => $pendingCount,
                    'in_progress' => $inProgressCount,
                    'completed' => $completedCount,
                    'my_tickets_count' => $myTicketsCount,
                    'my_assets_count' => $myAssetsCount,
                ],
                'recent_tickets' => $recentTickets,
                'my_assets' => $myAssets,
                'borrowed_assets' => $borrowedAssets,
            ]
        ], 200);
    }
}


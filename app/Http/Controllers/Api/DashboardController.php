<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Ticket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Admin dashboard: stats + recent tickets.
     * Returns: active tickets, pending loans, recent 5 tickets.
     */
    public function adminDashboard(Request $request)
    {
        // Tiket aktif (belum selesai/dibatalkan)
        $activeTicketsCount = Ticket::whereNotIn('status', [
            Ticket::STATUS_SELESAI, 'Selesai', 'Completed', 'completed',
            Ticket::STATUS_DIBATALKAN, 'Dibatalkan', 'Canceled', 'canceled'
        ])->count();

        // Tiket pending (menunggu penanganan pertama)
        $pendingTicketsCount = Ticket::whereIn('status', [
            Ticket::STATUS_MENUNGGU_PENGELOLA, 'Menunggu Pengecekan Pengelola', 'Pending', 'pending'
        ])->count();

        // Peminjaman aset yang menunggu persetujuan
        $pendingLoansCount = AssetLoan::whereIn('status', [
            AssetLoan::STATUS_PENDING, 'Pending', 'pending', 'Menunggu Persetujuan'
        ])->count();

        // 5 tiket terbaru masuk
        $recentTickets = Ticket::with(['reporter', 'asset'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id'            => $ticket->id,
                    'title'         => $ticket->title ?: ($ticket->asset ? $ticket->asset->name . ' Bermasalah' : 'Kendala Aset TIK'),
                    'status'        => $ticket->status,
                    'priority'      => $ticket->priority,
                    'reporter_name' => $ticket->reporter ? $ticket->reporter->name : '-',
                    'date'          => $ticket->created_at->translatedFormat('d M Y'),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'stats' => [
                    'active_tickets'  => $activeTicketsCount,
                    'pending_tickets' => $pendingTicketsCount,
                    'pending_loans'   => $pendingLoansCount,
                ],
                'recent_tickets' => $recentTickets,
            ]
        ], 200);
    }


    /**
     * User dashboard: ticket stats + recent tickets + assets.
     * Returns: pending/in_progress/completed counts, recent 5 tickets,
     * owned assets, and currently borrowed assets.
     */
    public function userDashboard(Request $request)
    {
        $user   = $request->user();
        $userId = $user->id;

        // === Statistik Tiket (breakdown per status group) ===
        $tickets = Ticket::where('reported_by', $userId)->get();

        $pendingStatuses = [
            Ticket::STATUS_MENUNGGU_PENGELOLA, 'Menunggu Pengecekan Pengelola', 'Pending', 'pending',
            Ticket::STATUS_KE_KETUA_TIM,
            Ticket::STATUS_KE_TEKNISI,
            Ticket::STATUS_MENUNGGU_BIAYA,
            Ticket::STATUS_APPROVED
        ];

        $pendingCount    = $tickets->whereIn('status', $pendingStatuses)->count();
        $inProgressCount = $tickets->whereIn('status', [
            Ticket::STATUS_IN_PROGRESS, 'Sedang Dikerjakan', 'In Progress', 'in_progress'
        ])->count();
        $completedCount  = $tickets->whereIn('status', [
            Ticket::STATUS_SELESAI, 'Selesai', 'Completed', 'completed',
            Ticket::STATUS_DIBATALKAN, 'Dibatalkan', 'Canceled', 'canceled'
        ])->count();

        // === Total Counts (sama dengan web) ===
        $myTicketsCount = $tickets->count();
        $myAssetsCount  = Asset::where('user_id', $userId)->count();

        // === 5 Tiket Terbaru (dengan relasi asset & technician) ===
        $recentTickets = Ticket::where('reported_by', $userId)
            ->with(['asset.deviceName', 'technician'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id'               => $ticket->id,
                    'title'            => $ticket->title ?: ($ticket->asset ? $ticket->asset->name . ' Bermasalah' : 'Kendala Aset TIK'),
                    'status'           => $ticket->status,
                    'priority'         => $ticket->priority,
                    'type'             => $ticket->type,
                    'date'             => $ticket->created_at->translatedFormat('d M Y'),
                    'asset_name'       => $ticket->asset ? $ticket->asset->name : null,
                    'technician_name'  => $ticket->technician ? $ticket->technician->name : null,
                ];
            });

        // === Aset yang dipertanggungjawabkan user ===
        $myAssets = Asset::with(['deviceName', 'room'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($asset) {
                return [
                    'id'             => $asset->id,
                    'name'           => $asset->name,
                    'asset_code'     => $asset->asset_code,
                    'status_kondisi' => $asset->status_kondisi,
                    'room'           => $asset->room ? $asset->room->name : '-',
                    'device_name'    => $asset->deviceName ? $asset->deviceName->name : null,
                ];
            });

        // === Aset yang sedang dipinjam oleh user ini ===
        $borrowedAssets = Asset::with(['deviceName', 'activeLoan.lender'])
            ->whereHas('activeLoan', fn($q) => $q->where('borrower_id', $userId))
            ->get()
            ->map(function ($asset) {
                return [
                    'id'             => $asset->id,
                    'name'           => $asset->name,
                    'asset_code'     => $asset->asset_code,
                    'status_kondisi' => $asset->status_kondisi,
                    'lender_name'    => $asset->activeLoan && $asset->activeLoan->lender
                        ? $asset->activeLoan->lender->name : '-',
                    'due_date'       => $asset->activeLoan && $asset->activeLoan->due_date
                        ? $asset->activeLoan->due_date->format('d M Y') : '-',
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => [
                'stats' => [
                    'pending'          => $pendingCount,
                    'in_progress'      => $inProgressCount,
                    'completed'        => $completedCount,
                    'my_tickets_count' => $myTicketsCount,
                    'my_assets_count'  => $myAssetsCount,
                ],
                'recent_tickets' => $recentTickets,
                'my_assets'      => $myAssets,
                'borrowed_assets' => $borrowedAssets,
            ]
        ], 200);
    }

    /**
     * Technician dashboard: tasks + repair timeline.
     * Returns: waiting/processing/completed counts, tasks list,
     * and a chronological timeline of the last completed repair.
     */
    public function technicianDashboard(Request $request)
    {
        $user   = $request->user();
        $userId = $user->id;
        $roleId = $request->query('role_id');
        if ($roleId) {
            $isKetuaTim = ((int) $roleId) === 7;
        } else {
            $isKetuaTim = $user->hasRole(7);
        }

        // Ambil tiket berdasarkan peran (Teknisi vs Ketua Tim)
        $tasks = Ticket::where(function ($q) use ($userId, $isKetuaTim) {
            $q->where('technician_id', $userId);
            if ($isKetuaTim) {
                // Ketua Tim juga melihat tiket timnya dan tiket antrean yang didelegasikan padanya
                $q->orWhere('team_leader_id', $userId)
                  ->orWhere('status', Ticket::STATUS_KE_KETUA_TIM);
            }
        })->get();

        $waitingCount = $tasks->whereIn('status', [
            Ticket::STATUS_KE_KETUA_TIM, 'Diteruskan ke Ketua Tim',
            Ticket::STATUS_KE_TEKNISI,   'Diteruskan ke Teknisi',
            Ticket::STATUS_MENUNGGU_BIAYA, 'Menunggu Persetujuan Biaya',
            Ticket::STATUS_APPROVED,       'Biaya Disetujui',
            'Pending', 'pending'
        ])->count();

        $processCount = $tasks->whereIn('status', [
            Ticket::STATUS_IN_PROGRESS, 'Sedang Dikerjakan', 'In Progress', 'in_progress'
        ])->count();

        $completedCount = $tasks->whereIn('status', [
            Ticket::STATUS_SELESAI, 'Selesai', 'Completed', 'completed'
        ])->count();

        // 5 tugas terbaru
        $recentTasks = Ticket::where(function ($q) use ($userId, $isKetuaTim) {
                $q->where('technician_id', $userId);
                if ($isKetuaTim) {
                    $q->orWhere('team_leader_id', $userId)
                      ->orWhere('status', Ticket::STATUS_KE_KETUA_TIM);
                }
            })
            ->with(['asset.deviceName', 'reporter'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id'            => $ticket->id,
                    'title'         => $ticket->title ?: ($ticket->asset ? $ticket->asset->name . ' Bermasalah' : 'Kendala TIK'),
                    'status'        => $ticket->status,
                    'priority'      => $ticket->priority,
                    'reporter_name' => $ticket->reporter ? $ticket->reporter->name : '-',
                    'date'          => $ticket->created_at->translatedFormat('d M Y'),
                ];
            });

        // Timeline alur perbaikan
        $lastDone = Ticket::where(function ($q) use ($userId, $isKetuaTim) {
                $q->where('technician_id', $userId);
                if ($isKetuaTim) {
                    $q->orWhere('team_leader_id', $userId);
                }
            })
            ->whereIn('status', [Ticket::STATUS_SELESAI, 'Selesai', 'Completed', 'completed'])
            ->with(['asset', 'reporter'])
            ->latest('updated_at')
            ->first();

        $repairTimeline = [];
        if ($lastDone) {
            $ticketLabel = $lastDone->title
                ?: ($lastDone->asset ? $lastDone->asset->name . ' Bermasalah' : 'Kendala TIK');

            $repairTimeline = [
                [
                    'title'   => 'Perbaikan Selesai',
                    'desc'    => $ticketLabel,
                    'time'    => $lastDone->updated_at->translatedFormat('d M Y, H:i') . ' WIB',
                    'is_done' => false,
                    'is_last' => false,
                ],
                [
                    'title'   => 'Mulai Pengerjaan',
                    'desc'    => $ticketLabel,
                    'time'    => $lastDone->created_at->addMinutes(30)->translatedFormat('d M Y, H:i') . ' WIB',
                    'is_done' => true,
                    'is_last' => false,
                ],
                [
                    'title'   => 'Tugas Diterima',
                    'desc'    => 'Dilaporkan oleh: ' . ($lastDone->reporter ? $lastDone->reporter->name : '-'),
                    'time'    => $lastDone->created_at->translatedFormat('d M Y, H:i') . ' WIB',
                    'is_done' => true,
                    'is_last' => true,
                ],
            ];
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'stats' => [
                    'waiting'    => $waitingCount,
                    'processing' => $processCount,
                    'completed'  => $completedCount,
                ],
                'tasks'          => $recentTasks,
                'repair_timeline' => $repairTimeline,
            ]
        ], 200);
    }
}

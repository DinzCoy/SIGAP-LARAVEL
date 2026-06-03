<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\Dashboard\DashboardStatsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(protected DashboardStatsService $layananDashboard) {}

    public function adminDashboard(Request $request)
    {
        $stats = $this->layananDashboard->getAdminApiStats();

        return response()->json([
            'status' => 'success',
            'data' => [
                'stats' => [
                    'active_tickets'  => $stats['active_tickets'],
                    'pending_tickets' => $stats['pending_tickets'],
                    'pending_loans'   => $stats['pending_loans'],
                ],
                'recent_tickets' => $stats['recent_tickets']->map(function ($ticket) {
                    return [
                        'id'            => $ticket->id,
                        'title'         => $ticket->title ?: ($ticket->asset ? $ticket->asset->name . ' Bermasalah' : 'Kendala Aset TIK'),
                        'status'        => $ticket->status,
                        'priority'      => $ticket->priority,
                        'reporter_name' => $ticket->reporter ? $ticket->reporter->name : '-',
                        'date'          => $ticket->created_at->translatedFormat('d M Y'),
                    ];
                }),
            ]
        ], 200);
    }

    public function userDashboard(Request $request)
    {
        $user = $request->user();
        $stats = $this->layananDashboard->getUserStats($user);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'stats' => [
                    'pending'          => $stats['pendingCount'],
                    'in_progress'      => $stats['inProgressCount'],
                    'completed'        => $stats['completedCount'],
                    'my_tickets_count' => $stats['myTicketsCount'],
                    'my_assets_count'  => $stats['myAssetsCount'],
                ],
                'recent_tickets' => $stats['myTickets']->map(function ($ticket) {
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
                }),
                'my_assets' => $stats['myAssets']->map(function ($asset) {
                    return [
                        'id'             => $asset->id,
                        'name'           => $asset->name,
                        'asset_code'     => $asset->asset_code,
                        'status_kondisi' => $asset->status_kondisi,
                        'room'           => $asset->room ? $asset->room->name : '-',
                        'device_name'    => $asset->deviceName ? $asset->deviceName->name : null,
                    ];
                }),
                'borrowed_assets' => $stats['borrowedAssets']->map(function ($asset) {
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
                }),
            ]
        ], 200);
    }

    public function technicianDashboard(Request $request)
    {
        $user = $request->user();
        $roleId = $request->query('role_id');
        if ($roleId) {
            $isKetuaTim = ((int) $roleId) === 7;
        } else {
            $isKetuaTim = $user->hasRole(7);
        }

        $stats = $this->layananDashboard->getTechnicianApiStats($user, $isKetuaTim);

        $repairTimeline = [];
        if ($stats['lastDone']) {
            $lastDone = $stats['lastDone'];
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
                    'waiting'    => $stats['waitingCount'],
                    'processing' => $stats['processCount'],
                    'completed'  => $stats['completedCount'],
                ],
                'tasks' => $stats['recentTasks']->map(function ($ticket) {
                    return [
                        'id'            => $ticket->id,
                        'title'         => $ticket->title ?: ($ticket->asset ? $ticket->asset->name . ' Bermasalah' : 'Kendala TIK'),
                        'status'        => $ticket->status,
                        'priority'      => $ticket->priority,
                        'reporter_name' => $ticket->reporter ? $ticket->reporter->name : '-',
                        'date'          => $ticket->created_at->translatedFormat('d M Y'),
                    ];
                }),
                'repair_timeline' => $repairTimeline,
            ]
        ], 200);
    }
}

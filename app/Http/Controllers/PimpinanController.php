<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\PcReport;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PimpinanController extends Controller
{
    // lapak eksklusif buat bos-bos biar bisa liat big picture
    public function dashboard()
    {
        // 1. stat tiket
        $totalTickets = Ticket::count();
        $completedTickets = Ticket::where('status', Ticket::STATUS_SELESAI)->count();
        $completionRate = $totalTickets > 0 ? round(($completedTickets / $totalTickets) * 100) : 0;
        
        // 2. stat aset
        $totalAssets = Asset::count();
        // status barang: aman, lagi meriang, atau wassalam
        $baikAssets = Asset::where('status_kondisi', Asset::KONDISI_BAIK)->count();
        $rusakRinganAssets = Asset::where('status_kondisi', Asset::KONDISI_RUSAK_RINGAN)->count();
        $rusakBeratAssets = Asset::where('status_kondisi', Asset::KONDISI_RUSAK_BERAT)->count();

        // 4. sebaran status tiket
        $ticketsByStatus = Ticket::selectRaw('status, count(*) as count')
                                 ->groupBy('status')
                                 ->pluck('count', 'status');

        $recentTickets = Ticket::with(['asset.deviceName', 'reporter', 'technician'])
                               ->orderBy('created_at', 'desc')
                               ->take(5)
                               ->get();

        // 5. trend tiket 5 bulan (bar chart)
        $trendLabels = [];
        $trendValues = [];
        for ($i = 4; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $trendLabels[] = $month->translatedFormat('M');
            $trendValues[] = Ticket::whereMonth('created_at', $month->month)
                                   ->whereYear('created_at', $month->year)
                                   ->count();
        }

        // 6. performa teknisi
        $technicianStats = \App\Models\User::withRole(\App\Models\User::ROLE_TEKNISI)->withCount([
            'ticketsAssigned as in_progress_count' => function ($query) {
                $query->where('status', Ticket::STATUS_IN_PROGRESS);
            },
            'ticketsAssigned as completed_count' => function ($query) {
                $query->where('status', Ticket::STATUS_SELESAI);
            },
            'ticketsAssigned as total_count'
        ])->get();

        // 7. analisis umur aset
        $avgAssetAge = DB::table('assets')
            ->join('device_names', 'assets.device_name_id', '=', 'device_names.id')
            ->whereNotNull('device_names.procurement_date')
            ->selectRaw('AVG(ABS(DATEDIFF(NOW(), procurement_date)) / 365) as avg_age')
            ->value('avg_age') ?? 0;
            
        $oldestAssets = Asset::with(['deviceName', 'room'])
            ->join('device_names', 'assets.device_name_id', '=', 'device_names.id')
            ->orderBy('device_names.procurement_date', 'asc')
            ->select('assets.*')
            ->take(5)
            ->get();

        // 8. sebaran umur (pie chart)
        $ageDistData = DB::table('assets')
            ->join('device_names', 'assets.device_name_id', '=', 'device_names.id')
            ->whereNotNull('device_names.procurement_date')
            ->selectRaw("
                CASE 
                    WHEN ABS(DATEDIFF(NOW(), procurement_date)) / 365 < 3 THEN 'Baru (< 3 Thn)'
                    WHEN ABS(DATEDIFF(NOW(), procurement_date)) / 365 BETWEEN 3 AND 5 THEN 'Menengah (3-5 Thn)'
                    ELSE 'Tua (> 5 Thn)'
                END as age_group,
                count(*) as count
            ")
            ->groupBy('age_group')
            ->pluck('count', 'age_group');

        // 9. Kepatuhan SLA
        $slaTrackedTickets = Ticket::where(function($query) {
            $query->whereNotNull('resolved_at')
                  ->orWhereIn('status', [Ticket::STATUS_SELESAI, Ticket::STATUS_DIBATALKAN]);
        })->get();
        $totalSlaTracked = $slaTrackedTickets->count();
        $slaBreached = $slaTrackedTickets->filter(function ($ticket) {
            return $ticket->is_sla_resolution_breached;
        })->count();
        $slaFulfilled = $totalSlaTracked - $slaBreached;
        $slaComplianceRate = $totalSlaTracked > 0 ? round(($slaFulfilled / $totalSlaTracked) * 100) : 100;

        return view('pimpinan.dashboard', compact(
            'totalTickets', 'completedTickets', 'completionRate',
            'ticketsByStatus', 'recentTickets',
            'totalAssets', 'baikAssets', 'rusakRinganAssets', 'rusakBeratAssets',
            'trendLabels', 'trendValues', 'technicianStats',
            'avgAssetAge', 'oldestAssets', 'ageDistData',
            'slaComplianceRate', 'slaFulfilled'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\PcReport;
use App\Models\Ticket;
use Illuminate\Http\Request;

class PimpinanController extends Controller
{
    public function dashboard()
    {
        // 1. Ticket & Cost Metrics
        $totalTickets = Ticket::count();
        $completedTickets = Ticket::where('status', 'Selesai')->count();
        $totalCost = Ticket::where('status', 'Selesai')->sum('estimated_cost');
        $completionRate = $totalTickets > 0 ? round(($completedTickets / $totalTickets) * 100) : 0;
        
        // 2. Asset Metrics
        $totalAssets = Asset::count();
        $baikAssets = Asset::where('status_kondisi', 'Baik')->count();
        $rusakRinganAssets = Asset::where('status_kondisi', 'Rusak Ringan')->count();
        $rusakBeratAssets = Asset::where('status_kondisi', 'Rusak Berat')->count();

        // 3. PC Online/Offline/Anomaly Status
        $onlinePcs  = PcReport::where('last_seen', '>=', now()->subMinutes(5))->count();
        $offlinePcs = PcReport::where('last_seen', '<', now()->subMinutes(5))->orWhereNull('last_seen')->count();
        $anomalyPcs = PcReport::where('is_trouble', true)->count();

        // 4. Ticket Status Breakdown
        $ticketsByStatus = Ticket::selectRaw('status, count(*) as count')
                                 ->groupBy('status')
                                 ->pluck('count', 'status');

        $recentTickets = Ticket::with(['asset', 'reporter'])
                               ->orderBy('created_at', 'desc')
                               ->take(5)
                               ->get();

        // 5. 5-Month Ticket Trend Data for Bar Chart
        $trendLabels = [];
        $trendValues = [];
        for ($i = 4; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $trendLabels[] = $month->translatedFormat('M');
            $trendValues[] = Ticket::whereMonth('created_at', $month->month)
                                   ->whereYear('created_at', $month->year)
                                   ->count();
        }

        return view('pimpinan.dashboard', compact(
            'totalTickets', 'completedTickets', 'totalCost', 'completionRate',
            'ticketsByStatus', 'recentTickets',
            'totalAssets', 'baikAssets', 'rusakRinganAssets', 'rusakBeratAssets',
            'onlinePcs', 'offlinePcs', 'anomalyPcs',
            'trendLabels', 'trendValues'
        ));
    }
}

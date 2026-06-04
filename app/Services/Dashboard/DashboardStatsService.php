<?php

namespace App\Services\Dashboard;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Room;
use App\Models\Ticket;
use App\Models\User;
use App\Models\PcReport;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardStatsService
{

    public function getTeknisiStats(User $user): array
    {
        return [

            'openTickets' => Ticket::where('technician_id', $user->id)
                ->active()
                ->count(),

            'completedTickets' => Ticket::where('technician_id', $user->id)
                ->where('status', Ticket::STATUS_SELESAI)
                ->count(),

            'recentTickets' => Ticket::with(['asset.deviceName', 'reporter'])
                ->where('technician_id', $user->id)
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    public function getPengelolaAsetStats(): array
    {
        return [
            'totalAssets' => Asset::count(),

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

    public function getRuanganStats(User $user): array
    {
        $roomIds  = Room::where('pic_id', $user->id)->pluck('id');
        $assetIds = Asset::whereIn('room_id', $roomIds)->pluck('id');

        return [
            'totalRooms'  => $roomIds->count(),
            'totalAssets' => $assetIds->count(),

            'brokenAssets' => Asset::whereIn('room_id', $roomIds)
                ->whereIn('status_kondisi', [Asset::KONDISI_RUSAK_RINGAN, Asset::KONDISI_RUSAK_BERAT])
                ->count(),

            'activeTickets' => Ticket::whereIn('asset_id', $assetIds)
                ->active()
                ->count(),

            'recentAssets' => Asset::with(['deviceName', 'room'])
                ->whereIn('room_id', $roomIds)
                ->latest()
                ->take(10)
                ->get(),
        ];
    }

    public function getUserStats(User $user): array
    {
        $tickets = Ticket::where('reported_by', $user->id)->get();

        return [

            'myTicketsCount'  => $tickets->count(),
            'myAssetsCount'   => Asset::where('user_id', $user->id)->count(),
            'pendingCount'    => $tickets->whereIn('status', Ticket::getPendingStatuses())->count(),
            'inProgressCount' => $tickets->whereIn('status', Ticket::getInProgressStatuses())->count(),
            'completedCount'  => $tickets->whereIn('status', Ticket::getSelesaiStatuses())->count(),

            'myTickets' => Ticket::with(['asset.deviceName', 'technician'])
                ->where('reported_by', $user->id)
                ->latest()
                ->take(5)
                ->get(),

            'myAssets' => Asset::with(['deviceName', 'room', 'activeLoan.borrower'])
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get(),

            'borrowedAssets' => Asset::with(['deviceName', 'activeLoan.lender'])
                ->whereHas('activeLoan', fn($q) => $q->where('borrower_id', $user->id))
                ->get(),
        ];
    }

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

    public function getPimpinanStats(): array
    {

        $totalTickets = Ticket::count();
        $completedTickets = Ticket::where('status', Ticket::STATUS_SELESAI)->count();
        $completionRate = $totalTickets > 0 ? round(($completedTickets / $totalTickets) * 100) : 0;

        $totalAssets = Asset::count();
        $baikAssets = Asset::where('status_kondisi', Asset::KONDISI_BAIK)->count();
        $rusakRinganAssets = Asset::where('status_kondisi', Asset::KONDISI_RUSAK_RINGAN)->count();
        $rusakBeratAssets = Asset::where('status_kondisi', Asset::KONDISI_RUSAK_BERAT)->count();

        $ticketsByStatus = Ticket::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $recentTickets = Ticket::with(['asset.deviceName', 'reporter', 'technician'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $trend = $this->getTicketTrend(5);

        $technicianStats = collect(\App\Services\Gamification\GamificationService::getLeaderboard())
            ->map(fn($item) => (object) $item);

        $ageStats = $this->getAssetAgeStats();

        $slaStats = $this->getSlaStats();

        $dashboardConfig = [
            'totalAssets' => $totalAssets,
            'trendLabels' => $trend['labels'],
            'trendValues' => $trend['values'],
            'baikAssets' => $baikAssets,
            'rusakRinganAssets' => $rusakRinganAssets,
            'rusakBeratAssets' => $rusakBeratAssets,
            'ageDistLabels' => $ageStats['distLabels'],
            'ageDistValues' => $ageStats['distValues']
        ];

        return [
            'totalTickets' => $totalTickets,
            'completedTickets' => $completedTickets,
            'completionRate' => $completionRate,
            'ticketsByStatus' => $ticketsByStatus,
            'recentTickets' => $recentTickets,
            'totalAssets' => $totalAssets,
            'baikAssets' => $baikAssets,
            'rusakRinganAssets' => $rusakRinganAssets,
            'rusakBeratAssets' => $rusakBeratAssets,
            'trendLabels' => $trend['labels'],
            'trendValues' => $trend['values'],
            'technicianStats' => $technicianStats,
            'avgAssetAge' => $ageStats['avgAge'],
            'oldestAssets' => $ageStats['oldestAssets'],
            'ageDistData' => $ageStats['distData'],
            'slaComplianceRate' => $slaStats['complianceRate'],
            'slaFulfilled' => $slaStats['fulfilled'],
            'dashboardConfig' => $dashboardConfig,
        ];
    }

    public function getAdminApiStats(): array
    {
        $activeTicketsCount = Ticket::whereNotIn('status', Ticket::getSelesaiStatuses())->count();

        $pendingTicketsCount = Ticket::whereIn('status', [
            Ticket::STATUS_MENUNGGU_PENGELOLA,
            'Menunggu Pengecekan Pengelola',
            'Pending',
            'pending'
        ])->count();

        $pendingLoansCount = AssetLoan::whereIn('status', [
            AssetLoan::STATUS_PENDING,
            'Pending',
            'pending',
            'Menunggu Persetujuan'
        ])->count();

        $recentTickets = Ticket::with(['reporter', 'asset'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return [
            'active_tickets'  => $activeTicketsCount,
            'pending_tickets' => $pendingTicketsCount,
            'pending_loans'   => $pendingLoansCount,
            'recent_tickets'  => $recentTickets,
        ];
    }

    public function getAdminWebStats(Request $request): array
    {
        $query = PcReport::query();

        if ($request->filled('filter_spesifik')) {
            $query = match ($request->filter_spesifik) {
                'bit_defender'    => $query->whereHas('installedSoftware', fn($q) => $q->where('software_name', 'like', '%Bitdefender%')),
                'no_bit_defender' => $query->whereDoesntHave('installedSoftware', fn($q) => $q->where('software_name', 'like', '%Bitdefender%')),
                'office_365'      => $query->whereHas('installedSoftware', fn($q) => $q->where('software_name', 'like', '%Office 365%')->orWhere('software_name', 'like', '%Microsoft 365%')),
                'no_bmn'          => $query->where(fn($q) => $q->whereDoesntHave('asset')->orWhereHas('asset', fn($q2) => $q2->whereNull('bmn_number')->orWhere('bmn_number', ''))),
                default           => $query,
            };
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('hostname', 'like', "%{$search}%")
                ->orWhere('ip_address', 'like', "%{$search}%"));
        }

        $reportIntervalDays = (int) SystemSetting::getValue('report_interval_days', 7);
        $reportingDeadline  = now()->subDays($reportIntervalDays);

        $totalPcs   = PcReport::count();
        $onlinePcs  = PcReport::online()->count();
        $offlinePcs = PcReport::offline()->count();

        $anomalyPcs = PcReport::where('is_trouble', true)
            ->orWhere(fn($q) => $q->where('last_seen', '<', $reportingDeadline)->orWhereNull('last_seen'))
            ->count();

        $reports = $query->with('asset')->orderByDesc('last_seen')->paginate(20)->withQueryString();

        return compact('reports', 'totalPcs', 'onlinePcs', 'offlinePcs', 'anomalyPcs', 'reportIntervalDays', 'reportingDeadline');
    }

    private function getTicketTrend(int $monthsCount): array
    {
        $trendLabels = [];
        $trendValues = [];
        for ($i = $monthsCount - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $trendLabels[] = $month->translatedFormat('M');
            $trendValues[] = Ticket::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
        }

        return [
            'labels' => $trendLabels,
            'values' => $trendValues,
        ];
    }

    private function getAssetAgeStats(): array
    {
        $assetsWithAge = DB::table('assets')
            ->join('device_names', 'assets.device_name_id', '=', 'device_names.id')
            ->whereNotNull('device_names.procurement_date')
            ->select('device_names.procurement_date')
            ->get();

        $avgAssetAge = 0;
        if ($assetsWithAge->isNotEmpty()) {
            $totalAge = $assetsWithAge->sum(fn($row) => abs(now()->diffInDays(Carbon::parse($row->procurement_date))) / 365);
            $avgAssetAge = round($totalAge / $assetsWithAge->count(), 1);
        }

        $oldestAssets = Asset::with(['deviceName', 'room'])
            ->join('device_names', 'assets.device_name_id', '=', 'device_names.id')
            ->orderBy('device_names.procurement_date', 'asc')
            ->select('assets.*')
            ->take(5)
            ->get();

        $ageDistData = [
            'Baru (< 3 Thn)'       => 0,
            'Menengah (3-5 Thn)'   => 0,
            'Tua (> 5 Thn)'        => 0,
        ];
        foreach ($assetsWithAge as $row) {
            $ageYears = abs(now()->diffInDays(Carbon::parse($row->procurement_date))) / 365;
            if ($ageYears < 3) {
                $ageDistData['Baru (< 3 Thn)']++;
            } elseif ($ageYears <= 5) {
                $ageDistData['Menengah (3-5 Thn)']++;
            } else {
                $ageDistData['Tua (> 5 Thn)']++;
            }
        }

        return [
            'avgAge' => $avgAssetAge,
            'oldestAssets' => $oldestAssets,
            'distData' => collect($ageDistData),
            'distLabels' => array_keys($ageDistData),
            'distValues' => array_values($ageDistData),
        ];
    }

    private function getSlaStats(): array
    {
        $slaTrackedTickets = Ticket::where(function ($query) {
            $query->whereNotNull('resolved_at')
                ->orWhereIn('status', [Ticket::STATUS_SELESAI, Ticket::STATUS_DIBATALKAN]);
        })->get();
        $totalSlaTracked = $slaTrackedTickets->count();
        $slaBreached = $slaTrackedTickets->filter(function ($ticket) {
            return $ticket->is_sla_resolution_breached;
        })->count();
        $slaFulfilled = $totalSlaTracked - $slaBreached;
        $slaComplianceRate = $totalSlaTracked > 0 ? round(($slaFulfilled / $totalSlaTracked) * 100) : 100;

        return [
            'complianceRate' => $slaComplianceRate,
            'fulfilled' => $slaFulfilled,
        ];
    }

    public function getTechnicianApiStats(User $user, bool $isKetuaTim): array
    {
        $userId = $user->id;

        $tasks = Ticket::where(function ($q) use ($userId, $isKetuaTim) {
            $q->where('technician_id', $userId);
            if ($isKetuaTim) {
                $q->orWhere('team_leader_id', $userId)
                    ->orWhere('status', Ticket::STATUS_KE_KETUA_TIM);
            }
        })->get();

        $waitingCount = $tasks->whereIn('status', [
            Ticket::STATUS_KE_KETUA_TIM,
            'Diteruskan ke Ketua Tim',
            Ticket::STATUS_KE_TEKNISI,
            'Diteruskan ke Teknisi',
            Ticket::STATUS_MENUNGGU_BIAYA,
            'Menunggu Persetujuan Biaya',
            Ticket::STATUS_APPROVED,
            'Biaya Disetujui',
            'Pending',
            'pending'
        ])->count();

        $processCount = $tasks->whereIn('status', Ticket::getInProgressStatuses())->count();

        $completedCount = $tasks->whereIn('status', Ticket::getSuccessCompletedStatuses())->count();

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
            ->get();

        $lastDone = Ticket::where(function ($q) use ($userId, $isKetuaTim) {
            $q->where('technician_id', $userId);
            if ($isKetuaTim) {
                $q->orWhere('team_leader_id', $userId);
            }
        })
            ->whereIn('status', Ticket::getSuccessCompletedStatuses())
            ->with(['asset', 'reporter'])
            ->latest('updated_at')
            ->first();

        return compact('waitingCount', 'processCount', 'completedCount', 'recentTasks', 'lastDone');
    }
}

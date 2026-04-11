<?php

namespace App\Http\Controllers;

use App\Models\PcReport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebReportController extends Controller
{
    /**
     * The number of minutes after which a PC is considered offline.
     */
    private const OFFLINE_THRESHOLD_MINUTES = 5;

    /**
     * Display a public-facing listing of PC reports with masked sensitive data.
     */
    public function index(Request $request): View
    {
        $query = $this->applyPcFilters(PcReport::query(), $request);

        if ($request->filled('search')) {
            $query->where('hostname', 'like', '%' . $request->search . '%');
        }

        $reports = $query->orderByDesc('last_seen')->paginate(20)->withQueryString();

        // Mask sensitive fields before presenting to non-admin viewers
        $reports->getCollection()->transform(function ($report) {
            $report->ip_address  = preg_replace('/(\d+\.\d+\.\d+\.)\d+/', '$1***', $report->ip_address);
            $report->mac_address = 'XX:XX:XX:XX:XX:XX';
            return $report;
        });

        return view('reports.index', compact('reports'));
    }

    /**
     * Display the Admin dashboard with full PC details and summary analytics.
     */
    public function adminIndex(Request $request): View
    {
        $query = $this->applyPcFilters(PcReport::query(), $request);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('hostname', 'like', "%{$search}%")
                                       ->orWhere('ip_address', 'like', "%{$search}%"));
        }

        $threshold  = now()->subMinutes(self::OFFLINE_THRESHOLD_MINUTES);
        $totalPcs   = PcReport::count();
        $onlinePcs  = PcReport::where('last_seen', '>=', $threshold)->count();
        $offlinePcs = PcReport::where(fn ($q) => $q->where('last_seen', '<', $threshold)->orWhereNull('last_seen'))->count();
        $anomalyPcs = PcReport::where('is_trouble', true)->count();

        $reports = $query->with('asset')->orderByDesc('last_seen')->paginate(20)->withQueryString();

        return view('reports.admin', compact('reports', 'totalPcs', 'onlinePcs', 'offlinePcs', 'anomalyPcs'));
    }

    /**
     * Display a detailed view for a specific PC report (Admin only).
     */
    public function show(string $id): View
    {
        $report    = PcReport::with(['installedSoftware', 'asset'])->findOrFail($id);
        $isOffline = empty($report->last_seen)
            || Carbon::parse($report->last_seen) < now()->subMinutes(self::OFFLINE_THRESHOLD_MINUTES);

        return view('reports.show', compact('report', 'isOffline'));
    }

    /**
     * Update the room assignment for a specific PC report.
     */
    public function updateRoom(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'room_name' => 'nullable|string|max:255',
        ]);

        $report = PcReport::findOrFail($id);
        $report->update(['room_name' => $request->room_name]);

        return back()->with('success', "Nama ruangan untuk {$report->hostname} berhasil diperbarui.");
    }

    /**
     * Remove a PC report from the database.
     */
    public function destroy(string $id): RedirectResponse
    {
        $report   = PcReport::findOrFail($id);
        $hostname = $report->hostname;
        $report->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', "Device {$hostname} berhasil dihapus dari sistem.");
    }

    /**
     * Export PC reports to an Excel file based on the current filters.
     */
    public function export(Request $request)
    {
        $fileName = 'Laporan_Aset_BPS_' . date('Y-m-d_H-i-s') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PcReportsExport($request->query('filter_spesifik'), $request->query('search')),
            $fileName
        );
    }

    /**
     * Apply the standard PC report filters (software-specific, BMN status) to a query.
     *
     * @param  Builder  $query
     * @param  Request  $request
     * @return Builder
     */
    private function applyPcFilters(Builder $query, Request $request): Builder
    {
        if (!$request->filled('filter_spesifik')) {
            return $query;
        }

        return match ($request->filter_spesifik) {
            'bit_defender' => $query->whereHas('installedSoftware', fn ($q) => $q->where('software_name', 'like', '%Bitdefender%')),
            'office_365'   => $query->whereHas('installedSoftware', fn ($q) => $q->where('software_name', 'like', '%Office 365%')->orWhere('software_name', 'like', '%Microsoft 365%')),
            'no_bmn'       => $query->where(fn ($q) => $q->whereDoesntHave('asset')->orWhereHas('asset', fn ($q2) => $q2->whereNull('bmn_number')->orWhere('bmn_number', ''))),
            default        => $query,
        };
    }
}
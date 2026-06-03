<?php

namespace App\Http\Controllers;

use App\Models\PcReport;
use App\Services\Dashboard\DashboardStatsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebReportController extends Controller
{
    public function __construct(protected DashboardStatsService $layananDashboard) {}

    public function index(Request $request): View
    {
        $query = $this->applyPcFilters(PcReport::query(), $request);

        if ($request->filled('search')) {
            $query->where('hostname', 'like', '%' . $request->search . '%');
        }

        $reports = $query->orderByDesc('last_seen')->paginate(20)->withQueryString();

        $reports->getCollection()->transform(function ($report) {
            $report->ip_address  = preg_replace('/(\d+\.\d+\.\d+\.)\d+/', '$1***', $report->ip_address);
            $report->mac_address = 'XX:XX:XX:XX:XX:XX';
            return $report;
        });

        return view('reports.index', compact('reports'));
    }

    public function adminIndex(Request $request): View
    {
        $stats = $this->layananDashboard->getAdminWebStats($request);

        return view('reports.admin', $stats);
    }

    public function show(string $id): View
    {
        $report    = PcReport::with(['installedSoftware', 'asset'])->findOrFail($id);
        $isOffline = $report->isOffline();

        return view('reports.show', compact('report', 'isOffline'));
    }

    public function updateRoom(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'room_name' => 'nullable|string|max:255',
        ]);

        $report = PcReport::findOrFail($id);
        $report->update(['room_name' => $request->room_name]);

        return back()->with('success', "Nama ruangan untuk {$report->hostname} berhasil diperbarui.");
    }

    public function destroy(string $id): RedirectResponse
    {
        $report   = PcReport::findOrFail($id);
        $hostname = $report->hostname;
        $report->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', "Device {$hostname} berhasil dihapus dari sistem.");
    }

    public function export(Request $request)
    {
        $fileName = 'Laporan_Aset_BPS_' . date('Y-m-d_H-i-s') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PcReportsExport($request->query('filter_spesifik'), $request->query('search')),
            $fileName
        );
    }

    private function applyPcFilters(Builder $query, Request $request): Builder
    {
        if (!$request->filled('filter_spesifik')) {
            return $query;
        }

        return match ($request->filter_spesifik) {
            'bit_defender'    => $query->whereHas('installedSoftware', fn ($q) => $q->where('software_name', 'like', '%Bitdefender%')),
            'no_bit_defender' => $query->whereDoesntHave('installedSoftware', fn ($q) => $q->where('software_name', 'like', '%Bitdefender%')),
            'office_365'      => $query->whereHas('installedSoftware', fn ($q) => $q->where('software_name', 'like', '%Office 365%')->orWhere('software_name', 'like', '%Microsoft 365%')),
            'no_bmn'          => $query->where(fn ($q) => $q->whereDoesntHave('asset')->orWhereHas('asset', fn ($q2) => $q2->whereNull('bmn_number')->orWhere('bmn_number', ''))),
            default           => $query,
        };
    }
}
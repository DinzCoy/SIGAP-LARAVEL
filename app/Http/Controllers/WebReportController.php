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
    // spill data pc buat user umum, no cepu-cepu
    public function index(Request $request): View
    {
        $query = $this->applyPcFilters(PcReport::query(), $request);

        if ($request->filled('search')) {
            $query->where('hostname', 'like', '%' . $request->search . '%');
        }

        $reports = $query->orderByDesc('last_seen')->paginate(20)->withQueryString();

        // sensor data sensitif buat yg bukan admin
        $reports->getCollection()->transform(function ($report) {
            $report->ip_address  = preg_replace('/(\d+\.\d+\.\d+\.)\d+/', '$1***', $report->ip_address);
            $report->mac_address = 'XX:XX:XX:XX:XX:XX';
            return $report;
        });

        return view('reports.index', compact('reports'));
    }

    // dashboard khusus admin buat mantau kesehatan semua pc
    public function adminIndex(Request $request): View
    {
        $query = $this->applyPcFilters(PcReport::query(), $request);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('hostname', 'like', "%{$search}%")
                                       ->orWhere('ip_address', 'like', "%{$search}%"));
        }

        // Ambil setting interval laporan (default 7 hari jika tidak ada)
        $reportIntervalDays = (int) \App\Models\SystemSetting::getValue('report_interval_days', 7);
        $reportingDeadline  = now()->subDays($reportIntervalDays);

        $totalPcs   = PcReport::count();
        $onlinePcs  = PcReport::online()->count();
        $offlinePcs = PcReport::offline()->count();
        
        // PC dianggap Anomali jika: is_trouble manual = true ATAU telat melapor sesuai interval settings
        $anomalyPcs = PcReport::where('is_trouble', true)
            ->orWhere(fn ($q) => $q->where('last_seen', '<', $reportingDeadline)->orWhereNull('last_seen'))
            ->count();

        $reports = $query->with('asset')->orderByDesc('last_seen')->paginate(20)->withQueryString();

        return view('reports.admin', compact('reports', 'totalPcs', 'onlinePcs', 'offlinePcs', 'anomalyPcs', 'reportIntervalDays', 'reportingDeadline'));
    }

    // kepoin detail spek satu pc sampe ke akar-akarnya
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

    // bungkus semua data ke excel biar bisa buat laporan ke bos
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
            'bit_defender' => $query->whereHas('installedSoftware', fn ($q) => $q->where('software_name', 'like', '%Bitdefender%')),
            'office_365'   => $query->whereHas('installedSoftware', fn ($q) => $q->where('software_name', 'like', '%Office 365%')->orWhere('software_name', 'like', '%Microsoft 365%')),
            'no_bmn'       => $query->where(fn ($q) => $q->whereDoesntHave('asset')->orWhereHas('asset', fn ($q2) => $q2->whereNull('bmn_number')->orWhere('bmn_number', ''))),
            default        => $query,
        };
    }
}
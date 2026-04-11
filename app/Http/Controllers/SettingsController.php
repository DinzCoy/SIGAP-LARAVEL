<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\LoginActivity;
use App\Models\WhitelistedIp;
use App\Models\PcReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Display the settings page with all tabs.
     */
    public function index()
    {
        $settings = SystemSetting::pluck('value', 'key')->toArray();
        $whitelistedIps = WhitelistedIp::orderBy('created_at', 'desc')->get();
        $loginActivities = LoginActivity::where('user_id', Auth::id())
            ->orderBy('logged_in_at', 'desc')
            ->take(10)
            ->get();

        return view('settings.index', compact('settings', 'whitelistedIps', 'loginActivities'));
    }

    /**
     * Update profile (name, email).
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    /**
     * Update system configuration (API key, server URL).
     */
    public function updateSystem(Request $request)
    {
        $validated = $request->validate([
            'api_key' => 'required|string|min:10|max:255',
            'server_url' => 'required|string|max:255',
        ]);

        SystemSetting::setValue('api_key', $validated['api_key']);
        SystemSetting::setValue('server_url', $validated['server_url']);

        return back()->with('success', 'Konfigurasi sistem berhasil disimpan.');
    }

    /**
     * Update anomaly thresholds.
     */
    public function updateThresholds(Request $request)
    {
        $validated = $request->validate([
            'ram_threshold' => 'required|integer|min:50|max:99',
            'disk_threshold_gb' => 'required|integer|min:1|max:500',
            'report_interval_days' => 'required|integer|min:1|max:90',
        ]);

        SystemSetting::setValue('ram_threshold', $validated['ram_threshold']);
        SystemSetting::setValue('disk_threshold_gb', $validated['disk_threshold_gb']);
        SystemSetting::setValue('report_interval_days', $validated['report_interval_days']);

        return back()->with('success', 'Threshold anomali berhasil disimpan.');
    }

    /**
     * Add an IP to the whitelist.
     */
    public function addWhitelistIp(Request $request)
    {
        $validated = $request->validate([
            'ip_address' => 'required|ip|unique:whitelisted_ips,ip_address',
            'label' => 'nullable|string|max:255',
        ]);

        WhitelistedIp::create($validated);

        return back()->with('success', 'IP berhasil ditambahkan ke whitelist.');
    }

    /**
     * Remove an IP from the whitelist.
     */
    public function removeWhitelistIp($id)
    {
        WhitelistedIp::findOrFail($id)->delete();
        return back()->with('success', 'IP berhasil dihapus dari whitelist.');
    }

    /**
     * Export PC reports data as CSV.
     */
    public function exportData()
    {
        $reports = PcReport::orderBy('hostname')->get();

        $csvFileName = 'pc_reports_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$csvFileName\"",
        ];

        $columns = ['Hostname', 'IP Address', 'MAC Address', 'Room', 'OS', 'RAM Used %', 'Disk Status', 'Trouble', 'Last Seen'];

        $callback = function () use ($reports, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($reports as $r) {
                $ramPercent = $r->total_ram_kb > 0
                    ? round((($r->total_ram_kb - $r->ram_free_kb) / $r->total_ram_kb) * 100, 1) . '%'
                    : '-';

                fputcsv($file, [
                    $r->hostname,
                    $r->ip_address,
                    $r->mac_address,
                    $r->room_name,
                    $r->os_name,
                    $ramPercent,
                    $r->disk_status,
                    $r->is_trouble ? 'YES: ' . $r->trouble_note : 'No',
                    $r->last_seen,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Backup database (mysqldump).
     */
    public function backupDatabase()
    {
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host');

        $fileName = 'backup_' . $dbName . '_' . date('Y-m-d_His') . '.sql';
        $filePath = storage_path('app/' . $fileName);

        $env = 'MYSQL_PWD=' . escapeshellarg($dbPass);
        $command = sprintf(
            '%s mysqldump -h %s -u %s %s > %s 2>&1',
            $env,
            escapeshellarg($dbHost),
            escapeshellarg($dbUser),
            escapeshellarg($dbName),
            escapeshellarg($filePath)
        );

        exec($command, $output, $result);

        if ($result !== 0 || !file_exists($filePath)) {
            return back()->with('error', 'Backup gagal. Pastikan mysqldump tersedia di server.');
        }

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Clean old logs based on retention setting.
     */
    public function cleanOldLogs()
    {
        $months = (int) SystemSetting::getValue('log_retention_months', 6);
        $cutoff = now()->subMonths($months);

        $deleted = PcReport::where('last_seen', '<', $cutoff)->delete();
        LoginActivity::where('logged_in_at', '<', $cutoff)->delete();

        return back()->with('success', "Berhasil membersihkan $deleted laporan lama (lebih dari $months bulan).");
    }

    /**
     * Update log retention setting.
     */
    public function updateRetention(Request $request)
    {
        $validated = $request->validate([
            'log_retention_months' => 'required|integer|min:1|max:36',
        ]);

        SystemSetting::setValue('log_retention_months', $validated['log_retention_months']);

        return back()->with('success', 'Pengaturan retensi log berhasil disimpan.');
    }
}

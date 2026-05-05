<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\LoginActivity;
use App\Models\WhitelistedIp;
use App\Models\PcReport;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{

    public function index()
    {
        $settings = SystemSetting::pluck('value', 'key')->toArray();
        $whitelistedIps = WhitelistedIp::orderBy('created_at', 'desc')->get();
        $loginActivities = LoginActivity::where('user_id', Auth::id())
            ->orderBy('logged_in_at', 'desc')
            ->take(10)
            ->get();
        $rooms = Room::with(['pic'])->withCount('assets')->orderBy('sort_order')->orderBy('name')->get();

        return view('settings.index', compact('settings', 'whitelistedIps', 'loginActivities', 'rooms'));
    }

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

    public function addWhitelistIp(Request $request)
    {
        $validated = $request->validate([
            'ip_address' => 'required|ip|unique:whitelisted_ips,ip_address',
            'label' => 'nullable|string|max:255',
        ]);

        WhitelistedIp::create($validated);

        return back()->with('success', 'IP berhasil ditambahkan ke whitelist.');
    }

    public function removeWhitelistIp($id)
    {
        WhitelistedIp::findOrFail($id)->delete();
        return back()->with('success', 'IP berhasil dihapus dari whitelist.');
    }

    public function exportData(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate   = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : null;

        $fileName = 'BPS_PC_Guardian_Super_Export_' . date('Y-m-d_H-i') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\Super\SystemSuperExport($startDate, $endDate),
            $fileName
        );
    }

    public function backupDatabase()
    {
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host');

        $fileName = 'backup_' . $dbName . '_' . date('Y-m-d_His') . '.sql';
        $filePath = storage_path('app/' . $fileName);

        // Menggunakan putenv agar kompatibel di Windows (CMD) maupun Linux (Bash)
        putenv("MYSQL_PWD={$dbPass}");
        $command = sprintf(
            'mysqldump -h %s -u %s %s > %s 2>&1',
            escapeshellarg($dbHost),
            escapeshellarg($dbUser),
            escapeshellarg($dbName),
            escapeshellarg($filePath)
        );

        exec($command, $output, $result);
        putenv("MYSQL_PWD="); // Hapus environment variables setelah pemakaian

        if ($result !== 0 || !file_exists($filePath)) {
            return back()->with('error', 'Backup gagal. Pastikan mysqldump tersedia di server.');
        }

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    public function cleanOldLogs()
    {
        $months = (int) SystemSetting::getValue('log_retention_months', 6);
        $cutoff = now()->subMonths($months);

        $deleted = PcReport::where('last_seen', '<', $cutoff)->delete();
        $deleted += LoginActivity::where('logged_in_at', '<', $cutoff)->delete();

        return back()->with('success', "Berhasil membersihkan $deleted laporan lama (lebih dari $months bulan).");
    }

    public function updateRetention(Request $request)
    {
        $validated = $request->validate([
            'log_retention_months' => 'required|integer|min:1|max:36',
        ]);

        SystemSetting::setValue('log_retention_months', $validated['log_retention_months']);

        return back()->with('success', 'Pengaturan retensi log berhasil disimpan.');
    }

    public function updateAgentSchedule(Request $request)
    {
        $validated = $request->validate([
            'agent_schedule_hours' => 'required|string|max:100',
            'agent_delay_per_room' => 'required|integer|min:60|max:1800',
        ]);

        // format jam: cuma angka 0-23
        $hours = array_filter(
            array_map('intval', explode(',', $validated['agent_schedule_hours'])),
            fn($h) => $h >= 0 && $h <= 23
        );
        $hours = array_unique($hours);
        sort($hours);

        SystemSetting::setValue('agent_schedule_hours', implode(',', $hours));
        SystemSetting::setValue('agent_delay_per_room', $validated['agent_delay_per_room']);

        return back()->with('success', 'Jadwal pengiriman agent berhasil disimpan.');
    }

    public function updateRoomOrder(Request $request)
    {
        $validated = $request->validate([
            'room_orders' => 'required|array',
            'room_orders.*' => 'required|integer|min:0',
        ]);

        foreach ($validated['room_orders'] as $roomId => $order) {
            Room::where('id', $roomId)->update(['sort_order' => $order]);
        }

        return back()->with('success', 'Urutan ruangan berhasil diperbarui.');
    }
}


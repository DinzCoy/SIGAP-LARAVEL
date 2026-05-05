<?php

namespace App\Http\Controllers;

use App\Models\PcReport;
use App\Models\Asset;
use App\Models\User;
use App\Models\Room;
use App\Models\AssetMovementLog;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PcReportController extends Controller
{
    // tempat nampung curhatan (report) spek pc dari agent
    public function store(Request $request): JsonResponse
    {
        $validApiKey = SystemSetting::getValue('api_key', 'BPS-SULSEL-SECRET-2026');

        if ($request->header('X-API-KEY') !== $validApiKey) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized Access'], 401);
        }

        $validated = $request->validate([
            'hostname'                  => 'required|string|max:255',
            'username'                  => 'nullable|string|max:255',
            'ip_address'                => 'required|ip',
            'mac_address'               => 'required|string|max:17',
            'room_name'                 => 'nullable|string|max:255',
            'os_name'                   => 'nullable|string|max:255',
            'os_build'                  => 'nullable|integer',
            'total_ram_kb'              => 'nullable|integer',
            'ram_free_kb'               => 'nullable|integer',
            'total_disk_b'              => 'nullable|integer',
            'disk_free_b'               => 'nullable|integer',
            'disk_status'               => 'nullable|string',
            'last_patch'                => 'nullable|string',
            'is_trouble'                => 'nullable|boolean',
            'trouble_note'              => 'nullable|string',
            'software_list'             => 'nullable|array',
            'software_list.*.name'      => 'required|string',
            'software_list.*.version'   => 'nullable|string',
            'software_list.*.publisher' => 'nullable|string',
        ]);

        $ramThreshold = (int) SystemSetting::getValue('ram_threshold', 90);
        $diskThresholdGb = (int) SystemSetting::getValue('disk_threshold_gb', 10);
        $diskThresholdBytes = $diskThresholdGb * 1024 * 1024 * 1024;

        $isTrouble = $validated['is_trouble'] ?? false;
        $troubleNote = $validated['trouble_note'] ?? '';

        // Override logic untuk RAM
        if (isset($validated['total_ram_kb']) && isset($validated['ram_free_kb']) && $validated['total_ram_kb'] > 0) {
            $usedRamPercent = round((($validated['total_ram_kb'] - $validated['ram_free_kb']) / $validated['total_ram_kb']) * 100, 2);
            if ($usedRamPercent > $ramThreshold) {
                $isTrouble = true;
                $troubleNote = "High RAM Usage Anomaly detected ({$usedRamPercent}%) [System Override]";
            }
        }

        // Override logic untuk Sisa Disk
        if (isset($validated['disk_free_b']) && $validated['disk_free_b'] < $diskThresholdBytes) {
            $isTrouble = true;
            $troubleNote = ($troubleNote ? $troubleNote . " | " : "") . "Disk Space Critical (Under {$diskThresholdGb}GB)";
        }

        $report = PcReport::updateOrCreate(
            ['mac_address' => $validated['mac_address']],
            [
                'hostname'     => $validated['hostname'],
                'username'     => $validated['username'] ?? null,
                'ip_address'   => $validated['ip_address'],
                'room_name'    => $validated['room_name'] ?? null,
                'os_name'      => $validated['os_name'] ?? null,
                'os_build'     => $validated['os_build'] ?? null,
                'total_ram_kb' => $validated['total_ram_kb'] ?? null,
                'ram_free_kb'  => $validated['ram_free_kb'] ?? null,
                'total_disk_b' => $validated['total_disk_b'] ?? null,
                'disk_free_b'  => $validated['disk_free_b'] ?? null,
                'disk_status'  => $validated['disk_status'] ?? null,
                'last_patch'   => $validated['last_patch'] ?? null,
                'is_trouble'   => $isTrouble,
                'trouble_note' => $troubleNote,
                'last_seen'    => now(),
            ]
        );

        // --- Logic: Auto Allocation & Room Sync ---
        $asset = Asset::where('mac_address', $validated['mac_address'])->first();
        if ($asset) {
            $oldUserId = $asset->user_id;
            $oldRoomId = $asset->room_id;
            $newUserId = $oldUserId;
            $newRoomId = $oldRoomId;
            $hasChanged = false;
            $reasons = [];

            // 1. Sync User (jika username dikirim dan cocok dengan database kita)
            if (!empty($validated['username'])) {
                $foundUser = User::where('username', $validated['username'])->first();
                if ($foundUser && $asset->user_id !== $foundUser->id) {
                    $newUserId = $foundUser->id;
                    $asset->user_id = $newUserId;
                    $asset->allocated_at = now();
                    $hasChanged = true;
                    $reasons[] = "Ownership auto-sync: {$validated['username']}";
                }
            }

            // 2. Sync Room (jika room_name dikirim dan cocok dengan database kita)
            if (!empty($validated['room_name'])) {
                $foundRoom = Room::where('name', $validated['room_name'])->first();
                if ($foundRoom && $asset->room_id !== $foundRoom->id) {
                    $newRoomId = $foundRoom->id;
                    $asset->room_id = $newRoomId;
                    $hasChanged = true;
                    $reasons[] = "Location auto-sync: {$validated['room_name']}";
                }
            }

            if ($hasChanged) {
                $asset->save();

                // Log pergerakan aset untuk audit
                AssetMovementLog::create([
                    'asset_id'    => $asset->id,
                    'old_user_id' => $oldUserId,
                    'new_user_id' => $newUserId,
                    'old_room_id' => $oldRoomId,
                    'new_room_id' => $newRoomId,
                    'action_type' => ($oldUserId !== $newUserId && $oldRoomId !== $newRoomId) ? 'Automated Ownership & Room Transfer' : ($oldUserId !== $newUserId ? 'Automated Ownership Transfer' : 'Automated Room Transfer'),
                    'reason'      => implode(' | ', $reasons),
                ]);
            }
        }

        if (!empty($validated['software_list'])) {
            $report->installedSoftware()->delete();

            $softwareData = [];
            foreach ($validated['software_list'] as $software) {
                $name = trim($software['name'] ?? '');
                if ($name === '') {
                    continue;
                }

                $softwareData[] = [
                    'pc_report_id'      => $report->id,
                    'software_name'     => substr($name, 0, 255),
                    'software_version'  => isset($software['version']) ? substr(trim($software['version']), 0, 255) : null,
                    'software_publisher' => isset($software['publisher']) ? substr(trim($software['publisher']), 0, 255) : null,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
            }

            // di-chunk biar memori ga jebol pas insert software
            foreach (array_chunk($softwareData, 200) as $chunk) {
                \App\Models\InstalledSoftware::insert($chunk);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Data logged successfully'], 200);
    }
}
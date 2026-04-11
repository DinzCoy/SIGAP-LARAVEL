<?php

namespace App\Http\Controllers;

use App\Models\PcReport;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PcReportController extends Controller
{
    /**
     * Store or update a PC monitoring report submitted by the Windows agent.
     *
     * Authentication is handled via the X-API-KEY request header, validated
     * against the value stored in system settings. Software inventory is
     * replaced in bulk on each report to reflect the current state.
     *
     * @param  Request       $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validApiKey = SystemSetting::getValue('api_key', 'BPS-SULSEL-SECRET-2026');

        if ($request->header('X-API-KEY') !== $validApiKey) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized Access'], 401);
        }

        $validated = $request->validate([
            'hostname'                  => 'required|string|max:255',
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

        $report = PcReport::updateOrCreate(
            ['hostname' => $validated['hostname']],
            [
                'mac_address'  => $validated['mac_address'],
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
                'is_trouble'   => $validated['is_trouble'] ?? false,
                'trouble_note' => $validated['trouble_note'] ?? null,
                'last_seen'    => now(),
            ]
        );

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

            // Chunk inserts to avoid memory exhaustion on large software lists
            foreach (array_chunk($softwareData, 200) as $chunk) {
                \App\Models\InstalledSoftware::insert($chunk);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Data logged successfully'], 200);
    }
}
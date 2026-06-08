<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgentConfigController extends Controller
{

    public function schedule(Request $request): JsonResponse
    {
        $validApiKey = SystemSetting::getValue('api_key', 'BPS-SULSEL-SECRET-2026');

        if ($request->header('X-API-KEY') !== $validApiKey) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $macAddress = $request->query('mac_address', '');

        $scheduledHoursRaw = SystemSetting::getValue('agent_schedule_hours', '9,15');
        $scheduledHours = array_map('intval', array_filter(explode(',', $scheduledHoursRaw)));

        $delayPerRoom = (int) SystemSetting::getValue('agent_delay_per_room', 300);

        $roomOrder = 0;
        $roomName = '';
        
        if (!empty($macAddress)) {
            $asset = \App\Models\Asset::where('mac_address', $macAddress)->first();
            if ($asset && $asset->room) {
                $roomOrder = $asset->room->sort_order;
                $roomName = $asset->room->name;
            }
        }

        $delaySeconds = $roomOrder * $delayPerRoom;

        $rooms = Room::orderBy('sort_order')->get(['name', 'sort_order']);

        return response()->json([
            'status'          => 'success',
            'scheduled_hours' => $scheduledHours,
            'delay_seconds'   => $delaySeconds,
            'delay_per_room'  => $delayPerRoom,
            'room_order'      => $roomOrder,
            'room_name'       => $roomName,
            'rooms'           => $rooms,
        ]);
    }
}

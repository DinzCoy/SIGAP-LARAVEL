<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgentConfigController extends Controller
{
    //Menentukan jadwal sinkronisasi agent ke sistem.
    public function schedule(Request $request): JsonResponse
    {
        $validApiKey = SystemSetting::getValue('api_key', 'BPS-SULSEL-SECRET-2026');

        if ($request->header('X-API-KEY') !== $validApiKey) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $roomName = $request->query('room_name', '');

        //Mengambil konfigurasi jadwal (format: "9,15").
        $scheduledHoursRaw = SystemSetting::getValue('agent_schedule_hours', '9,15');
        $scheduledHours = array_map('intval', array_filter(explode(',', $scheduledHoursRaw)));

        //Jeda antar ruangan dalam satuan detik.
        $delayPerRoom = (int) SystemSetting::getValue('agent_delay_per_room', 300);

        //Mengecek urutan ruangan.
        $room = Room::where('name', $roomName)->first();
        $roomOrder = $room ? $room->sort_order : 0;

        //Menghitung total jeda untuk ruangan ini.
        $delaySeconds = $roomOrder * $delayPerRoom;

        //Mengambil data urutan semua ruangan.
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

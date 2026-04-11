<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\DeviceName;
use App\Models\PcReport;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssetController extends Controller
{
    /**
     * Redirect Master Aset entry point to the Device Names index.
     */
    public function masterAset(): RedirectResponse
    {
        return redirect()->route('device-names.index');
    }

    /**
     * Display a paginated listing of assets with optional filters for
     * device name, room, BMN status, PC linking status, and keyword search.
     */
    public function index(Request $request): View
    {
        $query = Asset::with(['pcReport', 'room', 'deviceName', 'user'])
            ->when($request->device_name_id, fn ($q) => $q->where('device_name_id', $request->device_name_id))
            ->when($request->room, fn ($q) => $q->whereHas('room', fn ($r) => $r->where('slug', $request->room)));

        if ($request->filled('filter_linked')) {
            match ($request->filter_linked) {
                'yes'   => $query->whereHas('pcReport'),
                'no'    => $query->whereDoesntHave('pcReport'),
                default => null,
            };
        }

        if ($request->filled('filter_bmn')) {
            if ($request->filter_bmn === 'yes') {
                $query->whereNotNull('bmn_number')->where('bmn_number', '!=', '');
            } elseif ($request->filter_bmn === 'no') {
                $query->where(fn ($q) => $q->whereNull('bmn_number')->orWhere('bmn_number', ''));
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('bmn_number', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhereHas('deviceName', fn ($d) => $d->where('brand', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"));
            });
        }

        $perPage = $request->input('per_page', 10);
        if ($perPage === 'all') {
            $perPage = (clone $query)->count() ?: 10;
        }

        $assets              = $query->orderByDesc('id')->paginate($perPage)->withQueryString()->onEachSide(1);
        $unlinkedPcs         = PcReport::whereDoesntHave('asset')->orderBy('hostname')->get();
        $allDeviceNames      = DeviceName::orderBy('brand')->orderBy('name')->get();
        $currentDeviceNameId = $request->device_name_id;
        $rooms               = Room::orderBy('name')->get();
        $users               = User::orderBy('name')->get();

        return view('assets.index', compact(
            'assets', 'unlinkedPcs', 'allDeviceNames', 'currentDeviceNameId', 'rooms', 'users'
        ));
    }

    /**
     * Store a newly created asset record.
     *
     * Uses only() instead of all() to prevent mass assignment of unintended fields.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bmn_number'     => 'required|unique:assets,bmn_number',
            'device_name_id' => 'required|exists:device_names,id',
            'serial_number'  => 'nullable|string',
            'room_id'        => 'nullable|exists:rooms,id',
            'user_id'        => 'nullable|exists:users,id',
            'allocated_at'   => 'nullable|date',
            'status_kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
        ]);

        Asset::create($validated);

        return redirect()->back()->with('success', 'Aset BMN berhasil ditambahkan.');
    }

    /**
     * Update the specified asset record.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'bmn_number'     => 'required|unique:assets,bmn_number,' . $asset->id,
            'device_name_id' => 'required|exists:device_names,id',
            'serial_number'  => 'nullable|string',
            'room_id'        => 'nullable|exists:rooms,id',
            'user_id'        => 'nullable|exists:users,id',
            'allocated_at'   => 'nullable|date',
            'status_kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
        ]);

        $asset->update($validated);

        return redirect()->back()->with('success', 'Data Aset berhasil diperbarui.');
    }

    /**
     * Remove the specified asset record.
     */
    public function destroy(string $id): RedirectResponse
    {
        Asset::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Data Aset berhasil dihapus.');
    }

    /**
     * Link a PC (identified by MAC address) to an asset record.
     * This action is protected at the route level to Admin only.
     */
    public function linkDevice(Request $request, string $assetId): RedirectResponse
    {
        $request->validate([
            'mac_address' => 'required|exists:pc_reports,mac_address',
        ]);

        if (Asset::where('mac_address', $request->mac_address)->exists()) {
            return redirect()->back()->with('error', 'MAC Address ini sudah terhubung dengan aset lain!');
        }

        Asset::findOrFail($assetId)->update(['mac_address' => $request->mac_address]);

        return redirect()->back()->with('success', 'Device berhasil ditautkan ke BMN.');
    }
}

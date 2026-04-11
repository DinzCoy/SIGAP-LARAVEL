<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\DeviceName;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DeviceNameController extends Controller
{
    /**
     * Display all device name records, ordered by brand and name.
     */
    public function index(): View
    {
        $device_names = DeviceName::orderBy('brand')->orderBy('name')->get();

        return view('device_names.index', compact('device_names'));
    }

    /**
     * Show the form for creating a new device name entry.
     */
    public function create(): View
    {
        return view('device_names.create');
    }

    /**
     * Store a newly created device name and auto-generate asset slots
     * based on the specified quantity.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'brand'            => 'required|string|max:255',
            'name'             => 'required|string|max:255',
            'type'             => 'nullable|string|max:255',
            'quantity'         => 'required|integer|min:0',
            'procurement_date' => 'nullable|date',
            'description'      => 'nullable|string',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('device_images', 'public');
        }

        $deviceName = DeviceName::create($data);

        if ($deviceName->quantity > 0) {
            $assets = array_map(fn () => [
                'device_name_id' => $deviceName->id,
                'bmn_number'     => null,
                'status_kondisi' => 'Baik',
                'created_at'     => now(),
                'updated_at'     => now(),
            ], range(1, $deviceName->quantity));

            Asset::insert($assets);
        }

        return redirect()->route('device-names.index')
            ->with('success', "Data nama perangkat berhasil ditambahkan dan {$deviceName->quantity} aset didaftarkan.");
    }

    /**
     * Display details and asset breakdown for a specific device name.
     */
    public function show(DeviceName $deviceName): View
    {
        $deviceName->loadCount([
            'assets as registered_count',
            'assets as baik_count'         => fn ($q) => $q->where('status_kondisi', 'Baik'),
            'assets as rusak_ringan_count'  => fn ($q) => $q->where('status_kondisi', 'Rusak Ringan'),
            'assets as rusak_berat_count'   => fn ($q) => $q->where('status_kondisi', 'Rusak Berat'),
        ]);

        $assets = $deviceName->assets()->with(['room', 'room.pic', 'user'])->get();
        $rooms  = Room::orderBy('name')->get();

        return view('device_names.show', compact('deviceName', 'assets', 'rooms'));
    }

    /**
     * Show the form for editing the specified device name.
     */
    public function edit(DeviceName $deviceName): View
    {
        return view('device_names.edit', compact('deviceName'));
    }

    /**
     * Update the device name record and generate additional asset slots
     * if the quantity has been increased.
     */
    public function update(Request $request, DeviceName $deviceName): RedirectResponse
    {
        $request->validate([
            'brand'            => 'required|string|max:255',
            'name'             => 'required|string|max:255',
            'type'             => 'nullable|string|max:255',
            'quantity'         => 'required|integer|min:0',
            'procurement_date' => 'nullable|date',
            'description'      => 'nullable|string',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($deviceName->image) {
                Storage::disk('public')->delete($deviceName->image);
            }
            $data['image'] = $request->file('image')->store('device_images', 'public');
        }

        $deviceName->update($data);

        // Auto-generate new asset slots if quantity was increased
        $existingCount = $deviceName->assets()->count();
        $diff          = (int) $request->quantity - $existingCount;

        if ($diff > 0) {
            $newAssets = array_map(fn () => [
                'device_name_id' => $deviceName->id,
                'bmn_number'     => null,
                'status_kondisi' => 'Baik',
                'created_at'     => now(),
                'updated_at'     => now(),
            ], range(1, $diff));

            Asset::insert($newAssets);
        }

        return redirect()->route('device-names.index')
            ->with('success', 'Data nama perangkat berhasil diperbarui.');
    }

    /**
     * Remove the device name record and its associated image from storage.
     */
    public function destroy(DeviceName $deviceName): RedirectResponse
    {
        if ($deviceName->image) {
            Storage::disk('public')->delete($deviceName->image);
        }

        $deviceName->delete();

        return redirect()->route('device-names.index')
            ->with('success', 'Data nama perangkat berhasil dihapus.');
    }
}

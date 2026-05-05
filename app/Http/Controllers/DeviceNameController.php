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
    
    public function index(): View
    {
        $device_names = DeviceName::orderBy('brand')->orderBy('name')->get();

        return view('device_names.index', compact('device_names'));
    }

    public function create(): View
    {
        return view('device_names.create');
    }

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

    public function edit(DeviceName $deviceName): View
    {
        return view('device_names.edit', compact('deviceName'));
    }

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

        // auto bikin slot aset kalo qty nambah
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

    public function destroy(DeviceName $deviceName): RedirectResponse
    {
        if ($deviceName->image) {
            Storage::disk('public')->delete($deviceName->image);
        }

        // Hapus semua aset yang terkait dengan master ini
        $deviceName->assets()->delete();

        $deviceName->delete();

        return redirect()->route('device-names.index')
            ->with('success', 'Data nama perangkat berhasil dihapus.');
    }
}

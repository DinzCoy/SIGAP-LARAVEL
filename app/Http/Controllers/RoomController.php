<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoomController extends Controller
{
    
    // spill list ruangan di kantor
    public function index(): View
    {
        $rooms = Room::with('pic')->withCount([
            'assets',
            'assets as baik_count'         => fn ($q) => $q->where('status_kondisi', 'Baik'),
            'assets as rusak_ringan_count'  => fn ($q) => $q->where('status_kondisi', 'Rusak Ringan'),
            'assets as rusak_berat_count'   => fn ($q) => $q->where('status_kondisi', 'Rusak Berat'),
        ])->orderBy('name')->get();

        return view('rooms.index', compact('rooms'));
    }

    // panggung buat input ruangan baru
    public function create(): View
    {
        $users = $this->getPicCandidates();

        return view('rooms.create', compact('users'));
    }

    // bungkus data ruangan baru ke database
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:rooms',
            'description' => 'nullable|string',
            'pic_id'      => 'nullable|exists:users,id',
        ]);

        // ruang baru taro paling belakang
        $nextOrder = (Room::max('sort_order') ?? -1) + 1;

        Room::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'pic_id'      => $request->pic_id,
            'sort_order'  => $nextOrder,
        ]);

        return redirect()->route('rooms.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    // tempat edit-edit kalau ruangan ganti nama atau pic
    public function edit(Room $room): View
    {
        $users = $this->getPicCandidates();

        return view('rooms.edit', compact('room', 'users'));
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:rooms,name,' . $room->id,
            'description' => 'nullable|string',
            'pic_id'      => 'nullable|exists:users,id',
        ]);

        $room->update([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'pic_id'      => $request->pic_id,
        ]);

        return redirect()->route('rooms.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        if ($room->assets()->exists()) {
            return redirect()->route('rooms.index')
                ->with('error', 'Ruangan tidak dapat dihapus karena masih menampung aset.');
        }

        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Ruangan berhasil dihapus.');
    }

    // filter siapa aja yang bisa jadi pic ruangan
    private function getPicCandidates()
    {
        return User::whereHas('roles', fn ($q) => $q->whereIn('roles.id', [5, 6]))->get();
    }
}

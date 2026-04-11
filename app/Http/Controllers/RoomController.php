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
    /**
     * Display all rooms with their asset condition breakdown.
     */
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

    /**
     * Show the form for creating a new room.
     * Only users with role Pengelola Ruangan (5) or User (6) may be set as PIC.
     */
    public function create(): View
    {
        $users = $this->getPicCandidates();

        return view('rooms.create', compact('users'));
    }

    /**
     * Store a newly created room, generating its slug automatically from the name.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:rooms',
            'description' => 'nullable|string',
            'pic_id'      => 'nullable|exists:users,id',
        ]);

        Room::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'pic_id'      => $request->pic_id,
        ]);

        return redirect()->route('rooms.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified room.
     */
    public function edit(Room $room): View
    {
        $users = $this->getPicCandidates();

        return view('rooms.edit', compact('room', 'users'));
    }

    /**
     * Update the specified room record. Slug is regenerated from the new name.
     */
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

    /**
     * Remove a room from storage.
     * Deletion is blocked if the room still contains assets.
     */
    public function destroy(Room $room): RedirectResponse
    {
        if ($room->assets()->exists()) {
            return redirect()->route('rooms.index')
                ->with('error', 'Ruangan tidak dapat dihapus karena masih menampung aset.');
        }

        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Ruangan berhasil dihapus.');
    }

    /**
     * Get users eligible to be set as a Room PIC (roles: Pengelola Ruangan or User).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getPicCandidates()
    {
        return User::whereHas('roles', fn ($q) => $q->whereIn('roles.id', [5, 6]))->get();
    }
}

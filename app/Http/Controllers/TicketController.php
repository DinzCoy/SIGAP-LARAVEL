<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Room;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use App\Http\Requests\CekInputTiketBaru;
use App\Http\Requests\CekInputBalasanTiket;
use App\Http\Requests\CekUpdateStatusTiket;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Jobs\CompressImageJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function __construct(protected \App\Services\Ticket\TicketService $ticketService) {}

    public function index()
    {
        $user = Auth::user();
        $activeRole = session('active_role_id');

        $tickets = Ticket::forRole($user, (int) $activeRole)
            ->with(['asset.deviceName', 'reporter', 'technician', 'teamLeader'])
            ->latest()
            ->paginate(25);

        $userAssets = Asset::where('user_id', $user->id)->get();
        $rooms = Room::orderBy('name')->get();

        return view('tickets.index', compact('tickets', 'userAssets', 'rooms'));
    }

    public function show(int $id)
    {
        $ticket = Ticket::with(['asset.deviceName', 'reporter', 'technician', 'teamLeader', 'replies.user'])->findOrFail($id);

        Gate::authorize('view', $ticket);

        $technicians = collect();
        $activeRole = session('active_role_id');
        if (in_array($activeRole, [User::ROLE_ADMIN, User::ROLE_KETUA_TIM])) {
            $technicians = User::withRole(User::ROLE_TEKNISI)->get();
        }

        return view('tickets.show', compact('ticket', 'technicians'));
    }

    public function store(CekInputTiketBaru $request)
    {
        $this->ticketService->createTicket(
            $request->validated(),
            Auth::user(),
            $request->file('photo')
        );

        return redirect()->route('tickets.index')->with('success', 'Tiket berhasil dibuat.');
    }

    public function updateStatus(CekUpdateStatusTiket $request, int $id)
    {
        $ticket = Ticket::findOrFail($id);

        $this->ticketService->updateStatus(
            $ticket,
            $request->validated(),
            Auth::user(),
            session('active_role_id')
        );

        return back()->with('success', 'Status tiket berhasil diupdate.');
    }

    public function addReply(CekInputBalasanTiket $request, int $id)
    {
        $ticket = Ticket::findOrFail($id);

        Gate::authorize('reply', $ticket);

        TicketReply::create([
            'ticket_id' => $id,
            'user_id'   => Auth::id(),
            'message'   => $request->validated()['message'],
        ]);

        $message = 'Balasan berhasil ditambahkan.';
        if (in_array($ticket->status, [Ticket::STATUS_SELESAI, Ticket::STATUS_DIBATALKAN])) {
            $message = 'Balasan terkirim. Catatan: Tiket ini sudah ditutup. Jika ada masalah baru, silakan buat tiket baru ya!';
        }

        return back()->with('success', $message);
    }

    public function createRuangan()
    {
        $user    = Auth::user();
        $roomIds = Room::where('pic_id', $user->id)->pluck('id');

        $roomAssets = Asset::with(['deviceName', 'room'])
            ->whereIn('room_id', $roomIds)
            ->orderBy('room_id')
            ->get();

        $myRooms = Room::whereIn('id', $roomIds)->orderBy('name')->get();

        return view('rooms.lapor', compact('roomAssets', 'myRooms'));
    }

    public function storeRuangan(Request $request)
    {
        $validated = $request->validate([
            'asset_id'    => 'required|exists:assets,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'priority'    => 'required|in:Rendah,Sedang,Tinggi',
            'category'    => 'required|in:Service,Troubleshooting',
        ]);

        $roomIds = Room::where('pic_id', Auth::id())->pluck('id');

        $asset = Asset::whereIn('room_id', $roomIds)->find($validated['asset_id']);

        if (!$asset) {
            abort(403, 'Anda tidak memiliki wewenang untuk melaporkan aset dari ruangan tersebut.');
        }

        $kondisiBaru = $validated['priority'] === 'Tinggi'
            ? Asset::KONDISI_RUSAK_BERAT
            : Asset::KONDISI_RUSAK_RINGAN;

        $asset->update(['status_kondisi' => $kondisiBaru]);

        Ticket::create([
            'type'        => 'Asset',
            'category'    => $validated['category'],
            'asset_id'    => $asset->id,
            'room_id'     => $asset->room_id,
            'reported_by' => Auth::id(),
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'priority'    => $validated['priority'],
            'status'      => Ticket::STATUS_MENUNGGU_PENGELOLA,
        ]);

        return redirect()->route('tickets.index')
            ->with('success', 'Laporan kerusakan aset berhasil dikirim dan sedang menunggu pengecekan.');
    }
}

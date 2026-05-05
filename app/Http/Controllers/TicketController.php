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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $activeRole = session('active_role_id');

        // Pimpinan, Admin, & Pengelola Aset: akses semua tiket
        if (in_array($activeRole, [User::ROLE_PIMPINAN, User::ROLE_ADMIN, User::ROLE_PENGELOLA_ASET])) {
            $tickets = Ticket::with(['asset.deviceName', 'reporter', 'technician', 'teamLeader'])
                ->latest()
                ->paginate(25);
        }
        // Ketua Tim: akses tiket yang diteruskan atau ditugaskan
        elseif ($activeRole == User::ROLE_KETUA_TIM) {
            $tickets = Ticket::with(['asset.deviceName', 'reporter', 'technician', 'teamLeader'])
                ->where(function ($q) use ($user) {
                    $q->where('status', Ticket::STATUS_KE_KETUA_TIM)
                        ->orWhere('team_leader_id', $user->id);
                })
                ->latest()
                ->paginate(25);
        }
        // Teknisi: akses tiket yang ditugaskan kepada yang bersangkutan
        elseif ($activeRole == User::ROLE_TEKNISI) {
            $tickets = Ticket::with(['asset.deviceName', 'reporter', 'technician', 'teamLeader'])
                ->where('technician_id', $user->id)
                ->latest()
                ->paginate(25);
        }
        // PIC Ruangan: tiket yang berasal dari aset di ruangan yang dikelolanya
        // (termasuk tiket yang dilaporkan orang lain dari ruangan tersebut)
        elseif ($activeRole == User::ROLE_PIC_RUANGAN) {
            $roomIds   = Room::where('pic_id', $user->id)->pluck('id');
            $assetIds  = Asset::whereIn('room_id', $roomIds)->pluck('id');

            $tickets = Ticket::with(['asset.deviceName', 'reporter', 'technician', 'teamLeader'])
                ->where(function ($q) use ($user, $assetIds) {
                    // Tiket yang dilaporkan sendiri ATAU yang asetnya ada di ruangannya
                    $q->where('reported_by', $user->id)
                        ->orWhereIn('asset_id', $assetIds);
                })
                ->latest()
                ->paginate(25);
        }
        // Pelapor (User Biasa): akses tiket yang dibuat sendiri
        else {
            $tickets = Ticket::where('reported_by', $user->id)
                ->with(['asset.deviceName', 'reporter', 'technician', 'teamLeader'])
                ->latest()
                ->paginate(25);
        }

        // Data pendukung untuk modal pembuatan tiket
        $userAssets = Asset::where('user_id', $user->id)->get();
        $rooms = Room::orderBy('name')->get();

        return view('tickets.index', compact('tickets', 'userAssets', 'rooms'));
    }

    public function show($id)
    {
        $ticket = Ticket::with(['asset.deviceName', 'reporter', 'technician', 'teamLeader', 'replies.user'])->findOrFail($id);

        Gate::authorize('view', $ticket);

        // Data teknisi untuk modal penugasan oleh Ketua Tim/Admin
        $technicians = collect();
        $activeRole = session('active_role_id');
        if (in_array($activeRole, [User::ROLE_ADMIN, User::ROLE_KETUA_TIM])) {
            $technicians = User::withRole(User::ROLE_TEKNISI)->get();
        }

        return view('tickets.show', compact('ticket', 'technicians'));
    }

    public function store(CekInputTiketBaru $request)
    {
        $validated = $request->validated();
        $type = !empty($validated['asset_id']) ? 'Asset' : 'General';

        Ticket::create([
            'type' => $type,
            'category' => $validated['category'] ?? null,
            'asset_id' => $validated['asset_id'] ?? null,
            'room_id' => $validated['room_id'],
            'reported_by' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => Ticket::STATUS_MENUNGGU_PENGELOLA,
        ]);

        return redirect()->route('tickets.index')->with('success', 'Tiket berhasil dibuat.');
    }

    public function updateStatus(CekUpdateStatusTiket $request, $id)
    {
        $validated = $request->validated();
        $ticket = Ticket::findOrFail($id);
        $activeRole = session('active_role_id');

        $ticket->status = $validated['status'];

        if (array_key_exists('estimated_cost', $validated)) {
            $ticket->estimated_cost = $validated['estimated_cost'];
        }

        if (array_key_exists('category', $validated)) {
            $ticket->category = $validated['category'];
        }

        // SLA Tracking: Catat waktu respons pertama kali
        if (in_array($validated['status'], [Ticket::STATUS_KE_TEKNISI, Ticket::STATUS_IN_PROGRESS]) && is_null($ticket->responded_at)) {
            $ticket->responded_at = now();
        }

        // SLA Tracking: Catat waktu selesai
        if ($validated['status'] === Ticket::STATUS_SELESAI && is_null($ticket->resolved_at)) {
            $ticket->resolved_at = now();
            // Jika teknisi langsung bypass ke Selesai tanpa In Progress
            if (is_null($ticket->responded_at)) {
                $ticket->responded_at = now();
            }
        }

        // Penugasan teknisi oleh Ketua Tim atau Admin
        if ($validated['status'] === Ticket::STATUS_KE_TEKNISI && in_array($activeRole, [User::ROLE_ADMIN, User::ROLE_KETUA_TIM])) {
            if (!empty($validated['technician_id'])) {
                $ticket->technician_id = $validated['technician_id'];
                $ticket->team_leader_id = Auth::id();
            }
        }

        // Penugasan otomatis jika Teknisi langsung mengubah status menjadi In Progress
        if ($validated['status'] === Ticket::STATUS_IN_PROGRESS && $activeRole == User::ROLE_TEKNISI) {
            if (!$ticket->technician_id) {
                $ticket->technician_id = Auth::id();
            }
        }

        $ticket->save();

        // Jika tiket Selesai dan terkait aset → kembalikan kondisi aset ke Baik
        if ($validated['status'] === Ticket::STATUS_SELESAI && $ticket->asset_id) {
            Asset::where('id', $ticket->asset_id)
                ->update(['status_kondisi' => Asset::KONDISI_BAIK]);
        }

        // Catat perubahan status ke riwayat diskusi (System Message)
        $statusMsg = "⚙️ Status tiket diubah menjadi: ({$validated['status']}) oleh " . Auth::user()->name;
        if (!empty($validated['technician_id'])) {
            $tech = User::find($validated['technician_id']);
            if ($tech) {
                $statusMsg .= "\n👨‍🔧 Teknisi ditugaskan: ({$tech->name})";
            }
        }
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(), // Mencatat ID pengubah untuk akuntabilitas
            'message' => $statusMsg,
        ]);

        return back()->with('success', 'Status tiket berhasil diupdate.');
    }

    public function addReply(CekInputBalasanTiket $request, $id)
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

    // Form pelaporan kerusakan oleh PIC Ruangan — aset ditampilkan berdasarkan ruangan yang dikelola
    public function createRuangan()
    {
        $user    = Auth::user();
        $roomIds = Room::where('pic_id', $user->id)->pluck('id');

        // Semua aset yang berada di ruangan yang dipertanggungjawabkan PIC ini
        $roomAssets = Asset::with(['deviceName', 'room'])
            ->whereIn('room_id', $roomIds)
            ->orderBy('room_id')
            ->get();

        // Daftar ruangan untuk fallback pilihan lokasi (jika aset tidak dipilih)
        $myRooms = Room::whereIn('id', $roomIds)->orderBy('name')->get();

        return view('rooms.lapor', compact('roomAssets', 'myRooms'));
    }

    // Simpan tiket dari pelaporan PIC Ruangan
    public function storeRuangan(Request $request)
    {
        $validated = $request->validate([
            'asset_id'    => 'required|exists:assets,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'priority'    => 'required|in:Rendah,Sedang,Tinggi',
            'category'    => 'required|in:Service,Troubleshooting',
        ]);

        // Ambil ID ruangan yang SAAT INI dikelola oleh PIC ini (real-time, bukan cache)
        // Ini secara otomatis menangani kasus mutasi — jika PIC sudah dipindah,
        // aset dari ruangan lama tidak akan lolos validasi ini.
        $roomIds = Room::where('pic_id', Auth::id())->pluck('id');

        // Pastikan aset yang dilaporkan memang berada di ruangan yang masih dikelola PIC ini.
        // Jika tidak (misal: manipulasi POST atau sudah dimutasi), kembalikan 403.
        $asset = Asset::whereIn('room_id', $roomIds)->find($validated['asset_id']);

        if (!$asset) {
            abort(403, 'Anda tidak memiliki wewenang untuk melaporkan aset dari ruangan tersebut.');
        }

        // Update status kondisi aset sesuai tingkat prioritas laporan
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

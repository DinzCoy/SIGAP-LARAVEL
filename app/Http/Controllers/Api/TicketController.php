<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Asset;
use App\Models\User;
use App\Services\Notification\Tickets\TicketStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * GET /tickets
     * Daftar tiket milik user yang sedang login (formatted).
     */
    public function index(Request $request)
    {
        $tickets = Ticket::where('reported_by', $request->user()->id)
            ->with(['asset', 'technician'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($ticket) {
                return [
                    'id'          => $ticket->id,
                    'title'       => $ticket->title,
                    'description' => $ticket->description,
                    'type'        => $ticket->type,
                    'status'      => $ticket->status,
                    'priority'    => $ticket->priority,
                    'asset_id'    => $ticket->asset_id,
                    'asset_name'  => $ticket->asset ? $ticket->asset->name : null,
                    'technician'    => $ticket->technician ? $ticket->technician->name : null,
                    'technician_id' => $ticket->technician_id,
                    'photo_url'   => $ticket->photo_path
                        ? url('storage/' . $ticket->photo_path)
                        : null,
                    'created_at'  => $ticket->created_at,
                    'updated_at'  => $ticket->updated_at,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $tickets,
        ], 200);
    }

    /**
     * POST /tickets
     * Buat tiket baru dari mobile.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'category'    => 'required|in:Service,Troubleshooting',
            'asset_id'    => 'nullable|exists:assets,id',
            'foto'        => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        // Simpan foto jika ada
        $photoPath = null;
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $photoPath = $request->file('foto')->store('ticket-photos', 'public');
        }

        // Cari room_id jika asset_id disediakan
        $roomId = null;
        if ($request->asset_id) {
            $asset  = Asset::find($request->asset_id);
            $roomId = $asset ? $asset->room_id : null;

            // Otomatis ubah status kondisi aset jika service
            if ($request->category === 'Service') {
                $asset->update(['status_kondisi' => 'Rusak Ringan']);
            }
        }

        $type = !empty($request->asset_id) ? 'Asset' : 'General';

        $ticket = Ticket::create([
            'title'       => $request->title,
            'description' => $request->description,
            'type'        => $type,
            'category'    => $request->category,
            'status'      => Ticket::STATUS_MENUNGGU_PENGELOLA,
            'priority'    => 'Sedang',
            'reported_by' => $request->user()->id,
            'asset_id'    => $request->asset_id,
            'room_id'     => $roomId,
            'photo_path'  => $photoPath,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Laporan berhasil dikirim.',
            'data'    => [
                'ticket_id' => $ticket->id,
            ]
        ], 201);
    }

    /**
     * GET /user/tickets
     * Semua tiket milik user (raw, tanpa format – untuk admin panel mobile).
     */
    public function myTickets(Request $request)
    {
        $tickets = Ticket::where('reported_by', $request->user()->id)
            ->with(['asset'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $tickets,
        ], 200);
    }

    /**
     * GET /admin/tickets?status=...
     * Admin: semua tiket dengan optional filter status.
     */
    public function adminIndex(Request $request)
    {
        $status = $request->query('status');
        $user = $request->user();
        
        $roleId = $request->query('role_id');
        if ($roleId) {
            $isKetuaTim = ((int) $roleId) === 7;
            $isTeknisi = ((int) $roleId) === 3;
        } else {
            $isKetuaTim = $user->hasRole(7);
            $isTeknisi = $user->hasRole(3);
        }

        $query = Ticket::with(['asset', 'reporter', 'technician']);

        // Filter berdasarkan peran pengguna (Admin/Pengelola melihat semua)
        if ($isKetuaTim) {
            $query->where(function ($q) use ($user) {
                $q->where('status', Ticket::STATUS_KE_KETUA_TIM)
                  ->orWhere('team_leader_id', $user->id);
            });
        } elseif ($isTeknisi) {
            $query->where('technician_id', $user->id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $tickets = $query->orderBy('created_at', 'desc')->get()
            ->map(function ($ticket) {
                return [
                    'id'          => $ticket->id,
                    'title'       => $ticket->title,
                    'description' => $ticket->description,
                    'type'        => $ticket->type,
                    'category'    => $ticket->category,
                    'status'      => $ticket->status,
                    'priority'    => $ticket->priority,
                    'asset_id'    => $ticket->asset_id,
                    'asset_name'  => $ticket->asset ? $ticket->asset->name : null,
                    'reporter'    => $ticket->reporter ? $ticket->reporter->name : null,
                    'technician'    => $ticket->technician ? $ticket->technician->name : null,
                    'technician_id' => $ticket->technician_id,
                    'photo_url'   => $ticket->photo_path
                        ? url('storage/' . $ticket->photo_path)
                        : null,
                    'created_at'  => $ticket->created_at,
                    'updated_at'  => $ticket->updated_at,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $tickets,
        ], 200);
    }

    /**
     * POST /admin/tickets/{id}/status
     * Admin/Teknisi: update status tiket.
     * Body: { status: string, tanggapan?: string }
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status'        => 'required|string',
            'tanggapan'     => 'nullable|string',
            'technician_id' => 'nullable|exists:users,id',
        ]);

        $ticket = Ticket::find($id);
        if (!$ticket) {
            return response()->json(['status' => 'error', 'message' => 'Tiket tidak ditemukan'], 404);
        }

        $user = $request->user();
        $statusLama = $ticket->status;
        $statusBaru = $request->status;
        $tanggapan  = $request->tanggapan;

        $ticket->status = $statusBaru;

        // SLA Tracking: Catat waktu respons pertama kali
        if (in_array($statusBaru, [Ticket::STATUS_KE_TEKNISI, Ticket::STATUS_IN_PROGRESS]) && is_null($ticket->responded_at)) {
            $ticket->responded_at = now();
        }

        // SLA Tracking: Catat waktu selesai
        if ($statusBaru === Ticket::STATUS_SELESAI && is_null($ticket->resolved_at)) {
            $ticket->resolved_at = now();
            // Jika teknisi langsung bypass ke Selesai tanpa In Progress
            if (is_null($ticket->responded_at)) {
                $ticket->responded_at = now();
            }
        }

        // Penugasan teknisi oleh Ketua Tim atau Admin
        if ($statusBaru === Ticket::STATUS_KE_TEKNISI) {
            if ($request->filled('technician_id')) {
                $ticket->technician_id = $request->technician_id;
                // Jika yang menugaskan adalah Ketua Tim, jadikan dia team leader tiket ini
                if ($user->hasRole(7)) {
                    $ticket->team_leader_id = $user->id;
                }
            }
        }

        // Penugasan otomatis jika Teknisi mengubah status ke In Progress
        if ($statusBaru === Ticket::STATUS_IN_PROGRESS && $user->hasRole(3)) {
            if (!$ticket->technician_id) {
                $ticket->technician_id = $user->id;
            }
        }

        $ticket->save();

        // Jika tiket Selesai dan terkait aset → kembalikan kondisi aset ke Baik
        if ($statusBaru === Ticket::STATUS_SELESAI && $ticket->asset_id) {
            \App\Models\Asset::where('id', $ticket->asset_id)
                ->update(['status_kondisi' => \App\Models\Asset::KONDISI_BAIK]);
        }

        // Kirim notifikasi jika status benar-benar berubah
        if ($statusLama !== $statusBaru) {
            try {
                TicketStatusNotification::kirim($ticket, $statusBaru, $tanggapan);
            } catch (\Exception $e) {
                Log::warning('Gagal kirim notif tiket: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Status tiket berhasil diupdate.',
        ], 200);
    }

    /**
     * GET /technician/maintenance
     * Riwayat maintenance / servis aset yang sudah selesai dikerjakan OLEH TEKNISI YANG LOGIN.
     * - Jika role Teknisi: hanya tampilkan tiket di mana technician_id = user ini
     * - Jika role Ketua Tim: tampilkan tiket di mana team_leader_id = user ini (semua anggota tim)
     */
    public function maintenanceHistory(Request $request)
    {
        $user   = $request->user();
        $userId = $user->id;
        $limit  = (int) ($request->query('limit', 20));
        $page   = (int) ($request->query('page', 1));
        $bulan  = $request->query('bulan'); // format: YYYY-MM (opsional)

        // Tentukan apakah user adalah Ketua Tim (role_id 7)
        $roleId = $request->query('role_id');
        if ($roleId) {
            $isKetuaTim = ((int) $roleId) === 7;
        } else {
            $isKetuaTim = $user->hasRole(7);
        }

        $query = Ticket::with(['asset.room', 'asset.deviceName', 'reporter', 'technician', 'teamLeader'])
            ->whereNotNull('resolved_at')
            // ── Filter per role ────────────────────────────────────────────
            ->where(function ($q) use ($userId, $isKetuaTim) {
                // Teknisi: lihat tiket yang dia kerjakan sendiri
                $q->where('technician_id', $userId);
                // Ketua Tim: JUGA lihat tiket seluruh anggota tim yang dia pimpin
                if ($isKetuaTim) {
                    $q->orWhere('team_leader_id', $userId);
                }
            })
            ->when($bulan, fn($q) =>
                $q->whereYear('resolved_at', substr($bulan, 0, 4))
                  ->whereMonth('resolved_at', substr($bulan, 5, 2))
            )
            ->orderBy('resolved_at', 'desc');

        $paginated = $query->paginate($limit, ['*'], 'page', $page);

        $items = collect($paginated->items())->map(fn(Ticket $ticket) => [
            'id'            => $ticket->id,
            'judul'         => $ticket->title,
            'deskripsi'     => $ticket->description,
            'tipe'          => $ticket->type,
            'kategori'      => $ticket->category,
            'status'        => $ticket->status,
            'prioritas'     => $ticket->priority,
            'nama_aset'     => $ticket->asset
                                ? ($ticket->asset->deviceName ? $ticket->asset->deviceName->name : ($ticket->asset->brand ?? 'Aset'))
                                : null,
            'kode_aset'     => $ticket->asset ? $ticket->asset->bmn_number : null,
            'lokasi'        => $ticket->asset && $ticket->asset->room ? $ticket->asset->room->name : null,
            'pelapor'       => $ticket->reporter ? $ticket->reporter->name : null,
            'teknisi'       => $ticket->technician ? $ticket->technician->name : null,
            'ketua_tim'     => $ticket->teamLeader ? $ticket->teamLeader->name : null,
            // Flag untuk UI: apakah tiket ini milik anggota tim (bukan dikerjakan sendiri)?
            'is_delegated'  => $ticket->technician_id !== $userId,
            'foto_url'      => $ticket->photo_path
                                ? url('storage/' . $ticket->photo_path)
                                : null,
            'selesai_pada'  => $ticket->resolved_at ? $ticket->resolved_at->toISOString() : null,
            'dibuat_pada'   => $ticket->created_at ? $ticket->created_at->toISOString() : null,
        ]);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'data'         => $items,
                'total'        => $paginated->total(),
                'last_page'    => $paginated->lastPage(),
                'is_ketua_tim' => $isKetuaTim,
            ],
        ], 200);
    }

    public function listTechnicians(Request $request)
    {
        $technicians = User::withRole(User::ROLE_TEKNISI)->get(['id', 'name', 'email']);
        return response()->json([
            'status' => 'success',
            'data'   => $technicians
        ], 200);
    }

    /**
     * GET /technicians/leaderboard
     * Ambil data leaderboard gamifikasi teknisi.
     */
    public function leaderboard(Request $request)
    {
        $data = \App\Services\Gamification\GamificationService::getLeaderboard();
        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ], 200);
    }
}



<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Asset;
use App\Models\User;
use App\Services\Notification\Tickets\TicketStatusNotification;
use App\Services\ImageService;
use App\Jobs\CompressImageJob;
use App\Http\Requests\CekUpdateStatusTiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function __construct(protected \App\Services\Ticket\TicketService $ticketService) {}

    public function index(Request $request)
    {
        $query = Ticket::where('reported_by', $request->user()->id)
            ->with(['asset', 'technician', 'reporter'])
            ->orderBy('created_at', 'desc');

        $formatTicket = function ($ticket) {
            return [
                'id'          => $ticket->id,
                'title'       => $ticket->title,
                'description' => $ticket->description,
                'type'        => $ticket->type,
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
        };

        if ($request->has('page') || $request->has('limit')) {
            $perPage = (int) ($request->limit ?? 15);
            $paginated = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'data'         => collect($paginated->items())->map($formatTicket),
                    'last_page'    => $paginated->lastPage(),
                    'total'        => $paginated->total(),
                    'current_page' => $paginated->currentPage(),
                ],
            ], 200);
        }

        $tickets = $query->take(100)->get()->map($formatTicket);

        return response()->json([
            'status' => 'success',
            'data'   => $tickets,
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'category'    => 'required|in:Service,Troubleshooting',
            'priority'    => 'nullable|in:Rendah,Sedang,Tinggi',
            'asset_id'    => 'nullable|exists:assets,id',
            'foto'        => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        $ticket = $this->ticketService->createTicket(
            $validated,
            $request->user(),
            $request->file('foto')
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Laporan berhasil dikirim.',
            'data'    => [
                'ticket_id' => $ticket->id,
            ]
        ], 201);
    }

    public function myTickets(Request $request)
    {
        $query = Ticket::where('reported_by', $request->user()->id)
            ->with(['asset', 'technician', 'reporter'])
            ->orderBy('created_at', 'desc');

        if ($request->has('page') || $request->has('limit')) {
            $perPage = (int) ($request->limit ?? 15);
            $paginated = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'data'         => collect($paginated->items())->map(function ($ticket) {
                        return [
                            'id'          => $ticket->id,
                            'title'       => $ticket->title,
                            'description' => $ticket->description,
                            'type'        => $ticket->type,
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
                    }),
                    'last_page'    => $paginated->lastPage(),
                    'total'        => $paginated->total(),
                    'current_page' => $paginated->currentPage(),
                ],
            ], 200);
        }

        $tickets = $query->take(100)->get()->map(function ($ticket) {
            return [
                'id'          => $ticket->id,
                'title'       => $ticket->title,
                'description' => $ticket->description,
                'type'        => $ticket->type,
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

    public function adminIndex(Request $request)
    {
        $status = $request->query('status');
        $user = $request->user();

        $roleId = $request->header('X-Active-Role-ID') ?? $request->query('role_id');
        if (!$roleId) {
            if ($user->hasRole(User::ROLE_KETUA_TIM)) {
                $roleId = User::ROLE_KETUA_TIM;
            } elseif ($user->hasRole(User::ROLE_TEKNISI)) {
                $roleId = User::ROLE_TEKNISI;
            } else {
                $roleId = User::ROLE_ADMIN;
            }
        }

        $query = Ticket::with(['asset', 'reporter', 'technician'])->forRole($user, (int) $roleId);

        if ($status) {
            $map = [
                'menunggu' => [
                    Ticket::STATUS_MENUNGGU_PENGELOLA,
                    Ticket::STATUS_MENUNGGU_BIAYA
                ],
                'proses' => [
                    Ticket::STATUS_KE_KETUA_TIM,
                    Ticket::STATUS_KE_TEKNISI,
                    Ticket::STATUS_IN_PROGRESS,
                    Ticket::STATUS_APPROVED
                ],
                'selesai' => [
                    Ticket::STATUS_SELESAI,
                    Ticket::STATUS_DIBATALKAN
                ],
            ];

            if (isset($map[$status])) {
                $query->whereIn('status', $map[$status]);
            } else {
                $query->where('status', $status);
            }
        }

        $query->orderBy('created_at', 'desc');

        $formatTicket = function ($ticket) {
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
        };

        if ($request->has('page') || $request->has('limit')) {
            $perPage = (int) ($request->limit ?? 15);
            $paginated = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'data'         => collect($paginated->items())->map($formatTicket),
                    'last_page'    => $paginated->lastPage(),
                    'total'        => $paginated->total(),
                    'current_page' => $paginated->currentPage(),
                ],
            ], 200);
        }

        $tickets = $query->take(100)->get()->map($formatTicket);

        return response()->json([
            'status' => 'success',
            'data'   => $tickets,
        ], 200);
    }

    public function updateStatus(CekUpdateStatusTiket $request, int $id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) {
            return response()->json(['status' => 'error', 'message' => 'Tiket tidak ditemukan'], 404);
        }

        $activeRoleId = $request->header('X-Active-Role-ID') ?? $request->query('role_id');

        $this->ticketService->updateStatus(
            $ticket,
            $request->validated(),
            $request->user(),
            $activeRoleId ? (int) $activeRoleId : null
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Status tiket berhasil diupdate.',
        ], 200);
    }

    public function maintenanceHistory(Request $request)
    {
        $user   = $request->user();
        $userId = $user->id;
        $limit  = (int) ($request->query('limit', 20));
        $page   = (int) ($request->query('page', 1));
        $bulan  = $request->query('bulan');

        $roleId = $request->header('X-Active-Role-ID') ?? $request->query('role_id');
        if ($roleId) {
            $isKetuaTim = ((int) $roleId) === 7;
        } else {
            $isKetuaTim = $user->hasRole(7);
        }

        $query = Ticket::with(['asset.room', 'asset.deviceName', 'reporter', 'technician', 'teamLeader'])
            ->whereNotNull('resolved_at')

            ->where(function ($q) use ($userId, $isKetuaTim) {

                $q->where('technician_id', $userId);

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

    public function leaderboard(Request $request)
    {
        $data = \App\Services\Gamification\GamificationService::getLeaderboard();
        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ], 200);
    }
}

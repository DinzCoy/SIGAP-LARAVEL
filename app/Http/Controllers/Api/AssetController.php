<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Services\Assets\AssetService;
use App\Http\Requests\Api\StoreAssetRequest;
use App\Http\Requests\Api\TransferAssetRequest;
use App\Http\Requests\Api\UpdateAssetRoomRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AssetController extends Controller
{
    public function __construct(protected AssetService $assetService) {}

    public function scan(Request $request)
    {
        $request->validate([
            'asset_code' => 'required|string'
        ]);

        $code = $request->asset_code;
        $id = null;
        if (preg_match('/assets\/(\d+)\/scan/', $code, $matches)) {
            $id = $matches[1];
        }

        if ($id) {
            $asset = Asset::with([
                'room', 'deviceName', 'user',
                'activeLoan.borrower', 'pendingLoan.borrower',
                'tickets' => fn($q) => $q->whereNotNull('resolved_at')->orderByDesc('resolved_at')->limit(1),
            ])->find($id);
        } else {

            $asset = Asset::where('bmn_number', $code)
                ->with([
                    'room', 'deviceName', 'user',
                    'activeLoan.borrower', 'pendingLoan.borrower',
                    'tickets' => fn($q) => $q->whereNotNull('resolved_at')->orderByDesc('resolved_at')->limit(1),
                ])
                ->first();

            if (!$asset && is_numeric($code)) {
                $asset = Asset::with([
                    'room', 'deviceName', 'user',
                    'activeLoan.borrower', 'pendingLoan.borrower',
                    'tickets' => fn($q) => $q->whereNotNull('resolved_at')->orderByDesc('resolved_at')->limit(1),
                ])->find($code);
            }
        }

        if (!$asset) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aset tidak ditemukan. Pastikan QR Code valid.'
            ], 404);
        }

        $activeLoan = $asset->activeLoan ?? $asset->pendingLoan;

        $lastTicket = $asset->tickets->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $asset->id,
                'name' => $asset->deviceName ? $asset->deviceName->name : ($asset->brand ?? 'Aset ' . $asset->id),
                'asset_code' => $asset->bmn_number ?: null,
                'merk' => $asset->brand ?? ($asset->deviceName ? $asset->deviceName->brand : '-'),
                'status_kondisi' => $asset->status_kondisi,
                'room' => $asset->room ? $asset->room->name : 'Tidak ada ruangan',
                'category' => 'Aset BMN',
                'pemegang' => $activeLoan && $activeLoan->borrower ? $activeLoan->borrower->name : ($asset->user ? $asset->user->name : '-'),
                'tanggal_perolehan' => $asset->deviceName && $asset->deviceName->procurement_date
                    ? \Carbon\Carbon::parse($asset->deviceName->procurement_date)->format('d M Y')
                    : ($asset->allocated_at ? \Carbon\Carbon::parse($asset->allocated_at)->format('d M Y') : null),
                'riwayat_servis' => $lastTicket ? [
                    'id'           => $lastTicket->id,
                    'judul'        => $lastTicket->title,
                    'tanggal'      => $lastTicket->resolved_at ? $lastTicket->resolved_at->format('d M Y') : null,
                ] : null,
                'loan_status' => $activeLoan ? $activeLoan->status : 'available',
                'active_loan' => $activeLoan ? [
                    'id'       => $activeLoan->id,
                    'borrower' => $activeLoan->borrower ? $activeLoan->borrower->name : '-',
                    'due_date' => $activeLoan->due_date ? $activeLoan->due_date->format('d M Y') : null,
                ] : null
            ]
        ], 200);
    }

    public function index(Request $request)
    {
        $assets = Asset::with(['room', 'deviceName'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $assets
        ], 200);
    }

    public function userAssets(Request $request)
    {
        $assets = Asset::with(['room', 'deviceName'])
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $assets
        ], 200);
    }

    public function store(StoreAssetRequest $request)
    {
        $asset = $this->assetService->registerAsset($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Aset berhasil didaftarkan.',
            'data' => $asset
        ], 201);
    }

    public function transfer(TransferAssetRequest $request)
    {
        $asset = Asset::findOrFail($request->asset_id);

        $result = $this->assetService->requestTransfer(
            $asset,
            Auth::user(),
            $request->reason
        );

        if ($result['status'] === 'error') {
            return response()->json([
                'status'  => 'error',
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'status'  => 'success',
            'message' => $result['message'],
            'data'    => ['loan_id' => $result['loan']->id],
        ], 201);
    }

    public function available(Request $request)
    {
        $search   = $request->query('search');
        $kategori = $request->query('kategori');

        $assets = Asset::with(['room', 'deviceName'])

            ->whereIn('status_kondisi', ['Baik', 'Berfungsi'])

            ->whereDoesntHave('loans', fn($q) =>
                $q->whereIn('status', [AssetLoan::STATUS_ACTIVE, AssetLoan::STATUS_PENDING])
            )

            ->when($search, fn($q, $s) =>
                $q->where(fn($inner) =>
                    $inner->where('brand', 'like', "%{$s}%")
                          ->orWhereHas('deviceName', fn($d) =>
                              $d->where('name', 'like', "%{$s}%")
                          )
                          ->orWhere('bmn_number', 'like', "%{$s}%")
                )
            )
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn(Asset $asset) => [
                'id'            => $asset->id,
                'nama'          => $asset->deviceName ? $asset->deviceName->name : ($asset->brand ?? 'Aset ' . $asset->id),
                'merek'         => $asset->brand ?? ($asset->deviceName ? $asset->deviceName->brand : '-'),
                'kode'          => $asset->bmn_number ?: null,
                'lokasi'        => $asset->room ? $asset->room->name : 'Tidak ada ruangan',
                'kondisi'       => $asset->status_kondisi,
                'kategori'      => $asset->deviceName ? ($asset->deviceName->name ?? 'Aset BMN') : 'Aset BMN',
                'loan_status'   => 'available',
            ]);

        return response()->json([
            'status' => 'success',
            'data'   => $assets,
        ], 200);
    }

    public function listRooms()
    {
        $rooms = \App\Models\Room::orderBy('name')->get(['id', 'name']);
        return response()->json([
            'status' => 'success',
            'data' => $rooms
        ], 200);
    }

    public function updateRoom(UpdateAssetRoomRequest $request, int $id)
    {
        $activeRole = $request->header('X-Active-Role-ID') ?? session('active_role_id');
        if ((int)$activeRole !== \App\Models\User::ROLE_ADMIN) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya Administrator yang diizinkan untuk mengubah ruangan aset.'
            ], 403);
        }

        $asset = Asset::findOrFail($id);

        $asset = $this->assetService->updateAssetRoom(
            $asset,
            $request->room_id,
            $request->new_room_name,
            Auth::user()
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Ruangan aset berhasil diperbarui.',
            'data' => [
                'id' => $asset->id,
                'room' => $asset->room ? $asset->room->name : 'Tidak ada ruangan',
                'room_id' => $asset->room_id,
            ]
        ], 200);
    }
}

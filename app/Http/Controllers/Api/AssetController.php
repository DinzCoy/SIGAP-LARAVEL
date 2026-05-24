<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Services\Assets\AssetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            // Coba cari berdasarkan bmn_number terlebih dahulu
            $asset = Asset::where('bmn_number', $code)
                ->with([
                    'room', 'deviceName', 'user',
                    'activeLoan.borrower', 'pendingLoan.borrower',
                    'tickets' => fn($q) => $q->whereNotNull('resolved_at')->orderByDesc('resolved_at')->limit(1),
                ])
                ->first();
                
            // Jika tidak ditemukan dan kodenya numerik, coba cari berdasarkan ID sebagai fallback
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

        // Cek apakah aset sedang dipinjam atau sedang proses pengajuan pinjam
        $activeLoan = $asset->activeLoan ?? $asset->pendingLoan;

        // Ambil riwayat servis terakhir
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

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'asset_code' => 'required|string|unique:assets,bmn_number',
            'status_kondisi' => 'required|string',
            'room_id' => 'nullable|exists:rooms,id',
            'category_id' => 'nullable|exists:asset_categories,id',
        ]);

        $asset = Asset::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Aset berhasil didaftarkan.',
            'data' => $asset
        ], 201);
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'reason' => 'required|string',
        ]);

        $asset = Asset::findOrFail($request->asset_id);
        $user = Auth::user();

        if ($asset->user_id === $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aset ini sudah tercatat atas nama Anda.'
            ], 400);
        }

        $this->assetService->takeover($asset, $user, $request->reason);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Permintaan mutasi aset berhasil dikirim.'
        ], 200);
    }

    /**
     * GET /assets/available
     * Katalog aset yang sedang tidak dipinjam dan kondisinya baik.
     * Bisa diakses semua role (User, Admin, Teknisi).
     */
    public function available(Request $request)
    {
        $search   = $request->query('search');
        $kategori = $request->query('kategori');

        $assets = Asset::with(['room', 'deviceName'])
            // Hanya aset kondisi baik/berfungsi
            ->whereIn('status_kondisi', ['Baik', 'Berfungsi'])
            // Tidak memiliki peminjaman aktif maupun pending
            ->whereDoesntHave('loans', fn($q) =>
                $q->whereIn('status', [AssetLoan::STATUS_ACTIVE, AssetLoan::STATUS_PENDING])
            )
            // Filter pencarian opsional
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
}


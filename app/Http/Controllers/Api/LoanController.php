<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Services\Notification\Assets\AssetNotificationService;
use App\Services\Notification\Assets\AssetLoanApprovedNotification;
use App\Services\Notification\Assets\AssetLoanRejectedNotification;
use App\Services\Notification\Assets\AssetReturnedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LoanController extends Controller
{
    // ─── BORROWER ────────────────────────────────────────────────────────────

    /**
     * POST /loans
     * Ajukan peminjaman aset (setelah scan QR).
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_id'    => 'required|exists:assets,id',
            'loan_reason' => 'required|string',
            'due_date'    => 'required|date|after:today',
        ]);

        $asset = Asset::find($request->asset_id);

        if (!in_array($asset->status_kondisi, ['Berfungsi', 'Baik'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Aset tidak dalam kondisi Baik/Berfungsi, tidak dapat dipinjam.',
            ], 400);
        }

        $activeLoan = AssetLoan::where('asset_id', $asset->id)
            ->whereIn('status', [AssetLoan::STATUS_PENDING, AssetLoan::STATUS_ACTIVE])
            ->exists();

        if ($activeLoan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Aset ini sedang dipinjam atau dalam proses peminjaman oleh user lain.',
            ], 400);
        }

        $loan = AssetLoan::create([
            'asset_id'    => $request->asset_id,
            'borrower_id' => $request->user()->id,
            'loan_reason' => $request->loan_reason,
            'due_date'    => Carbon::parse($request->due_date)->endOfDay(),
            'status'      => AssetLoan::STATUS_PENDING,
        ]);

        // ✉️ Kirim notifikasi in-app ke pemilik aset / admin
        try {
            AssetNotificationService::kirim($loan);
        } catch (\Exception $e) {
            Log::warning('Gagal kirim notif permintaan pinjam: ' . $e->getMessage());
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Pengajuan peminjaman berhasil dikirim. Menunggu persetujuan.',
            'data'    => ['loan_id' => $loan->id],
        ], 201);
    }

    /**
     * GET /user/loans
     * Riwayat pinjaman milik user yang sedang login.
     */
    public function myLoans(Request $request)
    {
        $loans = AssetLoan::where('borrower_id', $request->user()->id)
            ->with(['asset.deviceName', 'lender'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn(AssetLoan $loan) => $this->formatLoan($loan));

        return response()->json([
            'status' => 'success',
            'data'   => ['loans' => $loans],
        ], 200);
    }

    // ─── ADMIN ───────────────────────────────────────────────────────────────

    /**
     * GET /admin/loans?status=menunggu|disetujui|selesai&limit=10&page=1
     * Admin: ambil semua pengajuan pinjaman dengan filter status & pagination.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = AssetLoan::with(['asset.deviceName', 'borrower', 'lender'])
            ->orderBy('created_at', 'desc');

        // Jika bukan Admin/Pengelola, HANYA tampilkan pinjaman yang merujuk pada aset miliknya
        if (!$user->isAdminOrPengelola()) {
            $query->whereHas('asset', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        if ($request->has('status') && $request->status !== '') {
            // Frontend bisa kirim: menunggu | disetujui | selesai
            $map = [
                'menunggu'  => [AssetLoan::STATUS_PENDING],
                'disetujui' => [AssetLoan::STATUS_ACTIVE],
                'selesai'   => [AssetLoan::STATUS_RETURNED, AssetLoan::STATUS_REJECTED],
            ];
            $statuses = $map[$request->status] ?? [$request->status];
            $query->whereIn('status', $statuses);
        }

        $perPage    = (int) ($request->limit ?? 10);
        $paginated  = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'data'      => collect($paginated->items())->map(fn($l) => $this->formatLoan($l, true)),
                'last_page' => $paginated->lastPage(),
                'total'     => $paginated->total(),
            ],
        ], 200);
    }

    /**
     * POST /admin/loans/{id}/approve
     * Admin: setujui atau tolak pengajuan.
     * Body: { status: "disetujui" | "ditolak", catatan_admin?: string }
     */
    public function approve(Request $request, int $id)
    {
        $request->validate([
            'status'        => 'required|in:disetujui,ditolak',
            'catatan_admin' => 'nullable|string',
        ]);

        $loan = AssetLoan::with('asset')->findOrFail($id);
        $user = $request->user();

        // Otorisasi: Hanya Admin/Pengelola atau pemilik aset yang boleh menyetujui
        if (!$user->isAdminOrPengelola() && $loan->asset->user_id !== $user->id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki akses untuk memproses pinjaman ini.',
            ], 403);
        }

        if ($loan->status !== AssetLoan::STATUS_PENDING) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pengajuan ini sudah diproses sebelumnya.',
            ], 400);
        }

        if ($request->status === 'disetujui') {
            $loan->update([
                'status'      => AssetLoan::STATUS_ACTIVE,
                'lender_id'   => $request->user()->id,
                'approved_at' => now(),
                'loaned_at'   => now(),
            ]);
            $message = 'Peminjaman telah disetujui.';

            // ✉️ Beritahu peminjam: pengajuannya disetujui (in-app + push FCM)
            try {
                AssetLoanApprovedNotification::kirim($loan);
            } catch (\Exception $e) {
                Log::warning('Gagal kirim notif disetujui: ' . $e->getMessage());
            }
        } else {
            $loan->update([
                'status'      => AssetLoan::STATUS_REJECTED,
                'rejected_at' => now(),
            ]);
            $message = 'Peminjaman telah ditolak.';

            // ✉️ Beritahu peminjam: pengajuannya ditolak (in-app + push FCM)
            try {
                AssetLoanRejectedNotification::kirim($loan);
            } catch (\Exception $e) {
                Log::warning('Gagal kirim notif ditolak: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => $message,
        ], 200);
    }

    /**
     * POST /loans/{id}/return
     * Kembalikan aset (borrower atau admin).
     * Body: { kondisi_kembali: string }
     */
    public function returnAsset(Request $request, int $id)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
        ]);

        $loan = AssetLoan::findOrFail($id);
        $user = $request->user();

        if ($loan->status !== AssetLoan::STATUS_ACTIVE) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Aset tidak sedang dalam status dipinjam.',
            ], 400);
        }

        // Hanya borrower atau admin yang boleh mengembalikan
        $isAdmin    = $user->hasRole(2);
        $isBorrower = $loan->borrower_id === $user->id;

        if (!$isAdmin && !$isBorrower) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak berhak mengembalikan aset ini.',
            ], 403);
        }

        $loan->update([
            'status'      => AssetLoan::STATUS_RETURNED,
            'returned_at' => now(),
        ]);

        // ✉️ Beritahu pemilik/admin: aset sudah dikembalikan
        try {
            AssetReturnedNotification::kirim($loan);
        } catch (\Exception $e) {
            Log::warning('Gagal kirim notif pengembalian: ' . $e->getMessage());
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Aset berhasil dikembalikan.',
        ], 200);
    }

    // ─── HELPER ──────────────────────────────────────────────────────────────

    private function formatLoan(AssetLoan $loan, bool $includeUser = false): array
    {
        $assetName = 'Unknown Asset';
        $assetCode = '-';
        
        if ($loan->asset) {
            $assetName = $loan->asset->deviceName 
                ? $loan->asset->deviceName->name 
                : ($loan->asset->brand ?? 'Aset ' . $loan->asset->id);
            $assetCode = $loan->asset->bmn_number ?: '-';
        }

        $data = [
            'id'          => $loan->id,
            'asset_id'    => $loan->asset_id,
            'asset_name'  => $assetName,
            'asset_code'  => $assetCode,
            'loan_reason' => $loan->loan_reason,
            'status'      => $loan->status,
            'start_date'  => $loan->created_at?->toISOString(),
            'due_date'    => $loan->due_date?->toISOString(),
            'returned_at' => $loan->returned_at?->toISOString(),
            'is_overdue'  => $loan->isOverdue(),
            'lender_name' => $loan->lender ? $loan->lender->name : 'Admin/Pengelola Aset',
            'created_at'  => $loan->created_at?->toISOString(),
        ];

        if ($includeUser) {
            $data['user_name'] = $loan->borrower ? $loan->borrower->name : '-';
            $data['user_id']   = $loan->borrower_id;
        }

        return $data;
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Services\Assets\AssetService;
use App\Http\Requests\Api\StoreLoanRequest;
use App\Http\Requests\Api\ApproveLoanRequest;
use App\Http\Requests\Api\ReturnLoanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function __construct(protected AssetService $assetService) {}

    public function store(StoreLoanRequest $request)
    {
        $asset = Asset::findOrFail($request->asset_id);

        $error = $this->assetService->requestLoan(
            $asset,
            $request->user(),
            $request->loan_reason,
            $request->due_date
        );

        if ($error) {
            return response()->json([
                'status'  => 'error',
                'message' => $error,
            ], 400);
        }

        $loan = AssetLoan::where('asset_id', $asset->id)
            ->where('borrower_id', $request->user()->id)
            ->where('status', AssetLoan::STATUS_PENDING)
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'status'  => 'success',
            'message' => 'Pengajuan peminjaman berhasil dikirim. Menunggu persetujuan.',
            'data'    => ['loan_id' => $loan?->id],
        ], 201);
    }

    public function myLoans(Request $request)
    {
        $query = AssetLoan::where('borrower_id', $request->user()->id)
            ->with(['asset.deviceName', 'lender'])
            ->orderBy('created_at', 'desc');

        if ($request->has('page') || $request->has('limit')) {
            $perPage = (int) ($request->limit ?? 10);
            $paginated = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'loans'        => collect($paginated->items())->map(fn(AssetLoan $loan) => $this->formatLoan($loan)),
                    'last_page'    => $paginated->lastPage(),
                    'total'        => $paginated->total(),
                    'current_page' => $paginated->currentPage(),
                ],
            ], 200);
        }

        $loans = $query->take(100)->get()->map(fn(AssetLoan $loan) => $this->formatLoan($loan));

        return response()->json([
            'status' => 'success',
            'data'   => ['loans' => $loans],
        ], 200);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = AssetLoan::with(['asset.deviceName', 'borrower', 'lender'])
            ->orderBy('created_at', 'desc');

        if (!$user->isAdminOrPengelola()) {
            $query->whereHas('asset', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        if ($request->has('status') && $request->status !== '') {
            $map = [
                'menunggu'  => [AssetLoan::STATUS_PENDING],
                'disetujui' => [AssetLoan::STATUS_ACTIVE],
                'selesai'   => [AssetLoan::STATUS_RETURNED, AssetLoan::STATUS_REJECTED],

                'mutasi'    => null,
            ];

            if ($request->status === 'mutasi') {
                $query->where('type', AssetLoan::TYPE_MUTASI);
            } else {
                $statuses = $map[$request->status] ?? [$request->status];
                $query->whereIn('status', $statuses)
                      ->where('type', AssetLoan::TYPE_PINJAM);
            }
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

    public function approve(ApproveLoanRequest $request, int $id)
    {
        $loan = AssetLoan::with('asset')->findOrFail($id);
        $user = $request->user();

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
            $this->assetService->approveLoan($loan, $user);
            $message = $loan->type === AssetLoan::TYPE_MUTASI
                ? 'Alokasi permanen telah disetujui. Kepemilikan aset sudah dipindahkan.'
                : 'Peminjaman telah disetujui.';
        } else {
            $this->assetService->rejectLoan($loan);
            $message = 'Peminjaman telah ditolak.';
        }

        return response()->json([
            'status'  => 'success',
            'message' => $message,
        ], 200);
    }

    public function returnAsset(ReturnLoanRequest $request, int $id)
    {
        $loan = AssetLoan::findOrFail($id);
        $user = $request->user();

        if ($loan->status !== AssetLoan::STATUS_ACTIVE) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Aset tidak sedang dalam status dipinjam.',
            ], 400);
        }

        $isAdmin    = $user->hasRole(2);
        $isBorrower = $loan->borrower_id === $user->id;

        if (!$isAdmin && !$isBorrower) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak berhak mengembalikan aset ini.',
            ], 403);
        }

        $this->assetService->returnLoan($loan->asset, $loan, $user);

        return response()->json([
            'status'  => 'success',
            'message' => 'Aset berhasil dikembalikan.',
        ], 200);
    }

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
            'type'        => $loan->type ?? AssetLoan::TYPE_PINJAM,
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
            $data['user_photo'] = $loan->borrower && $loan->borrower->photo_path 
                ? url('storage/' . $loan->borrower->photo_path) 
                : null;
            $data['asset_owner'] = $loan->asset && $loan->asset->user ? $loan->asset->user->name : 'Admin';
        }

        return $data;
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetLoan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'loan_reason' => 'required|string',
            'due_date' => 'required|date|after:today', // Harus dikembalikan minimal besok
        ]);

        $asset = Asset::find($request->asset_id);

        // Validasi apakah aset bisa dipinjam
        // Asumsi: Hanya aset dengan status "Berfungsi" dan tidak sedang dipinjam yang bisa diajukan
        if ($asset->status_kondisi !== 'Berfungsi') {
            return response()->json([
                'status' => 'error',
                'message' => 'Aset tidak dalam kondisi Berfungsi, tidak dapat dipinjam.'
            ], 400);
        }

        $activeLoan = AssetLoan::where('asset_id', $asset->id)
            ->whereIn('status', [AssetLoan::STATUS_PENDING, AssetLoan::STATUS_ACTIVE])
            ->exists();

        if ($activeLoan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aset ini sedang dipinjam atau dalam proses peminjaman oleh user lain.'
            ], 400);
        }

        // Buat pengajuan peminjaman
        $loan = AssetLoan::create([
            'asset_id' => $request->asset_id,
            'borrower_id' => $request->user()->id,
            // lender_id akan diisi nanti saat di-approve oleh pengelola barang / admin
            'loan_reason' => $request->loan_reason,
            'due_date' => Carbon::parse($request->due_date)->endOfDay(),
            'status' => AssetLoan::STATUS_PENDING,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan peminjaman berhasil dikirim. Menunggu persetujuan.',
            'data' => [
                'loan_id' => $loan->id,
            ]
        ], 201);
    }

    /**
     * Get user's active/pending loans for mobile dashboard.
     */
    public function myLoans(Request $request)
    {
        $loans = AssetLoan::where('borrower_id', $request->user()->id)
            ->with(['asset'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($loan) {
                return [
                    'id' => $loan->id,
                    'asset_name' => $loan->asset ? $loan->asset->name : 'Unknown Asset',
                    'asset_code' => $loan->asset ? $loan->asset->asset_code : '-',
                    'status' => $loan->status,
                    'due_date' => $loan->due_date ? $loan->due_date->format('d M Y') : '-',
                    'is_overdue' => $loan->isOverdue()
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'loans' => $loans
            ]
        ], 200);
    }
}

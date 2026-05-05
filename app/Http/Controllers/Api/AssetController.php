<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetLoan;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function scan(Request $request)
    {
        $request->validate([
            'asset_code' => 'required|string'
        ]);
        // Coba cari aset berdasarkan asset_code (kode unik yang biasa ada di QR)
        $asset = Asset::where('asset_code', $request->asset_code)
            ->with(['room', 'category'])
            ->first();

        if (!$asset) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aset tidak ditemukan. Pastikan QR Code valid.'
            ], 404);
        }

        // Cek apakah aset sedang dipinjam atau sedang proses pengajuan pinjam
        $activeLoan = AssetLoan::where('asset_id', $asset->id)
            ->whereIn('status', [AssetLoan::STATUS_PENDING, AssetLoan::STATUS_ACTIVE])
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $asset->id,
                'name' => $asset->name,
                'asset_code' => $asset->asset_code,
                'merk' => $asset->merk ?? '-',
                'status_kondisi' => $asset->status_kondisi,
                'room' => $asset->room ? $asset->room->name : 'Tidak ada ruangan',
                'category' => $asset->category ? $asset->category->name : 'Lainnya',
                'loan_status' => $activeLoan ? $activeLoan->status : 'available',
                'active_loan' => $activeLoan ? [
                    'id' => $activeLoan->id,
                    'borrower' => $activeLoan->borrower ? $activeLoan->borrower->name : '-',
                    'due_date' => $activeLoan->due_date ? $activeLoan->due_date->format('d M Y') : null,
                ] : null
            ]
        ], 200);
    }
}

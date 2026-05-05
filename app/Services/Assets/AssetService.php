<?php

namespace App\Services\Assets;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\AssetMovementLog;
use App\Models\User;
use App\Services\Notification\Assets\AssetNotificationService;
use App\Services\Notification\Assets\AssetLoanApprovedNotification;
use App\Services\Notification\Assets\AssetLoanRejectedNotification;
use App\Services\Notification\Assets\AssetReturnedNotification;
use Illuminate\Support\Facades\DB;

class AssetService
{
    /**
     * Menautkan MAC address PC ke aset BMN.
     * Mengembalikan pesan error (string) atau null jika sukses.
     */
    public function linkDevice(Asset $asset, string $macAddress): ?string
    {
        if (Asset::where('mac_address', $macAddress)->exists()) {
            return 'MAC Address ini sudah terhubung dengan aset lain!';
        }

        $asset->update(['mac_address' => $macAddress]);

        return null;
    }

    /**
     * Mengajukan permintaan peminjaman aset sementara.
     * Mengembalikan pesan error (string) atau null jika sukses.
     */
    public function requestLoan(Asset $asset, User $borrower, string $reason): ?string
    {
        if ($asset->user_id && $asset->user_id === $borrower->id) {
            return 'Aset ini sudah milik Anda, tidak perlu dipinjam.';
        }

        if ($asset->isPendingLoan()) {
            return 'Aset ini sedang dalam proses permintaan peminjaman.';
        }

        if ($asset->isOnLoan()) {
            return 'Aset ini sedang dipinjam oleh orang lain.';
        }

        $peminjaman = AssetLoan::create([
            'asset_id'    => $asset->id,
            'lender_id'   => $asset->user_id,
            'borrower_id' => $borrower->id,
            'loan_reason' => $reason,
            'loaned_at'   => now(),
            'status'      => AssetLoan::STATUS_PENDING,
        ]);

        // Kirim notifikasi ke pemilik atau Admin/Pengelola Aset
        AssetNotificationService::kirim($peminjaman);

        return null;
    }

    /**
     * Menyetujui permintaan peminjaman aset.
     */
    public function approveLoan(AssetLoan $loan): void
    {
        DB::transaction(function () use ($loan) {
            $loan->update([
                'status'      => AssetLoan::STATUS_ACTIVE,
                'approved_at' => now(),
            ]);

            AssetMovementLog::create([
                'asset_id'    => $loan->asset_id,
                'old_user_id' => $loan->lender_id,
                'new_user_id' => $loan->borrower_id,
                'old_room_id' => $loan->asset->room_id,
                'new_room_id' => $loan->asset->room_id,
                'action_type' => 'QR Loan',
                'reason'      => 'Dipinjam oleh ' . ($loan->borrower?->name ?? 'Unknown')
                               . '. Alasan: ' . ($loan->loan_reason ?? '-'),
            ]);
        });

        $loan->load(['asset.deviceName', 'lender', 'borrower']);
        $loan->borrower?->notify(new AssetLoanApprovedNotification($loan));
    }

    /**
     * Menolak permintaan peminjaman aset.
     */
    public function rejectLoan(AssetLoan $loan): void
    {
        $loan->update([
            'status'      => AssetLoan::STATUS_REJECTED,
            'rejected_at' => now(),
        ]);

        $loan->load(['asset.deviceName', 'lender', 'borrower']);
        $loan->borrower?->notify(new AssetLoanRejectedNotification($loan));
    }

    /**
     * Mengembalikan aset pinjaman ke pemilik asli.
     */
    public function returnLoan(Asset $asset, AssetLoan $loan, User $returnedBy): void
    {
        DB::transaction(function () use ($asset, $loan, $returnedBy) {
            $loan->update([
                'returned_at' => now(),
                'status'      => AssetLoan::STATUS_RETURNED,
            ]);

            AssetMovementLog::create([
                'asset_id'    => $asset->id,
                'old_user_id' => $loan->borrower_id,
                'new_user_id' => $loan->lender_id,
                'old_room_id' => $asset->room_id,
                'new_room_id' => $asset->room_id,
                'action_type' => 'QR Return',
                'reason'      => 'Dikembalikan oleh ' . $returnedBy->name,
            ]);
        });

        // Kirim notifikasi pengembalian
        AssetReturnedNotification::kirim($loan);
    }

    /**
     * Proses serah terima aset permanen via QR Code (Mutasi).
     */
    public function takeover(Asset $asset, User $newUser): void
    {
        DB::transaction(function () use ($asset, $newUser) {
            // Tutup peminjaman aktif jika ada
            $activeLoan = $asset->activeLoan;
            if ($activeLoan) {
                $activeLoan->update([
                    'returned_at' => now(),
                    'status'      => AssetLoan::STATUS_RETURNED,
                ]);
            }

            AssetMovementLog::create([
                'asset_id'    => $asset->id,
                'old_user_id' => $asset->user_id,
                'new_user_id' => $newUser->id,
                'old_room_id' => $asset->room_id,
                'new_room_id' => $asset->room_id,
                'action_type' => 'QR Transfer',
                'reason'      => 'Serah terima permanen via QR Code oleh ' . $newUser->name,
            ]);

            $asset->update([
                'user_id'      => $newUser->id,
                'allocated_at' => now(),
            ]);
        });
    }
}

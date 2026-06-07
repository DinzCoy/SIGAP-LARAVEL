<?php

namespace App\Services\Assets;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\AssetMovementLog;
use App\Models\User;
use App\Models\DeviceName;
use App\Models\Room;
use App\Services\Notification\Assets\AssetNotificationService;
use App\Services\Notification\Assets\AssetLoanApprovedNotification;
use App\Services\Notification\Assets\AssetLoanRejectedNotification;
use App\Services\Notification\Assets\AssetReturnedNotification;
use App\Services\Notification\Assets\AssetTransferNotification;
use Illuminate\Support\Facades\DB;

class AssetService
{

    public function linkDevice(Asset $asset, string $macAddress): ?string
    {
        if (Asset::where('mac_address', $macAddress)->exists()) {
            return 'MAC Address ini sudah terhubung dengan aset lain!';
        }

        $asset->update(['mac_address' => $macAddress]);

        return null;
    }

    public function requestLoan(Asset $asset, User $borrower, string $reason, $dueDate = null): ?string
    {
        if (!in_array($asset->status_kondisi, ['Berfungsi', 'Baik'])) {
            return 'Aset tidak dalam kondisi Baik/Berfungsi, tidak dapat dipinjam.';
        }

        if ($asset->user_id && $asset->user_id === $borrower->id) {
            return 'Aset ini sudah milik Anda, tidak perlu dipinjam.';
        }

        $activeLoan = AssetLoan::where('asset_id', $asset->id)
            ->whereIn('status', [AssetLoan::STATUS_PENDING, AssetLoan::STATUS_ACTIVE])
            ->exists();

        if ($activeLoan) {
            return 'Aset ini sedang dipinjam atau dalam proses peminjaman oleh user lain.';
        }

        $peminjaman = AssetLoan::create([
            'asset_id'    => $asset->id,
            'lender_id'   => $asset->user_id,
            'borrower_id' => $borrower->id,
            'loan_reason' => $reason,
            'due_date'    => $dueDate ? \Carbon\Carbon::parse($dueDate)->endOfDay() : null,
            'loaned_at'   => now(),
            'status'      => AssetLoan::STATUS_PENDING,
        ]);

        try {
            AssetNotificationService::kirim($peminjaman);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim notif permintaan pinjam: ' . $e->getMessage());
        }

        return null;
    }

    public function approveLoan(AssetLoan $loan, User $approver): void
    {
        DB::transaction(function () use ($loan, $approver) {
            $loan->update([
                'status'      => AssetLoan::STATUS_ACTIVE,
                'lender_id'   => $approver->id,
                'approved_at' => now(),
                'loaned_at'   => now(),
            ]);

            if ($loan->type === AssetLoan::TYPE_MUTASI) {

                $oldUser = $loan->asset->user;
                $loan->asset->update([
                    'user_id'      => $loan->borrower_id,
                    'allocated_at' => now(),
                ]);

                AssetMovementLog::create([
                    'asset_id'    => $loan->asset_id,
                    'old_user_id' => $loan->asset->user_id,
                    'new_user_id' => $loan->borrower_id,
                    'old_room_id' => $loan->asset->room_id,
                    'new_room_id' => $loan->asset->room_id,
                    'action_type' => 'QR Transfer',
                    'reason'      => 'Serah terima permanen disetujui oleh Admin: ' . $approver->name
                                   . '. Alasan: ' . ($loan->loan_reason ?? '-'),
                ]);
            } else {
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
            }
        });

        $loan->load(['asset.deviceName', 'lender', 'borrower']);

        try {
            AssetLoanApprovedNotification::kirim($loan);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim notif disetujui: ' . $e->getMessage());
        }
    }

    public function rejectLoan(AssetLoan $loan): void
    {
        $loan->update([
            'status'      => AssetLoan::STATUS_REJECTED,
            'rejected_at' => now(),
        ]);

        $loan->load(['asset.deviceName', 'lender', 'borrower']);

        try {
            AssetLoanRejectedNotification::kirim($loan);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim notif ditolak: ' . $e->getMessage());
        }
    }

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

        try {
            AssetReturnedNotification::kirim($loan);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim notif pengembalian: ' . $e->getMessage());
        }
    }

    public function takeover(Asset $asset, User $newUser, ?string $reason = null): void
    {
        $oldUser = $asset->user;

        DB::transaction(function () use ($asset, $newUser, $reason) {

            $activeLoan = $asset->activeLoan;
            if ($activeLoan) {
                $activeLoan->update([
                    'returned_at' => now(),
                    'status'      => AssetLoan::STATUS_RETURNED,
                ]);
            }

            $logReason = 'Serah terima permanen via QR Code oleh ' . $newUser->name;
            if ($reason) {
                $logReason .= '. Alasan: ' . $reason;
            }

            AssetMovementLog::create([
                'asset_id'    => $asset->id,
                'old_user_id' => $asset->user_id,
                'new_user_id' => $newUser->id,
                'old_room_id' => $asset->room_id,
                'new_room_id' => $asset->room_id,
                'action_type' => 'QR Transfer',
                'reason'      => $logReason,
            ]);

            $asset->update([
                'user_id'      => $newUser->id,
                'allocated_at' => now(),
            ]);
        });

        try {
            AssetTransferNotification::kirim($asset, $newUser, $oldUser, $reason);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('FCM/Database Notification Error in Takeover: ' . $e->getMessage());
        }
    }

    public function registerAsset(array $data): Asset
    {
        $kode = $data['kode'] ?? $data['asset_code'] ?? null;
        $nama = $data['nama'] ?? $data['name'] ?? null;
        $merek = $data['merek'] ?? $data['brand'] ?? 'Aset';
        $kategori = $data['kategori'] ?? 'Lainnya';
        $kondisi = $data['kondisi'] ?? $data['status_kondisi'] ?? 'Baik';
        $lokasiName = $data['lokasi'] ?? $data['room_id'] ?? null;

        $deviceName = DeviceName::firstOrCreate([
            'brand' => $merek,
            'name'  => $nama,
        ], [
            'type'  => $kategori,
        ]);

        $room = null;
        if ($lokasiName) {
            if (is_numeric($lokasiName)) {
                $room = Room::find($lokasiName);
            } else {
                $room = Room::firstOrCreate([
                    'name' => $lokasiName,
                ], [
                    'slug' => \Illuminate\Support\Str::slug($lokasiName),
                ]);
            }
        }

        return Asset::create([
            'bmn_number'     => $kode,
            'device_name_id' => $deviceName->id,
            'room_id'        => $room ? $room->id : null,
            'status_kondisi' => $kondisi,
            'brand'          => $merek,
        ]);
    }

    public function requestTransfer(Asset $asset, User $user, string $reason): array
    {

        if ($asset->user_id === $user->id) {
            return [
                'status'  => 'error',
                'message' => 'Aset ini sudah tercatat atas nama Anda.',
                'loan'    => null
            ];
        }

        $existingPending = AssetLoan::where('asset_id', $asset->id)
            ->where('borrower_id', $user->id)
            ->where('type', AssetLoan::TYPE_MUTASI)
            ->where('status', AssetLoan::STATUS_PENDING)
            ->exists();

        if ($existingPending) {
            return [
                'status'  => 'error',
                'message' => 'Anda sudah memiliki pengajuan alokasi yang sedang menunggu persetujuan.',
                'loan'    => null
            ];
        }

        $loan = AssetLoan::create([
            'asset_id'    => $asset->id,
            'borrower_id' => $user->id,
            'lender_id'   => $asset->user_id,
            'loan_reason' => $reason,
            'type'        => AssetLoan::TYPE_MUTASI,
            'status'      => AssetLoan::STATUS_PENDING,
        ]);

        try {
            AssetNotificationService::kirim($loan);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim notif mutasi: ' . $e->getMessage());
        }

        return [
            'status'  => 'success',
            'message' => 'Pengajuan alokasi/mutasi permanen berhasil dikirim. Menunggu persetujuan Admin.',
            'loan'    => $loan
        ];
    }

    public function updateAssetRoom(Asset $asset, ?int $roomId, ?string $newRoomName, User $actor): Asset
    {
        $oldRoomId = $asset->room_id;
        $finalRoomId = $roomId;

        $trimmedNewRoomName = $newRoomName ? trim($newRoomName) : '';

        if ($trimmedNewRoomName !== '') {
            $room = Room::firstOrCreate([
                'name' => $trimmedNewRoomName,
            ], [
                'slug' => \Illuminate\Support\Str::slug($trimmedNewRoomName),
                'sort_order' => (Room::max('sort_order') ?? -1) + 1,
            ]);
            $finalRoomId = $room->id;
        }

        $asset->room_id = $finalRoomId;
        $asset->save();

        if ($oldRoomId != $finalRoomId) {
            AssetMovementLog::create([
                'asset_id'    => $asset->id,
                'old_user_id' => $asset->user_id,
                'new_user_id' => $asset->user_id,
                'old_room_id' => $oldRoomId,
                'new_room_id' => $finalRoomId,
                'action_type' => 'room_change',
                'reason'      => 'Diubah lewat aplikasi mobile oleh Admin: ' . ($actor->name ?? $actor->username),
            ]);
        }

        return $asset->load('room');
    }
}

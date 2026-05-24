<?php

namespace App\Services\Notification\Assets;

use App\Models\Asset;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

/**
 * Notifikasi Serah Terima/Mutasi Aset Permanen
 * Dikirim kepada pemilik lama dan Admin ketika terjadi pengambilalihan/serah terima secara permanen.
 */
class AssetTransferNotification extends Notification
{
    use Queueable;

    protected Asset $asset;
    protected User $newUser;
    protected ?User $oldUser;
    protected ?string $reason;

    public function __construct(Asset $asset, User $newUser, ?User $oldUser, ?string $reason = null)
    {
        $this->asset = $asset;
        $this->newUser = $newUser;
        $this->oldUser = $oldUser;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $namaAset = ($this->asset->deviceName?->brand ?? '') . ' ' . ($this->asset->bmn_number ?? '-');
        $namaPenerima = $this->newUser->name;
        $namaPemberi = $this->oldUser ? $this->oldUser->name : 'Tanpa Pemilik';

        return [
            'tipe'          => 'mutasi_aset',
            'url'           => route('assets.scan', $this->asset->id),
            'judul'         => 'Mutasi Aset Berhasil ✓',
            'pesan'         => "Aset " . trim($namaAset) . " telah berhasil diserahterimakan dari {$namaPemberi} ke {$namaPenerima}.",
            'id_aset'       => $this->asset->id,
            'nama_aset'     => trim($namaAset),
            'penerima'      => $namaPenerima,
            'pemberi'       => $namaPemberi,
            'alasan'        => $this->reason,
            'waktu_mutasi'  => now()->toDateTimeString(),
        ];
    }

    public static function kirim(Asset $asset, User $newUser, ?User $oldUser, ?string $reason = null): void
    {
        $asset->load(['deviceName']);

        $namaAset = trim(($asset->deviceName?->brand ?? '') . ' ' . ($asset->bmn_number ?? '-'));
        $namaPenerima = $newUser->name;
        $namaPemberi = $oldUser ? $oldUser->name : 'Tanpa Pemilik';

        $pushTitle = 'Mutasi Aset Berhasil';
        $pushBody = "Aset {$namaAset} telah diserahterimakan dari {$namaPemberi} ke {$namaPenerima}.";
        $pushData = ['tipe' => 'mutasi_aset', 'asset_id' => (string) $asset->id];

        // 1. Kirim ke pemilik lama (jika ada)
        if ($oldUser && $oldUser->id !== $newUser->id) {
            $oldUser->notify(new self($asset, $newUser, $oldUser, $reason));
            FcmService::send($oldUser, $pushTitle, $pushBody, $pushData);
        }

        // 2. Kirim ke Admin & Pengelola Aset
        $admins = User::whereHas('roles', function ($q) {
            $q->whereIn('roles.id', [User::ROLE_ADMIN, User::ROLE_PENGELOLA_ASET]);
        })->get();

        /** @var User $admin */
        foreach ($admins as $admin) {
            if ($oldUser && $admin->id === $oldUser->id) {
                continue; // Sudah dikirim di langkah 1
            }
            if ($admin->id === $newUser->id) {
                continue; // Penerima mutasi tidak perlu dinotifikasi dirinya sendiri
            }
            $admin->notify(new self($asset, $newUser, $oldUser, $reason));
            FcmService::send($admin, $pushTitle, $pushBody, $pushData);
        }
    }
}

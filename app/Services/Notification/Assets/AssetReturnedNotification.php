<?php

namespace App\Services\Notification\Assets;

use App\Models\AssetLoan;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class AssetReturnedNotification extends Notification
{
    use Queueable;

    protected AssetLoan $peminjaman;

    public function __construct(AssetLoan $peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $namaAset     = ($this->peminjaman->asset?->deviceName?->brand ?? '')
                      . ' ' . ($this->peminjaman->asset?->bmn_number ?? '-');
        $namaPeminjam = $this->peminjaman->borrower?->name ?? 'Pengguna tidak dikenal';
        $fotoPeminjam = $this->peminjaman->borrower?->photo_path
            ? url('storage/' . $this->peminjaman->borrower->photo_path)
            : null;

        return [
            'tipe'          => 'peminjaman_dikembalikan',
            'url'           => route('assets.scan', $this->peminjaman->asset?->id),
            'judul'         => 'Aset Telah Dikembalikan',
            'pesan'         => "{$namaPeminjam} telah mengembalikan aset {$namaAset}.",
            'id_peminjaman' => $this->peminjaman->id,
            'id_aset'       => $this->peminjaman->asset?->id,
            'nama_aset'     => trim($namaAset),
            'nama_peminjam' => $namaPeminjam,
            'foto_peminjam' => $fotoPeminjam,
            'waktu_kembali' => now()->toDateTimeString(),
        ];
    }

    public static function kirim(AssetLoan $peminjaman): void
    {
        $peminjaman->load(['asset.deviceName', 'asset.user', 'borrower']);

        $namaAset    = trim(($peminjaman->asset?->deviceName?->brand ?? '') . ' ' . ($peminjaman->asset?->bmn_number ?? '-'));
        $namaPeminjam = $peminjaman->borrower?->name ?? 'Pengguna';
        $pushTitle   = 'Aset Telah Dikembalikan';
        $pushBody    = "{$namaPeminjam} telah mengembalikan aset {$namaAset}.";

        $imageUrl = null;
        if ($peminjaman->borrower?->photo_path) {
            $imageUrl = url('storage/' . $peminjaman->borrower->photo_path);
        }

        $pushData = [
            'tipe'          => 'peminjaman_dikembalikan',
            'loan_id'       => (string) $peminjaman->id,
            'foto_peminjam' => $imageUrl ?? '',
        ];

        if ($peminjaman->asset?->user_id) {
            $pemilik = $peminjaman->asset->user;
            $pemilik->notify(new self($peminjaman));
            FcmService::send($pemilik, $pushTitle, $pushBody, $pushData, $imageUrl);
        } else {
            $penerima = User::whereHas('roles', function ($q) {
                $q->whereIn('roles.id', [User::ROLE_ADMIN, User::ROLE_PENGELOLA_ASET]);
            })->get();

            NotificationFacade::send($penerima, new self($peminjaman));
            FcmService::sendToMany($penerima, $pushTitle, $pushBody, $pushData, $imageUrl);
        }
    }
}

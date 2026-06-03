<?php

namespace App\Services\Notification\Assets;

use App\Models\AssetLoan;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssetLoanRejectedNotification extends Notification
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
        $namaAset    = ($this->peminjaman->asset?->deviceName?->brand ?? '')
                     . ' ' . ($this->peminjaman->asset?->bmn_number ?? '-');
        $namaPemilik = $this->peminjaman->lender?->name ?? 'Admin/Pengelola Aset';

        return [
            'tipe'          => 'peminjaman_ditolak',
            'url'           => route('assets.scan', $this->peminjaman->asset?->id),
            'judul'         => 'Peminjaman Ditolak',
            'pesan'         => "Maaf, permintaan pinjam aset " . trim($namaAset) . " ditolak oleh {$namaPemilik}.",
            'id_peminjaman' => $this->peminjaman->id,
            'id_aset'       => $this->peminjaman->asset?->id,
            'nama_aset'     => trim($namaAset),
            'nama_pemilik'  => $namaPemilik,
            'waktu_tolak'   => $this->peminjaman->rejected_at?->toDateTimeString(),
        ];
    }

    public static function kirim(AssetLoan $peminjaman): void
    {
        $peminjaman->load(['asset.deviceName', 'borrower', 'lender']);
        if (!$peminjaman->borrower) return;

        $namaAset = trim(($peminjaman->asset?->deviceName?->brand ?? '') . ' ' . ($peminjaman->asset?->bmn_number ?? '-'));
        $rejector = $peminjaman->lender?->name ?? 'Admin/Pengelola Aset';

        $peminjaman->borrower->notify(new self($peminjaman));
        FcmService::send(
            $peminjaman->borrower,
            'Peminjaman Ditolak',
            "Maaf, permintaan pinjam aset {$namaAset} ditolak oleh {$rejector}.",
            ['tipe' => 'peminjaman_ditolak', 'loan_id' => (string) $peminjaman->id]
        );
    }
}

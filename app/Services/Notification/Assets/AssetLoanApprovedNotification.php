<?php

namespace App\Services\Notification\Assets;

use App\Models\AssetLoan;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssetLoanApprovedNotification extends Notification
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
        $fotoPeminjam = $this->peminjaman->borrower?->photo_path
            ? url('storage/' . $this->peminjaman->borrower->photo_path)
            : null;

        return [
            'tipe'          => 'peminjaman_disetujui',
            'url'           => route('assets.scan', $this->peminjaman->asset?->id),
            'judul'         => 'Peminjaman Disetujui ✓',
            'pesan'         => "Permintaan pinjam aset " . trim($namaAset) . " telah disetujui oleh {$namaPemilik}.",
            'id_peminjaman' => $this->peminjaman->id,
            'id_aset'       => $this->peminjaman->asset?->id,
            'nama_aset'     => trim($namaAset),
            'nama_pemilik'  => $namaPemilik,
            'foto_peminjam' => $fotoPeminjam,
            'waktu_setujui' => $this->peminjaman->approved_at?->toDateTimeString(),
        ];
    }

    public static function kirim(AssetLoan $peminjaman): void
    {
        $peminjaman->load(['asset.deviceName', 'borrower', 'lender']);
        if (!$peminjaman->borrower) return;

        $namaAset  = trim(($peminjaman->asset?->deviceName?->brand ?? '') . ' ' . ($peminjaman->asset?->bmn_number ?? '-'));
        $approver  = $peminjaman->lender?->name ?? 'Admin/Pengelola Aset';

        $peminjaman->borrower->notify(new self($peminjaman));

        $imageUrl = $peminjaman->borrower->photo_path
            ? url('storage/' . $peminjaman->borrower->photo_path)
            : null;

        FcmService::send(
            $peminjaman->borrower,
            'Peminjaman Disetujui ✓',
            "Permintaan pinjam aset {$namaAset} telah disetujui oleh {$approver}.",
            [
                'tipe'          => 'peminjaman_disetujui',
                'loan_id'       => (string) $peminjaman->id,
                'foto_peminjam' => $imageUrl ?? '',
            ],
            $imageUrl
        );
    }
}

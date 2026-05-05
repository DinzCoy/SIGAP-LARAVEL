<?php

namespace App\Services\Notification\Assets;

use App\Models\AssetLoan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifikasi Peminjaman Disetujui
 * Dikirim kepada peminjam ketika permintaan peminjaman asetnya disetujui
 * oleh pemilik aset, Admin, atau Pengelola Aset.
 * Channel: database (in-app notification panel)
 */
class AssetLoanApprovedNotification extends Notification
{
    use Queueable;

    protected AssetLoan $peminjaman;

    // Menyiapkan data peminjaman yang telah disetujui.
    public function __construct(AssetLoan $peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    // Menentukan channel pengiriman notifikasi (in-app via database).
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    // Representasi notifikasi dalam bentuk array untuk disimpan ke tabel notifications.
    public function toArray(object $notifiable): array
    {
        $namaAset    = ($this->peminjaman->asset?->deviceName?->brand ?? '')
                     . ' ' . ($this->peminjaman->asset?->bmn_number ?? '-');
        $namaPemilik = $this->peminjaman->lender?->name ?? 'Admin/Pengelola Aset';

        return [
            'tipe'          => 'peminjaman_disetujui',
            'url'           => route('assets.scan', $this->peminjaman->asset?->id),
            'judul'         => 'Peminjaman Disetujui ✓',
            'pesan'         => "Permintaan pinjam aset " . trim($namaAset) . " telah disetujui oleh {$namaPemilik}.",
            'id_peminjaman' => $this->peminjaman->id,
            'id_aset'       => $this->peminjaman->asset?->id,
            'nama_aset'     => trim($namaAset),
            'nama_pemilik'  => $namaPemilik,
            'waktu_setujui' => $this->peminjaman->approved_at?->toDateTimeString(),
        ];
    }
}

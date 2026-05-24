<?php

namespace App\Services\Notification\Assets;

use App\Models\AssetLoan;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

/**
 * Notifikasi Permintaan Peminjaman Aset
 * Dikirim kepada pemilik aset (atau Admin/Pengelola Aset jika tidak ada pemilik)
 * ketika pengguna mengajukan permintaan peminjaman aset sementara.
 * Channel: database (in-app notification panel)
 */
class AssetNotificationService extends Notification
{
    use Queueable;

    protected AssetLoan $peminjaman;

    // Menyiapkan data peminjaman yang baru diajukan.
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
        $namaAset     = ($this->peminjaman->asset?->deviceName?->brand ?? '')
                      . ' ' . ($this->peminjaman->asset?->bmn_number ?? '-');
        $namaPeminjam = $this->peminjaman->borrower?->name ?? 'Pengguna tidak dikenal';

        return [
            // Tipe notifikasi — digunakan di Blade untuk ikon/warna badge
            'tipe'          => 'permintaan_peminjaman',

            // URL halaman aksi (approve/reject ada di sini)
            'url'           => route('assets.scan', $this->peminjaman->asset?->id),

            // Ringkasan yang ditampilkan di panel notifikasi
            'judul'         => 'Permintaan Peminjaman Aset',
            'pesan'         => "{$namaPeminjam} mengajukan peminjaman aset {$namaAset}.",

            // Data detail untuk keperluan tampilan lanjutan
            'id_peminjaman' => $this->peminjaman->id,
            'id_aset'       => $this->peminjaman->asset?->id,
            'nama_aset'     => trim($namaAset),
            'nama_peminjam' => $namaPeminjam,
            'alasan'        => $this->peminjaman->loan_reason,
            'waktu_ajuan'   => $this->peminjaman->loaned_at?->toDateTimeString(),
        ];
    }

    // Kirim ke pemilik aset, atau broadcast ke Admin + Pengelola Aset jika tanpa pemilik.
    public static function kirim(AssetLoan $peminjaman): void
    {
        $peminjaman->load(['asset.deviceName', 'asset.user', 'borrower']);

        $namaAset    = trim(($peminjaman->asset?->deviceName?->brand ?? '') . ' ' . ($peminjaman->asset?->bmn_number ?? '-'));
        $namaPeminjam = $peminjaman->borrower?->name ?? 'Pengguna';
        $pushTitle   = 'Permintaan Peminjaman Aset';
        $pushBody    = "{$namaPeminjam} mengajukan peminjaman aset {$namaAset}.";
        $pushData    = ['tipe' => 'permintaan_peminjaman', 'loan_id' => (string) $peminjaman->id];

        if ($peminjaman->asset?->user_id) {
            // Ada pemilik — kirim ke pemilik saja
            $pemilik = $peminjaman->asset->user;
            $pemilik->notify(new self($peminjaman));
            FcmService::send($pemilik, $pushTitle, $pushBody, $pushData);
        } else {
            // Tidak ada pemilik — kirim ke semua Admin + Pengelola Aset
            $penerima = User::whereHas('roles', function ($q) {
                $q->whereIn('roles.id', [User::ROLE_ADMIN, User::ROLE_PENGELOLA_ASET]);
            })->get();

            NotificationFacade::send($penerima, new self($peminjaman));
            FcmService::sendToMany($penerima, $pushTitle, $pushBody, $pushData);
        }
    }
}

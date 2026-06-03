<?php

namespace App\Services\Notification\Assets;

use App\Models\AssetLoan;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AssetLoanReminderNotification extends Notification
{
    use Queueable;

    protected AssetLoan $peminjaman;
    protected bool $isOverdue;

    public function __construct(AssetLoan $peminjaman, bool $isOverdue = false)
    {
        $this->peminjaman = $peminjaman;
        $this->isOverdue = $isOverdue;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $namaAset = ($this->peminjaman->asset?->deviceName?->brand ?? '')
                  . ' ' . ($this->peminjaman->asset?->bmn_number ?? '-');

        $judul = $this->isOverdue ? 'Aset Terlambat Dikembalikan!' : 'Pengingat Pengembalian Aset';
        $pesan = $this->isOverdue
            ? "Masa pinjam aset {$namaAset} telah lewat tenggat. Harap segera dikembalikan."
            : "Masa pinjam aset {$namaAset} akan habis besok. Jangan lupa dikembalikan ya.";

        return [
            'tipe'          => 'loan',
            'judul'         => $judul,
            'pesan'         => $pesan,
            'id_peminjaman' => $this->peminjaman->id,
            'id_aset'       => $this->peminjaman->asset?->id,
            'nama_aset'     => trim($namaAset),
            'is_overdue'    => $this->isOverdue,
            'due_date'      => $this->peminjaman->due_date?->toDateString(),
        ];
    }

    public static function kirim(AssetLoan $peminjaman, bool $isOverdue = false): void
    {
        $peminjaman->load(['asset.deviceName', 'borrower']);
        $peminjam = $peminjaman->borrower;

        if (!$peminjam) {
            return;
        }

        $namaAset = trim(($peminjaman->asset?->deviceName?->brand ?? '') . ' ' . ($peminjaman->asset?->bmn_number ?? '-'));

        $pushTitle = $isOverdue ? 'Aset Terlambat Dikembalikan!' : 'Pengingat Pengembalian Aset';
        $pushBody  = $isOverdue
            ? "Masa pinjam aset {$namaAset} telah lewat tenggat. Harap segera dikembalikan."
            : "Masa pinjam aset {$namaAset} akan habis besok. Jangan lupa dikembalikan ya.";
        $pushData  = ['tipe' => 'loan', 'loan_id' => (string) $peminjaman->id];

        // Simpan ke database (tabel notifications)
        $peminjam->notify(new self($peminjaman, $isOverdue));

        // Kirim Push Notification via Firebase
        FcmService::send($peminjam, $pushTitle, $pushBody, $pushData);
        
        Log::info("Loan reminder sent to User ID {$peminjam->id} for Loan ID {$peminjaman->id} (Overdue: " . ($isOverdue ? 'Yes' : 'No') . ")");
    }
}

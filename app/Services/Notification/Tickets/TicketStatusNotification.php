<?php

namespace App\Services\Notification\Tickets;

use App\Models\Ticket;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifikasi Perubahan Status Tiket
 *
 * Dikirim kepada pihak yang relevan sesuai transisi status:
 *   - Menunggu Pengecekan Pengelola → reporter (tiket diterima)
 *   - Diteruskan ke Ketua Tim       → semua ketua tim
 *   - Diteruskan ke Teknisi         → semua teknisi
 *   - In Progress                   → reporter (sedang dikerjakan)
 *   - Menunggu Persetujuan Biaya    → reporter (butuh konfirmasi)
 *   - Approved                      → reporter (biaya disetujui)
 *   - Selesai                       → reporter (tiket selesai)
 *   - Dibatalkan                    → reporter (tiket dibatalkan)
 *
 * Channel: database (in-app) + FCM (push)
 */
class TicketStatusNotification extends Notification
{
    use Queueable;

    protected Ticket $ticket;
    protected string $statusBaru;
    protected ?string $tanggapan;

    public function __construct(Ticket $ticket, string $statusBaru, ?string $tanggapan = null)
    {
        $this->ticket    = $ticket;
        $this->statusBaru = $statusBaru;
        $this->tanggapan  = $tanggapan;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $nomorTiket = '#' . str_pad($this->ticket->id, 5, '0', STR_PAD_LEFT);
        [$judul, $pesan] = $this->_buildMessage($nomorTiket);

        return [
            'tipe'        => 'update_tiket',
            'judul'       => $judul,
            'pesan'       => $pesan,
            'status_baru' => $this->statusBaru,
            'tanggapan'   => $this->tanggapan,
            'id_tiket'    => $this->ticket->id,
            'judul_tiket' => $this->ticket->title,
        ];
    }

    // ─── Statik: Kirim notifikasi sesuai transisi status ─────────────────────

    /**
     * Panggil ini setelah status tiket diubah.
     * Metode ini menentukan sendiri siapa yang harus dinotifikasi.
     */
    public static function kirim(Ticket $ticket, string $statusBaru, ?string $tanggapan = null): void
    {
        $ticket->load(['reporter', 'technician', 'teamLeader']);
        $nomorTiket = '#' . str_pad($ticket->id, 5, '0', STR_PAD_LEFT);
        $notif      = new self($ticket, $statusBaru, $tanggapan);

        switch ($statusBaru) {

            // Reporter dikabari bahwa tiket sudah diterima sistem
            case Ticket::STATUS_MENUNGGU_PENGELOLA:
                if ($ticket->reporter) {
                    $ticket->reporter->notify($notif);
                    FcmService::send(
                        $ticket->reporter,
                        '📋 Tiket Diterima',
                        "Tiket {$nomorTiket} \"{$ticket->title}\" berhasil dikirim dan menunggu pengecekan.",
                        ['tipe' => 'update_tiket', 'ticket_id' => (string) $ticket->id]
                    );
                }
                break;

            // Semua Ketua Tim dinotifikasi
            case Ticket::STATUS_KE_KETUA_TIM:
                $ketuaTim = User::withRole(User::ROLE_KETUA_TIM)->get();
                /** @var \App\Models\User $u */
                foreach ($ketuaTim as $u) {
                    $u->notify($notif);
                    FcmService::send(
                        $u,
                        '👥 Tiket Menunggu Penugasan',
                        "Tiket {$nomorTiket} \"{$ticket->title}\" perlu segera ditugaskan ke teknisi.",
                        ['tipe' => 'update_tiket', 'ticket_id' => (string) $ticket->id]
                    );
                }
                // Reporter juga dikabari
                if ($ticket->reporter) {
                    $ticket->reporter->notify($notif);
                    FcmService::send(
                        $ticket->reporter,
                        '🔄 Tiket Diteruskan',
                        "Tiket {$nomorTiket} diteruskan ke Ketua Tim untuk penugasan teknisi.",
                        ['tipe' => 'update_tiket', 'ticket_id' => (string) $ticket->id]
                    );
                }
                break;

            // Hanya Teknisi terpilih yang ditugaskan yang dinotifikasi
            case Ticket::STATUS_KE_TEKNISI:
                if ($ticket->technician) {
                    $ticket->technician->notify($notif);
                    FcmService::send(
                        $ticket->technician,
                        '🔧 Ada Tiket Untukmu!',
                        "Tiket {$nomorTiket} \"{$ticket->title}\" diserahkan kepadamu. Kerjakan sekarang!",
                        ['tipe' => 'update_tiket', 'ticket_id' => (string) $ticket->id]
                    );
                }
                // Reporter dikabari juga
                if ($ticket->reporter) {
                    $ticket->reporter->notify($notif);
                    FcmService::send(
                        $ticket->reporter,
                        '🔄 Tiket Diteruskan ke Teknisi',
                        "Tiket {$nomorTiket} sedang ditangani oleh tim teknisi kami.",
                        ['tipe' => 'update_tiket', 'ticket_id' => (string) $ticket->id]
                    );
                }
                break;

            // Reporter dikabari tiket mulai dikerjakan
            case Ticket::STATUS_IN_PROGRESS:
                if ($ticket->reporter) {
                    $ticket->reporter->notify($notif);
                    FcmService::send(
                        $ticket->reporter,
                        '⚙️ Tiket Sedang Dikerjakan',
                        "Teknisi kami sedang menangani tiket {$nomorTiket}. Harap standby.",
                        ['tipe' => 'update_tiket', 'ticket_id' => (string) $ticket->id]
                    );
                }
                break;

            // Reporter diminta konfirmasi biaya
            case Ticket::STATUS_MENUNGGU_BIAYA:
                if ($ticket->reporter) {
                    $ticket->reporter->notify($notif);
                    FcmService::send(
                        $ticket->reporter,
                        '💰 Konfirmasi Biaya Diperlukan',
                        "Tiket {$nomorTiket} memerlukan persetujuan biaya dari Anda sebelum dilanjutkan.",
                        ['tipe' => 'update_tiket', 'ticket_id' => (string) $ticket->id]
                    );
                }
                break;

            // Reporter dikabari biaya disetujui
            case Ticket::STATUS_APPROVED:
                if ($ticket->reporter) {
                    $ticket->reporter->notify($notif);
                    FcmService::send(
                        $ticket->reporter,
                        '✅ Biaya Disetujui',
                        "Biaya perbaikan untuk tiket {$nomorTiket} telah disetujui. Pengerjaan akan dilanjutkan.",
                        ['tipe' => 'update_tiket', 'ticket_id' => (string) $ticket->id]
                    );
                }
                break;

            // Reporter dikabari tiket selesai
            case Ticket::STATUS_SELESAI:
                if ($ticket->reporter) {
                    $ticket->reporter->notify($notif);
                    FcmService::send(
                        $ticket->reporter,
                        '🎉 Tiket Selesai!',
                        "Hore! Tiket {$nomorTiket} \"{$ticket->title}\" telah selesai ditangani.",
                        ['tipe' => 'update_tiket', 'ticket_id' => (string) $ticket->id]
                    );
                }
                break;

            // Reporter dikabari tiket dibatalkan
            case Ticket::STATUS_DIBATALKAN:
                if ($ticket->reporter) {
                    $ticket->reporter->notify($notif);
                    FcmService::send(
                        $ticket->reporter,
                        '❌ Tiket Dibatalkan',
                        "Tiket {$nomorTiket} \"{$ticket->title}\" telah dibatalkan." .
                        ($tanggapan ? " Alasan: {$tanggapan}" : ''),
                        ['tipe' => 'update_tiket', 'ticket_id' => (string) $ticket->id]
                    );
                }
                break;
        }
    }

    // ─── Helper: bangun judul & pesan ─────────────────────────────────────────

    private function _buildMessage(string $nomorTiket): array
    {
        return match ($this->statusBaru) {
            Ticket::STATUS_MENUNGGU_PENGELOLA => [
                'Tiket Diterima',
                "Tiket {$nomorTiket} berhasil dikirim dan menunggu pengecekan.",
            ],
            Ticket::STATUS_KE_KETUA_TIM => [
                'Tiket Menunggu Penugasan',
                "Tiket {$nomorTiket} \"{$this->ticket->title}\" diteruskan ke Ketua Tim.",
            ],
            Ticket::STATUS_KE_TEKNISI => [
                'Tiket Diserahkan ke Teknisi',
                "Tiket {$nomorTiket} \"{$this->ticket->title}\" diserahkan untuk dikerjakan.",
            ],
            Ticket::STATUS_IN_PROGRESS => [
                'Tiket Sedang Dikerjakan',
                "Tiket {$nomorTiket} sedang dalam proses penanganan oleh teknisi.",
            ],
            Ticket::STATUS_MENUNGGU_BIAYA => [
                'Konfirmasi Biaya Diperlukan',
                "Tiket {$nomorTiket} memerlukan persetujuan biaya dari Anda.",
            ],
            Ticket::STATUS_APPROVED => [
                'Biaya Disetujui',
                "Biaya untuk tiket {$nomorTiket} telah disetujui.",
            ],
            Ticket::STATUS_SELESAI => [
                'Pengerjaan Selesai 🎉',
                "Tiket {$nomorTiket} \"{$this->ticket->title}\" telah selesai ditangani!",
            ],
            Ticket::STATUS_DIBATALKAN => [
                'Tiket Dibatalkan',
                "Tiket {$nomorTiket} dibatalkan." . ($this->tanggapan ? " Alasan: {$this->tanggapan}" : ''),
            ],
            default => [
                'Status Tiket Diperbarui',
                "Status tiket {$nomorTiket} berubah menjadi: {$this->statusBaru}.",
            ],
        };
    }
}

<?php

namespace App\Services\Notification\Tickets;

use App\Models\Asset;
use App\Models\PcReport;
use App\Models\Room;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class TicketNotificationService
{

    public function getNotifikasiUntukUser(): Collection
    {
        if (!Auth::check()) {
            return collect();
        }

        $user = Auth::user();
        $activeRoleId = session('active_role_id');
        $notifications = collect();

        $this->tambahNotifBerdasarkanRole($notifications, $activeRoleId);

        $this->tambahNotifPelapor($notifications, $user);

        $this->tambahNotifDariDatabase($notifications, $user);

        return $notifications->sortByDesc('time')->values();
    }

    protected function tambahNotifBerdasarkanRole(Collection $notifications, ?int $roleId): void
    {
        if ($roleId == User::ROLE_TEKNISI) {
            $pending = Ticket::whereIn('status', [
                Ticket::STATUS_KE_TEKNISI,
                Ticket::STATUS_IN_PROGRESS,
            ])->latest()->take(3)->get();

            foreach ($pending as $t) {
                $notifications->push([
                    'title' => 'Tugas Teknisi Baru',
                    'desc'  => 'Tiket #' . str_pad($t->id, 5, '0', STR_PAD_LEFT) . ' butuh penanganan segera.',
                    'url'   => route('tickets.show', $t->id),
                    'icon'  => 'wrench',
                    'time'  => $t->created_at,
                ]);
            }
        } elseif ($roleId == User::ROLE_PENGELOLA_ASET) {
            $pending = Ticket::where('status', Ticket::STATUS_MENUNGGU_PENGELOLA)
                ->latest()->take(3)->get();

            foreach ($pending as $t) {
                $notifications->push([
                    'title' => 'Tinjauan ' . ($t->type == 'Asset' ? 'Aset' : 'Layanan'),
                    'desc'  => 'Tiket #' . str_pad($t->id, 5, '0', STR_PAD_LEFT) . ' memerlukan pengecekan Anda.',
                    'url'   => route('tickets.show', $t->id),
                    'icon'  => 'clipboard-check',
                    'time'  => $t->created_at,
                ]);
            }
        } elseif ($roleId == User::ROLE_KETUA_TIM) {
            $pending = Ticket::where('status', Ticket::STATUS_KE_KETUA_TIM)
                ->latest()->take(3)->get();

            foreach ($pending as $t) {
                $notifications->push([
                    'title' => 'Tiket Menunggu Penugasan',
                    'desc'  => 'Tiket #' . str_pad($t->id, 5, '0', STR_PAD_LEFT) . ' perlu segera ditugaskan ke teknisi.',
                    'url'   => route('tickets.show', $t->id),
                    'icon'  => 'users',
                    'time'  => $t->created_at,
                ]);
            }
        } elseif ($roleId == User::ROLE_PIC_RUANGAN) {

            $user     = Auth::user();
            $roomIds  = Room::where('pic_id', $user->id)->pluck('id');
            $assetIds = Asset::whereIn('room_id', $roomIds)->pluck('id');

            $inProgress = Ticket::whereIn('asset_id', $assetIds)
                ->active()
                ->where('reported_by', '!=', $user->id)
                ->latest()
                ->take(3)
                ->get();

            foreach ($inProgress as $t) {
                $notifications->push([
                    'title' => 'Tiket Aktif di Ruangan Anda',
                    'desc'  => 'Tiket #' . str_pad($t->id, 5, '0', STR_PAD_LEFT) . ' sedang dalam proses penanganan.',
                    'url'   => route('tickets.show', $t->id),
                    'icon'  => 'activity',
                    'time'  => $t->updated_at,
                ]);
            }
        } elseif ($roleId == User::ROLE_ADMIN) {

            $anomalyPcs = PcReport::where('is_trouble', true)
                ->latest('last_seen')->take(3)->get();

            foreach ($anomalyPcs as $pc) {
                $notifications->push([
                    'title' => 'Peringatan Anomali PC',
                    'desc'  => 'PC ' . $pc->hostname . ' bermasalah: ' . $pc->trouble_note,
                    'url'   => route('admin.reports.show', $pc->id),
                    'icon'  => 'alert-triangle',
                    'time'  => $pc->last_seen ?? now(),
                ]);
            }

            $newTickets = Ticket::where('status', Ticket::STATUS_MENUNGGU_PENGELOLA)
                ->latest()->take(2)->get();

            foreach ($newTickets as $t) {
                $notifications->push([
                    'title' => 'Tiket Baru Masuk',
                    'desc'  => 'Tiket #' . str_pad($t->id, 5, '0', STR_PAD_LEFT) . ' menunggu pengecekan awal.',
                    'url'   => route('tickets.show', $t->id),
                    'icon'  => 'inbox',
                    'time'  => $t->created_at,
                ]);
            }
        }
    }

    protected function tambahNotifPelapor(Collection $notifications, User $user): void
    {

        $responses = Ticket::where('reported_by', $user->id)
            ->where('status', Ticket::STATUS_MENUNGGU_BIAYA)
            ->latest()->take(2)->get();

        foreach ($responses as $t) {
            $notifications->push([
                'title' => 'Persetujuan Biaya',
                'desc' => 'Tiket #' . str_pad($t->id, 5, '0', STR_PAD_LEFT) . ' membutuhkan konfirmasi dana dari Anda.',
                'url' => route('tickets.show', $t->id),
                'icon' => 'credit-card',
                'time' => $t->updated_at,
            ]);
        }

        $completed = Ticket::where('reported_by', $user->id)
            ->where('status', Ticket::STATUS_SELESAI)
            ->where('updated_at', '>=', now()->subDays(3))
            ->latest('updated_at')->take(2)->get();

        foreach ($completed as $t) {
            $notifications->push([
                'title' => 'Pengerjaan Selesai',
                'desc' => 'Hore! Tiket #' . str_pad($t->id, 5, '0', STR_PAD_LEFT) . ' milik Anda telah diperbaiki & diselesaikan.',
                'url' => route('tickets.show', $t->id),
                'icon' => 'check-circle',
                'time' => $t->updated_at,
            ]);
        }

        $repliedTickets = Ticket::where('reported_by', $user->id)
            ->whereHas('replies', function ($q) use ($user) {
                $q->where('user_id', '!=', $user->id)
                    ->where('created_at', '>=', now()->subDays(2));
            })
            ->latest('updated_at')->take(2)->get();

        foreach ($repliedTickets as $t) {
            $notifications->push([
                'title' => 'Pesan Baru',
                'desc' => 'Ada balasan/pesan baru untuk tiket #' . str_pad($t->id, 5, '0', STR_PAD_LEFT) . '.',
                'url' => route('tickets.show', $t->id),
                'icon' => 'message-square',
                'time' => $t->updated_at,
            ]);
        }
    }

    protected function tambahNotifDariDatabase(Collection $notifications, User $user): void
    {

        foreach ($user->unreadNotifications()->take(5)->get() as $notif) {
            $data = $notif->data;

            $icon = match($data['tipe'] ?? '') {
                'permintaan_peminjaman'   => 'shopping-cart',
                'peminjaman_disetujui'    => 'check-circle',
                'peminjaman_ditolak'      => 'x-circle',
                'peminjaman_dikembalikan' => 'refresh-cw',
                default                   => 'bell',
            };

            $notifications->push([
                'title' => $data['judul'] ?? 'Notifikasi Baru',
                'desc'  => $data['pesan'] ?? '',
                'url'   => $data['url'] ?? '#',
                'icon'  => $icon,
                'time'  => $notif->created_at,
                'id'    => $notif->id,
            ]);
        }
    }
}

<?php

namespace App\Services\Ticket;

use App\Models\Ticket;
use App\Models\Asset;
use App\Models\User;
use App\Models\TicketReply;
use App\Jobs\CompressImageJob;
use Illuminate\Support\Facades\Log;

class TicketService
{

    public function createTicket(array $data, User $reporter, $photoFile = null): Ticket
    {
        $assetId = $data['asset_id'] ?? null;
        $roomId = $data['room_id'] ?? null;

        $asset = null;
        if ($assetId) {
            $asset = Asset::find($assetId);
            if ($asset && !$roomId) {
                $roomId = $asset->room_id;
            }
        }

        $category = $data['category'] ?? null;
        if ($asset && $category === 'Service') {
            $asset->update(['status_kondisi' => Asset::KONDISI_RUSAK_RINGAN]);
        }

        $type = !empty($assetId) ? 'Asset' : 'General';

        $photoPath = null;
        if ($photoFile && method_exists($photoFile, 'isValid') && $photoFile->isValid()) {
            $photoPath = $photoFile->store('ticket-photos/raw', 'public');
        }

        $ticket = Ticket::create([
            'title'       => $data['title'],
            'description' => $data['description'],
            'type'        => $type,
            'category'    => $category,
            'status'      => Ticket::STATUS_MENUNGGU_PENGELOLA,
            'priority'    => $data['priority'] ?? 'Sedang',
            'reported_by' => $reporter->id,
            'asset_id'    => $assetId,
            'room_id'     => $roomId,
            'photo_path'  => $photoPath,
        ]);

        if ($photoPath) {
            CompressImageJob::dispatch(
                $photoPath,
                'ticket-photos',
                Ticket::class,
                $ticket->id,
                'photo_path',
                1280,
                80
            );
        }

        return $ticket;
    }

    public function updateStatus(Ticket $ticket, array $data, User $actor, ?int $activeRoleId = null): Ticket
    {
        $statusLama = $ticket->status;
        $statusBaru = $data['status'];
        $tanggapan = $data['tanggapan'] ?? null;

        $ticket->status = $statusBaru;

        if (array_key_exists('estimated_cost', $data)) {
            $ticket->estimated_cost = $data['estimated_cost'];
        }

        if (array_key_exists('category', $data)) {
            $ticket->category = $data['category'];
        }

        if (in_array($statusBaru, [Ticket::STATUS_KE_TEKNISI, Ticket::STATUS_IN_PROGRESS]) && is_null($ticket->responded_at)) {
            $ticket->responded_at = now();
        }

        if ($statusBaru === Ticket::STATUS_SELESAI && is_null($ticket->resolved_at)) {
            $ticket->resolved_at = now();

            if (is_null($ticket->responded_at)) {
                $ticket->responded_at = now();
            }
        }

        $isAdminOrKetuaTim = false;
        if ($activeRoleId !== null) {
            $isAdminOrKetuaTim = in_array((int)$activeRoleId, [User::ROLE_ADMIN, User::ROLE_KETUA_TIM]);
        } else {
            $isAdminOrKetuaTim = $actor->hasRole(User::ROLE_ADMIN) || $actor->hasRole(User::ROLE_KETUA_TIM);
        }

        if ($statusBaru === Ticket::STATUS_KE_TEKNISI && $isAdminOrKetuaTim) {
            if (!empty($data['technician_id'])) {
                $ticket->technician_id = $data['technician_id'];
                $ticket->team_leader_id = $actor->id;
            }
        }

        $isTeknisi = false;
        if ($activeRoleId !== null) {
            $isTeknisi = ((int)$activeRoleId) === User::ROLE_TEKNISI;
        } else {
            $isTeknisi = $actor->hasRole(User::ROLE_TEKNISI);
        }

        if ($statusBaru === Ticket::STATUS_IN_PROGRESS && $isTeknisi) {
            if (!$ticket->technician_id) {
                $ticket->technician_id = $actor->id;
            }
        }

        $ticket->save();

        if ($statusBaru === Ticket::STATUS_SELESAI && $ticket->asset_id) {
            Asset::where('id', $ticket->asset_id)
                ->update(['status_kondisi' => Asset::KONDISI_BAIK]);
        }

        $statusMsg = "⚙️ Status tiket diubah menjadi: ({$statusBaru}) oleh " . $actor->name;
        if (!empty($data['technician_id'])) {
            $tech = User::find($data['technician_id']);
            if ($tech) {
                $statusMsg .= "\n👨‍🔧 Teknisi ditugaskan: ({$tech->name})";
            }
        }
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $actor->id,
            'message'   => $statusMsg,
        ]);

        if ($statusLama !== $statusBaru) {
            try {
                \App\Services\Notification\Tickets\TicketStatusNotification::kirim($ticket, $statusBaru, $tanggapan);
            } catch (\Exception $e) {
                Log::warning('Gagal kirim notif tiket: ' . $e->getMessage());
            }
        }

        return $ticket;
    }
}

<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class AturanTiket
{

    public function viewAny(User $pengguna): bool
    {

        return true;
    }

    public function view(User $pengguna, Ticket $tiket): bool
    {
        $roleAktif = (int) session('active_role_id');

        if ($tiket->reported_by === $pengguna->id) {
            return true;
        }

        if (
            in_array($roleAktif, [
                User::ROLE_PIMPINAN,
                User::ROLE_ADMIN,
                User::ROLE_PENGELOLA_ASET,
            ], true)
        ) {
            return true;
        }

        if ($roleAktif === User::ROLE_TEKNISI) {
            return $tiket->technician_id === $pengguna->id;
        }

        if ($roleAktif === User::ROLE_KETUA_TIM) {
            return $tiket->team_leader_id === $pengguna->id
                || $tiket->status === Ticket::STATUS_KE_KETUA_TIM;
        }

        return false;
    }

    public function create(User $pengguna): bool
    {

        return in_array((int) session('active_role_id'), [
            User::ROLE_PIC_RUANGAN,
            User::ROLE_USER,
        ], true);
    }

    public function updateStatus(User $pengguna, Ticket $tiket): bool
    {

        return in_array((int) session('active_role_id'), [
            User::ROLE_ADMIN,
            User::ROLE_PENGELOLA_ASET,
            User::ROLE_KETUA_TIM,
            User::ROLE_TEKNISI,
        ], true);
    }

    public function reply(User $pengguna, Ticket $tiket): bool
    {
        $roleAktif = (int) session('active_role_id');
        $tiketSudahTutup = in_array($tiket->status, [
            Ticket::STATUS_SELESAI,
            Ticket::STATUS_DIBATALKAN,
        ], true);

        if ($tiketSudahTutup) {
            return in_array($roleAktif, [
                User::ROLE_ADMIN,
                User::ROLE_TEKNISI,
                User::ROLE_KETUA_TIM,
            ], true) || $tiket->reported_by === $pengguna->id;
        }

        return in_array($roleAktif, [
            User::ROLE_PIMPINAN,
            User::ROLE_ADMIN,
            User::ROLE_TEKNISI,
            User::ROLE_PENGELOLA_ASET,
            User::ROLE_KETUA_TIM,
        ], true)
            || $tiket->reported_by === $pengguna->id;
    }
}

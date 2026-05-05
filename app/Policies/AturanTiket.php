<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

/**
 * Aturan Akses Tiket
 * Mendefinisikan siapa saja yang berhak melakukan
 * aksi tertentu terhadap tiket di dalam sistem SIGAP.
 * Didaftarkan di AppServiceProvider via Gate::policy().
 */
class AturanTiket
{
    // Menentukan siapa yang bisa melihat daftar tiket.
    public function viewAny(User $pengguna): bool
    {
        // Semua pengguna yang sudah login boleh mengakses halaman daftar tiket.
        // Filter data per-role dilakukan di TicketController@index.
        return true;
    }

    // Menentukan siapa yang bisa melihat detail tiket tertentu.
    public function view(User $pengguna, Ticket $tiket): bool
    {
        $roleAktif = (int) session('active_role_id');

        // Pelapor tiket selalu bisa melihat tiket miliknya sendiri
        if ($tiket->reported_by === $pengguna->id) {
            return true;
        }

        // Pimpinan, Admin, dan Pengelola Aset: bisa lihat semua tiket
        if (
            in_array($roleAktif, [
                User::ROLE_PIMPINAN,
                User::ROLE_ADMIN,
                User::ROLE_PENGELOLA_ASET,
            ], true)
        ) {
            return true;
        }

        // Teknisi: hanya tiket yang ditugaskan kepadanya
        if ($roleAktif === User::ROLE_TEKNISI) {
            return $tiket->technician_id === $pengguna->id;
        }

        // Ketua Tim: tiket yang sudah diteruskan atau yang berada di bawah kepemimpinannya
        if ($roleAktif === User::ROLE_KETUA_TIM) {
            return $tiket->team_leader_id === $pengguna->id
                || $tiket->status === Ticket::STATUS_KE_KETUA_TIM;
        }

        return false;
    }

    // Menentukan siapa yang bisa membuat tiket baru.
    public function create(User $pengguna): bool
    {
        // PIC Ruangan dan User Biasa yang melaporkan masalah
        return in_array((int) session('active_role_id'), [
            User::ROLE_PIC_RUANGAN,
            User::ROLE_USER,
        ], true);
    }

    // Menentukan siapa yang bisa mengubah status tiket.
    public function updateStatus(User $pengguna, Ticket $tiket): bool
    {
        // Hanya petugas internal yang berwenang mengubah status penanganan tiket
        return in_array((int) session('active_role_id'), [
            User::ROLE_ADMIN,
            User::ROLE_PENGELOLA_ASET,
            User::ROLE_KETUA_TIM,
            User::ROLE_TEKNISI,
        ], true);
    }

    // Menentukan siapa yang bisa membalas diskusi tiket.
    public function reply(User $pengguna, Ticket $tiket): bool
    {
        $roleAktif = (int) session('active_role_id');
        $tiketSudahTutup = in_array($tiket->status, [
            Ticket::STATUS_SELESAI,
            Ticket::STATUS_DIBATALKAN,
        ], true);

        // Jika tiket sudah ditutup: hanya petugas IT dan pelapor yang masih bisa membalas
        if ($tiketSudahTutup) {
            return in_array($roleAktif, [
                User::ROLE_ADMIN,
                User::ROLE_TEKNISI,
                User::ROLE_KETUA_TIM,
            ], true) || $tiket->reported_by === $pengguna->id;
        }

        // Jika tiket masih aktif: semua petugas dan pelapor bisa membalas
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

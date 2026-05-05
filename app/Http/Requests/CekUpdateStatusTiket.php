<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CekUpdateStatusTiket extends FormRequest
{
    //Menentukan apakah user diizinkan mengubah status tiket.
    public function authorize(): bool
    {
        return in_array(session('active_role_id'), [
            User::ROLE_ADMIN,
            User::ROLE_PENGELOLA_ASET,
            User::ROLE_KETUA_TIM,
            User::ROLE_TEKNISI,
        ]);
    }

    public function rules(): array
    {
        $activeRole = session('active_role_id');
        $validStatuses = [];

        if ($activeRole == User::ROLE_ADMIN) {
            $validStatuses = [
                Ticket::STATUS_MENUNGGU_PENGELOLA,
                Ticket::STATUS_KE_KETUA_TIM,
                Ticket::STATUS_KE_TEKNISI,
                Ticket::STATUS_IN_PROGRESS,
                Ticket::STATUS_MENUNGGU_BIAYA,
                Ticket::STATUS_APPROVED,
                Ticket::STATUS_SELESAI,
                Ticket::STATUS_DIBATALKAN,
            ];
        } elseif ($activeRole == User::ROLE_PENGELOLA_ASET) {
            $validStatuses = [
                Ticket::STATUS_KE_KETUA_TIM,
                Ticket::STATUS_MENUNGGU_BIAYA,
                Ticket::STATUS_APPROVED,
                Ticket::STATUS_SELESAI,
                Ticket::STATUS_DIBATALKAN,
            ];
        } elseif ($activeRole == User::ROLE_KETUA_TIM) {
            $validStatuses = [
                Ticket::STATUS_KE_TEKNISI,
                Ticket::STATUS_SELESAI,
                Ticket::STATUS_DIBATALKAN,
            ];
        } elseif ($activeRole == User::ROLE_TEKNISI) {
            $validStatuses = [
                Ticket::STATUS_IN_PROGRESS,
                Ticket::STATUS_MENUNGGU_BIAYA,
                Ticket::STATUS_SELESAI,
                Ticket::STATUS_DIBATALKAN,
            ];
        }

        return [
            'status'        => ['required', \Illuminate\Validation\Rule::in($validStatuses)],
            'technician_id' => 'nullable|exists:users,id',
            'estimated_cost' => 'nullable|numeric|min:0',
            'category'      => 'nullable|string',
        ];
    }
}

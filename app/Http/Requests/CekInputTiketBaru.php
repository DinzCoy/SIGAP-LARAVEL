<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CekInputTiketBaru extends FormRequest
{
    //Cek apakah user punya izin untuk membuat tiket.
    public function authorize(): bool
    {
        return in_array(session('active_role_id'), [
            User::ROLE_PIC_RUANGAN,
            User::ROLE_USER,
        ]);
    }

    //Aturan validasi untuk pembuatan tiket baru.
    public function rules(): array
    {
        return [
            'category'    => 'nullable|string',
            'asset_id'    => 'nullable|exists:assets,id',
            'room_id'     => 'required|exists:rooms,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'priority'    => 'required|in:Rendah,Sedang,Tinggi',
        ];
    }
}

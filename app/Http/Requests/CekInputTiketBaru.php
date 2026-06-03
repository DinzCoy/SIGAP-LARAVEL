<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CekInputTiketBaru extends FormRequest
{

    public function authorize(): bool
    {
        return in_array(session('active_role_id'), [
            User::ROLE_PIC_RUANGAN,
            User::ROLE_USER,
        ]);
    }

    public function rules(): array
    {
        return [
            'category'    => 'nullable|string',
            'asset_id'    => 'nullable|exists:assets,id',
            'room_id'     => 'required|exists:rooms,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'priority'    => 'required|in:Rendah,Sedang,Tinggi',
            'photo'       => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ];
    }
}

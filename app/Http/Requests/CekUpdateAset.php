<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CekUpdateAset extends FormRequest
{
    // Otorisasi ditangani oleh middleware role:2,4 di web.php
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Ambil ID aset dari parameter route agar unique rule mengabaikan aset itu sendiri
        $assetId = $this->route('id');

        return [
            'bmn_number'     => ['required', Rule::unique('assets', 'bmn_number')->ignore($assetId)],
            'device_name_id' => ['required', 'exists:device_names,id'],
            'serial_number'  => ['nullable', 'string'],
            'room_id'        => ['nullable', 'exists:rooms,id'],
            'user_id'        => ['nullable', 'exists:users,id'],
            'allocated_at'   => ['nullable', 'date'],
            'status_kondisi' => ['required', Rule::in(Asset::kondisiList())],
        ];
    }

    public function messages(): array
    {
        return [
            'bmn_number.required'     => 'Nomor BMN wajib diisi.',
            'bmn_number.unique'       => 'Nomor BMN ini sudah digunakan oleh aset lain.',
            'device_name_id.required' => 'Nama perangkat wajib dipilih.',
            'device_name_id.exists'   => 'Perangkat yang dipilih tidak valid.',
            'status_kondisi.required' => 'Status kondisi aset wajib dipilih.',
            'status_kondisi.in'       => 'Status kondisi tidak valid. Pilih: Baik, Rusak Ringan, atau Rusak Berat.',
        ];
    }
}

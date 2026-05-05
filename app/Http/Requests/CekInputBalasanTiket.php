<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CekInputBalasanTiket extends FormRequest
{
    //Izin akses (otorisasi ditangani oleh Policy di Controller).
    public function authorize(): bool
    {
        return true; // Authorization handled by Policy in Controller
    }

    //Aturan validasi untuk pesan balasan tiket.
    public function rules(): array
    {
        return [
            'message' => 'required|string',
        ];
    }
}

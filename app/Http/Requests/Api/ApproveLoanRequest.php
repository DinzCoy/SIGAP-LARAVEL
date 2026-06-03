<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ApproveLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'        => 'required|in:disetujui,ditolak',
            'catatan_admin' => 'nullable|string',
        ];
    }
}

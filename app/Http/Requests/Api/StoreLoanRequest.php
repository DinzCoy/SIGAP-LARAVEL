<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_id'    => 'required|exists:assets,id',
            'loan_reason' => 'required|string',
            'due_date'    => 'required|date|after:today',
        ];
    }
}

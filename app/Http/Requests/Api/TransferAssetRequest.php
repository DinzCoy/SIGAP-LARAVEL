<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TransferAssetRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_id' => 'required|exists:assets,id',
            'reason'   => 'required|string|min:10',
        ];
    }
}

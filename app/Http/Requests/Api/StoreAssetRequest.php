<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'kode' => $this->kode ?? $this->asset_code,
            'nama' => $this->nama ?? $this->name,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|unique:assets,bmn_number',
            'merek' => 'nullable|string',
            'brand' => 'nullable|string',
            'kategori' => 'nullable|string',
            'kondisi' => 'nullable|string',
            'status_kondisi' => 'nullable|string',
            'lokasi' => 'nullable|string',
            'room_id' => 'nullable',
        ];
    }
}

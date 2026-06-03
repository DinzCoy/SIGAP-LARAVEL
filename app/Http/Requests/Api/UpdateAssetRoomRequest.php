<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetRoomRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id'       => 'nullable|integer|exists:rooms,id',
            'new_room_name' => 'nullable|string|max:255',
        ];
    }
}

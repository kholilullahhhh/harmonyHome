<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class TipeKamarRequest extends BaseRequest
{
    public function rules(): array
    {
        $id = $this->route('tipe-kamar');

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('tipe_kamars', 'name')->ignore($id)],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

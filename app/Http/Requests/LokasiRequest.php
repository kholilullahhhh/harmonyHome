<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class LokasiRequest extends BaseRequest
{
    public function rules(): array
    {
        $id = $this->route('lokasi');

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('lokasis', 'name')->ignore($id)],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}

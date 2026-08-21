<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class FasilitasRequest extends BaseRequest
{
    public function rules(): array
    {
        $id = $this->route('fasilitas');

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('fasilitas', 'name')->ignore($id)],
            'icon' => ['nullable', 'string', 'max:100'],
        ];
    }
}

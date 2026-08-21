<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class KostRequest extends BaseRequest
{
    public function rules(): array
    {
        $id = $this->route('kost');

        return [
            'user_id' => ['required', 'exists:users,id'],
            'lokasi_id' => ['required', 'exists:lokasis,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'address' => ['required', 'string', 'max:500'],
            'kelurahan' => ['nullable', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'rules' => ['nullable', 'string', 'max:5000'],
            'access_hours' => ['nullable', 'string', 'max:100'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'fasilitas' => ['nullable', 'array'],
            'fasilitas.*' => ['integer', Rule::exists('fasilitas', 'id')],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}

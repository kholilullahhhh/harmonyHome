<?php

namespace App\Http\Requests;

use App\Models\Kamar;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KamarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $kamarId = $this->route('kamar');

        return [
            'kost_id' => ['required', 'exists:kost,id'],
            'tipe_kamar_id' => ['required', 'exists:tipe_kamar,id'],
            'number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('kamar')->where(fn ($q) => $q->where('kost_id', $this->input('kost_id')))->ignore($kamarId),
            ],
            'price_monthly' => ['required', 'integer', 'min:0'],
            'size' => ['nullable', 'string', 'max:50'],
            'floor' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', Rule::in([
                Kamar::STATUS_AVAILABLE,
                Kamar::STATUS_RESERVED,
                Kamar::STATUS_OCCUPIED,
                Kamar::STATUS_MAINTENANCE,
            ])],
            'fasilitas' => ['nullable', 'array'],
            'fasilitas.*' => ['exists:fasilitas,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'number.unique' => 'Nomor kamar sudah digunakan di kost ini.',
        ];
    }
}

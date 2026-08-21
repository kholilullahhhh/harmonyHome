<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuestBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guest_name' => ['required', 'string', 'max:100'],
            'guest_email' => ['required', 'email', 'max:100'],
            'guest_phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'guest_identity_number' => ['required', 'string', 'max:50'],
            'guest_gender' => ['nullable', 'in:L,P'],
            'guest_birth_date' => ['nullable', 'date', 'before:today'],
            'guest_address' => ['nullable', 'string', 'max:500'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:36'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh di masa lalu.',
            'guest_phone.regex' => 'Format nomor HP tidak valid.',
        ];
    }
}

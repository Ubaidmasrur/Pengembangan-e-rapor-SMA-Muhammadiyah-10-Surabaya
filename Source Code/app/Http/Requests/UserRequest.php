<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // UserRequest.php
    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'role' => ['required', Rule::in(['siswa', 'guru', 'admin'])],
            'entity_id' => [$userId ? 'nullable' : 'required', 'integer'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => [
                $userId ? 'nullable' : 'required', // <== ini penting
                'string',
                'min:6',
                'confirmed',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'Peran wajib dipilih.',
            'role.in' => 'Peran tidak valid.',

            'entity_id.required' => 'Nama harus dipilih.',
            'entity_id.exists' => 'Data yang dipilih tidak ditemukan.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',

            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }
}

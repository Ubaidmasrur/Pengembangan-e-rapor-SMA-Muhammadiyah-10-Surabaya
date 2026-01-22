<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna berhak melakukan request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi yang berlaku untuk request ini.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Ambil model school dari route (untuk update)
        $school = $this->route('school');
        $schoolId = $school?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('schools', 'name')->ignore($schoolId),
            ],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'regex:/^[0-9]{9,15}$/', 'max:20'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('schools', 'email')->ignore($schoolId),
            ],
            'principal_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Pesan kustom untuk validasi.
     */
    public function messages(): array
    {
        return [
            'name.required'        => 'Nama sekolah wajib diisi.',
            'name.string'          => 'Nama harus berupa teks.',
            'name.unique'          => 'Nama sekolah sudah terdaftar.',
            'name.max'             => 'Nama maksimal 255 karakter.',
            'phone.max'            => 'Nomor telepon maksimal 20 karakter.',
            'phone.regex'          => 'Nomor telepon harus berupa angka dan sesuai format nomor Indonesia (contoh: 081234567890).',
            'email.email'          => 'Format email tidak valid.',
            'email.unique'         => 'Email sekolah sudah terdaftar.',
            'address.max'          => 'Alamat maksimal 500 karakter.',
            'principal_name.max'   => 'Nama kepala sekolah maksimal 255 karakter.',
        ];
    }

    /**
     * Penamaan atribut agar lebih ramah pengguna.
     */
    public function attributes(): array
    {
        return [
            'name'           => 'nama sekolah',
            'address'        => 'alamat',
            'phone'          => 'telepon',
            'email'          => 'email',
            'principal_name' => 'nama kepala sekolah',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $student = $this->route('student');
        $studentId = $student?->id;
        $userId = $student?->user_id;

        return [
            'name'             => ['required', 'string', 'max:255'],
            'nisn'             => [
                'required',
                'digits_between:8,12', // hanya angka dan panjang 10-12 digit
                Rule::unique('students', 'nisn')->ignore($studentId),
            ],
            'birth_date'       => ['required', 'date'],
            'gender'           => ['required', 'in:L,P'],
            'disability_type'  => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'            => 'Nama siswa wajib diisi.',
            'name.string'              => 'Nama harus berupa teks.',
            'name.max'                 => 'Nama maksimal 255 karakter.',

            'nisn.required'            => 'NISN siswa wajib diisi.',
            'nisn.digits_between'      => 'NISN harus berupa angka antara 8 sampai 12 digit.',
            'nisn.unique'              => 'NISN sudah digunakan.',

            'birth_date.required'      => 'Tanggal lahir wajib diisi.',
            'birth_date.date'          => 'Tanggal lahir tidak valid.',

            'gender.required'          => 'Jenis kelamin wajib dipilih.',
            'gender.in'                => 'Jenis kelamin harus L (Laki-laki) atau P (Perempuan).',

            'disability_type.string'   => 'Tipe disabilitas harus berupa teks.',
            'disability_type.max'      => 'Tipe disabilitas maksimal 255 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'            => 'nama siswa',
            'nisn'            => 'NISN',
            'birth_date'      => 'tanggal lahir',
            'gender'          => 'jenis kelamin',
            'disability_type' => 'tipe disabilitas',
        ];
    }
}

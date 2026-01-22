<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $guardianId = $this->route('guardian')?->id;

        return [
            'student_id'   => [
                'required',
                'exists:students,id',
                Rule::unique('guardians', 'student_id')->ignore($guardianId)->whereNull('deleted_at'),
            ],
            'name'         => ['required', 'string', 'max:255'],
            'relationship' => ['required', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'Siswa wajib dipilih.',
            'student_id.exists'   => 'Siswa tidak ditemukan.',
            'student_id.unique'   => 'Siswa ini sudah memiliki wali, pilih siswa lain.',
            'name.required'       => 'Nama wali wajib diisi.',
            'relationship.required' => 'Hubungan wajib diisi.',
        ];
    }

    public function attributes(): array
    {
        return [
            'student_id'   => 'siswa',
            'name'         => 'nama wali',
            'relationship' => 'hubungan keluarga',
            'phone'        => 'nomor telepon',
        ];
    }
}

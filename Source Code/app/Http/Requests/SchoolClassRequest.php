<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolClassRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Ambil model dari route (untuk update)
        $schoolClass   = $this->route('school_class');
        $schoolClassId = $schoolClass?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('school_classes', 'name')
                    ->ignore($schoolClassId)
                    ->where(fn($q) => $q->where('school_id', $this->input('school_id'))),
            ],
            'school_id' => [
                'required',
                'exists:schools,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Nama kelas wajib diisi.',
            'name.unique'    => 'Nama kelas sudah digunakan di sekolah ini.',
            'name.max'       => 'Nama kelas maksimal 255 karakter.',
            'school_id.required' => 'Sekolah wajib dipilih.',
            'school_id.exists'   => 'Sekolah tidak ditemukan.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'      => 'nama kelas',
            'school_id' => 'sekolah',
        ];
    }
}

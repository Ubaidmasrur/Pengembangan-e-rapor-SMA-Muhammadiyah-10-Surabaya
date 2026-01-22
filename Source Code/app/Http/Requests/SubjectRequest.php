<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubjectRequest extends FormRequest
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
        // Ambil model subject dari route (untuk update)
        $subject = $this->route('subject');
        $subjectId = $subject?->id;

        return [
            'name'             => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects', 'name')->ignore($subjectId),
            ],
            'type' => 'required|in:umum,khusus,ekstra',
            'min_grade' => 'nullable|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama mata pelajaran wajib diisi.',
            'name.string'   => 'Nama harus berupa teks.',
            'name.unique'   => 'Nama mata pelajaran sudah terdaftar.',
            'name.max'      => 'Nama maksimal 255 karakter.',
            'type.required' => 'Tipe mapel wajib dipilih.',
            'type.in'       => 'Tipe harus berupa umum, khusus, atau ekstra.',
            'min_grade.numeric' => 'Nilai minimum harus berupa angka.',
            'min_grade.min' => 'Nilai minimum tidak boleh kurang dari 0.',
            'min_grade.max' => 'Nilai minimum tidak boleh lebih dari 100.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'            => 'nama mata pelajaran',
            'type'            => 'tipe mata pelajaran',
            'min_grade'       => 'nilai minimum',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeacherRequest extends FormRequest
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
        // Ambil ID teacher jika sedang update (dari route binding)
        $teacherId = $this->route('teacher')?->id;

        return [
            'name' => 'required|string|max:255',
            'nip' => [
                'required',
                'digits_between:8,50',
                Rule::unique('teachers', 'nip')->ignore($teacherId),
            ],
        ];
    }

    /**
     * Custom error messages for validation
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama guru wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'nip.required' => 'NIP guru wajib diisi.',
            'nip.digits_between' => 'NIP harus berupa angka antara 8 sampai 50 digit.',
            'nip.unique' => 'NIP sudah digunakan.',
        ];
    }

    /**
     * Custom attribute names (for error messages)
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama guru',
            'nip' => 'NIP',
        ];
    }
}

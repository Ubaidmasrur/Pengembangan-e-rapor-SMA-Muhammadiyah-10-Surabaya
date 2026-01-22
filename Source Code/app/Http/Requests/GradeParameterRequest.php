<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GradeParameterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $gradeId = $this->route('grade_parameter')?->id; // untuk update

        return [
            'grade_letter' => [
                'required',
                Rule::in(['A', 'B', 'C', 'D', 'E']),
                Rule::unique('grade_parameters')->ignore($gradeId),
            ],
            'min_score'    => ['required', 'integer', 'min:0', 'max:100'],
            'max_score'    => ['required', 'integer', 'min:0', 'max:100', 'gt:min_score'],
        ];
    }

    public function messages(): array
    {
        return [
            'grade_letter.required' => 'Huruf nilai wajib diisi.',
            'grade_letter.in'       => 'Huruf nilai harus antara A sampai E.',
            'grade_letter.unique'   => 'Huruf nilai sudah ada.',
            'min_score.required'    => 'Skor minimum wajib diisi.',
            'max_score.required'    => 'Skor maksimum wajib diisi.',
            'max_score.gt'         => 'Skor maksimum harus lebih besar dari skor minimum.',
        ];
    }

    public function attributes(): array
    {
        return [
            'grade_letter' => 'huruf nilai',
            'min_score'    => 'skor minimum',
            'max_score'    => 'skor maksimum',
        ];
    }
}

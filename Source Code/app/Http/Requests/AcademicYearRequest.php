<?php

namespace App\Http\Requests;

use App\Models\AcademicYear;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class AcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'semester'           => ['required', Rule::in(['Ganjil', 'Genap'])],
            'year'               => ['required', 'regex:/^[0-9]{4}\/[0-9]{4}$/'],
            'is_active'          => ['nullable', 'boolean'],

            'start_month'        => [
                'required',
                'regex:/^\d{4}-(0[1-9]|1[0-2])$/'
            ],
            'end_month'          => [
                'required',
                'regex:/^\d{4}-(0[1-9]|1[0-2])$/'
            ],

            'start_month_select' => ['required', 'string'],
            'end_month_select'   => ['required', 'string'],
            'start_year_select'  => ['required', 'integer'],
            'end_year_select'    => ['required', 'integer'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $startYear = (int) $this->input('start_year_select');
            $endYear   = (int) $this->input('end_year_select');
            $startMonth = $this->input('start_month');
            $endMonth   = $this->input('end_month');
            $year = $this->input('year');
            // $year = "{$startYear}/{$endYear}";
            $semester = $this->input('semester');

            // Tahun mulai >= akhir
            if ($startYear >= $endYear) {
                $validator->errors()->add('start_year_select', 'Tahun mulai harus lebih kecil dari tahun selesai.');
            }

            // Beda tahun lebih dari 1
            if ($endYear - $startYear > 1) {
                $validator->errors()->add('start_year_select', 'Jarak tahun tidak boleh lebih dari 1 tahun.');
            }

            // Tahun sama
            if ($startYear === $endYear) {
                $validator->errors()->add('start_year_select', 'Tahun mulai dan tahun akhir tidak boleh sama.');
            }

            // Bulan mulai lebih besar dari bulan akhir
            if ($startMonth > $endMonth) {
                $validator->errors()->add('end_month_select', 'Bulan akhir tidak boleh sebelum bulan mulai.');
            }

            // Cek kombinasi unik
            $exists = AcademicYear::where('year', $year)
                ->where('semester', $semester)
                ->when($this->route('academic_year'), function ($query) {
                    $query->where('id', '!=', $this->route('academic_year')->id);
                })
                ->exists();

            if ($exists) {
                $validator->errors()->add('semester', 'Tahun ajaran dan semester tersebut sudah terdaftar.');
            }

            // dd($year);
            // dd($validator->errors());
        });
    }

    public function messages(): array
    {
        return [
            'semester.required' => 'Semester wajib dipilih.',
            'semester.in' => 'Semester tidak valid.',

            'year.required' => 'Tahun ajaran wajib diisi.',
            'year.regex'    => 'Format tahun ajaran harus 4 digit/4 digit (contoh: 2025/2026).',

            'start_month.required' => 'Bulan mulai wajib diisi.',
            'start_month.regex'    => 'Format bulan mulai harus dalam format YYYY-MM.',
            'end_month.required'   => 'Bulan akhir wajib diisi.',
            'end_month.regex'      => 'Format bulan akhir harus dalam format YYYY-MM.',

            'start_month_select.required' => 'Bulan mulai wajib dipilih.',
            'end_month_select.required'   => 'Bulan akhir wajib dipilih.',
            'start_year_select.required'  => 'Tahun bulan mulai wajib diisi.',
            'end_year_select.required'    => 'Tahun bulan akhir wajib diisi.',
        ];
    }

    public function attributes(): array
    {
        return [
            'semester'            => 'semester ajaran',
            'year'                => 'tahun ajaran',
            'start_month'         => 'bulan mulai',
            'end_month'           => 'bulan akhir',
            'start_month_select'  => 'bulan mulai',
            'end_month_select'    => 'bulan akhir',
            'start_year_select'   => 'tahun bulan mulai',
            'end_year_select'     => 'tahun bulan akhir',
        ];
    }
}

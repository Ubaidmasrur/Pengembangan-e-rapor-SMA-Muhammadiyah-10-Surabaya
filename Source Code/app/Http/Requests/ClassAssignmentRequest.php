<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ClassTeacherAssignment;
use App\Models\ClassStudentAssignment;
use App\Models\Student;

class ClassAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sesuaikan jika pakai policy
    }

    public function rules(): array
    {
        return [
            'class_id'         => ['required', 'exists:school_classes,id'],
            'teacher_id'       => ['required', 'exists:teachers,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'is_wali'          => ['nullable', 'boolean'],
            'students'         => ['required', 'array', 'min:1'],
            'students.*'       => ['required', 'exists:students,id'],
        ];
    }

    public function prepareForValidation(): void
    {
        // Normalisasi checkbox is_wali -> 0/1
        $this->merge([
            'is_wali' => $this->boolean('is_wali'),
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $classId         = (int) $this->input('class_id');
            $teacherId       = (int) $this->input('teacher_id');
            $academicYearId  = (int) $this->input('academic_year_id');
            $studentIds      = (array) $this->input('students', []);
            $currentId       = $this->route('class_assignment'); // id pada route update

            // Ambil kelas lama (jika edit)
            $previousClassId = null;
            if ($currentId) {
                $current = ClassTeacherAssignment::query()->find($currentId);
                $previousClassId = $current?->class_id;
            }

            // 1) Validasi: 1 guru hanya 1 kelas per tahun ajaran
            $teacherConflict = ClassTeacherAssignment::query()
                ->where('teacher_id', $teacherId)
                ->where('academic_year_id', $academicYearId)
                ->when($currentId, fn($q) => $q->where('id', '!=', $currentId))
                ->where('class_id', '!=', $classId)
                ->exists();

            if ($teacherConflict) {
                $v->errors()->add('teacher_id', 'Guru ini sudah memiliki kelas lain pada tahun ajaran yang sama.');
            }

            // 2) Validasi: satu kelas hanya boleh diampu satu guru pada tahun ajaran yang sama
            $classTaken = ClassTeacherAssignment::query()
                ->where('class_id', $classId)
                ->where('academic_year_id', $academicYearId)
                ->when($currentId, fn($q) => $q->where('id', '!=', $currentId))
                ->where('teacher_id', '!=', $teacherId)
                ->exists();

            if ($classTaken) {
                $v->errors()->add('class_id', 'Kelas ini sudah diampu oleh guru lain pada tahun ajaran yang sama.');
            }

            // 3) Validasi: siswa tidak boleh berada di 2 kelas pada tahun ajaran yang sama
            //    Saat EDIT: abaikan konflik yang berasal dari kelas lama (previousClassId)
            foreach ($studentIds as $idx => $studentId) {
                $conflictQuery = ClassStudentAssignment::query()
                    ->where('student_id', $studentId)
                    ->where('academic_year_id', $academicYearId);

                // Saat edit, abaikan baris di kelas lama dan (opsional) di kelas tujuan sendiri
                if ($currentId) {
                    $ignoreClassIds = array_values(array_filter([$previousClassId, $classId]));
                    if (!empty($ignoreClassIds)) {
                        $conflictQuery->whereNotIn('class_id', $ignoreClassIds);
                    }
                } else {
                    // Saat create, cukup pastikan dia belum ada di kelas lain pada tahun ajaran yang sama
                    $conflictQuery->where('class_id', '!=', $classId);
                }

                $conflict = $conflictQuery->exists();

                if ($conflict) {
                    $student  = Student::find($studentId);
                    $nisn     = $student?->nisn ?? 'N/A';
                    $nama     = $student?->name ?? 'Siswa';
                    // Taruh error di "students" agar mudah ditampilkan (bukan pakai key id)
                    $v->errors()->add('students', "Siswa {$nama} (NISN {$nisn}) sudah terdaftar di kelas lain pada tahun ajaran yang sama.");
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'class_id.required'         => 'Kelas wajib dipilih.',
            'class_id.exists'           => 'Kelas tidak valid.',
            'teacher_id.required'       => 'Guru wajib dipilih.',
            'teacher_id.exists'         => 'Guru tidak valid.',
            'academic_year_id.required' => 'Tahun ajaran wajib dipilih.',
            'academic_year_id.exists'   => 'Tahun ajaran tidak valid.',
            'students.required'         => 'Minimal 1 siswa harus dipilih.',
            'students.array'            => 'Format siswa tidak valid.',
            'students.min'              => 'Minimal 1 siswa harus dipilih.',
            'students.*.exists'         => 'Salah satu siswa tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'class_id'         => 'kelas',
            'teacher_id'       => 'guru',
            'academic_year_id' => 'tahun ajaran',
            'students'         => 'siswa',
        ];
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Database\Seeders\Concerns\TruncatesTables;

class ClassTeacherAssignmentSeeder extends Seeder
{
    // use TruncatesTables;
    public function run(): void
    {
        DB::table('class_teacher_assignments')->delete();
        $teachers = Teacher::all();
        $classes = SchoolClass::all();
        $academicYears = AcademicYear::all();

        foreach ($academicYears as $year) {
            foreach ($teachers as $teacher) {
                // Assign each teacher to one random class per semester
                $class = $classes->random();
                DB::table('class_teacher_assignments')->insert([
                    'teacher_id' => $teacher->id,
                    'class_id' => $class->id,
                    'academic_year_id' => $year->id,
                    'is_wali' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Database\Seeders\Concerns\TruncatesTables;

class ClassStudentAssignmentSeeder extends Seeder
{
    // use TruncatesTables;
    public function run()
    {
        DB::table('class_student_assignments')->delete();
        $students = Student::all();
        $classes = SchoolClass::all();
        $academicYears = AcademicYear::all();

        foreach ($academicYears as $year) {
            // Shuffle students for random assignment
            $shuffledStudents = $students->shuffle();
            $studentChunks = $shuffledStudents->chunk(ceil($students->count() / $classes->count()));
            $classIdx = 0;
            foreach ($classes as $class) {
                // Get chunk of students for this class in this semester
                $assignedStudents = isset($studentChunks[$classIdx]) ? $studentChunks[$classIdx] : collect();
                foreach ($assignedStudents as $student) {
                    DB::table('class_student_assignments')->insert([
                        'student_id' => $student->id,
                        'class_id' => $class->id,
                        'academic_year_id' => $year->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $classIdx++;
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentGrade;
use App\Models\StudentGradeDetail;
use App\Models\Subject;
use Database\Seeders\Concerns\TruncatesTables;

class StudentGradeDetailsSeeder extends Seeder
{
    use TruncatesTables;
    public function run(): void
    {
        $this->truncate(['student_grades', 'student_grade_details']);
        $faker = \Faker\Factory::create();

        $students = \App\Models\Student::all();
        $academicYears = \App\Models\AcademicYear::all();
        $subjects = \App\Models\Subject::all();
        $classes = \App\Models\SchoolClass::all();
        $teachers = \App\Models\Teacher::all();

        foreach ($academicYears as $year) {
            foreach ($students as $student) {
                // Find student's class for this academic year
                $classAssignment = \App\Models\ClassStudentAssignment::where('student_id', $student->id)
                    ->where('academic_year_id', $year->id)
                    ->first();
                if (!$classAssignment) continue;
                $classId = $classAssignment->class_id;

                // Find teacher for this class and year
                $teacherAssignment = \App\Models\ClassTeacherAssignment::where('class_id', $classId)
                    ->where('academic_year_id', $year->id)
                    ->first();
                $teacherId = $teacherAssignment ? $teacherAssignment->teacher_id : null;

                // Create StudentGrade (master)
                // Pilih nama bulan valid untuk period
                $validMonths = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                $period = $faker->randomElement($validMonths);
                $studentGrade = \App\Models\StudentGrade::create([
                    'student_id' => $student->id,
                    'academic_year_id' => $year->id,
                    'class_id' => $classId,
                    'teacher_id' => $teacherId,
                    'score' => null,
                    'grade_letter' => null,
                    'notes' => $faker->optional()->sentence,
                    'motorik' => $faker->numberBetween(70, 100),
                    'kognitif' => $faker->numberBetween(70, 100),
                    'sosial' => $faker->numberBetween(70, 100),
                    'period' => $period,
                ]);

                // Create StudentGradeDetail for each subject
                foreach ($subjects as $subject) {
                    \App\Models\StudentGradeDetail::create([
                        'student_grade_id' => $studentGrade->id,
                        'subject_id' => $subject->id,
                        'score' => $faker->numberBetween(70, 100),
                        'grade_letter' => null,
                        'notes' => $faker->optional()->sentence,
                    ]);
                }
            }
        }
    }
}

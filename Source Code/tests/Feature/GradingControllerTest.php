<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\GradeParameter;
use App\Models\StudentGrade;
use App\Models\StudentGradeDetail;

class GradingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_derives_grade_letter_and_preserves_master_period()
    {
        // Create necessary models
        $user = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create(['name' => 'T Guru', 'nip' => 'T001', 'user_id' => $user->id]);

        $studentUser = User::factory()->create(['role' => 'siswa']);
        $student = Student::create(['user_id' => $studentUser->id, 'nisn' => '12345', 'name' => 'Siswa Satu', 'gender' => 'L', 'birth_date' => now()]);

        $class = SchoolClass::create(['name' => 'Kelas 1']);
        $year = AcademicYear::create(['year' => '2024/2025', 'semester' => 'Ganjil']);
    $subject = Subject::create(['name' => 'Matematika', 'type' => 'umum']);

    // grade parameter: score 80 -> B (global parameter)
    GradeParameter::create(['min_score' => 70, 'max_score' => 84, 'grade_letter' => 'B']);

        $this->actingAs($user);

        // Test single store (modal) endpoint
        $response = $this->postJson(route('grades.store'), [
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'academic_year_id' => $year->id,
            'class_id' => $class->id,
            'score' => 80,
            'notes' => 'Good',
            'period' => 'Maret',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('student_grades', ['student_id' => $student->id, 'academic_year_id' => $year->id, 'class_id' => $class->id, 'period' => 'Maret']);
        $this->assertDatabaseHas('student_grade_details', ['score' => 80, 'grade_letter' => 'B']);

        // Test bulk store: new subject and ensure period from master applied/updated
    $subject2 = Subject::create(['name' => 'IPA', 'type' => 'umum']);
    GradeParameter::create(['min_score' => 85, 'max_score' => 100, 'grade_letter' => 'A']);

        $bulk = [
            'master_student_id' => $student->id,
            'master_academic_year_id' => $year->id,
            'master_class_id' => $class->id,
            'master_period' => 'April',
            'grades' => [
                [
                    'subject_id' => $subject2->id,
                    'score' => 90,
                ],
            ],
        ];

        $res2 = $this->postJson(route('grades.bulkStore'), $bulk);
        $res2->assertStatus(201);

        $this->assertDatabaseHas('student_grades', ['student_id' => $student->id, 'period' => 'April']);
        $this->assertDatabaseHas('student_grade_details', ['subject_id' => $subject2->id, 'grade_letter' => 'A']);
    }
}

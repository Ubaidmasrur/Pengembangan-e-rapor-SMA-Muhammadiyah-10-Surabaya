<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\GradeParameter;
use App\Models\StudentGradeDetail;
use App\Models\StudentGrade;
use App\Models\Student;
use App\Models\Subject;

uses(TestCase::class, RefreshDatabase::class);

it('derives grade letter from grade parameters based on score', function () {
    // Create subject and grade parameter
    $subject = Subject::create(['name' => 'Matematika', 'type' => 'umum']);

    GradeParameter::create([
        'min_score' => 0,
        'max_score' => 74,
        'grade_letter' => 'C',
    ]);
    GradeParameter::create([
        'min_score' => 75,
        'max_score' => 89,
        'grade_letter' => 'B',
    ]);
    GradeParameter::create([
        'min_score' => 90,
        'max_score' => 100,
        'grade_letter' => 'A',
    ]);

    // Create an in-memory detail instance (no need to persist student/master)
    $detail = new StudentGradeDetail([
        'subject_id' => $subject->id,
        'score' => 88,
    ]);

    expect($detail->grade_letter)->toBe('B');
});

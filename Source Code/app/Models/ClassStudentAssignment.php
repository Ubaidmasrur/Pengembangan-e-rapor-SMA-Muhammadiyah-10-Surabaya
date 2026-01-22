<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassStudentAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'class_student_assignments';

    protected $fillable = [
        'student_id',
        'class_id',
        'academic_year_id',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    // Search logic for grade input table
    public static function searchStudentAssignmentData($student_search = '', $class_id = '', $academic_year_id = '')
    {
        $query = self::query()
            ->join('students', 'class_student_assignments.student_id', '=', 'students.id')
            ->join('school_classes', 'class_student_assignments.class_id', '=', 'school_classes.id')
            ->join('academic_years', 'class_student_assignments.academic_year_id', '=', 'academic_years.id')
            ->select(
                'students.id as student_id',
                'students.name as student_name',
                'school_classes.name as class_name',
                'academic_years.year as academic_year',
                'academic_years.semester as semester'
            );

        if ($student_search) {
            $query->where(function ($q) use ($student_search) {
                $q->where('students.name', 'like', '%' . $student_search . '%')
                    ->orWhere('school_classes.name', 'like', '%' . $student_search . '%')
                    ->orWhere('academic_years.year', 'like', '%' . $student_search . '%')
                    ->orWhere('academic_years.semester', 'like', '%' . $student_search . '%');
            });
        }
        if ($class_id) {
            $query->where('school_classes.id', $class_id);
        }
        if ($academic_year_id) {
            $query->where('academic_years.id', $academic_year_id);
        }

        return $query->paginate(10);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class StudentGradeDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_grade_id',
        'subject_id',
        'score',
        'grade_letter',
        'notes',
        'fase',
        'fase_desc'
    ];

    public function studentGrade()
    {
        return $this->belongsTo(StudentGrade::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // Return grade letter: stored value or derived from GradeParameter
    public function getGradeLetterAttribute($value)
    {
        if ($value) return $value;

        $score = $this->score;
        $subjectId = $this->subject_id ?? optional($this->subject)->id;
        if ($score === null) return null;

        $query = GradeParameter::query();

        // Only apply subject filter when the column exists and we have a subject id
        if (Schema::hasColumn('grade_parameters', 'subject_id') && $subjectId) {
            $query->where('subject_id', $subjectId);
        }

        $param = $query->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->orderByDesc('min_score')
            ->first();

        return $param ? $param->grade_letter : null;
    }
}

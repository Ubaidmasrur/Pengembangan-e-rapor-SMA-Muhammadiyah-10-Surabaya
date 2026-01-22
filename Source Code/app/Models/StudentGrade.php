<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Models\AcademicYear;

class StudentGrade extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'subject_id',
        'academic_year_id',
        'class_id',
        'teacher_id',
        'score',
        'grade_letter',
        'notes',
        'motorik',
        'kognitif',
        'sosial',
        'period',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'motorik' => 'decimal:2',
        'kognitif' => 'decimal:2',
        'sosial' => 'decimal:2',
    ];

    /**
     * Normalize and set the period attribute.
     * Accepts YYYY-MM or Indonesian month names (e.g. 'Januari') and will
     * attempt to derive a YYYY-MM value using the related academic year when possible.
     */
    public function setPeriodAttribute($value)
    {
        if (is_null($value) || $value === '') {
            $this->attributes['period'] = null;
            return;
        }

        $v = trim($value);
        // If it's already YYYY-MM, accept
        if (preg_match('/^\d{4}-\d{2}$/', $v)) {
            $this->attributes['period'] = $v;
            return;
        }

        // Try to map Indonesian month names to number
        $months = [
            'januari' => '01','februari' => '02','maret' => '03','april' => '04','mei' => '05','juni' => '06',
            'juli' => '07','agustus' => '08','september' => '09','oktober' => '10','november' => '11','desember' => '12'
        ];

        $low = mb_strtolower($v);
        if (isset($months[$low])) {
            $mm = $months[$low];
            // Attempt to derive year from academicYear relation if present
            $year = null;
            try {
                if (!empty($this->academic_year_id)) {
                    $ay = AcademicYear::find($this->academic_year_id);
                    if ($ay && $ay->year) {
                        // academicYear->year expected like '2024/2025' -> take first part as starting year
                        if (strpos($ay->year, '/') !== false) {
                            $parts = explode('/', $ay->year);
                            $year = intval($parts[0]);
                        } else {
                            $year = intval($ay->year);
                        }
                    }
                }
            } catch (\Throwable $e) {
                $year = null;
            }

            if ($year) {
                $this->attributes['period'] = sprintf('%04d-%s', $year, $mm);
                return;
            }
        }

        // Fallback: store raw trimmed value (so nothing lost) but prefer null if invalid
        $this->attributes['period'] = $v;
    }

    /**
     * Scope for finding master record by student/year/class/period/teacher
     */
    public function scopeMasterKey($query, $studentId, $yearId, $classId, $period = null, $teacherId = null)
    {
        $q = $query->where('student_id', $studentId)
            ->where('academic_year_id', $yearId)
            ->where('class_id', $classId);
        if (!is_null($period)) $q->where('period', $period);
        if (!is_null($teacherId)) $q->where('teacher_id', $teacherId);
        return $q;
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function details()
    {
        return $this->hasMany(StudentGradeDetail::class, 'student_grade_id');
    }

    // Human-friendly period label (e.g. "Januari 2025")
    public function getPeriodLabelAttribute()
    {
        $period = $this->period;
        if (!$period) {
            return optional($this->academicYear)->year;
        }

        // If period is YYYY-MM, format into localized month name + year
        if (preg_match('/^(\d{4})-(\d{2})$/', $period, $m)) {
            try {
                $dt = Carbon::createFromFormat('Y-m', $period);
                return $dt->translatedFormat('F Y');
            } catch (\Exception $e) {
                return $period;
            }
        }

        return $period;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use App\Models\ClassStudentAssignment;

class ClassTeacherAssignment extends Model
{
    use SoftDeletes;

    protected $table = 'class_teacher_assignments';

    protected $fillable = [
        'teacher_id',
        'class_id',
        'academic_year_id',
        'is_wali',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function ($assignment) {
            // Jika soft delete (bukan forceDelete)
            if (! $assignment->isForceDeleting()) {
                ClassStudentAssignment::where('class_id', $assignment->class_id)
                    ->where('academic_year_id', $assignment->academic_year_id)
                    ->delete();
            }
        });

        static::restoring(function ($assignment) {
            // Restore siswa juga
            ClassStudentAssignment::withTrashed()
                ->where('class_id', $assignment->class_id)
                ->where('academic_year_id', $assignment->academic_year_id)
                ->restore();
        });

        static::forceDeleted(function ($assignment) {
            // Hapus permanen siswa juga
            ClassStudentAssignment::withTrashed()
                ->where('class_id', $assignment->class_id)
                ->where('academic_year_id', $assignment->academic_year_id)
                ->forceDelete();
        });
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function classStudentAssignments()
    {
        return $this->hasMany(ClassStudentAssignment::class, 'class_id', 'class_id')
            ->whereColumn('academic_year_id', 'academic_year_id');
    }

    public static function groupedWithStudentCounts(
        ?string $search = null,
        ?string $academicYearFilter = null,
        ?string $className = null,
        ?string $studentName = null
    ): Collection {
        $query = self::with(['class', 'teacher', 'academicYear'])->withTrashed();

        // 🔍 Handle search guru/kelas
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('class', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('teacher', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // 🎯 Tahun Ajaran + Semester
        if ($academicYearFilter && str_contains($academicYearFilter, '-')) {
            [$year, $semesterName] = explode('-', $academicYearFilter);

            // Map "Ganjil" => 1, "Genap" => 2
            $semesterMap = [
                'Ganjil' => 1,
                'Genap' => 2,
            ];

            $semester = $semesterMap[ucfirst(strtolower($semesterName))] ?? null;

            if (! $semester) {
                return collect(); // semester invalid
            }

            $academicYear = AcademicYear::where('year', $year)
                ->where('semester', $semester)
                ->first();

            if (! $academicYear) {
                return collect(); // kombinasi tidak cocok
            }

            $query->where('academic_year_id', $academicYear->id);
        }

        // 📌 Filter berdasarkan nama kelas
        if ($className) {
            $query->whereHas('class', fn($q) => $q->where('name', 'like', "%{$className}%"));
        }

        // 👨‍🎓 Filter berdasarkan nama siswa
        if ($studentName) {
            $query->whereHas('classStudentAssignments.student', function ($q) use ($studentName) {
                $q->where('name', 'like', "%{$studentName}%");
            });
        }

        // 🔁 Grouping
        $assignments = $query->get()->groupBy(function ($item) {
            return implode('-', [
                $item->academic_year_id,
                $item->class_id,
                $item->teacher_id,
            ]);
        });

        return $assignments->map(function ($groupItems) {
            $academicYear  = $groupItems->first()->academicYear;
            $class         = $groupItems->first()->class;
            $teacher       = $groupItems->first()->teacher;
            $isWali        = $groupItems->contains('is_wali', true);
            $assignmentIds = $groupItems->pluck('id')->all();

            $studentCount = \App\Models\ClassStudentAssignment::where('academic_year_id', $academicYear->id)
                ->where('class_id', $class->id)
                ->count();

            return (object) [
                'academicYear'   => $academicYear,
                'class'          => $class,
                'teacher'        => $teacher,
                'is_wali'        => $isWali,
                'students_count' => $studentCount,
                'id'             => $assignmentIds,
            ];
        })->values();
    }
}
